<?php
/**
 * English Language File for the Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * All text strings used by the Market PRO module in the Cotonti interface:
 * - module name and description (info_name, info_desc, info_notes)
 * - settings in the admin panel (cfg_…)
 * - hints for fields (cfg_…_hint)
 * - user and administrator interface strings
 * - plural forms ($Ls)
 *
 * Filename: modules/market/lang/market.en.lang.php
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 26, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');


// ========================
// MODULE INFORMATION (ADMIN)
// ========================
$L['info_name']  = 'Market PRO';
$L['info_desc']  = 'E-commerce module that can operate as a single-seller store or as a marketplace with vendor showcases.';
$L['info_notes'] = 'More: <a href="https://abuyfile.com/ru/market/cotonti/plugs/marketpro" target="_blank">documentation and links</a>.';

$L['market_title']       = $L['info_name']; // backwards compatibility for old extentions
$L['market_desc']        = $L['info_desc']; // backwards compatibility for old extentions

$L['Market']       = $L['info_name']; // backwards compatibility for old extentions
$L['market']       = $L['info_name']; // backwards compatibility for old extentions


/* 
 * ======================================================
 * ► START ◄
 * ------------------------------------------------------
 * MODULE SETTINGS (CONFIGURATION)
 * ======================================================
*/
// =========================================
// BEGIN COT EXT CONFIG
// =========================================
$L['cfg_marketlist_default_title'] = 'Default store title';
$L['cfg_marketlist_default_title_hint'] = 'Store title shown when no category or item is selected';

$L['cfg_marketlist_default_desc'] = 'Default store description';
$L['cfg_marketlist_default_desc_hint'] = 'Store description shown when no category or item is selected';

// --- Currency and rates ---
$L['cfg_market_currency'] = '<strong>Base currency</strong> - default monetary unit of price';
$L['cfg_market_currency_hint'] = 'Usually this is the national currency of your country. The name of such local currency can be written in full or abbreviated. Affects only the public visibility of the item price in the specified currency unit. Values of such price (integer) are entered into the <code>fieldmrkt_costdflt</code> field of the module table.';

$L['cfg_market_for_rate_base_currency_cost_usd'] = 'Hard currency for rate calculation (e.g. USD)';
$L['cfg_market_for_rate_base_currency_cost_usd_hint'] = 'International currency used to calculate the item price in the base currency. For example, you sell an item in local currency, but the supplier delivers goods only when paid in US dollars';

$L['cfg_market_rate_value_fieldmrkt_costdflt_to_cost_usd'] = 'Rate of base currency to hard currency';
$L['cfg_market_rate_value_fieldmrkt_costdflt_to_cost_usd_hint'] = 'Value of the rate of your local currency to the international one. For example, local currency is Bitcoin (BTC) and we count how much local currency is needed to buy 1 US dollar. Now it is about 0.000012, but as a separator we use a dot, not a comma (0.000012). <br><a href="https://www.xe.com/currencyconverter/convert/?Amount=1&From=USD&To=EUR" target="_blank"><strong>Current world currency rates</strong></a>. Insert the value of your currency';

// --- Schema.org currency code ---
$L['cfg_market_currency_schema_org']        = 'Schema.org currency code';
$L['cfg_market_currency_schema_org_hint']   = 'Currency code according to ISO 4217 for Schema.org microdata.';
$L['cfg_market_currency_schema_org_params'] = [
    'USD' => 'USD — US Dollar',
    'EUR' => 'EUR — Euro',
    'RUB' => 'RUB — Russian ruble',
    'UAH' => 'UAH — Ukrainian hryvnia',
    'KZT' => 'KZT — Kazakhstani tenge',
    'BYN' => 'BYN — Belarusian ruble',
    'UZS' => 'UZS — Uzbek sum',
    'KGS' => 'KGS — Kyrgyzstani som',
    'TJS' => 'TJS — Tajikistani somoni',
    'TMT' => 'TMT — Turkmenistani manat',
    'AZN' => 'AZN — Azerbaijani manat',
    'AMD' => 'AMD — Armenian dram',
    'MDL' => 'MDL — Moldovan leu',
    'JPY' => 'JPY — Japanese yen',
    'CNY' => 'CNY — Chinese yuan',
    'BTC' => 'BTC — Bitcoin',
];

// --- Categories and display ---
$L['cfg_marketmaxlistsperpage'] = 'Max. categories per page';
$L['cfg_marketmaxlistsperpage_hint'] = 'Number of categories displayed on one list page';

