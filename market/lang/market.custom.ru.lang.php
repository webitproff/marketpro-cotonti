<?php
/**
 * Russian Custom Language File for Market module Cotonti v1.+, PHP 8.5+, MySQL 8.4
 * Custom localization file for Cotonti using via function cot_langfile_custom() in system/functions.custom.php
 * How it works:  https://github.com/webitproff/functions.custom.php-cotonti
 * How it works:  https://abuyfile.com/ru/cotonti/reading/rukovodstvo-po-polzovatelskim-funkciyam-cotonti
 *
 *
 * Filename: market.custom.ru.lang.php
 *
 * Path:    modules/market/lang/market.custom.ru.lang.php
 *
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 * API Extrafields:     https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 27, 2026
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
 * ► НАЧАЛО ◄
 * ------------------------------------------------------
 * СИСТЕМА. РЕДАКТИРОВАТЬ МОЖНО, НО УДАЛЯТЬ СТРОКИ НЕЛЬЗЯ!
 * ======================================================
*/

$L['market_headline_showcase_without_categories'] = 'Витрина';

// ========================
// ПЕРЕОПРЕДЕЛЕНИЕ КОНФИГУРАЦИИ ИЗ ЯЗЫКОВОГО ФАЙЛА
// ========================
$useCfgMarketFromLang = true; // использовать значения конфигурации из файла локализации // Use configuration values from the localization file

if ($useCfgMarketFromLang === true) {
    $cfg['market']['marketlist_default_title'] = 'Витрина Market PRO';
    $cfg['market']['marketlist_default_desc'] = '<span class="badge text-bg-primary">CMS</span>, <span class="badge text-bg-success">Скрипт</span> и <span class="badge text-bg-info">Движок</span> - веб сайта онлайн-витрины, интернет магазина инфопродуктов и цифровых товаров. Разные цены в разных валютах на товар. Онлайн-оплата в криптовалюте за товары и услуги.';
}
$L['market_marketprofilter_to_filter'] = 'Фильтровать'; 

// ===========================
//  * эту строку никогда не передавать в шаблон.
//  * она используется внутри как параметр
//  * $t->assign('MARKET_BUY_DESCRIPTION', $buy_description);
// ===========================
$L['market_text_under_description'] = 'Купить <span class="fw-600"> %s </span> по низким ценам. Интернет-магазин предлагает заказать %s, просмотреть полное описание, детальные характеристики, фото товара и актуальные цены со скидками';

$L['market_text_description_header_json'] = 'Купить %s по низким ценам. Интернет-магазин предлагает заказать %s, просмотреть полное описание, детальные характеристики, фото товара и актуальные цены со скидками';

/* 
 * ======================================================
 * СИСТЕМА. РЕДАКТИРОВАТЬ МОЖНО, НО УДАЛЯТЬ СТРОКИ НЕЛЬЗЯ!
 * ------------------------------------------------------
 * ► КОНЕЦ ◄
 * ======================================================
*/


// ОБЩАЯ КАТЕГОРИЯ - УСЛУГИ БИЗНЕСУ
// ======== Русская версия ========
$L['market_metatitle_service'] = '';
$L['market_metadesc_service'] = '';
$L['market_list_cat_title_lang_line_service'] = '';
$L['market_list_cat_title_ListItemschemaOrg_service'] = 'Услуги бизнесу';
$L['market_list_cat_precise_values_service'] = '';
$L['market_og_title_service'] = '';
$L['market_og_description_service'] = '';
$L['market_og_image_service'] = '';  // themes/index36/img/market_cat/service/service.webp
$L['market_promo_bottom_service'] = '';

// ===========================
// ЭКСТРАПОЛЯ окализации заголовков (_TITLE)
// ===========================

// $L['xtra_field_name_title'] = 'локализованный заголовок/описание экстраполя';

$L['market_youtube_id_title'] = 'Видео обзор';
$L['market_youtube_id_tooltip'] = '&lt;em&gt;Смотреть&lt;/em&gt; &lt;u&gt;тематический видео обзор товара&lt;/u&gt; &lt;b&gt;опубликованный пользователями&lt;/b&gt; с YouTube';
$L['market_youtube_id_datacaption'] = '&lt;em&gt;Сейчас вы смотрите ознакомительное видео.&lt;/em&gt; &lt;u&gt;Обзор этого или похожего товара&lt;/u&gt; &lt;b&gt;от пользователей&lt;/b&gt; с YouTube &lt;em&gt;для общего понимания, что это за продукт&lt;/em&gt;';
$L['market_youtube_id_edit_tpl'] = '(загрузите видео по идентификатору) Пример ID из ссылки https://www.youtube.com/watch?v=<code>w7nB7YC8jc8</code>';
$L['market_forum_link_title'] = 'Обсуждение';
$L['market_forum_link_tooltip'] = '&lt;em&gt;Задайте вопрос на форуме и получите помощь.&lt;/em&gt; &lt;u&gt;Обсуждение&lt;/u&gt; &lt;b&gt;по теме этой статьи&lt;/b&gt;';
$L['market_forum_link_edit_tpl'] = 'Ссылка на раздел или тему форума только текущего сайта';

$L['market_github_rc_title'] = '';
$L['market_github_rc_tooltip'] = '';



//это для форума
$L['forums_topic_github_rc_title'] = 'Ссылка на GitHub';
$L['forums_topic_github_rc_title'] = 'Ресурсы';
$L['forums_topic_github_rc_tooltip'] = '&lt;em&gt;Исходный код&lt;/em&gt; &lt;u&gt;и дополнительные ресурсы&lt;/u&gt; &lt;b&gt;на GitHub&lt;/b&gt;';
$L['forums_topic_github_rc_edit_hint'] = 'Ссылка на ресурсы на GitHub';
$L['forums_topic_youtube_id_title'] = 'Видео обзор';
$L['forums_topic_youtube_id_tooltip'] = '&lt;em&gt;Смотреть&lt;/em&gt; &lt;u&gt;В ЭТОМ&lt;/u&gt; &lt;b&gt;окне&lt;/b&gt;';
$L['forums_topic_youtube_id_edit_hint'] = '(загрузите видео по идентификатору) Пример ID <b>w7nB-YC8jc8</b><br> из ссылки https://www.youtube.com/watch?v=<code>w7nB-YC8jc8</code>';
$L['forums_topic_market_link_title'] = 'Позиция на маркете';
$L['forums_topic_market_link_tooltip'] = '&lt;em&gt;Страница в маркетплейсе.&lt;/em&gt; &lt;u&gt;Товар может быть бесплатным&lt;/u&gt; &lt;b&gt;Товар, который обсуждается в этой теме форума&lt;/b&gt;';
$L['forums_topic_market_link_edit_hint'] = 'Ссылка на товар или услугу в маркетплейсе текущего сайта';


$L['market_tab_text_title'] = 'Описание';
$L['market_tab_cost_title'] = 'Цена';
$L['market_tab_delivery_title'] = 'Доставка';
$L['market_tab_payment_title'] = 'Оплата';
$L['market_tab_return_title'] = 'Возврат';
$L['market_tab_warranty_title'] = 'Гарантия';

$L['market_tab_cost_content'] = '';

$L['market_tab_delivery_content'] = '';
$L['market_tab_payment_content'] = '';
$L['market_tab_payment_content_alert'] = '';

$L['market_tab_return_content'] = '';
$L['market_tab_warranty_content'] = '';