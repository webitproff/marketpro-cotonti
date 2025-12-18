<?php
/**
 * Market API
 * filename market.functions.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsService;
use cot\modules\market\inc\MarketDictionary;
use cot\plugins\comments\inc\CommentsService;

defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_langfile('market', ExtensionsDictionary::TYPE_MODULE);
require_once cot_incfile('market', ExtensionsDictionary::TYPE_MODULE, 'resources');
require_once cot_incfile('forms');
require_once cot_incfile('extrafields');


// Tables and extras
Cot::$db->registerTable('market');

cot_extrafields_register_table('market');

if (empty(Cot::$structure['market'])) {
    Cot::$structure['market'] = [];
}
/**
 * Renders structure dropdown forcot_market_selectbox_structure_select2 with Select[](https://select2.org/) support and indented subcategories
 *
 * @param string $extension Extension code
 * @param string $check Selected value
 * @param string $name Dropdown name
 * @param string $subcat Show only subcats of selected category
 * @param bool $hidePrivate Hide private categories
 * @param bool $isModule TRUE for modules, FALSE for plugins
 * @param bool $addEmpty Allow empty choice
 * @param mixed $attrs Additional attributes as an associative array or a string
 * @param string $customRC Custom resource string name
 * @return string
 */
function cot_market_selectbox_structure_select2(
    $extension,
    $check,
    $name,
    $subcat = '',
    $hidePrivate = true,
    $isModule = true,
    $addEmpty = false,
    $attrs = '',
    $customRC = ''
) {
    // Получаем черный список категорий из конфигурации
    $blacklist_cfg = Cot::$cfg['market']['marketblacktreecatspage'] ?? '';
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    $categories = is_array(Cot::$structure[$extension]) ? Cot::$structure[$extension] : [];

    $options = [];

    foreach ($categories as $code => $category) {
        // Пропускаем категории, которые находятся в черном списке
        if (in_array($code, $blacklist)) {
            continue;
        }

        $display = ($hidePrivate && $isModule) ? cot_auth($extension, $code, 'W') : true;

        if ($display && !empty($subcat) && isset(Cot::$structure[$extension][$subcat])) {
            $mtch = Cot::$structure[$extension][$subcat]['path'] . '.';
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($category['path'], 0, $mtchlen) == $mtch || $code === $subcat);
        }

        if ((!$isModule || cot_auth($extension, $code, 'R')) && $code !== 'all' && $display) {
            $depth = substr_count($category['path'], '.');
            $selected = ($code === $check) ? ' selected' : '';
            $attrs_str = is_array($attrs) ? cot_rc_attr_string($attrs) : $attrs;

            $options[] = '<option value="' . htmlspecialchars($code) . '" data-depth="' . $depth . '"' . $selected . ' ' . $attrs_str . '>' .
                         htmlspecialchars($category['title']) . '</option>';
        }
    }

    if ($addEmpty) {
        array_unshift($options, '<option value="">---</option>');
    }

    /* === Hook === */
    foreach (cot_getextplugins('selectBox.structure') as $pl) {
        include $pl;
    }
    /* ===== */

    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . implode("\n", $options) . '</select>';
}
/**
 * Select page cat for search form. Используется с Select2[](https://select2.org/)
 *
 * @global array $structure
 * @param string $check Selected category code
 * @param string $name Name of the select input
 * @param string $subcat Parent category code for filtering subcategories
 * @param bool $hideprivate Hide private categories
 * @return string
 */
function cot_market_selectcat_select2($check, $name, $subcat = '', $hideprivate = true)
{
    // Доступ к глобальной структуре категорий
    global $structure;

    // Получаем черный список категорий из конфигурации
    $blacklist_cfg = Cot::$cfg['market']['marketblacktreecatspage'] ?? '';
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    // Проверяем, что массив категорий существует, иначе инициализируем пустым
    $structure['market'] = is_array($structure['market']) ? $structure['market'] : [];

    // Переменная для накопления всех option'ов
    $options = '';

    // Добавляем пустой вариант для "Все категории"
    $options .= '<option value=""' . (empty($check) ? ' selected' : '') . '>Все категории</option>';

    // Перебираем все категории в разделе 'market'
    foreach ($structure['market'] as $i => $x) {
        // Пропускаем категории, которые находятся в черном списке
        if (in_array($i, $blacklist)) {
            continue;
        }

        // Проверяем, разрешён ли просмотр категории (если нужно скрывать приватные)
        $display = $hideprivate ? cot_auth('market', $i, 'R') : true;

        // Если нужно фильтровать подкатегории, проверяем, входит ли текущая категория в фильтр
        if ($display && !empty($subcat) && isset($structure['market'][$subcat])) {
            // Формируем строку пути родительской категории с точкой на конце
            $mtch = $structure['market'][$subcat]['path'] . ".";
            // Длина этого пути
            $mtchlen = mb_strlen($mtch);
            // Проверяем, что путь текущей категории начинается с пути родителя или совпадает с ним
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        // Если есть права на чтение категории, она не "all" и подходит по фильтру
        if ($display && $i !== 'all') {
            // Считаем глубину категории — количество точек в пути
            $depth = substr_count($x['path'], '.');

            // Определяем, выбрана ли эта категория в данный момент
            $selected = ($i == $check) ? ' selected' : '';

            // Формируем тег option с value, data-depth и текстом
            $options .= '<option value="' . htmlspecialchars($i) . '" data-depth="' . $depth . '"' . $selected . '>' .
                        htmlspecialchars($x['title']) . '</option>';
        }
    }

    // Возвращаем полный select с классом Bootstrap
    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . $options . '</select>';
}

/**
 * Select page cat for search form
 * 
 * @global array $structure
 * @param type $check
 * @param type $name
 * @param type $subcat
 * @param type $hideprivate
 * @return string
 */
function cot_market_selectcat($check, $name, $subcat = '', $hideprivate = true)
{
    global $structure;

    // Load blacklist from configuration
    $blacklist_cfg = $cfg['market']['marketblacktreecatspage'] ?? '';
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    $structure['market'] = (is_array($structure['market'])) ? $structure['market'] : array();

    $result_array = array();
    foreach ($structure['market'] as $i => $x)
    {
        // Skip categories in blacklist
        if (in_array($i, $blacklist)) {
            continue;
        }

        $display = ($hideprivate) ? cot_auth('market', $i, 'R') : true;
        if ($display && !empty($subcat) && isset($structure['market'][$subcat]))
        {
            $mtch = $structure['market'][$subcat]['path'].".";
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        if (cot_auth('market', $i, 'R') && $i != 'all' && $display) {
            $result_array[$i] = $x['tpath'];
        }
    }

    return cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), true);
}

