<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */
//global $m, $cot_modules, $usr, $db_market, $db_users;

/**
 * Market manager & Queue of store items
 * filename market.admin.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

use cot\modules\market\inc\MarketDictionary;
use cot\modules\market\inc\MarketControlService;

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');
cot_block(Cot::$usr['isadmin']);

$t = new XTemplate(cot_tplfile('market.admin', 'module', true));

require_once cot_incfile('market', 'module');

$adminPath[] = [cot_url('admin', 'm=extensions'), Cot::$L['Extensions']];
$adminPath[] = [cot_url('admin', 'm=extensions&a=details&mod='.$m), $cot_modules[$m]['title']];
$adminPath[] = [cot_url('admin', 'm='.$m), Cot::$L['Administration']];
$adminHelp = Cot::$L['adm_lang_market_help_market'];
$adminTitle = Cot::$L['market_Market'];

$id = cot_import('id', 'G', 'INT');
$c  = cot_import('c',  'G', 'TXT'); // выбранная категория из URL
$sq = cot_import('sq', 'G', 'TXT'); // поисковый запрос
$sq = ($sq !== null) ? trim($sq) : '';

list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['market']['marketmaxlistsperpage']);

$sorttype = cot_import('sorttype', 'R', 'ALP');
$sorttype = empty($sorttype) ? 'id' : $sorttype;
if ($sorttype != 'id' && !Cot::$db->fieldExists(Cot::$db->market, "fieldmrkt_$sorttype")) {
	$sorttype = 'id';
}
$sqlsorttype = 'fieldmrkt_'.$sorttype;

$sort_type = cot_market_config_order(true);

$sortway = cot_import('sortway', 'R', 'ALP');
$sortway = empty($sortway) ? 'desc' : $sortway;
$sort_way = [
	'asc' => Cot::$L['Ascending'],
	'desc' => Cot::$L['Descending'],
];
$sqlsortway = $sortway;

$filter = cot_import('filter', 'R', 'ALP');
//$filter = empty($filter) ? 'valqueue' : $filter;
$filter = empty($filter) ? 'all' : $filter;
$filter_type = [
	'all' => Cot::$L['All'],
	'valqueue' => Cot::$L['adm_lang_market_valqueue'],
	'validated' => Cot::$L['adm_lang_market_validated'],
	'expired' => Cot::$L['adm_lang_market_expired'],
	'drafts' => Cot::$L['market_drafts'],
];

$urlParams = ['m' => 'market'];
if ($sorttype != 'id') {
    $urlParams['sorttype'] = $sorttype;
}
if ($sortway != 'desc') {
    $urlParams['sortway'] = $sortway;
}
if ($filter != 'valqueue') {
    $urlParams['filter'] = $filter;
}
if (!empty($sq)) {
    $urlParams['sq'] = $sq;
}
if (!empty($c)) {
    $urlParams['c'] = $c;
}

/**
 * Common UrlParams without pagination
 * @deprecated
 */
$common_params = http_build_query($urlParams, '', '&');

if ($pg > 1) {
    $urlParams['d'] = $durl;
}

// Блок формирования $sqlwhere
if ($filter == 'all') {
    $sqlwhere = "fieldmrkt_title IS NOT NULL AND fieldmrkt_title != ''";
} elseif ($filter == 'valqueue') {
    $sqlwhere = 'fieldmrkt_state = ' . MarketDictionary::STATE_PENDING . " AND fieldmrkt_title IS NOT NULL AND fieldmrkt_title != ''";
} elseif ($filter == 'validated') {
    $sqlwhere = 'fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED . " AND fieldmrkt_title IS NOT NULL AND fieldmrkt_title != ''";
} elseif ($filter == 'drafts') {
    $sqlwhere = 'fieldmrkt_state = ' . MarketDictionary::STATE_DRAFT . " AND fieldmrkt_title IS NOT NULL AND fieldmrkt_title != ''";
} elseif ($filter == 'expired') {
    $sqlwhere = 'fieldmrkt_expire > 0 AND fieldmrkt_expire < ' . (int) Cot::$sys['now'] . " AND fieldmrkt_title IS NOT NULL AND fieldmrkt_title != ''";
} else {
    $sqlwhere = "fieldmrkt_title IS NOT NULL AND fieldmrkt_title != ''";
}

