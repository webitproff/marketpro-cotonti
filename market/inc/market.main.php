<?php
/**
 * Store item display. It is displayed on its own full page
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 * 
 * Filename: market.main.php
 *
 * Path:    modules/market/inc/market.main.php
 *
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ФАЙЛУ market.main.php
 * ============================================================
 *
 * Назначение:
 *   Отображает отдельную страницу товара модуля Market. Файл вызывается,
 *   когда пользователь открывает товар по прямому URL (с ID или алиасом).
 *   Он загружает данные товара, проверяет права доступа, формирует
 *   SEO-метаинформацию, генерирует теги товара и владельца, обрабатывает
 *   многостраничный текст и выводит готовую страницу через шаблон.
 *
 * Основные параметры URL:
 *   c=<код категории> — категория товара (обязателен для ЧПУ);
 *   id=<числовой ID>  — идентификатор товара (если нет алиаса);
 *   al=<алиас>        — алиас товара (если используется ЧПУ);
 *   pg=<номер>        — номер вкладки для многостраничного текста (опционально).
 *
 * Логика работы:
 *   1. Импортирует параметры id, al, c, pg.
 *   2. Выполняет SQL-запрос для получения данных товара (с JOIN пользователя).
 *   3. Если товар не найден — 404.
 *   4. Загружает полные данные владельца через UsersRepository.
 *   5. Проверяет права доступа на чтение в категории товара.
 *   6. Проверяет, что товар опубликован или доступен текущему пользователю
 *      (владельцу или администратору); иначе — 403.
 *   7. Увеличивает счётчик просмотров (или добавляет скрипт для AJAX-счётчика,
 *      если включено статическое кэширование).
 *   8. Формирует подзаголовок страницы (из мета-заголовка или названия товара).
 *   9. Генерирует теги товара (MARKET_*) и владельца (MARKET_OWNER_*).
 *  10. Обрабатывает многостраничность текста (разделитель [newpage]),
 *      строит навигацию по вкладкам.
 *  11. Выводит сообщения, хуки, парсит шаблон и возвращает HTML.
 *
 * Используемые классы и сервисы:
 *   MarketDictionary — константы состояний товара (STATE_PENDING и др.);
 *   UsersRepository  — получение данных пользователя по ID;
 *   Cot::$db         — прямые запросы к базе данных;
 *   XTemplate        — шаблонизатор для вывода страницы.
 *
 * Хуки:
 *   market.first             — в начале, до загрузки товара;
 *   market.main              — после установки основных переменных и шаблона;
 *   market.tags              — перед финальным парсингом шаблона.
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 * API Extrafields:     https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Sep 10, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

// Подключаем классы для работы 
// Импорт класса MarketDictionary, содержащего константы состояний товара.
use cot\modules\market\inc\MarketDictionary;
// Импорт класса UsersRepository для загрузки данных пользователя по ID.
use cot\users\UsersRepository;

defined('COT_CODE') or die('Wrong URL');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');
cot_block(Cot::$usr['auth_read']);
// global $db_market, $db_users;
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

/**
 * Добавляем поле user_id в массив $item.
 * Оно равно ownerid товара.
 * Это нужно для шаблона, где может использоваться {PHP.item.user_id}.
 */
$item['user_id'] = $item['fieldmrkt_ownerid'];

/**
 * Загружаем полный профиль владельца через UsersRepository.
 * Это необходимо для корректной генерации тегов пользователя
 * (cot_generate_usertags), которой нужны все поля user_*.
 * getById() возвращает массив данных пользователя или null, если пользователь не найден.
 */
$owner_info = UsersRepository::getInstance()->getById((int)$item['fieldmrkt_ownerid']);

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

Cot::$out['subtitle'] = empty($item['fieldmrkt_metatitle']) ? $item['fieldmrkt_title'] : $item['fieldmrkt_metatitle'];
Cot::$out['desc'] = empty($item['fieldmrkt_metadesc']) ? strip_tags($item['fieldmrkt_desc']) : strip_tags($item['fieldmrkt_metadesc']);
Cot::$out['keywords'] = !empty($item['fieldmrkt_keywords']) ? strip_tags($item['fieldmrkt_keywords']) : '';

// Building the canonical URL
$itemurl_params = array('c' => $item['fieldmrkt_cat']);
empty($al) ? $itemurl_params['id'] = $id : $itemurl_params['al'] = $al;
if ($pg > 0) {
	$itemurl_params['pg'] = $pg;
}
Cot::$out['canonical_uri'] = cot_url('market', $itemurl_params);


// Формируем имя, объект и путь шаблона

$tpl_PartExt = $cat['tpl'];
$mskin = cot_tplfile(['market', $tpl_PartExt], 'module', true);
if (empty($mskin)) cot_error('Шаблон не найден');
$tpl_Path = $sys['abs_url'] . $mskin;


Cot::$env['last_modified'] = $item['fieldmrkt_updated'];
Cot::$sys['noindex'] = false;
Cot::$R['code_noindex'] = '';