$L['cfg_marketmaxlistsperpageadmin'] = 'Max. items per page in admin panel';
$L['cfg_marketmaxlistsperpageadmin_hint'] = 'Number of records per page of the item list in the administrative panel';

$L['cfg_marketblacktreecatspage'] = 'Category blacklist';
$L['cfg_marketblacktreecatspage_hint'] = 'Category codes excluded from the category tree on pages (e.g. system, unvalidated)';

// --- Sorting and structure ---
$L['cfg_market_main_order'] = 'Main sorting (home page)';
$L['cfg_market_main_order_hint'] = 'Field and direction of item sorting on the store home page';

// --- Main settings ---
$L['cfg_marketmarkup'] = 'Enable markup in description';
$L['cfg_marketmarkup_hint'] = 'Whether to use the visual text editor in the item description. For example: HTML or BBCode';

$L['cfg_marketparser'] = 'Description parser';
$L['cfg_marketparser_hint'] = 'Select a parser for processing the item description (e.g. BBCode, HTML, etc.). If HTML is available — use it.';

$L['cfg_marketcount_admin'] = 'Count administrator visits';
$L['cfg_marketcount_admin_hint'] = 'Include administrator visits in site traffic statistics';

$L['cfg_marketautovalidate'] = 'Automatic item validation';
$L['cfg_marketautovalidate_hint'] = 'Automatically approve publication of items created by a user with section administration rights';

$L['cfg_market_select2_custom_css'] = 'Enable custom Select2 styles from the Market module';
$L['cfg_market_select2_custom_css_hint'] = 'Includes the file <strong>modules/market/css/marketSelect2CustomStyles.css</strong>, which contains styles for customizing the Select2 library used to output categories in various locations. It is recommended to move the styles to your theme stylesheet and disable this option.';

$L['cfg_market_select2_custom_js'] = 'Enable custom Select2 scripts from the Market module';
$L['cfg_market_select2_custom_js_hint'] = 'Includes the file <strong>modules/market/js/marketSelect2CustomJS.js</strong>, which contains scripts for customizing the Select2 library used to output categories in various locations. It is recommended to move these scripts to a separate script file in your theme folder and disable this option.';
// =========================================
// END COT EXT CONFIG
// =========================================

// =========================================
// BEGIN COT EXT CONFIG STRUCTURE
// =========================================
$L['cfg_marketorder'] = 'Sort field';
$L['cfg_marketorder_hint'] = 'Field by which items are sorted in the category';
$L['cfg_marketorder_params'] = [];

$L['cfg_marketway'] = 'Sort direction';
$L['cfg_marketway_hint'] = 'Sort direction: ascending or descending';
$L['cfg_marketway_params'] = [$L['Ascending'], $L['Descending']];

$L['cfg_maxrowsperpage'] = 'Max. items per list page';
$L['cfg_maxrowsperpage_hint'] = 'Number of items displayed on one list page';

$L['cfg_markettruncatetext'] = 'Limit text size in item lists';
$L['cfg_markettruncatetext_hint'] = '0 to disable';

$L['cfg_marketallowemptytext'] = 'Allow empty item description';
$L['cfg_marketallowemptytext_hint'] = 'Allow publishing items without description';

// --- Default SEO ---
$L['cfg_marketmetatitle'] = 'Meta title';
$L['cfg_marketmetatitle_hint'] = 'Default meta title tag for store pages';

$L['cfg_marketmetadesc'] = 'Meta description';
$L['cfg_marketmetadesc_hint'] = 'Default meta description tag for store pages';

// --- Category settings (STRUCTURE) ---
$L['cfg_marketmaxlistsperpageincat'] = 'Max. subcategories per category page';
$L['cfg_marketmaxlistsperpageincat_hint'] = 'Number of subcategories displayed on a page inside a category';

$L['cfg_marketkeywords'] = 'Keywords';
$L['cfg_marketkeywords_hint'] = 'Default meta keywords tag for store pages';

// --- Duplicate setting required for categories ---
$L['cfg_marketmaxlistsperpage'] = 'Max. categories per page'; // duplicated in STRUCTURE section market.setup.php. It necessary for categories
// =========================================
// END COT EXT CONFIG STRUCTURE
// =========================================

/* 
 * ======================================================
 * MODULE SETTINGS (CONFIGURATION)
 * ------------------------------------------------------
 * ► END ◄
 * ======================================================
*/
// ========================
// MAIN INTERFACE STRINGS
// ========================
$L['market_title_general'] = 'Market';
$L['market_title_in_links'] = 'Market showcases';
$L['market_seller_vendors_title'] = 'Showcases and Sellers';
$L['market_categories'] = 'Market categories';