$params = []; // параметры для SQL

// Поиск
if (!empty($sq)) {
    // Без алиаса "p." — чтобы COUNT(*) тоже работал
    $sqlwhere .= " AND (fieldmrkt_title LIKE :sq OR fieldmrkt_text LIKE :sq)";
    $params['sq'] = "%$sq%";
}

// Фильтр по категории (учитываем подкатегории)
$catsub = cot_structure_children('market', !empty($c) ? $c : '');
if (!empty($catsub)) {
    $sqlwhere .= " AND fieldmrkt_cat IN ('" . implode("','", $catsub) . "')";
}

// Загружаем структуру категорий перед циклом
cot::$structure['market'] = (!empty(cot::$structure['market']) && is_array(cot::$structure['market'])) ?
    cot::$structure['market'] : [];
$backUrl = cot_import('back', 'G', 'HTM');
$backUrl = !empty($backUrl) ? base64_decode($backUrl) : cot_url('admin', $urlParams, '', true);

/* === Hook  === */
foreach (cot_getextplugins('market.admin.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'validate') {
	cot_check_xg();

	/* === Hook  === */
	foreach (cot_getextplugins('market.admin.validate') as $pl) {
		include $pl;
	}
	/* ===== */

    $row = Cot::$db->query(
        'SELECT fieldmrkt_id, fieldmrkt_alias, fieldmrkt_cat, fieldmrkt_state FROM ' . Cot::$db->market
        . ' WHERE fieldmrkt_id = ?',
        $id
    )->fetch();
	if ($row) {
        if ($row['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED) {
            cot_message('#' . $id . ' - ' . Cot::$L['adm_lang_market_already_updated']);
            cot_redirect($backUrl);
        }

		$usr['isadmin_local'] = cot_auth('market', $row['fieldmrkt_cat'], 'A');
		cot_block($usr['isadmin_local']);
        $data = ['fieldmrkt_state' => MarketDictionary::STATE_PUBLISHED];

		$sql_market = Cot::$db->update(Cot::$db->market, $data, "fieldmrkt_id = $id");

		/* === Hook  === */
		foreach (cot_getextplugins('market.admin.validate.done') as $pl) {
			include $pl;
		}
		/* ===== */

		cot_log(
            Cot::$L['Market'] . ' #' . $id . ' - ' . Cot::$L['adm_lang_market_queue_validated'],
            'market',
            'validate',
            'done'
        );

		if (Cot::$cache) {
            Cot::$cache->db->remove('structure', 'system');
			if (Cot::$cfg['cache_market']) {
                Cot::$cache->static->clearByUri(cot_market_url($row));
                Cot::$cache->static->clearByUri(cot_url('market', ['c' => $row['fieldmrkt_cat']]));
			}
			if (Cot::$cfg['cache_index']) {
                Cot::$cache->static->clear('index');
			}
		}
		cot_message('#' . $id . ' - ' . Cot::$L['adm_lang_market_queue_validated']);

	} else {
        cot_error('#' . $id . ' - ' . Cot::$L['nf']);
	}

    cot_redirect($backUrl);
} elseif ($a == 'unvalidate') {
	cot_check_xg();

	/* === Hook  === */
	foreach (cot_getextplugins('market.admin.unvalidate') as $pl) {
		include $pl;
	}
	/* ===== */

    $row = Cot::$db->query(
        'SELECT fieldmrkt_id, fieldmrkt_alias, fieldmrkt_cat, fieldmrkt_state FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?',
        $id
    )->fetch();
    if ($row) {
        if ($row['fieldmrkt_state'] == MarketDictionary::STATE_PENDING) {
            cot_message('#' . $id . ' - ' . Cot::$L['adm_lang_market_already_updated']);
            cot_redirect($backUrl);
        }

		Cot::$usr['isadmin_local'] = cot_auth('market', $row['fieldmrkt_cat'], 'A');
		cot_block($usr['isadmin_local']);

		$sql_market = Cot::$db->update(
            Cot::$db->market,
            ['fieldmrkt_state' => MarketDictionary::STATE_PENDING],
            'fieldmrkt_id = ?',
            $id
        );

		cot_log(Cot::$L['Market'] . ' #' . $id . ' - ' . Cot::$L['adm_lang_market_queue_unvalidated'], 'market', 'unvalidated', 'done');

		if (Cot::$cache) {
            Cot::$cache->db->remove('structure', 'system');
			if (Cot::$cfg['cache_market']) {
                Cot::$cache->static->clearByUri(cot_market_url($row));
                Cot::$cache->static->clearByUri(cot_url('market', ['c' => $row['fieldmrkt_cat']]));
			}
			if (Cot::$cfg['cache_index']) {
                Cot::$cache->static->clear('index');
			}
		}

		cot_message('#' . $id . ' - ' . Cot::$L['adm_lang_market_queue_unvalidated']);

    } else {
        cot_error('#' . $id . ' - ' . Cot::$L['nf']);
	}

    cot_redirect($backUrl);
} elseif ($a == 'delete') {
	cot_check_xg();

	/* === Hook  === */
	foreach (cot_getextplugins('market.admin.delete') as $pl) {
		include $pl;
	}
	/* ===== */

    $resultOrMessage = MarketControlService::getInstance()->delete($id);
    if ($resultOrMessage !== false) {
        /* === Hook === */
		foreach (cot_getextplugins('market.admin.delete.done') as $pl) {
			include $pl;
		}
		/* ===== */

        cot_message('#' . $id . ' - ' . $resultOrMessage);
    } else {
        cot_error('#' . $id . ' - ' . Cot::$L['adm_lang_market_failed']);
    }

    cot_redirect(cot_url('admin', $urlParams, '', true));
} elseif ($a == 'update_checked') {
	$paction = cot_import('paction', 'P', 'TXT');
	$s = cot_import('s', 'P', 'ARR');

	if ($paction == 'validate' && is_array($s)) {
		cot_check_xp();

		$perelik = '';
		$notfoundet = '';
		foreach ($s as $i => $k) {
			if ($s[$i] == '1' || $s[$i] == 'on') {
				/* === Hook  === */
				foreach (cot_getextplugins('market.admin.checked_validate') as $pl) {
					include $pl;
				}
				/* ===== */

				$sql_market = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?', $i);
				if ($row = $sql_market->fetch()) {
					$id = $row['fieldmrkt_id'];
					$usr['isadmin_local'] = cot_auth('market', $row['fieldmrkt_cat'], 'A');
					cot_block($usr['isadmin_local']);

					$sql_market = Cot::$db->update(
                        Cot::$db->market,
                        ['fieldmrkt_state' => MarketDictionary::STATE_PUBLISHED],
                        'fieldmrkt_id= ?',
                        $id
                    );

					cot_log(
                        Cot::$L['Market'] . ' #' . $id . ' - ' . Cot::$L['adm_lang_market_queue_validated'],
                        'market',
                        'validate',
                        'done'
                    );

					if (Cot::$cache && Cot::$cfg['cache_market']) {
                        Cot::$cache->static->clearByUri(cot_market_url($row));
                        Cot::$cache->static->clearByUri(cot_url('market', ['c' => $row['fieldmrkt_cat']]));
					}

					$perelik .= '#' . $id . ', ';
				} else {
					$notfoundet .= '#' . $id . ' - ' . Cot::$L['Error'] . '<br  />';
				}
			}
		}

        if (Cot::$cache) {
            Cot::$cache->db->remove('structure', 'system');
            if (Cot::$cfg['cache_index']) {
                Cot::$cache->static->clear('index');
            }
        }

        if (!empty($notfoundet)) {
            cot_error($notfoundet);
        }

		if (!empty($perelik)) {
			cot_message($perelik . ' - ' . Cot::$L['adm_lang_market_queue_validated']);
		}

        cot_redirect(cot_url('admin', $urlParams, '', true));
	} elseif ($paction == 'delete' && is_array($s)) {
		cot_check_xp();

		$perelik = '';
		$notfoundet = '';
        $marketService = MarketControlService::getInstance();
		foreach ($s as $id => $k) {
			if ($s[$id] == '1' || $s[$id] == 'on') {

				/* === Hook  === */
				foreach (cot_getextplugins('market.admin.checked_delete') as $pl) {
					include $pl;
				}
				/* ===== */

                $resultOrMessage = $marketService->delete((int) $id);
                if ($resultOrMessage !== false) {
                    /* === Hook === */
                    foreach (cot_getextplugins('market.admin.delete.done') as $pl) {
                        include $pl;
                    }
                    /* ===== */
                    if ($perelik !== '') {
                        $perelik .= ', ';
                    }
                    $perelik .= '#' . $id;
                } else {
                    $notfoundet .= '#'. $id . ' - ' . Cot::$L['Error'] . '<br  />';
                }
			}
		}

        if (!empty($notfoundet)) {
            cot_error($notfoundet);
        }

        if (!empty($perelik)) {
            cot_message($perelik . ' - ' . Cot::$L['market_deleted']);
        }

        cot_redirect(cot_url('admin', $urlParams, '', true));
	}
}

// COUNT(*) — без алиаса, поэтому и в $sqlwhere не используем префикс p.
$totalitems = Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->market . ' WHERE ' . $sqlwhere,
    $params
)->fetchColumn();

$pagenav = cot_pagenav(
	'admin',
	$common_params,
	$d,
	$totalitems,
	Cot::$cfg['market']['marketmaxlistsperpage'],
	'd',
	'',
	Cot::$cfg['jquery'] && Cot::$cfg['turnajax']
);

$sql_market = Cot::$db->query(
    "SELECT p.*, u.user_name
	FROM $db_market as p
	LEFT JOIN $db_users AS u ON u.user_id = p.fieldmrkt_ownerid
	WHERE $sqlwhere
	ORDER BY $sqlsorttype $sqlsortway
	LIMIT $d, ".Cot::$cfg['market']['marketmaxlistsperpage'],
    $params
);

$ii = 0;
$sqllist_rowset = $sql_market->fetchAll(); // Сохраняем результат для подсчёта

// Сообщение о поиске — используем ОБЩЕЕ количество результатов
$searchMsg = '';
if ($sq !== '') {
    if ((int)$totalitems > 0) {
        $searchMsg = cot_declension($totalitems, ['позиция', 'позиции', 'позиций'])
            . ' найдено по запросу: <strong>' . htmlspecialchars($sq) . '</strong>';
    } else {
        $searchMsg = 'По запросу <strong>' . htmlspecialchars($sq) . '</strong> ничего не найдено';
    }
}

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('market.admin.loop');
/* ===== */

foreach ($sqllist_rowset as $row) {
    $sub_count = 0;
    if (isset(Cot::$structure['market'][$row["fieldmrkt_cat"]])) {
        $sql_market_subcount = Cot::$db->query("SELECT SUM(structure_count) FROM $db_structure WHERE structure_path LIKE '" . Cot::$db->prep(Cot::$structure['market'][$row["fieldmrkt_cat"]]['rpath']) . "%'");
        $sub_count = $sql_market_subcount->fetchColumn();
    }

    $categoryPath = isset(Cot::$structure['market'][$row['fieldmrkt_cat']]['path'])
        ? Cot::$structure['market'][$row['fieldmrkt_cat']]['path']
        : (isset(Cot::$structure['market'][$row['fieldmrkt_cat']]['code']) ? Cot::$structure['market'][$row['fieldmrkt_cat']]['code'] : $row['fieldmrkt_cat']);

    // НЕ затираем $urlParams для админки
    $itemUrlParams = ['c' => $categoryPath];
    if (!empty($row['fieldmrkt_alias'])) {
        $itemUrlParams['al'] = $row['fieldmrkt_alias'];
    } else {
        $itemUrlParams['id'] = $row['fieldmrkt_id'];
    }
    $row['item_pageurl'] = cot_url('market', $itemUrlParams);

    $t->assign(cot_generate_markettags($row, 'ADMIN_MARKET_', 200));
    $t->assign([
        'ADMIN_MARKET_ID_URL' => $row['item_pageurl'],
        'ADMIN_MARKET_OWNER' => cot_build_user($row['fieldmrkt_ownerid'], $row['user_name']),

        'ADMIN_MARKET_URL_FOR_VALIDATED' => cot_confirm_url(cot_url('admin', $common_params . '&a=validate&id=' . $row['fieldmrkt_id'] . '&d=' . $durl . '&' . cot_xg()), 'market', 'market_confirm_validate'),
        'ADMIN_MARKET_URL_FOR_UNVALIDATE' => cot_confirm_url(cot_url('admin', $common_params . '&a=unvalidate&id=' . $row['fieldmrkt_id'] . '&d=' . $durl . '&' . cot_xg()), 'market', 'market_confirm_unvalidate'),
        'ADMIN_MARKET_URL_FOR_DELETED' => cot_confirm_url(cot_url('admin', $common_params . '&a=delete&id=' . $row['fieldmrkt_id'] . '&d=' . $durl . '&' . cot_xg()), 'market', 'market_confirm_delete'),
        'ADMIN_MARKET_URL_FOR_EDIT' => cot_url('market', 'm=edit&id=' . $row['fieldmrkt_id']),
        'ADMIN_MARKET_ODDEVEN' => cot_build_oddeven($ii),
        'ADMIN_MARKET_CAT_COUNT' => $sub_count,
    ]);
    $t->assign(cot_generate_usertags($row['fieldmrkt_ownerid'], 'ADMIN_MARKET_OWNER_'), htmlspecialchars($row['user_name'] ?? ''));
    foreach ($extp as $pl) {
        include $pl;
    }
    $t->parse('MAIN.MARKET_ROW');
    $ii++;
}

$totaldbitems = Cot::$db->countRows($db_market);
$sql_market_queued = Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->market . ' WHERE fieldmrkt_state = ' . MarketDictionary::STATE_PENDING
);
$sys['marketqueued'] = $sql_market_queued->fetchColumn();

