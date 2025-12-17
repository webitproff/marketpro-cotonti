<?php
/**
 * Russian Language File for the Market Module (market.ru.lang.php)
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

$L['cfg_marketmarkup'] = 'Разметка в описании товара';
$L['cfg_marketmarkup_hint'] = 'Включить использование HTML или BBCode в описании товара';

$L['cfg_marketparser'] = 'Парсер описания';
$L['cfg_marketparser_hint'] = 'Выберите парсер для обработки описания товара (например, BBCode, HTML и т.д.)';

$L['cfg_marketcount_admin'] = 'Считать посещения администраторов';
$L['cfg_marketcount_admin_hint'] = 'Включить посещения администраторов в статистику посещаемости сайта';

$L['cfg_marketautovalidate'] = 'Автоматическое утверждение товаров';
$L['cfg_marketautovalidate_hint'] = 'Автоматически утверждать публикацию товаров, созданных пользователем с правом администрирования раздела';

$L['cfg_marketmaxlistsperpage'] = 'Макс. количество категорий на странице';

$L['cfg_markettitle_page'] = 'Формат заголовка товара';
$L['cfg_markettitle_page_hint'] = 'Опции: {TITLE}, {CATEGORY}';

$L['cfg_marketblacktreecatspage'] = 'Черный список категорий';
$L['cfg_marketblacktreecatspage_hint'] = 'Коды категорий, исключенные из дерева категорий на страницах (например: system, unvalidated)';
$L['cfg_market_currency'] = 'Валюта по умолчанию';
$L['cfg_market_currency_hint'] = 'Ни на что не влияет, чисто для информации';

// === STRUCTURE ===
$L['cfg_marketorder'] = 'Поле сортировки';
$L['cfg_marketorder_params'] = [];

$L['cfg_marketway'] = 'Направление сортировки';
$L['cfg_marketway_params'] = [$L['Ascending'], $L['Descending']];

$L['cfg_maxrowsperpage'] = 'Макс. элементов на странице списка';

$L['cfg_markettruncatetext'] = 'Ограничить размер текста в списках товаров';
$L['cfg_markettruncatetext_hint'] = '0 для отключения';

$L['cfg_marketallowemptytext'] = 'Разрешить пустое описание товара';

$L['cfg_marketkeywords'] = 'Ключевые слова';
$L['cfg_marketmetatitle'] = 'Meta-заголовок';
$L['cfg_marketmetadesc'] = 'Meta-описание';

$L['cfg_marketmaxlistsperpage'] = 'Макс. количество категорий на странице'; // duplicated. It necessary for categories

$L['info_desc'] = 'Управление контентом: товары и категории товаров';



$L['market_Market'] = 'Market PRO';
$L['market'] = 'Market PRO'; // обратная
$L['maintitle_in_list_c_empty'] = 'Товары и поставщики';
$L['market_categories'] = 'Категории Market PRO';
/**
 * переопределяем сетап конфигурации того, что у нас в админке модуля
 * Управление сайтом / Конфигурация / 
*/

$useCfgMarketFromLang = true; // использовать значения конфигурации из файла локализации // Use configuration values from the localization file

if ($useCfgMarketFromLang === true) {
    $cfg['market']['marketlist_default_title'] = 'Витрина Market PRO';
    $cfg['market']['marketlist_default_desc'] = '<span class="badge text-bg-primary">CMS</span>, <span class="badge text-bg-success">Скрипт</span> и <span class="badge text-bg-info">Движок</span> - веб сайта онлайн-витрины, интернет магазина инфопродуктов и цифровых товаров. Разные цены в разных валютах на товар. Онлайн-оплата в криптовалюте за товары и услуги.';
}


$L['adm_lang_market_valqueue'] = 'В очереди на утверждение';
$L['adm_lang_market_validated'] = 'Утвержденные';
$L['adm_lang_market_expired'] = 'С истекшим сроком';
$L['adm_lang_market_structure'] = 'Структура товаров (категории)';
$L['adm_lang_market_sort'] = 'Сортировать';
$L['adm_lang_market_sortingorder'] = 'Порядок сортировки по умолчанию в категории';
$L['adm_lang_market_showall'] = 'Показать все';
$L['adm_lang_market_help_market'] = 'Строка справки и помощи из файла локализации и глобального для админки in admin.main.php <code>\'ADMIN_HELP\' => $adminHelp </code> Товары категории «system» не отображаются в списках и являются самостоятельными записями';
$L['adm_lang_market_fileyesno'] = 'Файл (да/нет)';
$L['adm_lang_market_fileurl'] = 'URL файла';
$L['adm_lang_market_filecount'] = 'Количество загрузок';
$L['adm_lang_market_filesize'] = 'Размер файла';