/* === Hook === */
foreach (cot_getextplugins('market.main') as $pl) {
	include $pl;
}
/* ============ */

$t = new XTemplate($mskin);


// require_once cot_incfile('users', 'module');

// Генерируем теги товара и присваиваем их шаблону.
// Параметры:
// $item — данные товара,
// 'MARKET_' — префикс тегов,
// 0 — не обрезать текст,
// false — не показывать админ-кнопки,
// Cot::$cfg['homebreadcrumb'] — добавлять ли ссылку на главную в хлебные крошки,
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


/**
 * Добавляем теги владельца.
 */
// Генерируем HTML-ссылку на профиль пользователя.
$t->assign('MARKET_OWNER', cot_build_user($item['fieldmrkt_ownerid'], $item['user_name']));
// Генерируем дополнительные теги пользователя (например, MARKET_OWNER_NAME, MARKET_OWNER_ID и т.д.).
$t->assign(cot_generate_usertags($item, 'MARKET_OWNER_'));

// Формируем ссылку на витрину продавца (используется в шаблоне)
$vendorShowcaseUrl = !empty($item['user_name'])
    ? cot_url('market', ['m' => 'vendor', 'u' => $item['user_name']])
    : '';

// Передаём теги в шаблон
$t->assign([
    // Прямая ссылка на витрину продавца
    'MARKET_OWNER_VENDOR_URL' => $vendorShowcaseUrl,

    // Готовая HTML-ссылка с текстом «Витрина продавца»
    'MARKET_OWNER_VENDOR_LINK' => $vendorShowcaseUrl
        ? cot_rc_link($vendorShowcaseUrl, Cot::$L['market_owner_vendor_link'], 'class="vendor-showcase-link"')
        : '',
]);

// ===== ФОРМИРОВАНИЕ ОПИСАНИЯ С НАЗВАНИЕМ ТОВАРА (С УЧЁТОМ ПЕРЕВОДА) =====
// Определяем название товара с учётом перевода, если плагин i18n4marketpro активен
global $L; // <-- добавить эту строку
$title_to_use = $item['fieldmrkt_title'];
if (cot_plugin_active('i18n4marketpro')) {
    $current_lang = Cot::$usr['lang'] ?: Cot::$cfg['defaultlang'];
    if ($current_lang != Cot::$cfg['defaultlang']) {
        // Регистрируем таблицу переводов, если ещё не зарегистрирована
        if (!isset(Cot::$db->i18n4marketpro_pages)) {
            Cot::$db->registerTable('i18n4marketpro_pages');
        }
        $trans = Cot::$db->query(
            "SELECT ipage_title FROM " . Cot::$db->i18n4marketpro_pages .
            " WHERE ipage_id = ? AND ipage_locale = ?",
            [$item['fieldmrkt_id'], $current_lang]
        )->fetch();
        if ($trans && !empty($trans['ipage_title'])) {
            $title_to_use = $trans['ipage_title'];
        }
    }
}

// Формируем описание с подстановкой названия товара (дважды, т.к. в строке два %s)
$buy_description = sprintf(
    $L['market_text_under_description'] ?? '%s',
    htmlspecialchars($title_to_use),
    htmlspecialchars($title_to_use)
);

// Передаём в шаблон как отдельный тег
$t->assign('MARKET_BUY_DESCRIPTION', $buy_description);
// ===== КОНЕЦ =====


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
/* 	$tmp = Cot::$cfg['easypagenav'];
	Cot::$cfg['easypagenav'] = false;
	$pn = cot_pagenav('market', (empty($al) ? 'id='.$id : 'al='.$al), $item['fieldmrkt_tab'], $item['fieldmrkt_totaltabs'], 1, 'pg');
	$item['fieldmrkt_tabnav'] = $pn['main'];
	Cot::$cfg['easypagenav'] = $tmp; 
*/
	// Временно выключаем easypagenav, чтобы номера шли с 0
	$tmp = Cot::$cfg['easypagenav'];
	Cot::$cfg['easypagenav'] = false;

	// Самое главное — передаём категорию ОБЯЗАТЕЛЬНО
	$base = 'c=' . $item['fieldmrkt_cat'];
	if (!empty($al)) {
		$base .= '&al=' . $al;
	} else {
		$base .= '&id=' . $id;
	}

	$pn = cot_pagenav(
		'market',
		$base,                          // ← вот здесь была главная косячина
		$item['fieldmrkt_tab'],
		$item['fieldmrkt_totaltabs'],
		1,
		'pg'
	);

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
$t->assign('TPL_PATH', $tpl_Path); // в шаблоне показываем путь к нему


/**
 * ============================================================================
 * cot_market_get_by_id_as_recommend()
 * ============================================================================
 * Поиск товаров по ID и если он есть функция создает теги для шаблона.
 * Выбираются только товары с fieldmrkt_state = 0, то есть опубликованные.
 * =================================
 */
cot_market_get_by_id_as_recommend($t);


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


