<?php

/**
 * Store item display on CMF Cotonti Siena v.0.9.26 PHP 8.4.
 * filename market.main.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */
// market.main.php
use cot\modules\market\inc\MarketDictionary;

defined('COT_CODE') or die('Wrong URL');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');
cot_block(Cot::$usr['auth_read']);
 global $db_market, $db_users;
$id = cot_import('id', 'G', 'INT');
$al = Cot::$db->prep(cot_import('al', 'G', 'TXT'));
$c = cot_import('c', 'G', 'TXT');
$pg = cot_import('pg', 'G', 'INT');

$join_columns = isset($join_columns) ? $join_columns : '';
$join_condition = isset($join_condition) ? $join_condition : '';

/* === Hook === */
foreach (cot_getextplugins('market.first') as $pl) {
	include $pl;
}
/* ===== */

if ($id > 0 || !empty($al)) {
	$where = (!empty($al)) ? "p.fieldmrkt_alias='".$al."'" : 'p.fieldmrkt_id='.$id;
	if (!empty($c)) {
        $where .= " AND p.fieldmrkt_cat = " . Cot::$db->quote($c);
    }
	$sql_item = Cot::$db->query("SELECT p.*, u.* $join_columns
		FROM $db_market AS p $join_condition
		LEFT JOIN $db_users AS u ON u.user_id=p.fieldmrkt_ownerid
		WHERE $where LIMIT 1");
}

if (!$id && empty($al) || !$sql_item || $sql_item->rowCount() == 0) {
	cot_die_message(404);
}
$item = $sql_item->fetch();

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', $item['fieldmrkt_cat'], 'RWA1');
cot_block(Cot::$usr['auth_read']);

$al = empty($item['fieldmrkt_alias']) ? '' : $item['fieldmrkt_alias'];
$id = (int) $item['fieldmrkt_id'];
$cat = Cot::$structure['market'][$item['fieldmrkt_cat']];

$sys['sublocation'] = $item['fieldmrkt_title'];

$item['fieldmrkt_tab'] = empty($pg) ? 0 : $pg;

$urlParams = ['c' => $item['fieldmrkt_cat']];
if (!empty($al)) {
    $urlParams['al'] = $al;
} else {
    $urlParams['id'] = $id;
}
$item['fieldmrkt_pageurl'] = cot_url('market', $urlParams, '', true);

if (
    (
        $item['fieldmrkt_state'] == MarketDictionary::STATE_PENDING
        || $item['fieldmrkt_state'] == MarketDictionary::STATE_DRAFT
    )
    && (!Cot::$usr['isadmin'] && Cot::$usr['id'] != $item['fieldmrkt_ownerid'])
) {
    cot_log("Attempt to directly access an un-validated store item", 'sec', 'market', 'error');
    cot_die_message(403, TRUE);
}

$itemHasMessages = cot_check_messages();

$itemStaticCacheEnabled = Cot::$cache
    && Cot::$usr['id'] === 0
    && Cot::$cfg['cache_market']
    && !$itemHasMessages
    && (!isset(Cot::$cfg['cache_market_blacklist']) || !in_array($item['fieldmrkt_cat'], Cot::$cfg['cache_market_blacklist']));

// Store item views counter
if (!Cot::$usr['isadmin'] || Cot::$cfg['market']['marketcount_admin']) {
    if (!$itemStaticCacheEnabled) {
        $item['fieldmrkt_count']++;
        Cot::$db->update(
            Cot::$db->market,
            ['fieldmrkt_count' => $item['fieldmrkt_count']],
            'fieldmrkt_id = ?',
            $item['fieldmrkt_id']
        );
    } else {
        Resources::embedFooter(
            'fetch("' . cot_url(
                'market',
                ['e' => 'market', 'm' => 'counter', 'a' => 'views', 'id' => $item['fieldmrkt_id']],
                '',
                true
            ) . '")'
        );
    }
}

if ($item['fieldmrkt_cat'] == 'system') {
    Cot::$out['subtitle'] = empty($item['fieldmrkt_metatitle']) ? $item['fieldmrkt_title'] : $item['fieldmrkt_metatitle'];
} else {
	$title_params = array(
		'TITLE' => empty($item['fieldmrkt_metatitle']) ? $item['fieldmrkt_title'] : $item['fieldmrkt_metatitle'],
		'CATEGORY' => $cat['title']
	);
    Cot::$out['subtitle'] = cot_title(Cot::$cfg['market']['markettitle_page'], $title_params);
}
Cot::$out['desc'] = empty($item['fieldmrkt_metadesc']) ? strip_tags($item['fieldmrkt_desc']) : strip_tags($item['fieldmrkt_metadesc']);
Cot::$out['keywords'] = !empty($item['fieldmrkt_keywords']) ? strip_tags($item['fieldmrkt_keywords']) : '';

// Building the canonical URL
$itemurl_params = array('c' => $item['fieldmrkt_cat']);
empty($al) ? $itemurl_params['id'] = $id : $itemurl_params['al'] = $al;
if ($pg > 0) {
	$itemurl_params['pg'] = $pg;
}
Cot::$out['canonical_uri'] = cot_url('market', $itemurl_params);

$mskin = cot_tplfile(array('market', $cat['tpl']));

Cot::$env['last_modified'] = $item['fieldmrkt_updated'];
Cot::$sys['noindex'] = false;
Cot::$R['code_noindex'] = '';

/* === Hook === */
foreach (cot_getextplugins('market.main') as $pl) {
	include $pl;
}
/* ============ */


require_once cot_incfile('users', 'module');
$t = new XTemplate($mskin);

$t->assign(
    cot_generate_markettags(
        $item,
        'MARKET_',
        0,
        Cot::$usr['isadmin'],
        Cot::$cfg['homebreadcrumb'],
        '',
        $item['fieldmrkt_pageurl']
    )
);
$t->assign('MARKET_OWNER', cot_build_user($item['fieldmrkt_ownerid'], $item['user_name']));
$t->assign(cot_generate_usertags($item, 'MARKET_OWNER_'));


// Multi tabs
$item['fieldmrkt_tabs'] = explode('[newpage]', $t->vars['MARKET_TEXT'], 99);
$item['fieldmrkt_totaltabs'] = count($item['fieldmrkt_tabs']);

if ($item['fieldmrkt_totaltabs'] > 1) {
	if (empty($item['fieldmrkt_tabs'][0])) {
		$remove = array_shift($item['fieldmrkt_tabs']);
		$item['fieldmrkt_totaltabs']--;
	}
	$max_tab = $item['fieldmrkt_totaltabs'] - 1;
	$item['fieldmrkt_tab'] = ($item['fieldmrkt_tab'] > $max_tab) ? 0 : $item['fieldmrkt_tab'];
	$item['fieldmrkt_tabtitles'] = array();

	for ($i = 0; $i < $item['fieldmrkt_totaltabs']; $i++) {
		if (mb_strpos($item['fieldmrkt_tabs'][$i], '<br />') === 0) {
			$item['fieldmrkt_tabs'][$i] = mb_substr($item['fieldmrkt_tabs'][$i], 6);
		}

		$p1 = mb_strpos($item['fieldmrkt_tabs'][$i], '[title]');
		$p2 = mb_strpos($item['fieldmrkt_tabs'][$i], '[/title]');

		if ($p2 > $p1 && $p1 < 4) {
			$item['fieldmrkt_tabtitle'][$i] = mb_substr($item['fieldmrkt_tabs'][$i], $p1 + 7, ($p2 - $p1) - 7);
			if ($i == $item['fieldmrkt_tab']) {
				$item['fieldmrkt_tabs'][$i] = trim(str_replace('[title]'.$item['fieldmrkt_tabtitle'][$i].'[/title]', '', $item['fieldmrkt_tabs'][$i]));
			}
		} else {
			$item['fieldmrkt_tabtitle'][$i] = $i == 0 ? $item['fieldmrkt_title'] : Cot::$L['Market'] . ' ' . ($i + 1);
		}
		$tab_url = empty($al)
            ? cot_url('market', 'c='.$item['fieldmrkt_cat'].'&id='.$id.'&pg='.$i)
            : cot_url('market', 'c='.$item['fieldmrkt_cat'].'&al='.$al.'&pg='.$i);
		$item['fieldmrkt_tabtitles'][] .= cot_rc_link($tab_url, ($i+1).'. '.$item['fieldmrkt_tabtitle'][$i],
			array('class' => 'market_tabtitle'));
		$item['fieldmrkt_tabs'][$i] = str_replace('[newpage]', '', $item['fieldmrkt_tabs'][$i]);
		$item['fieldmrkt_tabs'][$i] = preg_replace('#^(<br />)+#', '', $item['fieldmrkt_tabs'][$i]);
		$item['fieldmrkt_tabs'][$i] = trim($item['fieldmrkt_tabs'][$i]);
	}

	$item['fieldmrkt_tabtitles'] = implode('<br />', $item['fieldmrkt_tabtitles']);
	$item['fieldmrkt_text'] = $item['fieldmrkt_tabs'][$item['fieldmrkt_tab']];

	// Temporarily disable easypagenav to allow 0-based numbers
	$tmp = Cot::$cfg['easypagenav'];
	Cot::$cfg['easypagenav'] = false;
	$pn = cot_pagenav('market', (empty($al) ? 'id='.$id : 'al='.$al), $item['fieldmrkt_tab'], $item['fieldmrkt_totaltabs'], 1, 'pg');
	$item['fieldmrkt_tabnav'] = $pn['main'];
	Cot::$cfg['easypagenav'] = $tmp;

	$t->assign([
		'MARKET_MULTI_TABNAV' => $item['fieldmrkt_tabnav'],
		'MARKET_MULTI_TABTITLES' => $item['fieldmrkt_tabtitles'],
		'MARKET_MULTI_CURTAB' => $item['fieldmrkt_tab'] + 1,
		'MARKET_MULTI_MAXTAB' => $item['fieldmrkt_totaltabs'],
		'MARKET_TEXT' => $item['fieldmrkt_text'],
	]);
	$t->parse('MAIN.MARKET_MULTI');
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('market.tags') as $pl) {
	include $pl;
}
/* ===== */
if (Cot::$usr['isadmin'] || Cot::$usr['id'] == $item['fieldmrkt_ownerid']) {
	$t->parse('MAIN.MARKET_ADMIN');
}


$t->parse('MAIN');
$moduleBody = $t->text('MAIN');

if ($itemStaticCacheEnabled) {
	Cot::$cache->static->write();
}