/**
 * Считает количество товаров в категории и во всех её потомках
 *
 * @param string $cat Код категории
 * @return int
 */
function cot_market_count_with_children($cat)
{
    global $structure, $db, $db_market;

    if (empty($cat) || !isset($structure['market'][$cat])) {
        return 0;
    }

    // собираем все дочерние категории всех уровней
    $cats = [$cat];
    foreach ($structure['market'] as $code => $data) {
        if (strpos($data['path'], $structure['market'][$cat]['path'] . '.') === 0) {
            $cats[] = $code;
        }
    }

    // безопасные плейсхолдеры
    $placeholders = implode(',', array_fill(0, count($cats), '?'));

    $sql = "
        SELECT COUNT(*) 
        FROM $db_market
        WHERE fieldmrkt_state = 0
          AND fieldmrkt_cat IN ($placeholders)
    ";

    return (int)$db->query($sql, $cats)->fetchColumn();
}


/**
 * Формирует иерархическую структуру дерева категорий для модуля market
 *
 * @param string $parent Код родительской категории, пустой для корневого уровня
 * @param string|array $selected Код(ы) выбранной категории для подсветки (строка или массив)
 * @param int $level Текущий уровень в иерархии категорий
 * @param string $template Файл шаблона для использования (зарезервировано)
 * @return string|bool Отрендеренный HTML для дерева категорий или false, если нет дочерних элементов
 */
function cot_build_structure_market_tree($parent = '', $selected = '', $level = 0, $template = '')
{
    global $structure, $cfg, $db, $sys, $cot_extrafields, $db_structure, $db_market;
    global $i18n_notmain, $i18n_locale, $i18n_write, $i18n_admin, $i18n_read, $db_i18n_pages;

    $blacklist_cfg = $cfg['market']['marketblacktreecatspage'] ?? '';
	
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    $urlparams = [];

    /* === Hook === */
    foreach (cot_getextplugins('market.tree.first') as $pl)
    {
        include $pl;
    }
    /* ===== */

    if (empty($parent))
    {
        $i18n_enabled = $i18n_read;
        $children = [];
        $allcat = cot_structure_children('market', '');
        foreach ($allcat as $x)
        {
            if (
                mb_substr_count($structure['market'][$x]['path'], ".") == 0 &&
                !in_array($x, $blacklist)
            ) {
                $children[] = $x;
            }
        }
    }
    else
    {
        $i18n_enabled = $i18n_read && cot_i18n_enabled($parent);
        $children = array_filter($structure['market'][$parent]['subcats'] ?? [], function($cat) use ($blacklist) {
            return !in_array($cat, $blacklist);
        });
    }

    $mskin = cot_tplfile(['market', 'tree', $template], 'module');
    $t1 = new XTemplate($mskin);

    /* === Hook === */
    foreach (cot_getextplugins('market.tree.main') as $pl)
    {
        include $pl;
    }
    /* ===== */

    if (count($children) == 0)
    {
        return false;
    }

    $total_count = 0;
    if ($db->tableExists($db_market)) {
        $result = $db->query("SELECT COUNT(*) AS total FROM $db_market WHERE fieldmrkt_state = 0")->fetch();
        $total_count = $result['total'] ?? 0;
    }

    $title = '';
    $desc = '';
    $count = 0;
    $icon = '';
    if (!empty($parent) && isset($structure['market'][$parent])) {
        $title = $structure['market'][$parent]['title'];
        $desc  = $structure['market'][$parent]['desc'];
        $count = $structure['market'][$parent]['count'];
        $icon  = $structure['market'][$parent]['icon'];
    }

    $t1->assign([
        "TITLE" => htmlspecialchars($title),
        "DESC" => $desc,
        "COUNT" => $count,
        "ICON" => $icon,
        "HREF" => cot_url("market", $urlparams + ['c' => $parent]),
        "LEVEL" => $level,
        "TOTAL_COUNT" => $total_count,
    ]);

    $jj = 0;

    /* === Hook - Part1 : Set === */
    $extp = cot_getextplugins('market.tree.loop');
    /* ===== */

    foreach ($children as $row)
    {
        if (in_array($row, $blacklist)) {
            continue;
        }

        $jj++;
        $urlparams['c'] = $row;
        $subcats = !empty($structure['market'][$row]['subcats']) ? array_filter($structure['market'][$row]['subcats'], function($cat) use ($blacklist) {
            return !in_array($cat, $blacklist);
        }) : [];
		$parent_count = cot_market_count_with_children($row);

        $t1->assign([
            "ROW_ID" => $row,
            "ROW_TITLE" => htmlspecialchars($structure['market'][$row]['title']),
            "ROW_DESC" => $structure['market'][$row]['desc'],
            "ROW_COUNT" => $structure['market'][$row]['count'],
			 "ROW_PARENT_COUNT" => $parent_count ? ' <span class="badge bg-info ms-1 small">'.$parent_count.'</span>' : '',
            "ROW_ICON" => $structure['market'][$row]['icon'],
            "ROW_HREF" => cot_url("market", $urlparams),
            "ROW_SELECTED" => (!empty($selected) && (strpos($selected, $row) === 0 || $selected === $row)) ? 1 : 0,
			"ROW_SUBCAT" => !empty($subcats) ? cot_build_structure_market_tree($row, $selected, $level + 1, $template) : '',
           // "ROW_SELECTED" => ((is_array($selected) && in_array($row, $selected)) || (!is_array($selected) && $row == $selected)) ? 1 : 0,
           // "ROW_SUBCAT" => !empty($subcats) ? cot_build_structure_market_tree($row, $selected, $level + 1) : '',
            "ROW_LEVEL" => $level,
            "ROW_ODDEVEN" => cot_build_oddeven($jj),
            "ROW_JJ" => $jj
        ]);

        foreach ($cot_extrafields[$db_structure] as $exfld)
        {
            $uname = strtoupper($exfld['field_name']);
            $t1->assign([
                'ROW_'.$uname.'_TITLE' => isset($L['structure_'.$exfld['field_name'].'_title']) ? $L['structure_'.$exfld['field_name'].'_title'] : $exfld['field_description'],
                'ROW_'.$uname => cot_build_extrafields_data('structure', $exfld, $structure['market'][$row][$exfld['field_name']]),
                'ROW_'.$uname.'_VALUE' => $structure['market'][$row][$exfld['field_name']],
            ]);
        }

        if ($i18n_enabled && $i18n_notmain){
            $x_i18n = cot_i18n_get_cat($row, $i18n_locale);
            if ($x_i18n){
                if(!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang']){
                    $urlparams['l'] = $i18n_locale;
                }
                $t1->assign([
                    'ROW_URL' => cot_url('market', $urlparams),
                    'ROW_TITLE' => $x_i18n['title'],
                    'ROW_DESC' => $x_i18n['desc'],
                ]);
            }
        }

        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl)
        {
            include $pl;
        }
        /* ===== */

        $t1->parse("MAIN.CATS");
    }

    if ($jj == 0)
    {
        return false;
    }

    $t1->parse("MAIN");
    return $t1->text("MAIN");
}

