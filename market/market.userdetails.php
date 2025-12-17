<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=users.details.tags
 * [END_COT_EXT]
 */

/**
 * Market module
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
use cot\modules\market\inc\MarketDictionary;
require_once cot_incfile('market', 'module');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('market', 'any', 'RWA');

$tab = cot_import('tab', 'G', 'ALP');
$category = ($tab == 'market') ? cot_import('cat', 'G', 'TXT') : '';
list($pg, $d, $durl) = cot_import_pagenav('dmarket', Cot::$cfg['market']['cat___default']['marketmaxlistsperpage']);

// Вкладка товаров
$t1 = new XTemplate(cot_tplfile(['market', 'userdetails'], 'module'));
$t1->assign([
    'MARKET_ADD_URL' => cot_url('market', 'm=add'),
    'MARKET_ADD_SHOWBUTTON' => $usr['auth_write'] ? true : false, // Для совместимости
]);

$where = [];
$params = [];
$order = [];

if ($usr['id'] == 0 || ($usr['id'] != $urr['user_id'] && !$usr['isadmin'])) {
    $where['state'] = 'fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED;
}

if ($category) {
    $where['cat'] = 'fieldmrkt_cat = ' . Cot::$db->quote($category);
}

$where['owner'] = 'fieldmrkt_ownerid = ' . (int)$urr['user_id'];

$order['date'] = 'fieldmrkt_date DESC';

$wherecount = $where;
if (isset($wherecount['cat'])) {
    unset($wherecount['cat']);
}

/* === Hook === */
foreach (cot_getextplugins('market.userdetails.query') as $pl) {
    include $pl;
}
/* ===== */

$where = array_filter($where);
$wherecount = array_filter($wherecount);
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$wherecount_sql = $wherecount ? 'WHERE ' . implode(' AND ', $wherecount) : '';
$order_sql = $order ? 'ORDER BY ' . implode(', ', $order) : '';

$sql_market_count_cat = Cot::$db->query("SELECT fieldmrkt_cat, COUNT(fieldmrkt_cat) as cat_count FROM $db_market $wherecount_sql GROUP BY fieldmrkt_cat")->fetchAll();

$sql_market_count = Cot::$db->query("SELECT COUNT(*) FROM $db_market $wherecount_sql");
$market_count_all = $market_count = $sql_market_count->fetchColumn();

$sqllist = Cot::$db->query("SELECT * FROM $db_market AS m
    $where_sql
    $order_sql
    LIMIT $d, " . Cot::$cfg['market']['cat___default']['marketmaxlistsperpage']);

foreach ($sql_market_count_cat as $value) {
    $t1->assign([
        'MARKET_CAT_ROW_TITLE' => Cot::$structure['market'][$value['fieldmrkt_cat']]['title'] ?? '',
        'MARKET_CAT_ROW_ICON' => Cot::$structure['market'][$value['fieldmrkt_cat']]['icon'] ?? '',
        'MARKET_CAT_ROW_URL' => cot_url('users', ['m' => 'details', 'id' => $urr['user_id'], 'u' => $urr['user_name'], 'tab' => 'market', 'cat' => $value['fieldmrkt_cat']]),
        'MARKET_CAT_ROW_COUNT_MARKET' => $value['cat_count'],
        'MARKET_CAT_ROW_SELECT' => ($category && $category == $value['fieldmrkt_cat']) ? 1 : '',
    ]);
    $t1->parse('MAIN.CAT_ROW');
}

$opt_array = [
    'm' => 'details',
    'id' => $urr['user_id'],
    'u' => $urr['user_name'],
    'tab' => 'market',
];
if ($category) {
    $market_count = $sql_market_count_cat[array_search($category, array_column($sql_market_count_cat, 'fieldmrkt_cat'))]['cat_count'] ?? $market_count;
    $opt_array['cat'] = $category;
}

$pagenav = cot_pagenav('users', $opt_array, $d, $market_count, Cot::$cfg['market']['cat___default']['marketmaxlistsperpage'], 'dmarket');

$t1->assign([
    'PAGENAV_PAGES' => $pagenav['main'],
    'PAGENAV_PREV' => $pagenav['prev'],
    'PAGENAV_NEXT' => $pagenav['next'],
    'PAGENAV_COUNT' => $market_count,
]);

$sqllist_rowset = $sqllist->fetchAll();
$sqllist_idset = [];

foreach ($sqllist_rowset as $item) {
    $sqllist_idset[$item['fieldmrkt_id']] = $item['fieldmrkt_alias'];
}

/* === Hook === */
$extp = cot_getextplugins('market.userdetails.loop');
/* ===== */

foreach ($sqllist_rowset as $item) {
    $marketTags = cot_generate_markettags(
        $item,
        'MARKET_ROW_',
        Cot::$cfg['market']['markettruncatetext'] ?? 0,
        Cot::$usr['isadmin'],
        Cot::$cfg['homebreadcrumb']
    );

    if (!empty($marketTags['MARKET_ROW_ADMIN_DELETE_URL'])) {
        $urlParams = $opt_array;
        if ($durl > 0) {
            $urlParams['dmarket'] = $durl;
        }
        $deleteUrl = cot_url(
            'market',
            [
                'm' => 'edit',
                'a' => 'update',
                'delete' => '1',
                'id' => $item['fieldmrkt_id'],
                'x' => Cot::$sys['xk'],
                'redirect' => base64_encode(cot_url('users', $urlParams, '', true)),
            ]
        );
        $deleteConfirmUrl = cot_confirm_url($deleteUrl, 'market');
        $marketTags['MARKET_ROW_ADMIN_DELETE'] = cot_rc_link(
            $deleteConfirmUrl,
            Cot::$L['Delete'],
            'class="confirmLink"'
        );
        $marketTags['MARKET_ROW_ADMIN_DELETE_URL'] = $deleteConfirmUrl;
    }

    $t1->assign($marketTags);

    /* === Hook === */
    foreach ($extp as $pl) {
        include $pl;
    }
    /* ===== */

    $t1->parse('MAIN.MARKET_ROWS');
}

/* === Hook === */
foreach (cot_getextplugins('market.userdetails.tags') as $pl) {
    include $pl;
}
/* ===== */

Cot::$sys['noindex'] = false; // Убираем noindex для вкладки товаров

$t1->parse('MAIN');

$t->assign([
    'USERS_DETAILS_MARKET_COUNT' => $market_count_all,
    'USERS_DETAILS_MARKET_URL' => cot_url('users', ['m' => 'details', 'id' => $urr['user_id'], 'u' => $urr['user_name'], 'tab' => 'market']),
]);

$t->assign('MARKET', $t1->text('MAIN'));
