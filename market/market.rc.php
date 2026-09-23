<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

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


// Подключаем языковой файл модуля Market (если ещё не загружен)
if (!isset($L['market_price_converted_label'])) {
    require_once cot_langfile('market', 'module');
}



if (cot_module_active('market')) {
    // Получаем нужные строки и значения из конфигурации
    $converted_label = $L['market_price_converted_label'] ?? 'Цена в UAH (по курсу USD):';
    $default_label   = $L['market_price'] ?? 'Цена:';
    $rate            = $cfg['market']['market_rate_value_fieldmrkt_costdflt_to_cost_usd'] ?? 44.50;

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
                const uahPrice = usdPrice * rate;
                const formatted = uahPrice.toLocaleString('ru-RU', {
                    minimumFractionDigits: (uahPrice % 1 !== 0) ? 2 : 0,
                    maximumFractionDigits: 2
                });
                // Вставляем только число, без валюты (валюта выводится отдельно в шаблоне)
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