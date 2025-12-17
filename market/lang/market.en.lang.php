<?php
/**
 * English Language File for the Market Module (market.en.lang.php)
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');
global $cfg;
if (!isset($L['PFS'])) {
    $mainLangFile = cot_langfile('main', 'core');
    if (file_exists($mainLangFile)) {
        include $mainLangFile;
    }
}

$L['cfg_marketmarkup'] = 'Markup in product description';
$L['cfg_marketmarkup_hint'] = 'Enable the use of HTML or BBCode in product description';

$L['cfg_marketparser'] = 'Description parser';
$L['cfg_marketparser_hint'] = 'Select parser for processing product description (e.g., BBCode, HTML, etc.)';

$L['cfg_marketcount_admin'] = 'Count admin visits';
$L['cfg_marketcount_admin_hint'] = 'Include admin visits in site traffic statistics';

$L['cfg_marketautovalidate'] = 'Automatic product approval';
$L['cfg_marketautovalidate_hint'] = 'Automatically approve products created by users with section administration rights';

$L['cfg_marketmaxlistsperpage'] = 'Max number of categories per page';

$L['cfg_markettitle_page'] = 'Product title format';
$L['cfg_markettitle_page_hint'] = 'Options: {TITLE}, {CATEGORY}';

$L['cfg_marketblacktreecatspage'] = 'Category blacklist';
$L['cfg_marketblacktreecatspage_hint'] = 'Category codes excluded from the category tree on pages (e.g., system, unvalidated)';
$L['cfg_market_currency'] = 'Default currency';
$L['cfg_market_currency_hint'] = 'For informational purposes only';

// === STRUCTURE ===
$L['cfg_marketorder'] = 'Sort field';
$L['cfg_marketorder_params'] = [];

$L['cfg_marketway'] = 'Sort direction';
$L['cfg_marketway_params'] = [$L['Ascending'], $L['Descending']];

$L['cfg_maxrowsperpage'] = 'Max items per list page';

$L['cfg_markettruncatetext'] = 'Limit text length in product lists';
$L['cfg_markettruncatetext_hint'] = '0 to disable';

$L['cfg_marketallowemptytext'] = 'Allow empty product description';

$L['cfg_marketkeywords'] = 'Keywords';
$L['cfg_marketmetatitle'] = 'Meta title';
$L['cfg_marketmetadesc'] = 'Meta description';

$L['cfg_marketmaxlistsperpage'] = 'Max number of categories per page'; // duplicated for categories

$L['info_desc'] = 'Content management: products and product categories';

$L['market_Market'] = 'Market PRO';
$L['maintitle_in_list_c_empty'] = 'Products and suppliers';

/**
 * Override configuration setup for module admin panel
 * Site Management / Configuration /
 */

$useCfgMarketFromLang = true; // Use configuration values from the localization file

if ($useCfgMarketFromLang === true) {
    $cfg['market']['marketlist_default_title'] = 'Market PRO Showcase';
    $cfg['market']['marketlist_default_desc'] =
        '<span class="badge text-bg-primary">CMS</span>, <span class="badge text-bg-success">Script</span> and <span class="badge text-bg-info">Engine</span> - a web platform for an online showcase, an info-product store and digital goods marketplace. Different prices in different currencies for a product. Online crypto payments for goods and services.';
}


$L['adm_lang_market_valqueue'] = 'Pending approval';
$L['adm_lang_market_validated'] = 'Approved';
$L['adm_lang_market_expired'] = 'Expired';
$L['adm_lang_market_structure'] = 'Product structure (categories)';
$L['adm_lang_market_sort'] = 'Sort';
$L['adm_lang_market_sortingorder'] = 'Default sorting order in category';
$L['adm_lang_market_showall'] = 'Show all';
$L['adm_lang_market_help_market'] = 'Products in the "system" category are not displayed in lists and are standalone entries';
$L['adm_lang_market_fileyesno'] = 'File (yes/no)';
$L['adm_lang_market_fileurl'] = 'File URL';
$L['adm_lang_market_filecount'] = 'Download count';
$L['adm_lang_market_filesize'] = 'File size';

$L['market_contentAuthor'] = 'Product posted by';
$L['market_seller'] = 'Product seller';
$L['market_addtitle'] = 'Add product';
$L['market_addsubtitle'] = 'Fill in the required fields and submit the form to continue';
$L['market_edittitle'] = 'Product properties';
$L['market_editsubtitle'] = 'Edit the necessary fields and click "Submit" to continue';
$L['market_addedit_seo'] = 'SEO optimization. Optional';
$L['market_addedit_desc'] = 'Short description for product lists. Optional';
$L['market_addedit_text'] = 'Product description';
$L['market_addedit_text_hint'] = 'No links, spam, or clutter';
$L['market_all_items'] = 'All products';
$L['market_all_items_desc'] = 'All available store products';

$L['market_aliascharacters'] = 'The use of "+", "/", "?", "%", "#", "&" characters in aliases is not allowed';
$L['market_catmissing'] = 'Category code is missing';
$L['market_clone'] = 'Clone product';
$L['market_confirm_delete'] = 'Do you really want to delete this product?';
$L['market_confirm_validate'] = 'Do you want to approve this product?';
$L['market_confirm_unvalidate'] = 'Do you really want to send this product to the approval queue?';
$L['market_date_now'] = 'Update product date';
$L['market_deleted'] = 'Product deleted';
$L['market_deletedToTrash'] = 'Product moved to trash';
$L['market_drafts'] = 'Drafts';
$L['market_drafts_desc'] = 'Products saved as drafts';
$L['market_notavailable'] = 'Product will be published in';
$L['market_textmissing'] = 'Product description must not be empty';
$L['market_titletooshort'] = 'Title is too short or missing';
$L['market_validation'] = 'Pending approval';
$L['market_validation_desc'] = 'Your products awaiting administrator approval';

$L['market_file'] = 'Attach file';
$L['market_filehint'] = '(if file uploads are enabled, fill in the fields below)';
$L['market_urlhint'] = '(if a file is attached)';
$L['market_filesize'] = 'File size, KB';
$L['market_filesizehint'] = '(if a file is attached)';
$L['market_filehitcount'] = 'Downloads';
$L['market_filehitcounthint'] = '(if a file is attached)';
$L['market_metakeywords'] = 'Keywords';
$L['market_metatitle'] = 'Meta title';
$L['market_metadesc'] = 'Meta description';

$L['market_formhint'] = 'After filling out the form, the product will be placed in the approval queue and hidden until approved by an administrator.';

$L['market_pageid'] = 'Product ID';
$L['market_deletepage'] = 'Delete product';

$L['market_savedasdraft'] = 'Product saved as draft';

$L['market_status_draft'] = 'Draft';
$L['market_status_pending'] = 'Pending';
$L['market_status_approved'] = 'Approved';
$L['market_status_published'] = 'Published';
$L['market_status_expired'] = 'Expired';
$L['market_linesperpage'] = 'Items per page';
$L['market_linesinthissection'] = 'Items in this section';

$L['market_date_published'] = 'Publication date:';
$L['market_latest_update'] = 'Updated:';

$Ls['pages'] = "product,products";
$Ls['unvalidated_market'] = "unapproved product,unapproved products";
$Ls['market_in_drafts'] = "product in drafts,products in drafts";

// market.userdetails.php
$L['market_add_product'] = 'Add product';
$L['market_user_products'] = 'User products';
$L['market_no_products'] = 'No products';
$L['market_price'] = 'Default price';