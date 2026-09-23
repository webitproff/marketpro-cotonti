<?php
/**
 * English Custom Language File for Market module Cotonti v1.+, PHP 8.5+, MySQL 8.4
 * Custom localization file for Cotonti using via function cot_langfile_custom() in system/functions.custom.php
 * How it works:  https://github.com/webitproff/functions.custom.php-cotonti
 * How it works:  https://abuyfile.com/ru/cotonti/reading/rukovodstvo-po-polzovatelskim-funkciyam-cotonti
 *
 * Filename: market.custom.en.lang.php
 *
 * Path:    modules/market/lang/market.custom.en.lang.php
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
 * ► START ◄
 * ------------------------------------------------------
 * SYSTEM. YOU CAN EDIT, BUT YOU MUST NOT DELETE LINES!
 * ======================================================
*/

$L['market_headline_showcase_without_categories'] = 'Showcase';

// ========================
// OVERRIDE CONFIGURATION FROM LANGUAGE FILE
// ========================
$useCfgMarketFromLang = true; // use configuration values from the localization file // Use configuration values from the localization file

if ($useCfgMarketFromLang === true) {
    $cfg['market']['marketlist_default_title'] = 'Market PRO Showcase';
    $cfg['market']['marketlist_default_desc'] = '<span class="badge text-bg-primary">CMS</span>, <span class="badge text-bg-success">Script</span> and <span class="badge text-bg-info">Engine</span> - for an online showcase website, e-commerce store of info products and digital goods. Different prices in different currencies per item. Online crypto payments for goods and services.';
}
$L['market_marketprofilter_to_filter'] = 'Filter'; 

// ===========================
//  * never pass this string to a template.
//  * it is used internally as a parameter
//  * $t->assign('MARKET_BUY_DESCRIPTION', $buy_description);
// ===========================
$L['market_text_under_description'] = 'Buy <span class="fw-600"> %s </span> at low prices. The online store offers to order %s, view the full description, detailed specifications, product photos and current discounted prices';

$L['market_text_description_header_json'] = 'Buy %s at low prices. The online store offers to order %s, view the full description, detailed specifications, product photos and current discounted prices';

/* 
 * ======================================================
 * SYSTEM. YOU CAN EDIT, BUT YOU MUST NOT DELETE LINES!
 * ------------------------------------------------------
 * ► END ◄
 * ======================================================
*/


// COMMON CATEGORY - BUSINESS SERVICES
// ======== Russian version ========
$L['market_metatitle_service'] = '';
$L['market_metadesc_service'] = '';
$L['market_list_cat_title_lang_line_service'] = '';
$L['market_list_cat_title_ListItemschemaOrg_service'] = 'Business services';
$L['market_list_cat_precise_values_service'] = '';
$L['market_og_title_service'] = '';
$L['market_og_description_service'] = '';
$L['market_og_image_service'] = '';  // themes/index36/img/market_cat/service/service.webp
$L['market_promo_bottom_service'] = '';

// ===========================
// EXTRAFIELD LOCALIZATION OF TITLES (_TITLE)
// ===========================

// $L['xtra_field_name_title'] = 'localized title/description of the extrafield';

$L['market_youtube_id_title'] = 'Video review';
$L['market_youtube_id_tooltip'] = '&lt;em&gt;Watch&lt;/em&gt; &lt;u&gt;a thematic video review of the item&lt;/u&gt; &lt;b&gt;published by users&lt;/b&gt; from YouTube';
$L['market_youtube_id_datacaption'] = '&lt;em&gt;You are currently watching an overview video.&lt;/em&gt; &lt;u&gt;A review of this or a similar item&lt;/u&gt; &lt;b&gt;by users&lt;/b&gt; from YouTube &lt;em&gt;for a general understanding of what this product is&lt;/em&gt;';
$L['market_youtube_id_edit_tpl'] = '(upload a video by its ID) Example ID from the link https://www.youtube.com/watch?v=<code>w7nB7YC8jc8</code>';
$L['market_forum_link_title'] = 'Discussion';
$L['market_forum_link_tooltip'] = '&lt;em&gt;Ask a question on the forum and get help.&lt;/em&gt; &lt;u&gt;Discussion&lt;/u&gt; &lt;b&gt;on the topic of this article&lt;/b&gt;';
$L['market_forum_link_edit_tpl'] = 'Link to a forum section or topic of the current site only';

$L['market_github_rc_title'] = '';
$L['market_github_rc_tooltip'] = '';



//this is for the forum
$L['forums_topic_github_rc_title'] = 'GitHub link';
$L['forums_topic_github_rc_title'] = 'Resources';
$L['forums_topic_github_rc_tooltip'] = '&lt;em&gt;Source code&lt;/em&gt; &lt;u&gt;and additional resources&lt;/u&gt; &lt;b&gt;on GitHub&lt;/b&gt;';
$L['forums_topic_github_rc_edit_hint'] = 'Link to resources on GitHub';
$L['forums_topic_youtube_id_title'] = 'Video review';
$L['forums_topic_youtube_id_tooltip'] = '&lt;em&gt;Watch&lt;/em&gt; &lt;u&gt;in THIS&lt;/u&gt; &lt;b&gt;window&lt;/b&gt;';
$L['forums_topic_youtube_id_edit_hint'] = '(upload a video by its ID) Example ID <b>w7nB-YC8jc8</b><br> from the link https://www.youtube.com/watch?v=<code>w7nB-YC8jc8</code>';
$L['forums_topic_market_link_title'] = 'Market item';
$L['forums_topic_market_link_tooltip'] = '&lt;em&gt;Marketplace page.&lt;/em&gt; &lt;u&gt;The item may be free&lt;/u&gt; &lt;b&gt;The item discussed in this forum topic&lt;/b&gt;';
$L['forums_topic_market_link_edit_hint'] = 'Link to the item or service in the marketplace of the current site';


$L['market_tab_text_title'] = 'Description';
$L['market_tab_cost_title'] = 'Price';
$L['market_tab_delivery_title'] = 'Delivery';
$L['market_tab_payment_title'] = 'Payment';
$L['market_tab_return_title'] = 'Return';
$L['market_tab_warranty_title'] = 'Warranty';

$L['market_tab_cost_content'] = '';

$L['market_tab_delivery_content'] = '';
$L['market_tab_payment_content'] = '';
$L['market_tab_payment_content_alert'] = '';

$L['market_tab_return_content'] = '';
$L['market_tab_warranty_content'] = '';