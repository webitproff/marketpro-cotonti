<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=footer.main
[END_COT_EXT]
==================== */

/**
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ХУКУ `footer.main` И ФАЙЛУ market.footer.main.php
 * ============================================================
 *
 * Хук `footer.main` в Cotonti вызывается внутри /system/footer.php
 * в блоке `if (Cot::$sys['displayFooter'])`, сразу после создания
 * объекта XTemplate для футера и до присвоения тегов шаблона.
 *
 * Файл market.footer.main.php отвечает за подключение пользовательских
 * файлов кастомизации библиотеки Select2 (CSS и JS) к подвалу страницы
 * только для модуля Market на фронтэнде.
 *
 * Механика:
 *   Resources::linkFileFooter() наполняет внутренний реестр $footerRc,
 *   который затем собирается в строку через Resources::renderFooter()
 *   (вызывается позже в этом же footer.php) и уходит в тег FOOTER_RC
 *   шаблона подвала. CSS отдаётся раньше JS автоматически, потому что
 *   renderFooter() делает ksort() по ключу типа ресурса.
 *
 * Область действия:
 *   — только фронтэнд (defined('COT_ADMIN') отсекает админку);
 *   — только модуль Market (Cot::$env['ext'] === 'market'); это значение
 *     заполняется роутером в момент разбора запроса и гарантированно
 *     доступно на этом хуке для всех локаций модуля (market, market.<cat>,
 *     market.<cat>.pagehasid, market.vendors и т.п.).
 *
 * Параметры конфигурации модуля (см. market.setup.php, секция
 * BEGIN_COT_EXT_CONFIG):
 *   market_select2_custom_css — radio (0/1). Разрешает подключение
 *     модульных стилей кастомизации Select2.
 *   market_select2_custom_js  — radio (0/1). Разрешает подключение
 *     модульных скриптов кастомизации Select2.
 *
 * Файлы кастомизации:
 *   modules/market/css/marketSelect2CustomStyles.css
 *   modules/market/js/marketSelect2CustomJS.js
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 25, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

// Защита от прямого вызова файла
defined('COT_CODE') or die('Wrong URL.');
// ВЫХОДИМ ЕСЛИ ЛОКАЦИЯ НЕ НАША.
// УСТАНАВЛИВАЕТСЯ В modules/market/market.php
if (Cot::$env['location'] !== 'market') {
    return;
}

/* 
 * ======================================================
 * ► НАЧАЛО ◄
 * ------------------------------------------------------
 * Select2: пользовательские CSS/JS из модуля Market
 * ======================================================
*/
// Пользовательские стили Select2 — только если опция включена
// (radio=1 в market.setup.php) и файл физически существует.
if (!empty(Cot::$cfg['market']['market_select2_custom_css'])) {
    $css = Cot::$cfg['modules_dir'] . '/market/css/marketSelect2CustomStyles.css';
    if (file_exists($css)) {
        Resources::linkFileFooter($css, 'css');
    }
}

// Пользовательские скрипты Select2 — только если опция включена
// (radio=1 в market.setup.php) и файл физически существует.
if (!empty(Cot::$cfg['market']['market_select2_custom_js'])) {
    $js = Cot::$cfg['modules_dir'] . '/market/js/marketSelect2CustomJS.js';
    if (file_exists($js)) {
        Resources::linkFileFooter($js, 'js');
    }
}

/* 
 * ======================================================
 * Select2: пользовательские CSS/JS из модуля Market
 * ------------------------------------------------------
 * ► КОНЕЦ ◄
 * ======================================================
*/