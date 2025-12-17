<?php
/**
 * Ukrainian Language File for the Market Module (market.uk.lang.php)
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

$L['cfg_marketmarkup'] = 'Розмітка в описі товару';
$L['cfg_marketmarkup_hint'] = 'Увімкнути використання HTML або BBCode в описі товару';

$L['cfg_marketparser'] = 'Парсер опису';
$L['cfg_marketparser_hint'] = 'Виберіть парсер для обробки опису товару (наприклад, BBCode, HTML тощо)';

$L['cfg_marketcount_admin'] = 'Рахувати відвідування адміністраторів';
$L['cfg_marketcount_admin_hint'] = 'Включити відвідування адміністраторів до статистики відвідуваності сайту';

$L['cfg_marketautovalidate'] = 'Автоматичне затвердження товарів';
$L['cfg_marketautovalidate_hint'] = 'Автоматично затверджувати товари, створені користувачем із правами адміністрування розділу';

$L['cfg_marketmaxlistsperpage'] = 'Макс. кількість категорій на сторінці';

$L['cfg_markettitle_page'] = 'Формат заголовка товару';
$L['cfg_markettitle_page_hint'] = 'Опції: {TITLE}, {CATEGORY}';

$L['cfg_marketblacktreecatspage'] = 'Чорний список категорій';
$L['cfg_marketblacktreecatspage_hint'] = 'Коди категорій, виключені з дерева категорій на сторінках (наприклад: system, unvalidated)';
$L['cfg_market_currency'] = 'Валюта за замовчуванням';
$L['cfg_market_currency_hint'] = 'Лише для інформаційних цілей';

// === STRUCTURE ==
$L['cfg_marketorder'] = 'Поле сортування';
$L['cfg_marketorder_params'] = [];

$L['cfg_marketway'] = 'Напрямок сортування';
$L['cfg_marketway_params'] = [$L['Ascending'], $L['Descending']];

$L['cfg_maxrowsperpage'] = 'Макс. елементів на сторінці списку';

$L['cfg_markettruncatetext'] = 'Обмежити розмір тексту в списках товарів';
$L['cfg_markettruncatetext_hint'] = '0 для відключення';

$L['cfg_marketallowemptytext'] = 'Дозволити порожній опис товару';

$L['cfg_marketkeywords'] = 'Ключові слова';
$L['cfg_marketmetatitle'] = 'Мета-заголовок';
$L['cfg_marketmetadesc'] = 'Мета-опис';

$L['cfg_marketmaxlistsperpage'] = 'Макс. кількість категорій на сторінці'; // duplicated for categories

$L['info_desc'] = 'Керування контентом: товари та категорії товарів';

$L['market_Market'] = 'Market PRO';
$L['maintitle_in_list_c_empty'] = 'Товари та постачальники';

/**
 * Налаштування конфігурації для адмінпанелі модуля
 * Керування сайтом / Конфігурація /
 */

$useCfgMarketFromLang = true; // Використовувати значення конфігурації з файлу локалізації

if ($useCfgMarketFromLang === true) {
    $cfg['market']['marketlist_default_title'] = 'Вітрина Market PRO';
    $cfg['market']['marketlist_default_desc'] =
        '<span class="badge text-bg-primary">CMS</span>, <span class="badge text-bg-success">Скрипт</span> і <span class="badge text-bg-info">Движок</span> — веб-платформа онлайн-вітрини, інтернет-магазину інфопродуктів та цифрових товарів. Різні ціни в різних валютах на товар. Онлайн-оплата криптовалютою за товари та послуги.';
}


$L['adm_lang_market_valqueue'] = 'В черзі на затвердження';
$L['adm_lang_market_validated'] = 'Затверджені';
$L['adm_lang_market_expired'] = 'З вичерпаним терміном';
$L['adm_lang_market_structure'] = 'Структура товарів (категорії)';
$L['adm_lang_market_sort'] = 'Сортувати';
$L['adm_lang_market_sortingorder'] = 'Порядок сортування за замовчуванням у категорії';
$L['adm_lang_market_showall'] = 'Показати всі';
$L['adm_lang_market_help_market'] = 'Товари категорії «system» не відображаються у списках і є самостійними записами';
$L['adm_lang_market_fileyesno'] = 'Файл (так/ні)';
$L['adm_lang_market_fileurl'] = 'URL файлу';
$L['adm_lang_market_filecount'] = 'Кількість завантажень';
$L['adm_lang_market_filesize'] = 'Розмір файлу';