// ========================
// MARKET ADMIN PANEL: LOCALIZATION FOR market.admin.tpl
// ========================
$L['adm_market_configuration'] = 'Configuration';
$L['adm_market_categories'] = 'Categories';
$L['adm_market_extrafields'] = 'Extrafields';
$L['adm_market_search'] = 'Search';
$L['adm_market_category'] = 'Category';
$L['adm_market_select_filter_options'] = 'Filter options';
$L['adm_market_select_status_publication'] = 'Select publication status';
$L['adm_market_sort'] = 'Sort';
$L['adm_market_select_filter_sorting_direction'] = 'Sorting direction';
$L['adm_market_filter'] = 'Filter';
$L['adm_market_prune'] = 'Reset';
$L['adm_market_id'] = 'ID';
$L['adm_market_status'] = 'Status';
$L['adm_market_title'] = 'Title';
$L['adm_market_action'] = 'Action';
$L['adm_market_validate'] = 'Validate';
$L['adm_market_delete'] = 'Delete';
$L['adm_market_open'] = 'Open';
$L['adm_market_edit'] = 'Edit';
$L['adm_market_none'] = 'None';
$L['adm_market_total'] = 'Total';
$L['adm_market_onpage'] = 'On page';
$L['adm_market_text'] = 'Text';

// ========================
// ADMIN PANEL: STATUSES, HELP, FILES
// ========================
$L['adm_lang_market_valqueue'] = 'Validation queue';
$L['adm_lang_market_validated'] = 'Validated';
$L['adm_lang_market_expired'] = 'Expired';
$L['adm_lang_market_structure'] = 'Item structure (categories)';
$L['adm_lang_market_sort'] = 'Sort';
$L['adm_lang_market_sortingorder'] = 'Default sorting order in category';
$L['adm_lang_market_showall'] = 'Show all';
$L['adm_lang_market_help_market'] = 'Help string from the localization file and global admin help in admin.main.php <code>\'ADMIN_HELP\' => $adminHelp </code> Items of the "system" category are not displayed in lists and are standalone records';
$L['adm_lang_market_fileyesno'] = 'File (yes/no)';
$L['adm_lang_market_fileurl'] = 'File URL';
$L['adm_lang_market_filecount'] = 'Download count';
$L['adm_lang_market_filesize'] = 'File size';

// ========================
// STATS WIDGET (admin home page)
// ========================
$L['market_stats_activity'] = 'Activity';
$L['market_stats_today']    = 'Today';
$L['market_stats_week']     = 'Last 7 days';
$L['market_stats_month']    = 'Last 30 days';
$L['market_stats_latest']   = 'Latest items';
$L['market_stats_top_cats'] = 'Top categories';
$L['market_stats_top_viewed'] = 'Top viewed';

// ========================
// BULK ACTIONS AND AJAX LOAD
// ========================
$L['market_adm_select_all'] = 'Select all';
$L['market_adm_deselect_all'] = 'Deselect all';

// ========================
// FORMS: ADD AND EDIT ITEM
// ========================
$L['market_form_add_item_title'] = 'Create item card';
$L['market_form_add_item_subtitle'] = 'Fill in at least the required &#128681; fields and save the form. When editing the item you can fill in the rest.';
$L['market_form_item_edit_title'] = 'Editing item properties';
$L['market_form_item_edit_subtitle'] = 'Change and fill in the required fields and save the form';
$L['market_form_parser'] = 'Markup (parser)';
$L['market_form_parser_hint'] = 'Visual text editor, if configured. If <code>HTML</code> is available for selection — always use it';
$L['market_form_text_full'] = 'Item description';
$L['market_form_text_full_hint'] = 'No links, spam or garbage';
$L['market_form_date_now'] = 'Update item date';
$L['market_form_meta_title'] = 'Meta title';
$L['market_form_meta_title_hint'] = 'Title for the browser tab. Up to 70 characters.';
$L['market_form_meta_desc'] = 'Meta description';
$L['market_form_meta_desc_hint'] = 'Explain to search engines what this item is. Up to 155 characters.';
$L['market_form_category'] = 'Item category';
$L['market_form_category_hint'] = 'Select the showcase structure section that best matches your item by its characteristics, qualities and properties';
$L['market_form_meta_h1'] = 'SEO H1 heading';
$L['market_form_meta_h1_hint'] = 'Main and user-visible page heading. Used to output on the item card page instead of the regular heading/title';
$L['market_form_owner'] = 'Item owner';
$L['market_form_owner_hint'] = 'Also the seller. Acts as the publication author and owner of the item card page on the site';
$L['market_form_item_title'] = 'Item title';
$L['market_form_item_title_hint'] = 'Regular item title visible in lists, orders, etc.';
$L['market_form_item_desc'] = 'Introductory description';
$L['market_form_item_desc_hint'] = 'Short introductory item description visible in lists under the item name';
$L['market_form_item_alias'] = 'Alias in SEF URL';
$L['market_form_item_alias_hint'] = 'Unique alias as part of the item URL. Fill with Latin letters and no special characters.';
$L['market_form_item_cost_usd'] = 'Price in USD';
$L['market_form_item_cost_usd_hint'] = 'Optional. Needed if prices in price lists are in dollars. Enter the price in USD, it is automatically converted and shows the price in the site currency at the current rate. Copy this value and paste it into the price field of your item in the local (base) currency of your showcase.';
$L['market_form_item_fieldmrkt_costdflt']  = 'Price/cost in base currency';
$L['market_form_item_price_base_after_rate']          = 'Price at current rate';