/**
 * Cuts the store item after 'more' tag or after the first page (if multipage)
 *
 * @param string $html Store item body
 * @return string
 */
function cot_cut_more_market($html)
{
	$mpos = mb_strpos($html, '<!--more-->');
	if ($mpos === false) {
		$mpos = mb_strpos($html, '[more]');
	}
	if ($mpos === false) {
        if (preg_match('#<hr *class="more" */?>#', $html, $matches, PREG_OFFSET_CAPTURE)) {
            $mpos = $matches[0][1];
        }
	}
	if ($mpos !== false) {
		$html = mb_substr($html, 0, $mpos);
	}
	$mpos = mb_strpos($html, '[newpage]');
	if ($mpos !== false) {
		$html = mb_substr($html, 0, $mpos);
	}
	if (mb_strpos($html, '[title]')) {
		$html = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $html);
	}
	return $html;
}

/**
 * Reads raw data from file
 *
 * @param string $file File path
 * @return string
 */
function cot_readraw_market($file)
{
	return (mb_strpos($file, '..') === false && file_exists($file)) ? file_get_contents($file) : 'File not found : '.$file; // TODO need translate
}

/**
 * Returns all store item tags for coTemplate
 *
 * @param int|array $item_data Store item Info Array or ID
 * @param string $tag_prefix Prefix for tags
 * @param int $textLength Text truncate
 * @param bool $admin_rights Store item Admin Rights
 * @param bool $pagepath_home Add home link for store item path
 * @param string $emptytitle Store item title text if item does not exist
 * @param string $backUrl BackUrl for store item validate actions
 *
 * @return array|null
 * @global CotDB $db
 */