$L['market_contentAuthor'] = 'Товар розмістив';
$L['market_seller'] = 'Продавець товару';
$L['market_addtitle'] = 'Додати товар';
$L['market_addsubtitle'] = 'Заповніть необхідні поля та надішліть форму для продовження';
$L['market_edittitle'] = 'Властивості товару';
$L['market_editsubtitle'] = 'Змініть необхідні поля та натисніть "Надіслати" для продовження';
$L['market_addedit_seo'] = 'SEO-оптимізація. Заповнюється за бажанням';
$L['market_addedit_desc'] = 'Короткий опис для перегляду в списках товарів. Заповнюється за бажанням';
$L['market_addedit_text'] = 'Опис товару';
$L['market_addedit_text_hint'] = 'Без посилань, спаму та сміття';
$L['market_all_items'] = 'Всі товари';
$L['market_all_items_desc'] = 'Всі доступні товари магазину';

$L['market_aliascharacters'] = 'Недопустиме використання символів "+", "/", "?", "%", "#", "&" в аліасах';
$L['market_catmissing'] = 'Код категорії відсутній';
$L['market_clone'] = 'Клонувати товар';
$L['market_confirm_delete'] = 'Ви дійсно хочете видалити цей товар?';
$L['market_confirm_validate'] = 'Бажаєте затвердити цей товар?';
$L['market_confirm_unvalidate'] = 'Ви дійсно хочете відправити цей товар у чергу на затвердження?';
$L['market_date_now'] = 'Актуалізувати дату товару';
$L['market_deleted'] = 'Товар видалено';
$L['market_deletedToTrash'] = 'Товар переміщено до кошика';
$L['market_drafts'] = 'Чернетки';
$L['market_drafts_desc'] = 'Товари, збережені в чернетках';
$L['market_notavailable'] = 'Товар буде опубліковано через';
$L['market_textmissing'] = 'Опис товару не повинен бути порожнім';
$L['market_titletooshort'] = 'Назва надто коротка або відсутня';
$L['market_validation'] = 'Очікують затвердження';
$L['market_validation_desc'] = 'Ваші товари, які ще не затверджені адміністратором';

$L['market_file'] = 'Прикріпити файл';
$L['market_filehint'] = '(при увімкненні завантажень заповніть поля нижче)';
$L['market_urlhint'] = '(якщо прикріплено файл)';
$L['market_filesize'] = 'Розмір файлу, Кб';
$L['market_filesizehint'] = '(якщо прикріплено файл)';
$L['market_filehitcount'] = 'Завантажень';
$L['market_filehitcounthint'] = '(якщо прикріплено файл)';
$L['market_metakeywords'] = 'Ключові слова';
$L['market_metatitle'] = 'Мета-заголовок';
$L['market_metadesc'] = 'Мета-опис';

$L['market_formhint'] = 'Після заповнення форми товар буде поміщено в чергу на затвердження і буде прихований до затвердження адміністратором.';

$L['market_pageid'] = 'ID товару';
$L['market_deletepage'] = 'Видалити товар';

$L['market_savedasdraft'] = 'Товар збережено в чернетках';

$L['market_status_draft'] = 'Чернетка';
$L['market_status_pending'] = 'На розгляді';
$L['market_status_approved'] = 'Затверджено';
$L['market_status_published'] = 'Опубліковано';
$L['market_status_expired'] = 'Застарілий';
$L['market_linesperpage'] = 'Записів на сторінку';
$L['market_linesinthissection'] = 'Записів у розділі';

$L['market_date_published'] = 'Дата розміщення:';
$L['market_latest_update'] = 'Оновлено:';

$Ls['pages'] = "товар,товари,товарів";
$Ls['unvalidated_market'] = "незатверджений товар,незатверджені товари,незатверджених товарів";
$Ls['pages_in_drafts'] = "товар у чернетках,товари у чернетках,товарів у чернетках";

// market.userdetails.php
$L['market_add_product'] = 'Додати товар';
$L['market_user_products'] = 'Товари користувача';
$L['market_no_products'] = 'Немає товарів';
$L['market_price'] = 'Ціна за замовчуванням';