$L['market_form_item_pcod']               = 'Code/SKU';
$L['market_form_item_pcod_hint']          = 'Your item code for quick search on the site. Usually used when filling items from your suppliers\' price lists';
$L['market_form_extrafield']               = 'Extrafields.';
$L['market_form_extrafield_not_found']     = 'Extrafields for the market module have not been created yet.';
$L['market_form_extrafield_link'] = 
    'Edit' .
    '<a href="' . Cot::$cfg['mainurl'] . '/' . cot_url('admin', 'm=extrafields&n=' . Cot::$db_x . 'market', '', true) . '" target="_blank">' .
    '<strong> ' . $L['Extrafields'] . ' </strong></a>.';

$L['market_form_extrafield_hint']          = 'Extrafields are your additional custom fields for your item, to enter and output any information of various types. If there are more than a few, it is recommended to use a separate plugin <a href="https://github.com/webitproff/xtradbrowmarket-cotonti" target="_blank">Extrafields Market Custom i18n</a>.';

$L['market_goto_add_new_item_title'] = 'Add item';
$L['market_goto_add_new_item_title_hint'] = 'Explanation about Add item (placeholder)';

$L['market_goto_edit_item_title'] = 'Edit item';
$L['market_goto_edit_item_title_hint'] = 'Explanation about Edit item (placeholder)';

// ========================
// ERRORS, CONFIRMATIONS AND NOTIFICATIONS
// ========================
$L['market_aliascharacters'] = 'Characters "+", "/", "?", "%", "#", "&" are not allowed in aliases';
$L['market_catmissing'] = 'Category code is missing';
$L['market_clone'] = 'Clone item';
$L['market_confirm_delete'] = 'Are you sure you want to delete this item?';
$L['market_confirm_validate'] = 'Do you want to validate this item?';
$L['market_confirm_unvalidate'] = 'Are you sure you want to send this item to the validation queue?';

$L['market_deleted'] = 'Item deleted';
$L['market_deletedToTrash'] = 'Item moved to trash';
$L['market_drafts'] = 'Drafts';
$L['market_drafts_desc'] = 'Items saved as drafts';
$L['market_notavailable'] = 'The item will be published in';
$L['market_textmissing'] = 'The item description must not be empty';
$L['market_titletooshort'] = 'The title is too short or missing';
$L['market_validation'] = 'Awaiting validation';
$L['market_validation_desc'] = 'Your items that have not yet been validated by an administrator';
$L['market_savedasdraft'] = 'The item was saved as a draft';
$L['market_formhint'] = 'After filling out the form, the item will be placed in the validation queue and will be hidden until an administrator validates it.';

// ========================
// ITEM MANAGEMENT (ACTIONS)
// ========================
$L['market_pageid'] = 'Item ID';
$L['market_deletepage'] = 'Delete item';

$L['market_preview'] = 'Preview';
$L['market_preview_notice'] = 'This is a preview. Changes are saved as a draft.';
$L['market_publish'] = 'Publish';
$L['market_edit'] = 'Edit';

// ========================
// ITEM STATUSES
// ========================
$L['market_status_draft'] = 'Draft';
$L['market_status_pending'] = 'Pending review';
$L['market_status_approved'] = 'Approved';
$L['market_status_published'] = 'Published';
$L['market_status_expired'] = 'Expired';