function cot_generate_markettags(
    $item_data,
    $tag_prefix = '',
    $textLength = 0,
    $admin_rights = null,
    $pagepath_home = false,
    $emptytitle = '',
    $backUrl = null
) {
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R, $cfg;

	global $db, $cot_extrafields, $db_market, $usr, $sys, $cot_yesno, $structure, $db_structure;

	static $extp_first = null, $extp_main = null;
	static $market_auth = [];

	if (is_null($extp_first)) {
		$extp_first = cot_getextplugins('markettags.first');
		$extp_main = cot_getextplugins('markettags.main');
	}

	/* === Hook === */
	foreach ($extp_first as $pl) {
		include $pl;
	}
	/* ===== */

	if (!empty($item_data) && !is_array($item_data)) {
        $itemID = (int) $item_data;
        $item_data = null;
        if ($itemID > 0) {
            $sql = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ? LIMIT 1', $itemID);
            $item_data = $sql->fetch();
        }
	}

    if (empty($item_data)) {
        return null;
    }

	if ($item_data['fieldmrkt_id'] > 0 && !empty($item_data['fieldmrkt_title'])) {
		if (is_null($admin_rights)) {
			if (!isset($market_auth[$item_data['fieldmrkt_cat']])) {
				$market_auth[$item_data['fieldmrkt_cat']] = cot_auth('market', $item_data['fieldmrkt_cat'], 'RWA1');
			}
			$admin_rights = (bool) $market_auth[$item_data['fieldmrkt_cat']][2];
		}
		$pagepath = cot_structure_buildpath('market', $item_data['fieldmrkt_cat']);
		$catpath = cot_breadcrumbs($pagepath, $pagepath_home, false);
        $item_data['fieldmrkt_pageurl'] = cot_market_url($item_data);
		$pageLink = [[$item_data['fieldmrkt_pageurl'], $item_data['fieldmrkt_title']]];
		$breadcrumbs = cot_breadcrumbs(array_merge($pagepath, $pageLink), $pagepath_home);


		$date_format = 'datetime_medium';

		$text = cot_parse($item_data['fieldmrkt_text'], $cfg['market']['marketmarkup'], $item_data['fieldmrkt_parser']);
		$text_cut = cot_cut_more_market($text);
		if ($textLength > 0 && mb_strlen($text_cut) > $textLength) {
			$text_cut = cot_string_truncate($text_cut, $textLength);
		}
		$cutted = mb_strlen($text) > mb_strlen($text_cut);

		$cat_url = cot_url('market', ['c' => $item_data['fieldmrkt_cat']]);

        $urlParams = [
            'm' => 'market',
            'a' => 'validate',
            'id' => $item_data['fieldmrkt_id'],
            'x' => Cot::$sys['xk'],
        ];
        if (!empty($backUrl)) {
            $urlParams['back'] = base64_encode($backUrl);
        }
		$validate_url = cot_url('admin', $urlParams);

        $urlParams['a'] = 'unvalidate';
		$unvalidate_url = cot_url('admin', $urlParams);

		$edit_url = cot_url('market', "m=edit&id={$item_data['fieldmrkt_id']}");
		$delete_url = cot_url('market', "m=edit&a=update&delete=1&id={$item_data['fieldmrkt_id']}&x={$sys['xk']}");

		$item_data['fieldmrkt_status'] = cot_market_status(
			$item_data['fieldmrkt_state'],
		);

        $catTitle = isset($structure['market'][$item_data['fieldmrkt_cat']]['title'])
            ? htmlspecialchars($structure['market'][$item_data['fieldmrkt_cat']]['title'])
            : '';
        $catDescription = isset($structure['market'][$item_data['fieldmrkt_cat']]['desc'])
            ? $structure['market'][$item_data['fieldmrkt_cat']]['desc']
            : '';
        $itemDescription = (isset($item_data['fieldmrkt_desc']) && $item_data['fieldmrkt_desc'] !== '')
            ? htmlspecialchars($item_data['fieldmrkt_desc'])
            : '';
        $temp_array = [
			'URL' => $item_data['fieldmrkt_pageurl'],
			'ID' => $item_data['fieldmrkt_id'],
			'TITLE' => htmlspecialchars($item_data['fieldmrkt_title'], ENT_COMPAT, 'UTF-8', false),
            'BREADCRUMBS' => $breadcrumbs,
			'BREADCRUMBS_ITEM' => cot_breadcrumbs(
				array_merge(
					[[cot_url('index'), Cot::$L['Main']]],
					[[cot_url('market'), Cot::$L['market_Market']]],
					cot_structure_buildpath('market', $item_data['fieldmrkt_cat']),
					[htmlspecialchars($item_data['fieldmrkt_title'], ENT_QUOTES, 'UTF-8')]
				),
				$pagepath_home,
				false
			),
			'ALIAS' => $item_data['fieldmrkt_alias'],
			'STATE' => $item_data['fieldmrkt_state'],
			'STATUS' => $item_data['fieldmrkt_status'],
			'LOCAL_STATUS' => $L['market_status_' . $item_data['fieldmrkt_status']],
			'CAT' => $item_data['fieldmrkt_cat'],
			'CAT_URL' => $cat_url,
			'CAT_TITLE' => $catTitle,
			'CAT_PATH' => $catpath,
			'CAT_PATH_SHORT' => cot_rc_link($cat_url, $catTitle),
			'CAT_DESCRIPTION' => $catDescription,
			'CAT_ICON' => !empty($structure['market'][$item_data['fieldmrkt_cat']]['icon'])
                ? cot_rc(
                    'img_structure_cat',
                    [
                        'icon' => $structure['market'][$item_data['fieldmrkt_cat']]['icon'],
                        'title' => $catTitle,
                        'desc' => htmlspecialchars($catDescription),
                    ]
                )
                : '',
            'CAT_ICON_SRC' => isset($structure['market'][$item_data['fieldmrkt_cat']]['icon'])
                ? $structure['market'][$item_data['fieldmrkt_cat']]['icon']
                : '',

			'DESCRIPTION' => $itemDescription,
			'TEXT' => $text,
			'TEXT_SHORT' => cot_cutstring(strip_tags($item_data['fieldmrkt_text']), 250),
			'TEXT_CUT' => $text_cut,
			'TEXT_IS_CUT' => $cutted,
			'DESCRIPTION_OR_TEXT' => $itemDescription !== '' ? $itemDescription : $text,
			'DESCRIPTION_OR_TEXT_CUT' => $itemDescription !== '' ? $itemDescription : $text_cut,
			'MORE' => ($cutted) ? cot_rc('list_more', ['page_url' => $item_data['fieldmrkt_pageurl']]) : '',
			'AUTHOR' => (isset($item_data['fieldmrkt_author']) && $item_data['fieldmrkt_author'] != '')
                ? htmlspecialchars($item_data['fieldmrkt_author'])
                : '',
			'OWNER_ID' => $item_data['fieldmrkt_ownerid'],
			'OWNER_NAME' => (isset($item_data['user_name']) && $item_data['user_name'] != '')
                ? htmlspecialchars($item_data['user_name'])
                : '',
			'COSTDFLT' => (floor($item_data['fieldmrkt_costdflt']) != $item_data['fieldmrkt_costdflt']) ? number_format($item_data['fieldmrkt_costdflt'], '2', '.', ' ') : number_format($item_data['fieldmrkt_costdflt'], '0', '.', ' '),
			
            'CREATED' => cot_date($date_format, $item_data['fieldmrkt_date']),
			'UPDATED' => cot_date($date_format, $item_data['fieldmrkt_updated']),
            'CREATED_STAMP' => $item_data['fieldmrkt_date'],
			'UPDATED_STAMP' => $item_data['fieldmrkt_updated'],
			'HITS' => $item_data['fieldmrkt_count'],
            'ADMIN' => $admin_rights
                ? cot_rc('list_row_admin', ['unvalidate_url' => $unvalidate_url, 'edit_url' => $edit_url])
                : '',
		];

		// Admin tags
		if ($admin_rights) {
			$validate_confirm_url = cot_confirm_url($validate_url, 'market', 'market_confirm_validate');
			$unvalidate_confirm_url = cot_confirm_url($unvalidate_url, 'market', 'market_confirm_unvalidate');
			$delete_confirm_url = cot_confirm_url($delete_url, 'market', 'market_confirm_delete');
			$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, Cot::$L['Edit']);
			$temp_array['ADMIN_EDIT_URL'] = $edit_url;
			$temp_array['ADMIN_UNVALIDATE'] = $item_data['fieldmrkt_state'] == MarketDictionary::STATE_PENDING
                ? cot_rc_link($validate_confirm_url, Cot::$L['Validate'], 'class="confirmLink"')
                : cot_rc_link($unvalidate_confirm_url, Cot::$L['Putinvalidationqueue'], 'class="confirmLink"');
			$temp_array['ADMIN_UNVALIDATE_URL'] = $item_data['fieldmrkt_state'] == 1 ?
				$validate_confirm_url : $unvalidate_confirm_url;
			$temp_array['ADMIN_DELETE'] = cot_rc_link($delete_confirm_url, $L['Delete'], 'class="confirmLink"');
			$temp_array['ADMIN_DELETE_URL'] = $delete_confirm_url;
		} elseif ($usr['id'] == $item_data['fieldmrkt_ownerid']) {
			$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
			$temp_array['ADMIN_EDIT_URL'] = $edit_url;
		}

		if (cot_auth('market', 'any', 'W')) {
			$clone_url = cot_url('market', "m=add&c={$item_data['fieldmrkt_cat']}&clone={$item_data['fieldmrkt_id']}");
			$temp_array['ADMIN_CLONE'] = cot_rc_link($clone_url, $L['market_clone']);
			$temp_array['ADMIN_CLONE_URL'] = $clone_url;
		}

		// Extrafields
        if (!empty(Cot::$extrafields[Cot::$db->market])) {
            foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
				$tag = mb_strtoupper($exfld['field_name']);
                $exfld_title = cot_extrafield_title($exfld, 'market_');

				$temp_array[$tag . '_TITLE'] = $exfld_title;
                $temp_value = null;
                if (isset($item_data['fieldmrkt_'.$exfld['field_name']])) {
                    $temp_value = $item_data['fieldmrkt_'.$exfld['field_name']];
                }
				$temp_array[$tag] = cot_build_extrafields_data('market', $exfld, $temp_value, $item_data['fieldmrkt_parser']);
				$temp_array[$tag . '_VALUE'] = $temp_value;
			}
		}

		// Extra fields for structure
		if (isset(Cot::$extrafields[Cot::$db->structure])) {
			foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
				$tag = mb_strtoupper($exfld['field_name']);
                $exfld_title = cot_extrafield_title($exfld, 'structure_');

				$temp_array['CAT_' . $tag . '_TITLE'] = $exfld_title;
                $temp_value = null;
                if (isset(Cot::$structure['market'][$item_data['fieldmrkt_cat']][$exfld['field_name']])) {
                    $temp_value = Cot::$structure['market'][$item_data['fieldmrkt_cat']][$exfld['field_name']];
                }
				$temp_array['CAT_' . $tag] = cot_build_extrafields_data('structure', $exfld, $temp_value);
				$temp_array['CAT_' . $tag.'_VALUE'] = $temp_value;
			}
		}

		/* === Hook === */
		foreach ($extp_main as $pl) {
			include $pl;
		}
		/* ===== */

	} else {
		$temp_array = [
			'TITLE' => (!empty($emptytitle)) ? $emptytitle : Cot::$L['Deleted'],
		];
	}

	$return_array = [];
	foreach ($temp_array as $key => $val) {
		$return_array[$tag_prefix . $key] = $val;
	}

	return $return_array;
}