$L['market_contentAuthor'] = 'Товар разместил';
$L['market_seller'] = 'Продавец товара';
$L['market_addtitle'] = 'Добавить товар';
$L['market_addsubtitle'] = 'Заполните необходимые поля и отправьте форму для продолжения';
$L['market_edittitle'] = 'Свойства товара';
$L['market_editsubtitle'] = 'Измените необходимые поля и нажмите "Отправить" для продолжения';
$L['market_addedit_seo'] = 'SEO-оптимизация. Заполняется по желанию';
$L['market_addedit_desc'] = 'Краткое описание для просмотра в списках товаров. Заполняется по желанию';
$L['market_addedit_text'] = 'Описание товара';
$L['market_addedit_text_hint'] = 'Без ссылок, спама и муссора';
$L['market_all_items'] = 'Все товары';
$L['market_all_items_desc'] = 'Все доступные товары магазина';


$L['market_aliascharacters'] = 'Недопустимо использование символов "+", "/", "?", "%", "#", "&" в алиасах';
$L['market_catmissing'] = 'Код категории отсутствует';
$L['market_clone'] = 'Клонировать товар';
$L['market_confirm_delete'] = 'Вы действительно хотите удалить этот товар?';
$L['market_confirm_validate'] = 'Хотите утвердить этот товар?';
$L['market_confirm_unvalidate'] = 'Вы действительно хотите отправить этот товар в очередь на утверждение?';
$L['market_date_now'] = 'Актуализировать дату товара';
$L['market_deleted'] = 'Товар удален';
$L['market_deletedToTrash'] = 'Товар удален в корзину';
$L['market_drafts'] = 'Черновики';
$L['market_drafts_desc'] = 'Товары, сохраненные в черновиках';
$L['market_notavailable'] = 'Товар будет опубликован через';
$L['market_textmissing'] = 'Описание товара не должно быть пустым';
$L['market_titletooshort'] = 'Название слишком короткое либо отсутствует';
$L['market_validation'] = 'Ожидают утверждения';
$L['market_validation_desc'] = 'Ваши товары, которые еще не утверждены администратором';

$L['market_metatitle'] = 'Meta-заголовок';
$L['market_metadesc'] = 'Meta-описание';

$L['market_formhint'] = 'После заполнения формы товар будет помещён в очередь на утверждение и будет скрыт до утверждения администратором.';

$L['market_pageid'] = 'ID товара';
$L['market_deletepage'] = 'Удалить товар';

$L['market_savedasdraft'] = 'Товар сохранён в черновиках';

$L['market_status_draft'] = 'Черновик';
$L['market_status_pending'] = 'На рассмотрении';
$L['market_status_approved'] = 'Утверждён';
$L['market_status_published'] = 'Опубликован';
$L['market_status_expired'] = 'Устарел';
$L['market_linesperpage'] = 'Записей на страницу';
$L['market_linesinthissection'] = 'Записей в разделе';

$L['market_date_published'] = 'Дата размещения:';
$L['market_latest_update'] = 'Обновлено:';


$Ls['pages'] = "товар,товара,товаров";
$Ls['unvalidated_market'] = "неутверждённый товар,неутверждённые товары,неутверждённых товаров";
$Ls['market_in_drafts'] = "товар в черновиках,товары в черновиках,товаров в черновиках";

$L['market_myproducts'] = 'Мои товары';

$L['market_catalog'] = 'Каталог';
$L['market_go_to_catalog'] = 'Перейти к товарам';
$L['market_edit_product'] = 'Редактировать товар';
$L['market_add_product_title'] = 'Добавление товара в магазин';
$L['market_edit_product_title'] = 'Редактирование товара из магазина';


$L['market_catEmpty'] = 'В категории пока нет товаров';


// market.userdetails.php
$L['market_add_product'] = 'Добавить товар';
$L['market_user_products'] = 'Товары пользователя';
$L['market_no_products'] = 'Нет товаров';
$L['market_price'] = 'Цена по умолчанию';