// ========================
// LISTS AND GENERAL
// ========================
$L['market_linesperpage'] = 'Records per page';
$L['market_linesinthissection'] = 'Records in section';
$L['market_date_published'] = 'Date published';
$L['market_latest_update'] = 'Updated';
$L['market_all_items'] = 'All items';
$L['market_all_items_desc'] = 'All available store items';
$L['market_contentAuthor'] = 'Item posted by';
$L['market_seller'] = 'Item seller';
$L['market_catalog'] = 'Catalog';
$L['market_price'] = 'Price';
$L['market_price_international'] = 'Price in international currency (for conversion)';
$L['market_price_base']          = 'Price/cost in base currency';
$L['market_price_converted_label'] = $L['market_price_base'];
$L['market_go_to_catalog'] = 'Go to items';
$L['market_no_products'] = 'No items';
$L['market_catEmpty'] = 'There are no items in this category yet';

// ========================
// DECLENSIONS (PLURAL FORMS)
// ========================
$Ls['pages'] = "item,items,items";
$Ls['unvalidated_market'] = "unvalidated item,unvalidated items,unvalidated items";
$Ls['market_in_drafts'] = "item in drafts,items in drafts,items in drafts";

// ========================
// USER ACCOUNT AND CATALOG
// ========================
$L['market_myproducts'] = 'My items';

// ========================
// PUBLIC PROFILE AND USER ITEMS
// --- Strings from market.userdetails.php ---
// ========================
$L['market_users_products'] = 'User items';
$L['market_load_more'] = 'Load more (page %d of %d)';
$L['market_loading'] = '<i class="fa fa-spinner fa-spin"></i> Loading...';
$L['market_load_error'] = 'Loading error. Please try again.';

// ========================
// FILES AND UPLOADS
// ========================
$L['File'] = 'File';
$L['extf_onserver'] = 'on server';
$L['extf_replacefile'] = 'Replace file';
$L['extf_choosefile'] = 'Choose file';
$L['extf_deletefile'] = 'Delete current file';
$L['extf_deletehint'] = 'check the box and apply changes';

// ========================
// SEARCH RESULTS
// ========================
$Ls['market_declen_items_sq_found'] = "item,items,items";
$L['market_search_found'] = 'Found total <span class="badge rounded-pill bg-primary bg-opacity-10 text-dark"> %1$s </span>, on this page: %2$s for query: <span class="badge rounded-pill bg-success"> %3$s </span>';
$L['market_search_none'] = 'Nothing found for query %1$s';
$L['market_search_in_title']              = 'Search only in titles';
$L['market_search_in_title_and_descr']    = 'Search in titles and description';
$L['market_search_in_pcod']               = 'Search by item code';


// ========================
// MARKET.MAIN | ITEM CARD
// ========================
$L['market_get_by_id_as_recommend'] = 'People often order this together with this item';

// ========================
// MISC INTERFACE ELEMENTS
// ========================
$L['market_read_more'] = 'Read more';
$L['market_collapse']  = 'Collapse';

// ========================
// VENDOR
// ========================
$L['market_vendors_title'] = 'Sellers';
$L['market_vendors_total'] = 'Total sellers';
$L['market_vendors_sort_title'] = 'Sort by';
$L['market_vendors_sort_last_item'] = 'By latest item';
$L['market_vendors_sort_items_count'] = 'By items count';
$L['market_vendors_sort_username'] = 'By name';
$L['market_vendors_sort_regdate'] = 'By registration date';
$L['market_vendors_sort_last_seen'] = 'By last activity';
$L['market_vendors_items'] = 'items';
$L['market_vendors_categories'] = 'categories';
$L['market_vendors_registered'] = 'Registered';
$L['market_vendors_goto_showcase'] = 'Showcase';
$L['market_vendors_profile'] = 'Profile';
$L['market_vendors_empty'] = 'No sellers found yet';
$L['market_vendor_page_title'] = 'Seller showcase %s';
$L['market_vendor_products'] = 'Items';
$L['market_vendor_categories'] = 'Categories';
$L['market_vendor_registered'] = 'Registration date';
$L['market_vendor_categories_of'] = 'Seller categories';
$L['market_vendor_no_categories'] = 'The seller has no categories yet';
$L['market_vendor_empty'] = 'The seller has no items yet';
$L['market_vendor_profile_link'] = 'profile';
$L['market_owner_vendor_link'] = 'Seller showcase';
$L['market_vendors_search_username'] = 'Enter seller username';

// Custom localization file for Cotonti using via function cot_langfile_custom() in system/functions.custom.php
// include File from Path: modules/market/lang/market.custom.en.lang.php
// How it works:  https://github.com/webitproff/functions.custom.php-cotonti
// How it works:  https://abuyfile.com/ru/cotonti/reading/rukovodstvo-po-polzovatelskim-funkciyam-cotonti
if (function_exists('cot_langfile_custom')) {
    cot_langfile_custom('market', 'module');
}