/**
 * Possible values for category sorting order
 * @param bool $adminpart Call from admin part
 * @return array
 */
function cot_market_config_order($adminpart = false)
{
	global $cot_extrafields, $L, $db_market;

	$options_sort = [
		'id' => $L['Id'],
		'title' => $L['Title'],
		'desc' => $L['Description'],
		'text' => $L['Body'],
		'ownerid' => $L['Owner'],
		'date' => $L['Date'],
		'count' => $L['Count'],
		'updated' => $L['Updated'],
		'cat' => $L['Category']
	];

	foreach($cot_extrafields[$db_market] as $exfld) {
		$options_sort[$exfld['field_name']] = isset($L['market_'.$exfld['field_name'].'_title']) ? $L['market_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	}

	if ($adminpart || version_compare('0.9.19', Cot::$cfg['version']) < 1) {
		return $options_sort;
	} else {
		// old style trick, will be removed in next versions
		$L['cfg_order_params'] = array_values($options_sort);
		return array_keys($options_sort);
	}
}
/**
 * Determines store item status
 *
 * @param int $fieldmrkt_state
 * @return string 'draft', 'pending' or 'published'
 */
function cot_market_status($fieldmrkt_state)
{
	if ($fieldmrkt_state == 0) {
		return 'published';
	} elseif ($fieldmrkt_state == 2) {
		return 'draft';
	}
	return 'pending';
}

/**
 * Returns store item category counter
 * Used in Admin/Structure/Resync All
 *
 * @param string $category Category code
 * @return int
 */
function cot_market_sync($category)
{
    if (empty($category)) {
        return 0;
    }

    return (int) Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->market) .
        ' WHERE fieldmrkt_cat=?',
        $category
    )->fetchColumn();
}

/**
 * Recalculate and update structure counters
 * @param string $category Category code
 * @return void
 */
function cot_market_updateStructureCounters($category)
{
    if (empty($category) || empty(Cot::$structure['market'][$category])) {
        return;
    }

    $count = cot_market_sync($category);

    Cot::$db->query('UPDATE ' . Cot::$db->quoteTableName(Cot::$db->structure) .
        ' SET structure_count = ' . $count .
        " WHERE structure_area='market' AND structure_code = :category", ['category' => $category]);

    if (Cot::$cache) {
        Cot::$cache->db->remove('structure', 'system');
    }
}

/**
 * Update store item category code
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 * @global CotDB $db
 */
function cot_market_updatecat($oldcat, $newcat)
{
	global $db, $db_structure, $db_market;
	return (bool) $db->update($db_market, ["fieldmrkt_cat" => $newcat], "fieldmrkt_cat='".$db->prep($oldcat)."'");
}


/**
 * Url address of the store item
 *
 * @param array $data Store item data as array
 * @param array $params Additional URL Parameters
 * @param string $tail URL postfix, e.g. anchor
 * @param bool $htmlspecialcharsBypass If TRUE, will not convert & to & and so on.
 * @param bool $ignoreAppendix If TRUE, $cot_url_appendix will be ignored for this URL
 * @return string Valid HTTP URL
 */
function cot_market_url($data, $params = [], $tail = '', $htmlspecialcharsBypass = false, $ignoreAppendix = false)
{
    $urlParams = ['c' => $data['fieldmrkt_cat']];
    if (!empty($data['fieldmrkt_alias'])) {
        $urlParams['al'] = $data['fieldmrkt_alias'];
    } elseif (!empty($data['fieldmrkt_id'])) {
        $id = (int) $data['fieldmrkt_id'];
        if ($id <= 0) {
            return '';
        }
        $urlParams['id'] = $id;
    } else {
        return '';
    }

    if (!empty($params)) {
        $urlParams = array_merge($urlParams, $params);
    }

    return cot_url('market', $urlParams, $tail, $htmlspecialcharsBypass, $ignoreAppendix);
}

/**
 * Returns permissions for a store item category.
 * @param  string $cat Category code
 * @return array       Permissions array with keys: 'auth_read', 'auth_write', 'isadmin', 'auth_download'
 */
function cot_market_auth($cat = null)
{
	if (empty($cat)) {
		$cat = 'any';
	}
	$auth = [];
	[$auth['auth_read'], $auth['auth_write'], $auth['isadmin'], $auth['auth_download']] = cot_auth('market', $cat, 'RWA1');
	return $auth;
}
/**
 * Импортирует данные элемента хранилища из параметров запроса.
 * @param строка $source - метод запроса источника для параметров
 * @param массив $ritem - данные существующего элемента хранилища из базы данных
 * @param массив $массив разрешений auth
 * @return массив - данные элемента хранилища
 */
/**
 * Imports store item data from request parameters.
 * @param string $source Source request method for parameters
 * @param array $ritem  Existing store item data from database
 * @param array $auth   Permissions array
 * @return array Store item data
 */
