<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ХУКУ `rc` И ФАЙЛУ market.rc.php
 * ============================================================
 *
 * Хук `rc` в Cotonti вызывается в /system/common.php в секции
 * «Head Resources» — внутри блока `if (!COT_AJAX)` и внутри условия
 * `if (!isset($cot_rc_html[$theme]) || !$cache || !$cfg['headrc_consolidate'] || defined('COT_ADMIN'))`,
 * сразу после `cot_rc_add_standard()`:
 *
 *     foreach (cot_getextplugins('rc') as $pl) {
 *         include $pl;
 *     }
 *
 * Файл market.rc.php отвечает за три блока:
 *   1) Подключение JS-скрипта дерева категорий Market —
 *      с выбором файла в зависимости от активного плагина urleditor.
 *   2) Внедрение инлайн-скрипта конвертера цены (USD → локальная валюта)
 *      через Resources::embedFooter().
 *   3) Блок кастомизации Select2 — закомментирован; функциональность
 *      перенесена в market.footer.main.php.
 *
 * Механика:
 *   Регистрация JS-файла выполняется через Resources::linkFileFooter(),
 *   инлайн-скрипта — через Resources::embedFooter().
 *   Обе функции регистрируют ресурс в футере страницы.
 *
 * Область действия:
 *   — market tree: файл подключается, если существует физически.
 *     Выбор файла:
 *       • если активен плагин urleditor и его preset равен
 *         'handy' | 'myconfig' | 'marketplace' → marketTreeScriptURLEditor.js;
 *       • во всех остальных случаях → marketTreeScript.js.
 *   — конвертер цены: выполняется только при cot_module_active('market').
 *     Перед этим подгружается языковой файл модуля market
 *     (cot_langfile('market', 'module')), если не подгружен ранее.
 *   — Select2-блок: закомментирован целиком, не выполняется.
 *
 * Параметры конфигурации модуля:
 *   $cfg['market']['market_rate_value_fieldmrkt_costdflt_to_cost_usd'] —
 *     курс USD для конвертера, значение по умолчанию 44.90.
 *
 * Языковые строки:
 *   $L['market_price_converted_label'] — подпись для сконвертированной цены;
 *   $L['market_price']                 — стандартная подпись цены.
 *
 * Файлы:
 *   modules/market/js/marketTreeScript.js
 *   modules/market/js/marketTreeScriptURLEditor.js
 *   modules/market/css/marketSelect2CustomStyles.css (в закомментированном блоке)
 *   modules/market/js/marketSelect2CustomJS.js      (в закомментированном блоке)
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 26, 2026
 *
 * @package    market
 * @subpackage Setup
 * @version    5.7.9
 * @author     webitproff
 * @copyright  Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license    BSD
 * ============================================================
 */
 
 
use cot\extensions\ExtensionsService;

defined('COT_CODE') or die('Wrong URL');

global $L, $cfg;
/* 
 * ======================================================
 * ► НАЧАЛО ◄
 * ------------------------------------------------------
 * market tree: active category + раскрытие родителей
 * ======================================================
*/
$useUrlEditorTree = false;

if (cot_plugin_active('urleditor')) {
    $preset = $cfg['plugin']['urleditor']['preset'] ?? 'none';
    if ($preset === 'handy' || $preset === 'myconfig' || $preset === 'marketplace') {
        $useUrlEditorTree = true;
    }
}

if ($useUrlEditorTree) {
    // URL Editor ON
    $file = Cot::$cfg['modules_dir'] . '/market/js/marketTreeScriptURLEditor.js';
} else {
    // URL Editor OFF (default)
    $file = Cot::$cfg['modules_dir'] . '/market/js/marketTreeScript.js';
}

if (file_exists($file)) {
    Resources::linkFileFooter($file, 'js');
}
/* 
 * ======================================================
 * market tree: active category + раскрытие родителей
 * ------------------------------------------------------
 * ► КОНЕЦ ◄
 * ======================================================
*/


/* 
 * ======================================================
 * ► НАЧАЛО ◄
 * ------------------------------------------------------
 * Конвертер стоимости в местной валюте, если есть цена в долларах
 * Не переносим в market.footer.main.php потому что цены конвертируем 
 * на главной и в других локациях, где могут быть товары
 * ======================================================
*/

// Подключаем языковой файл модуля Market (если ещё не загружен)
if (!isset($L['market_price_converted_label'])) {
    require_once cot_langfile('market', 'module');
}