$t->assign([
	'ADMIN_MARKET_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=module&p=market'),
	'ADMIN_MARKET_URL_ADD' => cot_url('market', 'm=add'),
	'ADMIN_MARKET_URL_EXTRAFIELDS' => cot_url('admin', 'm=extrafields&n=' . $db_market),
	'ADMIN_MARKET_URL_STRUCTURE' => cot_url('admin', 'm=structure&n=market'),
	'ADMIN_MARKET_FORM_URL' => cot_url('admin', $common_params.'&a=update_checked&d=' . $durl),
	'ADMIN_MARKET_ORDER' => cot_selectbox($sorttype, 'sorttype', array_keys($sort_type), array_values($sort_type), false),
	'ADMIN_MARKET_WAY' => cot_selectbox($sortway, 'sortway', array_keys($sort_way), array_values($sort_way), false),
	'ADMIN_MARKET_FILTER' => cot_selectbox($filter, 'filter', array_keys($filter_type), array_values($filter_type), false),
	'ADMIN_MARKET_TOTALDBITEMS' => $totaldbitems,
    'ADMIN_MARKET_ON_PAGE' => $ii,
    'ADMIN_MARKET_SEARCH_ACTION_URL' => cot_url('admin', $urlParams, '', true), // URL формы поиска
    'ADMIN_MARKET_SEARCH_SQ' => cot_inputbox(
        'text',
        'sq',
        !empty($sq) ? htmlspecialchars($sq) : '',
        'class="schstring form-control" autofocus'
    ),
    // Выпадающий список категорий с поддержкой Select2
    "ADMIN_MARKET_SEARCH_CAT_SELECT2" => cot_market_selectcat_select2($c, 'c'),
	// Поле ввода поиска
    'ADMIN_MARKET_SEARCH_RESULT_MSG' => $searchMsg,
]);

$t->assign(cot_generatePaginationTags($pagenav));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('market.admin.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminMain = $t->text('MAIN');
