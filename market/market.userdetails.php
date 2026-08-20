<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=users.details.tags
 * [END_COT_EXT]
 */

/**
 * Market module
 * FILENAME: market.userdetails.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
use cot\modules\market\inc\MarketDictionary;
require_once cot_incfile('market', 'module');

$perpageincat = isset(Cot::$cfg['market']['cat___default']['marketmaxlistsperpageincat'])
    ? Cot::$cfg['market']['cat___default']['marketmaxlistsperpageincat']
    : (Cot::$cfg['market']['cat___default']['marketmaxlistsperpage'] ?? 10);

// ========== НАЧАЛО: AJAX-обработчик ==========
if (cot_import('ajax', 'G', 'INT') == 1)
{
    global $usr, $urr, $db_market;

    $tab = cot_import('tab', 'G', 'ALP');
    $category = ($tab == 'market') ? cot_import('cat', 'G', 'TXT') : '';
    $d = cot_import('dmarket', 'G', 'INT');

    $where = [];
    $order = [];
    if ($usr['id'] == 0 || ($usr['id'] != $urr['user_id'] && !$usr['isadmin']))
        $where['state'] = 'fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED;
    if ($category)
        $where['cat'] = 'fieldmrkt_cat = ' . Cot::$db->quote($category);
    $where['owner'] = 'fieldmrkt_ownerid = ' . (int)$urr['user_id'];
    $order['date'] = 'fieldmrkt_date DESC';

    foreach (cot_getextplugins('market.userdetails.query') as $pl) include $pl;

    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $order_sql = $order ? 'ORDER BY ' . implode(', ', $order) : '';

    // Общее количество товаров
    $totalitems = Cot::$db->query("SELECT COUNT(*) FROM $db_market AS m $where_sql")->fetchColumn();

    $sqllist = Cot::$db->query("SELECT * FROM $db_market AS m $where_sql $order_sql LIMIT $d, $perpageincat");
    $items = $sqllist->fetchAll();

    if (empty($items)) {
        header('Content-Type: application/json');
        echo json_encode(['rows' => '', 'pagination' => '']);
        exit;
    }

    $extp_loop = cot_getextplugins('market.userdetails.loop');
    $ajax_tpl = new XTemplate(cot_tplfile(['market', 'userdetails'], 'module'));

    foreach ($items as $item_data)
    {
        $tags = cot_generate_markettags($item_data, 'MARKET_ROW_', Cot::$cfg['market']['markettruncatetext'] ?? 0, $usr['isadmin'], Cot::$cfg['homebreadcrumb']);
        if (!empty($tags['MARKET_ROW_ADMIN_DELETE_URL']))
        {
            $urlParams = ['m' => 'details', 'id' => $urr['user_id'], 'u' => $urr['user_name'], 'tab' => 'market'];
            if ($category) $urlParams['cat'] = $category;
            if ($d > 0) $urlParams['dmarket'] = $d;
            $delUrl = cot_url('market', ['m' => 'edit', 'a' => 'update', 'delete' => '1', 'id' => $item_data['fieldmrkt_id'], 'x' => Cot::$sys['xk'], 'redirect' => base64_encode(cot_url('users', $urlParams, '', true))]);
            $delConfirm = cot_confirm_url($delUrl, 'market');
            $tags['MARKET_ROW_ADMIN_DELETE'] = cot_rc_link($delConfirm, Cot::$L['Delete'], 'class="confirmLink"');
            $tags['MARKET_ROW_ADMIN_DELETE_URL'] = $delConfirm;
        }
        foreach ($extp_loop as $pl) include $pl;
        $ajax_tpl->assign($tags);
        $ajax_tpl->parse('MAIN.MARKET_ROWS');
    }
    $rows_html = $ajax_tpl->text('MAIN.MARKET_ROWS');

    // Генерация HTML пагинации через отдельный шаблон
    $opt_array = ['m'=>'details', 'id'=>$urr['user_id'], 'u'=>$urr['user_name'], 'tab'=>'market'];
    if ($category) $opt_array['cat'] = $category;

    $pagenav = cot_pagenav('users', $opt_array, $d, $totalitems, $perpageincat, 'dmarket');

    $pagination_tpl = new XTemplate(cot_tplfile(['market', 'pagination'], 'module'));
    $pagination_tpl->assign(cot_generatePaginationTags($pagenav));
    $pagination_tpl->parse('MAIN');
    $pagination_html = $pagination_tpl->text('MAIN');

    header('Content-Type: application/json');
    echo json_encode([
        'rows' => $rows_html,
        'pagination' => $pagination_html
    ]);
    exit;
}
// ========== КОНЕЦ: AJAX-обработчик ==========

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('market', 'any', 'RWA');

$tab = cot_import('tab', 'G', 'ALP');
$category = ($tab == 'market') ? cot_import('cat', 'G', 'TXT') : '';
list($pg, $d, $durl) = cot_import_pagenav('dmarket', $perpageincat);

// Вкладка товаров
$t1 = new XTemplate(cot_tplfile(['market', 'userdetails'], 'module'));
$t1->assign([
    'MARKET_ADD_URL' => cot_url('market', 'm=add'),
    'MARKET_ADD_SHOWBUTTON' => $usr['auth_write'] ? true : false,
]);

$where = [];
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

// Переменные для кнопки "Загрузить ещё"
$totalpages = ceil($market_count / $perpageincat);
$currentpage = floor($d / $perpageincat) + 1;

$t1->assign([
    'LOAD_MORE_URL' => cot_url('users', ['m' => 'details', 'id' => $urr['user_id'], 'u' => $urr['user_name'], 'tab' => 'market', 'cat' => $category, 'ajax' => 1]),
    'LOAD_MORE_PERPAGE' => $perpageincat,
    'LOAD_MORE_TOTALPAGES' => $totalpages,
    'LOAD_MORE_CURRENTPAGE' => $currentpage,
    'LOAD_MORE_LANG' => json_encode([
        'load_more' => $L['market_load_more'] ?? 'Загрузить ещё (страница %d из %d)',
        'loading'   => $L['market_loading'] ?? '<i class="fa fa-spinner fa-spin"></i> Загрузка...',
        'error'     => $L['market_load_error'] ?? 'Ошибка загрузки. Попробуйте ещё раз.'
    ])
]);

$sqllist = Cot::$db->query("SELECT * FROM $db_market AS m
    $where_sql
    $order_sql
    LIMIT $d, $perpageincat");

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

$pagenav = cot_pagenav('users', $opt_array, $d, $market_count, $perpageincat, 'dmarket');
$t1->assign(cot_generatePaginationTags($pagenav));

$sqllist_rowset = $sqllist->fetchAll();

/* === Hook === */
$extp = cot_getextplugins('market.userdetails.loop');
/* ===== */

foreach ($sqllist_rowset as $item_data) {
    $marketTags = cot_generate_markettags(
        $item_data,
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
                'id' => $item_data['fieldmrkt_id'],
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

Cot::$sys['noindex'] = false;

$t1->parse('MAIN');

$t->assign([
    'USERS_DETAILS_MARKET_COUNT' => $market_count_all,
    'USERS_DETAILS_MARKET_URL' => cot_url('users', ['m' => 'details', 'id' => $urr['user_id'], 'u' => $urr['user_name'], 'tab' => 'market']),
]);

$t->assign('MARKET', $t1->text('MAIN'));