function cot_market_import($source = 'POST', $ritem = [], $auth = [])
{
	global $cfg, $db_market, $cot_extrafields, $usr, $sys;

	if (count($auth) == 0) {
		$auth = cot_market_auth($ritem['fieldmrkt_cat']);
	}

	if ($source == 'D' || $source == 'DIRECT') {
		// A trick so we don't have to affect every line below
		global $_PATCH;
		$_PATCH = $ritem;
		$source = 'PATCH';
	}

	$ritem['fieldmrkt_cat']      = cot_import('ritemmarketcat', $source, 'TXT', 255);
	$ritem['fieldmrkt_alias']    = cot_import('ritemmarketalias', $source, 'TXT', 255);
	$ritem['fieldmrkt_title']    = cot_import('ritemmarkettitle', $source, 'TXT', 255);
	$ritem['fieldmrkt_desc']     = cot_import('ritemmarketdesc', $source, 'TXT', 255);
	$ritem['fieldmrkt_text']     = cot_import('ritemmarkettext', $source, 'HTM');
	$ritem['fieldmrkt_parser']   = cot_import('ritemmarketparser', $source, 'ALP', 64);
	$ritem['fieldmrkt_author']   = cot_import('ritemmarketauthor', $source, 'TXT', 100);

	$ritem['fieldmrkt_costdflt'] = cot_import('ritemmarketcostdflt', $source, 'NUM');

	$ritemmarketdatenow           = cot_import('ritemmarketdatenow', $source, 'BOL');
	$ritem['fieldmrkt_date']     = cot_import_date('ritemmarketdate', true, false, $source);
	$ritem['fieldmrkt_date']     = ($ritemmarketdatenow || is_null($ritem['fieldmrkt_date'])) ? $sys['now'] : (int) $ritem['fieldmrkt_date'];
	
	$ritem['fieldmrkt_updated']  = $sys['now'];

	$ritem['fieldmrkt_metatitle'] = cot_import('ritemmarketmetatitle', $source, 'TXT', 255);
	$ritem['fieldmrkt_metadesc'] = cot_import('ritemmarketmetadesc', $source, 'TXT', 255);

	$rmspublish               = cot_import('rmspublish', $source, 'ALP'); // For backwards compatibility
	$ritem['fieldmrkt_state']    = ($rmspublish == 'OK') ? 0 : cot_import('ritemmarketstate', $source, 'INT');

	if ($auth['isadmin'] && isset($ritem['fieldmrkt_ownerid'])) {
		$ritem['fieldmrkt_count']     = cot_import('ritemmarketcount', $source, 'INT');
		$ritem['fieldmrkt_ownerid']   = cot_import('ritemmarketownerid', $source, 'INT');
	} else {
		$ritem['fieldmrkt_ownerid'] = Cot::$usr['id'];
	}

	$parser_list = cot_get_parsers();

	if (
        empty($ritem['fieldmrkt_parser'])
        || !in_array($ritem['fieldmrkt_parser'], $parser_list)
        || $ritem['fieldmrkt_parser'] != 'none'
        && !cot_auth('plug', $ritem['fieldmrkt_parser'], 'W')
    ) {
		$ritem['fieldmrkt_parser'] = isset(Cot::$sys['marketparser']) ? Cot::$sys['marketparser'] : Cot::$cfg['market']['marketparser'];
	}

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->market])) {
        foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
            $value = isset($ritem['fieldmrkt_' . $exfld['field_name']]) ? $ritem['fieldmrkt_' . $exfld['field_name']] : null ;
            $ritem['fieldmrkt_' . $exfld['field_name']] = cot_import_extrafields('ritemmarket' . $exfld['field_name'], $exfld,
                $source, $value, 'market_');
        }
    }

	return $ritem;
}
/**
 * Validates store item data.
 * @param  array   $ritem Imported store item data
 * @return boolean        TRUE if validation is passed or FALSE if errors were found
 */
function cot_market_validate($ritem)
{
	global $structure;

	cot_check(empty($ritem['fieldmrkt_cat']), 'market_catmissing', 'ritemmarketcat');
	if ($structure['market'][$ritem['fieldmrkt_cat']]['locked']) {
		global $L;
		require_once cot_langfile('message', 'core');
		cot_error('msg602_body', 'ritemmarketcat');
	}
	cot_check(mb_strlen($ritem['fieldmrkt_title']) < 2, 'market_titletooshort', 'ritemmarkettitle');

	cot_check(!empty($ritem['fieldmrkt_alias']) && preg_match('`[+/?%#&]`', $ritem['fieldmrkt_alias']), 'market_aliascharacters', 'ritemmarketalias');

	$allowemptytext = Cot::$cfg['market']['cat_' . $ritem['fieldmrkt_cat']]['marketallowemptytext']
        ?? Cot::$cfg['market']['cat___default']['marketallowemptytext'];

	cot_check(!$allowemptytext && empty($ritem['fieldmrkt_text']), 'market_textmissing', 'ritemmarkettext');

	return !cot_error_found();
}

/**
 * Adds a new store item to the CMS.
 * @param array $ritem Store item data
 * @param array $auth Permissions array
 * @return ?int New store item ID or NULL on error
 */
function cot_market_add(&$ritem, $auth = [])
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	if (cot_error_found()) {
		return false;
	}

	if (count($auth) == 0) {
		$auth = cot_market_auth($ritem['fieldmrkt_cat']);
	}

	if (!empty($ritem['fieldmrkt_alias'])) {
		$item_count = Cot::$db->query(
            'SELECT COUNT(*) FROM ' . Cot::$db->market . ' WHERE fieldmrkt_alias = ?',
            $ritem['fieldmrkt_alias']
        )->fetchColumn();
		if ($item_count > 0) {
			$ritem['fieldmrkt_alias'] = $ritem['fieldmrkt_alias'] . rand(1000, 9999);
		}
	}

	if (
        $ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED
        && !($auth['isadmin'] && Cot::$cfg['market']['marketautovalidate'])
    ) {
        $ritem['fieldmrkt_state'] = MarketDictionary::STATE_PENDING;
	}

	/* === Hook === */
	foreach (cot_getextplugins('market.add.add.query') as $pl) {
		include $pl;
	}
	/* ===== */

	if (Cot::$db->insert(Cot::$db->market, $ritem)) {
		$id = (int) Cot::$db->lastInsertId();
		cot_extrafield_movefiles();
        cot_market_updateStructureCounters($ritem['fieldmrkt_cat']);
	} else {
		$id = null;
	}

	/* === Hook === */
	foreach (cot_getextplugins('market.add.add.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if ($ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED && Cot::$cache) {
		if (Cot::$cfg['cache_market']) {
            Cot::$cache->static->clearByUri(cot_market_url($ritem));
            Cot::$cache->static->clearByUri(cot_url('market', ['c' => $ritem['fieldmrkt_cat']]));
		}
		if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
		}
	}

	cot_shield_update(30, "r market");
	cot_log('Add store item #' . $id, 'market', 'add', 'done');

	return $id;
}

