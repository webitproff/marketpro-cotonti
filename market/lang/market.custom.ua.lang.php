<?php
/**
 * Український користувацький файл локалізації для модуля Market Cotonti v1.+, PHP 8.5+, MySQL 8.4
 * Користувацький файл локалізації для Cotonti за допомогою функції cot_langfile_custom() у system/functions.custom.php
 * Як це працює:  https://github.com/webitproff/functions.custom.php-cotonti
 * Як це працює:  https://abuyfile.com/ru/cotonti/reading/rukovodstvo-po-polzovatelskim-funkciyam-cotonti
 *
 * Ім'я файлу: market.custom.ua.lang.php
 *
 * Шлях:    modules/market/lang/market.custom.ua.lang.php
 *
 * Джерело та оновлення   https://github.com/webitproff/marketpro-cotonti
 * Детальніше:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Підтримка:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 * API Extrafields:     https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Дата: 27 серпня 2026
 *
 * @package market
 * @version 5.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL.');
/* 
 * ======================================================
 * ► ПОЧАТОК ◄
 * ------------------------------------------------------
 * СИСТЕМА. РЕДАГУВАТИ МОЖНА, АЛЕ ВИДАЛЯТИ РЯДКИ НЕ МОЖНА!
 * ======================================================
*/

$L['market_headline_showcase_without_categories'] = 'Вітрина';

// ========================
// ПЕРЕВИЗНАЧЕННЯ КОНФІГУРАЦІЇ З МОВНОГО ФАЙЛУ
// ========================
$useCfgMarketFromLang = true; // використовувати значення конфігурації з файлу локалізації // Use configuration values from the localization file

if ($useCfgMarketFromLang === true) {
    $cfg['market']['marketlist_default_title'] = 'Вітрина Market PRO';
    $cfg['market']['marketlist_default_desc'] = '<span class="badge text-bg-primary">CMS</span>, <span class="badge text-bg-success">Скрипт</span> і <span class="badge text-bg-info">Движок</span> - вебсайту онлайн-вітрини, інтернет-магазину інфопродуктів і цифрових товарів. Різні ціни в різних валютах на товар. Онлайн-оплата в криптовалюті за товари та послуги.';
}
$L['market_marketprofilter_to_filter'] = 'Фільтрувати'; 

// ===========================
//  * цей рядок ніколи не передавати в шаблон.
//  * він використовується всередині як параметр
//  * $t->assign('MARKET_BUY_DESCRIPTION', $buy_description);
// ===========================
$L['market_text_under_description'] = 'Купити <span class="fw-600"> %s </span> за низькими цінами. Інтернет-магазин пропонує замовити %s, переглянути повний опис, детальні характеристики, фото товару та актуальні ціни зі знижками';

$L['market_text_description_header_json'] = 'Купити %s за низькими цінами. Інтернет-магазин пропонує замовити %s, переглянути повний опис, детальні характеристики, фото товару та актуальні ціни зі знижками';

/* 
 * ======================================================
 * СИСТЕМА. РЕДАГУВАТИ МОЖНА, АЛЕ ВИДАЛЯТИ РЯДКИ НЕ МОЖНА!
 * ------------------------------------------------------
 * ► КІНЕЦЬ ◄
 * ======================================================
*/


// ЗАГАЛЬНА КАТЕГОРІЯ - ПОСЛУГИ БІЗНЕСУ
// ======== Російська версія ========
$L['market_metatitle_service'] = '';
$L['market_metadesc_service'] = '';
$L['market_list_cat_title_lang_line_service'] = '';
$L['market_list_cat_title_ListItemschemaOrg_service'] = 'Послуги бізнесу';
$L['market_list_cat_precise_values_service'] = '';
$L['market_og_title_service'] = '';
$L['market_og_description_service'] = '';
$L['market_og_image_service'] = '';  // themes/index36/img/market_cat/service/service.webp
$L['market_promo_bottom_service'] = '';