if (cot_module_active('market')) {
    // Получаем нужные строки и значения из конфигурации
    $converted_label = $L['market_price_converted_label'] ?? 'Цена в местной валюте (по курсу USD):';
    $default_label   = $L['market_price'] ?? 'Цена:';
    $rate            = $cfg['market']['market_rate_value_fieldmrkt_costdflt_to_cost_usd'] ?? 44.90;

    // Экранируем для безопасной вставки в JavaScript
    $converted_label_js = json_encode($converted_label, JSON_HEX_APOS | JSON_HEX_QUOT);
    $default_label_js   = json_encode($default_label, JSON_HEX_APOS | JSON_HEX_QUOT);
    $rate_js            = json_encode((float)$rate);

    Resources::embedFooter(<<<JS
    document.addEventListener('DOMContentLoaded', function() {
        const convertedLabel = {$converted_label_js};
        const defaultLabel   = {$default_label_js};
        const defaultRate    = {$rate_js};

        document.querySelectorAll('.market-price').forEach(function(el) {
            const usdPrice = parseFloat(el.dataset.usdPrice);
            const rate     = parseFloat(el.dataset.rate) || defaultRate;

            if (!isNaN(usdPrice) && usdPrice > 0 && !isNaN(rate) && rate > 0) {
                const dfltPrice = usdPrice * rate;
                const formatted = dfltPrice.toLocaleString('ru-RU', {
                    minimumFractionDigits: (dfltPrice % 1 !== 0) ? 2 : 0,
                    maximumFractionDigits: 2
                });
                // Вставляем только число, без валюты (валюта выводится отдельно в шаблоне) ( скрипт в market.rc.php )
                el.innerHTML = formatted;

                const label = el.closest('p, div, span, .fw-bold')?.querySelector('.price-label');
                if (label) {
                    label.textContent = convertedLabel;
                }
            } else {
                // Возвращаем стандартную подпись, если USD не используется
                const label = el.closest('p, div, span, .fw-bold')?.querySelector('.price-label');
                if (label && label.textContent !== defaultLabel) {
                    label.textContent = defaultLabel;
                }
            }
        });
    });
    JS
    );
}
/* 
 * ======================================================
 * Конвертер стоимости в местной валюте, если есть цена в долларах
 * ------------------------------------------------------
 * ► КОНЕЦ ◄
 * ======================================================
*/

/* 
 * ======================================================
 * ► НАЧАЛО ◄
 * ------------------------------------------------------
 * Select2: пользовательские CSS/JS из модуля Market
 * ======================================================
*/
/* Select2: пользовательские CSS/JS — только для модуля market на фронтэнде */
// Условие подключения файлов Select2: они должны попасть в футер только тогда,
// когда мы находимся на фронтэнде (не в админке) и внутри модуля market.
// Проверка `!defined('COT_ADMIN')` отсекает админку — там этот блок не нужен.
// Проверка `isset($_GET['e'])` защищает от notice, если параметр отсутствует.
// Проверка `$_GET['e'] === 'market'` ограничивает область модулем market.
// Resources::embedFooter('/* env[ext]=' . var_export(Cot::$env['ext'] ?? null, true) . ' */', 'js');

// Resources::embedFooter('/* env_ext=' . var_export($env['ext'] ?? 'KEY_MISSING', true)
//     . ' | cot_env_ext=' . var_export(Cot::$env['ext'] ?? 'KEY_MISSING', true)
//     . ' | same=' . (($GLOBALS['env'] ?? null) === Cot::$env ? 'yes' : 'no') . ' */', 'js');
	
	
// if (
//     !defined('COT_ADMIN')
//     && isset($_GET['e'])
//     && $_GET['e'] === 'market'
// ) {
    // Формируем абсолютный путь к CSS-файлу кастомизации Select2 внутри модуля market.
    // Cot::$cfg['modules_dir'] — корневая папка модулей (обычно modules/).
    // Путь должен существовать физически на диске.
//     $select2Css = Cot::$cfg['modules_dir'] . '/market/css/marketSelect2CustomStyles.css';
    // Проверяем, что файл реально существует — иначе подключать нечего.
    // Это защищает от ошибок, если файл переименован или удалён.
//     if (file_exists($select2Css)) {
        // Регистрируем CSS-файл для вывода в футере страницы.
        // Resources::linkFileFooter наполняет внутренний реестр $footerRc,
        // который затем соберётся в FOOTER_RC при рендере footer.php.
        // Второй аргумент 'css' — тип ресурса.
//         Resources::linkFileFooter($select2Css, 'css');
//     }

    // Формируем абсолютный путь к JS-файлу кастомизации Select2 внутри модуля market.
    // Cot::$cfg['modules_dir'] — корневая папка модулей (обычно modules/).
    // Путь должен существовать физически на диске.
//     $select2Js = Cot::$cfg['modules_dir'] . '/market/js/marketSelect2CustomJS.js';
    // Проверяем, что файл реально существует — иначе подключать нечего.
    // Это защищает от ошибок, если файл переименован или удалён.
//     if (file_exists($select2Js)) {
        // Регистрируем JS-файл для вывода в футере страницы.
        // Resources::linkFileFooter наполняет внутренний реестр $footerRc,
        // который затем соберётся в FOOTER_RC при рендере footer.php.
        // Второй аргумент 'js' — тип ресурса.
//         Resources::linkFileFooter($select2Js, 'js');
//     }
// }
/* 
 * ======================================================
 * Select2: пользовательские CSS/JS из модуля Market
 * ------------------------------------------------------
 * ► КОНЕЦ ◄
 * ======================================================
*/