/**
 * Updates a store item in the CMS.
 * @param int $id Store item ID
 * @param array $ritem Store item data
 * @param array $auth  Permissions array
 * @return bool TRUE on success, FALSE on error
 */
function cot_market_update($id, &$ritem, $auth = [])
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

    if (cot_error_found()) {
		return false;
	}

	if (count($auth) == 0) {
		$auth = cot_market_auth($ritem['fieldmrkt_cat']);
	}

	if (!empty($ritem['fieldmrkt_alias'])) {
		$item_count = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
            ' WHERE fieldmrkt_alias = ? AND fieldmrkt_id != ?', array($ritem['fieldmrkt_alias'], $id))->fetchColumn();
		if ($item_count > 0) {
			$ritem['fieldmrkt_alias'] = $ritem['fieldmrkt_alias'] . rand(1000, 9999);
		}
	}

	$row_item = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?', $id)->fetch();

    if (
        $ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED
        && !($auth['isadmin'] && Cot::$cfg['market']['marketautovalidate'])
    ) {
        $ritem['fieldmrkt_state'] = MarketDictionary::STATE_PENDING;
    }

    Cot::$cache && Cot::$cache->db->remove('structure', 'system');

	if (!Cot::$db->update(Cot::$db->market, $ritem, 'fieldmrkt_id = ?', $id)) {
		return false;
	}
	cot_log("Edited store item #" . $id, 'market', 'edit', 'done');

	cot_extrafield_movefiles();

	/* === Hook === */
	foreach (cot_getextplugins('market.edit.update.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if (
        ($ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED  || $ritem['fieldmrkt_cat'] != $row_item['fieldmrkt_cat'])
        && Cot::$cache
    ) {
		if (Cot::$cfg['cache_market']) {
            Cot::$cache->static->clearByUri(cot_market_url($ritem));
            Cot::$cache->static->clearByUri(cot_url('market', ['c' => $ritem['fieldmrkt_cat']]));

			if ($ritem['fieldmrkt_cat'] != $row_item['fieldmrkt_cat']) {
                Cot::$cache->static->clearByUri(cot_market_url($row_item));
                Cot::$cache->static->clearByUri(cot_url('market', ['c' => $row_item['fieldmrkt_cat']]));
			}
		}
		if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
		}
	}

	return true;
}

/**
 * Generates store item list widget
 * @param string|string[] $categories Custom parent categories code
 * @param int $count Number of items to show. 0 - all items
 * @param string $template Path for template file
 * @param string $order Sorting order (SQL)
 * @param string $condition Custom selection filter (SQL)
 * @param bool $active_only Custom parent category code
 * @param bool $use_subcat Include subcategories TRUE/FALSE
 * @param bool $exclude_current Exclude the current store item from the rowset for items.
 * @param string $blacklist Category black list, semicolon separated
 * @param string $pagination Pagination symbol
 * @param int $cache_ttl Cache lifetime in seconds, 0 disables cache
 * @return string Parsed HTML
 */