// ===========================
// ЕКСТРАПОЛЯ ЛОКАЛІЗАЦІЇ ЗАГОЛОВКІВ (_TITLE)
// ===========================

// $L['xtra_field_name_title'] = 'локалізований заголовок/опис екстраполя';

$L['market_youtube_id_title'] = 'Відеоогляд';
$L['market_youtube_id_tooltip'] = '&lt;em&gt;Дивитися&lt;/em&gt; &lt;u&gt;тематичний відеоогляд товару&lt;/u&gt; &lt;b&gt;опублікований користувачами&lt;/b&gt; з YouTube';
$L['market_youtube_id_datacaption'] = '&lt;em&gt;Зараз ви дивитеся ознайомлювальне відео.&lt;/em&gt; &lt;u&gt;Огляд цього або схожого товару&lt;/u&gt; &lt;b&gt;від користувачів&lt;/b&gt; з YouTube &lt;em&gt;для загального розуміння, що це за продукт&lt;/em&gt;';
$L['market_youtube_id_edit_tpl'] = '(завантажте відео за ідентифікатором) Приклад ID з посилання https://www.youtube.com/watch?v=<code>w7nB7YC8jc8</code>';
$L['market_forum_link_title'] = 'Обговорення';
$L['market_forum_link_tooltip'] = '&lt;em&gt;Поставте питання на форумі та отримайте допомогу.&lt;/em&gt; &lt;u&gt;Обговорення&lt;/u&gt; &lt;b&gt;за темою цієї статті&lt;/b&gt;';
$L['market_forum_link_edit_tpl'] = 'Посилання на розділ або тему форуму лише поточного сайту';

$L['market_github_rc_title'] = '';
$L['market_github_rc_tooltip'] = '';



//це для форуму
$L['forums_topic_github_rc_title'] = 'Посилання на GitHub';
$L['forums_topic_github_rc_title'] = 'Ресурси';
$L['forums_topic_github_rc_tooltip'] = '&lt;em&gt;Початковий код&lt;/em&gt; &lt;u&gt;та додаткові ресурси&lt;/u&gt; &lt;b&gt;на GitHub&lt;/b&gt;';
$L['forums_topic_github_rc_edit_hint'] = 'Посилання на ресурси на GitHub';
$L['forums_topic_youtube_id_title'] = 'Відеоогляд';
$L['forums_topic_youtube_id_tooltip'] = '&lt;em&gt;Дивитися&lt;/em&gt; &lt;u&gt;У ЦЬОМУ&lt;/u&gt; &lt;b&gt;вікні&lt;/b&gt;';
$L['forums_topic_youtube_id_edit_hint'] = '(завантажте відео за ідентифікатором) Приклад ID <b>w7nB-YC8jc8</b><br> з посилання https://www.youtube.com/watch?v=<code>w7nB-YC8jc8</code>';
$L['forums_topic_market_link_title'] = 'Позиція на маркеті';
$L['forums_topic_market_link_tooltip'] = '&lt;em&gt;Сторінка в маркетплейсі.&lt;/em&gt; &lt;u&gt;Товар може бути безкоштовним&lt;/u&gt; &lt;b&gt;Товар, який обговорюється в цій темі форуму&lt;/b&gt;';
$L['forums_topic_market_link_edit_hint'] = 'Посилання на товар або послугу в маркетплейсі поточного сайту';


$L['market_tab_text_title'] = 'Опис';
$L['market_tab_cost_title'] = 'Ціна';
$L['market_tab_delivery_title'] = 'Доставка';
$L['market_tab_payment_title'] = 'Оплата';
$L['market_tab_return_title'] = 'Повернення';
$L['market_tab_warranty_title'] = 'Гарантія';

$L['market_tab_cost_content'] = '';

$L['market_tab_delivery_content'] = '';
$L['market_tab_payment_content'] = '';
$L['market_tab_payment_content_alert'] = '';

$L['market_tab_return_content'] = '';
$L['market_tab_warranty_content'] = '';