function cot_market_enum(
    $categories = '',
    $count = 0,
    $template = '',
    $order = '',
    $condition = '',
	$active_only = true,
    $use_subcat = true,
    $exclude_current = false,
    $blacklist = '',
    $pagination = '',
    $cache_ttl = null
) {
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	global $db, $db_market, $db_users, $structure, $cfg, $sys, $lang, $cache;

	// Compile lists
	if (!is_array($blacklist)) {
		$blacklist = str_replace(' ', '', $blacklist);
		$blacklist = (!empty($blacklist)) ? explode(',', $blacklist) : array();
	}

	// Get the cats
	if (!empty($categories)) {
		if (!is_array($categories)) {
			$categories = str_replace(' ', '', $categories);
			$categories = explode(',', $categories);
		}
		$categories = array_unique($categories);
		if ($use_subcat) {
			$total_categories = [];
			foreach ($categories as $cat) {
				$cats = cot_structure_children('market', $cat, $use_subcat);
				$total_categories = array_merge($total_categories, $cats);
			}
			$categories = array_unique($total_categories);
		}
		$categories = (count($blacklist) > 0 ) ? array_diff($categories, $blacklist) : $categories;
		$where['cat'] = "fieldmrkt_cat IN ('" . implode("','", $categories) . "')";
	} elseif (count($blacklist)) {
		$where['cat_black'] = "fieldmrkt_cat NOT IN ('" . implode("','", $blacklist) . "')";
	}

	$where['condition'] = $condition;

	if ($exclude_current && defined('COT_MARKET') && !defined('COT_LIST')) {
		global $id;
        $tmp = 0;
        if (!empty($id)) {
            $tmp = (int) $id;
        }
		if (!empty($tmp)) {
            $where['fieldmrkt_id'] = "fieldmrkt_id != $tmp";
        }
	}
	if ($active_only) {
		$where['state'] = 'fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED;
		$where['date'] = "fieldmrkt_begin <= {$sys['now']} AND (fieldmrkt_expire = 0 OR fieldmrkt_expire > {$sys['now']})";
	}

	// Get pagination number if necessary
	if (!empty($pagination)) {
		[$pg, $d, $durl] = cot_import_pagenav($pagination, $count);
	} else {
		$d = 0;
	}

	// Display the items
	$mskin = (!empty($template) && file_exists($template)) ?
        $template : cot_tplfile(array('market', 'enum', $template), 'module');

    $cns_join_tables = '';
	$cns_join_columns = '';

	/* === Hook === */
	foreach (cot_getextplugins('market.enum.query') as $pl) {
		include $pl;
	}
	/* ===== */

    // Todo move it to comments plugin
	if (cot_plugin_active('comments')) {
		global $db_com;
		require_once cot_incfile('comments', 'plug');
		$cns_join_columns .= ", (SELECT COUNT(*) FROM `$db_com` WHERE com_area = 'market' AND com_code = p.fieldmrkt_id) AS com_count";
	}
	$sql_order = empty($order) ? 'ORDER BY fieldmrkt_date DESC' : "ORDER BY $order";
	$sql_limit = ($count > 0) ? "LIMIT $d, $count" : '';
	$where = array_filter($where);
	$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';

	$sql_total = "SELECT COUNT(*) FROM $db_market AS p $cns_join_tables $where";
	$sql_query = "SELECT p.*, u.* $cns_join_columns FROM $db_market AS p LEFT JOIN $db_users AS u ON p.fieldmrkt_ownerid = u.user_id
			$cns_join_tables $where $sql_order $sql_limit";

	$t = new XTemplate($mskin);

	isset($md5hash) || $md5hash = 'market_enum_'.md5(str_replace($sys['now'], '_time_', $mskin.$lang.$sql_query));

	if ($cache && (int) $cache_ttl > 0) {
		$item_query_html = $cache->disk->get($md5hash, 'market', (int) $cache_ttl);

		if (!empty($item_query_html)) {
			return $item_query_html;
		}
	}

	$totalitems = $db->query($sql_total)->fetchColumn();
	$sql = $db->query($sql_query);

	$sql_rowset = $sql->fetchAll();
	$jj = 0;
	foreach ($sql_rowset as $item) {
		$jj++;
		$t->assign(cot_generate_markettags($item, 'MARKET_ROW_', Cot::$cfg['market']['cat___default']['markettruncatetext']));

		$t->assign([
			'MARKET_ROW_NUM' => $jj,
			'MARKET_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'MARKET_ROW_RAW' => $item,
		]);

		$t->assign(cot_generate_usertags($item, 'MARKET_ROW_OWNER_'));

		/* === Hook === */
		foreach (cot_getextplugins('market.enum.loop') as $pl) {
			include $pl;
		}
		/* ===== */

		if (cot_plugin_active('comments')) {
			$itemUrlParams = empty($item['fieldmrkt_alias'])
                ? ['c' => $item['fieldmrkt_cat'], 'id' => $item['fieldmrkt_id']]
                : ['c' => $item['fieldmrkt_cat'], 'al' => $item['fieldmrkt_alias']];
			$t->assign([
				'MARKET_ROW_COMMENTS_LINK' => cot_commentsLink(
                    'market',
                    $itemUrlParams,
                    MarketDictionary::SOURCE_MARKET,
                    $item['fieldmrkt_id'],
                    $item['fieldmrkt_cat'],
                    $item
                ),
				'MARKET_ROW_COMMENTS_COUNT' => CommentsService::getInstance()
                    ->getCount(MarketDictionary::SOURCE_MARKET, $item['fieldmrkt_id'], $item),
			]);
		}

		$t->parse("MAIN.MARKET_ROW");
	}

	// Render pagination
	$url_params = $_GET;
    if (isset($url_params['rwr'])) {
        unset($url_params['rwr']);
    }
	$url_area = 'index';
    $extensionService = ExtensionsService::getInstance();
	$extensionCode = cot_import('e', 'G', 'ALP');
    if (!empty($extensionCode)) {
        if ($extensionService->isModuleActive($extensionCode)) {
            $url_area = $url_params['e'];
            unset($url_params['e']);
        } elseif ($extensionService->isPluginActive($extensionCode)) {
            $url_area = 'plug';
        }
    }
	unset($url_params[$pagination]);

    $pagenav = [
        'main' => null,
        'prev' => null,
        'next' => null,
        'first' => null,
        'last' => null,
        'current' => 1,
        'total' => 1,
    ];

	if (!empty($pagination)) {
		$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $count, $pagination);
	}

    $t->assign(cot_generatePaginationTags($pagenav));

	/* === Hook === */
	foreach (cot_getextplugins('market.enum.tags') as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN");
	$item_query_html = $t->text("MAIN");

	if ($cache && (int) $cache_ttl > 0) {
		$cache->disk->store($md5hash, $item_query_html, 'market');
	}
	return $item_query_html;
}


/**
 * Возвращает список товаров market для отображения на главной
 *
 * @param string $template Шаблон для вывода (по умолчанию 'index')
 * @param int $count Количество товаров для отображения (по умолчанию 5)
 * @param string $sqlsearch Дополнительные условия WHERE для SQL (по умолчанию '')
 * @param string $order Порядок сортировки SQL (по умолчанию 'fieldmrkt_date DESC')
 * @return string Сформированный HTML код для вывода
 */
function cot_getmarketlist($template = 'index', $count = 5, $sqlsearch = '', $order = 'fieldmrkt_updated DESC')
{
    global $db, $db_market, $cfg, $db_users;

    // Проверка прав доступа пользователя для модуля market
    list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('market', 'any', 'RWA');

    // Инициализация шаблона
    $t = new XTemplate(cot_tplfile(['market', $template], 'module'));

    // Подготовка дополнительных условий поиска
    $sqlsearch = !empty($sqlsearch) ? ' AND ' . $sqlsearch : '';

    // Длина текста для обрезки: если не задана в конфиге, ставим значение по умолчанию
    $truncateLen = isset($cfg['market']['markettruncatetext']) ? (int)$cfg['market']['markettruncatetext'] : 200;

    // Основной запрос к базе market с JOIN на таблицу пользователей
    $sqllist = $db->query("SELECT p.*, u.user_name 
        FROM $db_market AS p 
        LEFT JOIN $db_users AS u ON u.user_id = p.fieldmrkt_ownerid 
        WHERE p.fieldmrkt_state = 0 $sqlsearch 
        ORDER BY $order 
        LIMIT " . (int)$count);

    // Получение всех результатов запроса
    $sqllist_rowset = $sqllist->fetchAll();

    // Собираем ID и алиасы товаров (может понадобиться для дальнейшей обработки)
    $sqllist_idset = [];
    foreach ($sqllist_rowset as $item) {
        $sqllist_idset[$item['fieldmrkt_id']] = $item['fieldmrkt_alias'];
    }

    // Нумерация для чередования классов (odd/even)
    $jj = 0;
    foreach ($sqllist_rowset as $item) {
        $jj++;

        // Присвоение тегов владельца товара
        $t->assign(cot_generate_usertags($item, 'MARKET_ROW_OWNER_'));

        // Присвоение тегов товара с учетом длины обрезки текста
        $t->assign(cot_generate_markettags($item, 'MARKET_ROW_', $truncateLen, 
                                           $usr['isadmin'], $cfg['homebreadcrumb']));

        // Чередование классов для строк (odd/even)
        $t->assign([
            'MARKET_ROW_ODDEVEN' => cot_build_oddeven($jj),
        ]);

        // Парсинг одной строки товара
        $t->parse('MARKET.MARKET_ROW');
    }

    // Парсинг всего блока товаров
    $t->parse('MARKET');

    // Возвращаем готовый HTML
    return $t->text('MARKET');
}
