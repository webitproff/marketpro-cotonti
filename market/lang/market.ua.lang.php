<?php
/**
 * Ukrainian Language File for the Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Всі текстові рядки, що використовуються модулем Market PRO в інтерфейсі Cotonti:
 * - назва та опис модуля (info_name, info_desc, info_notes)
 * - налаштування в адмін-панелі (cfg_…)
 * - підказки до полів (cfg_…_hint)
 * - рядки інтерфейсу користувача та адміністратора
 * - відмінювання для множинних чисел ($Ls)
 *
 * Filename: modules/market/lang/market.ua.lang.php
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 09, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');


// ========================
// ІНФОРМАЦІЯ ПРО МОДУЛЬ (АДМІНКА)
// ========================
$L['info_name']  = 'Market PRO';
$L['info_desc']  = 'Модуль інтернет-магазину, який може працювати з одним продавцем або як торгова площадка з вітринами продавців.';
$L['info_notes'] = 'Детальніше: <a href="https://abuyfile.com/ru/market/cotonti/plugs/marketpro" target="_blank">документація та посилання на неї</a>.';

$L['market_title']       = $L['info_name']; // backwards compatibility for old extentions
$L['market_desc']        = $L['info_desc']; // backwards compatibility for old extentions

$L['Market']       = $L['info_name']; // backwards compatibility for old extentions
$L['market']       = $L['info_name']; // backwards compatibility for old extentions


/* 
 * ======================================================
 * ► ПОЧАТОК ◄
 * ------------------------------------------------------
 * НАЛАШТУВАННЯ МОДУЛЯ (КОНФІГУРАЦІЯ)
 * ======================================================
*/
// =========================================
// BEGIN COT EXT CONFIG
// =========================================
$L['cfg_marketlist_default_title'] = 'Заголовок магазину за замовчуванням';
$L['cfg_marketlist_default_title_hint'] = 'Заголовок магазину, який відображається, коли не обрано категорію або товар';

$L['cfg_marketlist_default_desc'] = 'Опис магазину за замовчуванням';
$L['cfg_marketlist_default_desc_hint'] = 'Опис магазину, який відображається, коли не обрано категорію або товар';

// --- Валюта та курси ---
$L['cfg_market_currency'] = '<strong>Базова валюта</strong> - грошова одиниця вартості за замовчуванням';
$L['cfg_market_currency_hint'] = 'Зазвичай це національна валюта вашої держави. Назву такої місцевої валюти можна писати повністю або скорочено. Впливає лише на публічну видимість вартості товару в зазначеній одиниці вартості. Значення такої вартості (ціле число) вносяться в поле <code>fieldmrkt_costdflt</code> таблиці модуля.';

$L['cfg_market_for_rate_base_currency_cost_usd'] = 'Тверда валюта для розрахунку курсу (наприклад, USD)';
$L['cfg_market_for_rate_base_currency_cost_usd_hint'] = 'Міжнародна валюта, за якою будемо розраховувати вартість товару в базовій валюті. Припустимо, ви продаєте товар у місцевій валюті, а постачальник постачає вам товар лише за оплати в доларах США';

$L['cfg_market_rate_value_fieldmrkt_costdflt_to_cost_usd'] = 'Курс базової валюти до твердої валюти';
$L['cfg_market_rate_value_fieldmrkt_costdflt_to_cost_usd_hint'] = 'Значення курсу вашої місцевої валюти до міжнародної. Наприклад, місцева валюта — біткоїн (BTC), і ми рахуємо, скільки місцевої валюти потрібно, щоб купити 1 долар США. Зараз це приблизно 0,000012, але як розділювач використовуємо не кому, а крапку (0.000012). <br><a href="https://www.xe.com/currencyconverter/convert/?Amount=1&From=USD&To=EUR" target="_blank"><strong>Актуальні курси світових валют</strong></a>. Вставляємо значення своєї валюти';

// --- Код валюти Schema.org ---
$L['cfg_market_currency_schema_org']        = 'Код валюти Schema.org';
$L['cfg_market_currency_schema_org_hint']   = 'Код валюти за стандартом ISO 4217 для мікророзмітки Schema.org.';
$L['cfg_market_currency_schema_org_params'] = [
    'USD' => 'USD — Долар США',
    'EUR' => 'EUR — Євро',
    'RUB' => 'RUB — Російський рубль',
    'UAH' => 'UAH — Українська гривня',
    'KZT' => 'KZT — Казахстанський тенге',
    'BYN' => 'BYN — Білоруський рубль',
    'UZS' => 'UZS — Узбецький сум',
    'KGS' => 'KGS — Киргизький сом',
    'TJS' => 'TJS — Таджицький сомоні',
    'TMT' => 'TMT — Туркменський манат',
    'AZN' => 'AZN — Азербайджанський манат',
    'AMD' => 'AMD — Вірменський драм',
    'MDL' => 'MDL — Молдовський лей',
    'JPY' => 'JPY — Японська єна',
    'CNY' => 'CNY — Китайський юань',
    'BTC' => 'BTC — Біткоїн',
];

// --- Категорії та відображення ---
$L['cfg_marketmaxlistsperpage'] = 'Макс. кількість категорій на сторінці';
$L['cfg_marketmaxlistsperpage_hint'] = 'Кількість категорій, що відображаються на одній сторінці списку';

$L['cfg_marketmaxlistsperpageadmin'] = 'Макс. елементів на сторінці в адмін-панелі';
$L['cfg_marketmaxlistsperpageadmin_hint'] = 'Кількість записів на сторінці списку товарів в адміністративній панелі';

$L['cfg_marketblacktreecatspage'] = 'Чорний список категорій';
$L['cfg_marketblacktreecatspage_hint'] = 'Коди категорій, виключених з дерева категорій на сторінках (наприклад: system, unvalidated)';

// --- Сортування та структура ---
$L['cfg_market_main_order'] = 'Основне сортування (головна)';
$L['cfg_market_main_order_hint'] = 'Поле та напрямок сортування товарів на головній сторінці магазину';

// --- Основні налаштування ---
$L['cfg_marketmarkup'] = 'Увімкнути розмітку в описі';
$L['cfg_marketmarkup_hint'] = 'Використовувати чи ні візуальний редактор тексту в описі товару. Наприклад: HTML або BBCode';

$L['cfg_marketparser'] = 'Парсер опису';
$L['cfg_marketparser_hint'] = 'Виберіть парсер для обробки опису товару (наприклад, BBCode, HTML тощо). Якщо доступний HTML — саме його й ставимо.';

$L['cfg_marketcount_admin'] = 'Рахувати відвідування адміністраторів';
$L['cfg_marketcount_admin_hint'] = 'Включити відвідування адміністраторів у статистику відвідуваності сайту';

$L['cfg_marketautovalidate'] = 'Автоматичне затвердження товарів';
$L['cfg_marketautovalidate_hint'] = 'Автоматично затверджувати публікацію товарів, створених користувачем з правом адміністрування розділу';
// =========================================
// END COT EXT CONFIG
// =========================================

// =========================================
// BEGIN COT EXT CONFIG STRUCTURE
// =========================================
$L['cfg_marketorder'] = 'Поле сортування';
$L['cfg_marketorder_hint'] = 'Поле, за яким сортуються товари в категорії';
$L['cfg_marketorder_params'] = [];

$L['cfg_marketway'] = 'Напрямок сортування';
$L['cfg_marketway_hint'] = 'Напрямок сортування: за зростанням або за спаданням';
$L['cfg_marketway_params'] = [$L['Ascending'], $L['Descending']];

$L['cfg_maxrowsperpage'] = 'Макс. елементів на сторінці списку';
$L['cfg_maxrowsperpage_hint'] = 'Кількість товарів, що відображаються на одній сторінці списку';

$L['cfg_markettruncatetext'] = 'Обмежити розмір тексту у списках товарів';
$L['cfg_markettruncatetext_hint'] = '0 для вимкнення';

$L['cfg_marketallowemptytext'] = 'Дозволити порожній опис товару';
$L['cfg_marketallowemptytext_hint'] = 'Дозволити публікацію товарів без опису';

// --- SEO за замовчуванням ---
$L['cfg_marketmetatitle'] = 'Meta-заголовок';
$L['cfg_marketmetatitle_hint'] = 'Meta-тег title за замовчуванням для сторінок магазину';

$L['cfg_marketmetadesc'] = 'Meta-опис';
$L['cfg_marketmetadesc_hint'] = 'Meta-тег description за замовчуванням для сторінок магазину';

// --- Налаштування в категоріях (STRUCTURE) ---
$L['cfg_marketmaxlistsperpageincat'] = 'Макс. кількість підкатегорій на сторінці категорії';
$L['cfg_marketmaxlistsperpageincat_hint'] = 'Кількість підкатегорій, що відображаються на сторінці всередині категорії';

$L['cfg_marketkeywords'] = 'Ключові слова';
$L['cfg_marketkeywords_hint'] = 'Meta-тег keywords за замовчуванням для сторінок магазину';

// --- Дублікат налаштування, необхідний для категорій ---
$L['cfg_marketmaxlistsperpage'] = 'Макс. кількість категорій на сторінці'; // duplicated in STRUCTURE section market.setup.php. It necessary for categories
// =========================================
// END COT EXT CONFIG STRUCTURE
// =========================================

/* 
 * ======================================================
 * НАЛАШТУВАННЯ МОДУЛЯ (КОНФІГУРАЦІЯ)
 * ------------------------------------------------------
 * ► КІНЕЦЬ ◄
 * ======================================================
*/

// ========================
// ОСНОВНІ РЯДКИ ІНТЕРФЕЙСУ
// ========================
$L['market_title_general'] = 'Маркет';
$L['market_title_in_links'] = 'Вітрини ринку';
$L['market_seller_vendors_title'] = 'Вітрини та Продавці';
$L['market_categories'] = 'Категорії ринку';


// ========================
// АДМІН-ПАНЕЛЬ MARKET: ЛОКАЛІЗАЦІЯ ШАБЛОНА market.admin.tpl
// ========================
$L['adm_market_configuration'] = 'Конфігурація';
$L['adm_market_categories'] = 'Категорії';
$L['adm_market_extrafields'] = 'Додаткові поля';
$L['adm_market_search'] = 'Пошук';
$L['adm_market_category'] = 'Категорія';
$L['adm_market_select_filter_options'] = 'Опції фільтра';
$L['adm_market_select_status_publication'] = 'Вибрати статус публікації';
$L['adm_market_sort'] = 'Сортувати';
$L['adm_market_select_filter_sorting_direction'] = 'Напрямок сортування';
$L['adm_market_filter'] = 'Фільтр';
$L['adm_market_prune'] = 'Скинути';
$L['adm_market_id'] = 'ID';
$L['adm_market_status'] = 'Статус';
$L['adm_market_title'] = 'Заголовок';
$L['adm_market_action'] = 'Дія';
$L['adm_market_validate'] = 'Затвердити';
$L['adm_market_delete'] = 'Видалити';
$L['adm_market_open'] = 'Відкрити';
$L['adm_market_edit'] = 'Редагувати';
$L['adm_market_none'] = 'Немає';
$L['adm_market_total'] = 'Всього';
$L['adm_market_onpage'] = 'На сторінці';
$L['adm_market_text'] = 'Текст';

// ========================
// АДМІН-ПАНЕЛЬ: СТАТУСИ, ДОВІДКА, ФАЙЛИ
// ========================
$L['adm_lang_market_valqueue'] = 'У черзі на затвердження';
$L['adm_lang_market_validated'] = 'Затверджені';
$L['adm_lang_market_expired'] = 'Із закінченим терміном';
$L['adm_lang_market_structure'] = 'Структура товарів (категорії)';
$L['adm_lang_market_sort'] = 'Сортувати';
$L['adm_lang_market_sortingorder'] = 'Порядок сортування за замовчуванням у категорії';
$L['adm_lang_market_showall'] = 'Показати всі';
$L['adm_lang_market_help_market'] = 'Рядок довідки та допомоги з файлу локалізації і глобального для адмінки in admin.main.php <code>\'ADMIN_HELP\' => $adminHelp </code> Товари категорії «system» не відображаються у списках і є самостійними записами';
$L['adm_lang_market_fileyesno'] = 'Файл (так/ні)';
$L['adm_lang_market_fileurl'] = 'URL файлу';
$L['adm_lang_market_filecount'] = 'Кількість завантажень';
$L['adm_lang_market_filesize'] = 'Розмір файлу';

// ========================
// ВІДЖЕТ СТАТИСТИКИ (головна адмінки)
// ========================
$L['market_stats_activity'] = 'Активність';
$L['market_stats_today']    = 'Сьогодні';
$L['market_stats_week']     = 'За 7 днів';
$L['market_stats_month']    = 'За 30 днів';
$L['market_stats_latest']   = 'Останні товари';
$L['market_stats_top_cats'] = 'Топ категорій';
$L['market_stats_top_viewed'] = 'Top viewed';

// ========================
// МАСОВІ ДІЇ ТА AJAX-ЗАВАНТАЖЕННЯ
// ========================
$L['market_adm_select_all'] = 'Вибрати все';
$L['market_adm_deselect_all'] = 'Зняти всі позначки';

// ========================
// ФОРМИ: ДОДАВАННЯ ТА РЕДАГУВАННЯ ТОВАРУ
// ========================
$L['market_form_add_item_title'] = 'Створення картки товару';
$L['market_form_add_item_subtitle'] = 'Заповніть як мінімум обов’язкові &#128681; поля та збережіть дані форми. Під час редагування товару ви зможете заповнити решту.';
$L['market_form_item_edit_title'] = 'Редагуємо властивості товару';
$L['market_form_item_edit_subtitle'] = 'Змініть і заповніть необхідні поля та збережіть дані форми';
$L['market_form_parser'] = 'Розмітка (парсер)';
$L['market_form_parser_hint'] = 'Візуальний текстовий редактор, якщо він налаштований. Якщо доступний для вибору <code>HTML</code> — саме його завжди й ставимо';
$L['market_form_text_full'] = 'Опис товару';
$L['market_form_text_full_hint'] = 'Без посилань, спаму та сміття';
$L['market_form_date_now'] = 'Актуалізувати дату товару';
$L['market_form_meta_title'] = 'Meta-заголовок';
$L['market_form_meta_title_hint'] = 'Заголовок у вкладку браузера. До 70 символів.';
$L['market_form_meta_desc'] = 'Meta-опис';
$L['market_form_meta_desc_hint'] = 'Пояснюємо пошуковим системам, що це за товар. До 155 символів.';
$L['market_form_category'] = 'Категорія товару';
$L['market_form_category_hint'] = 'Виберіть розділ структури вітрини, який найточніше відповідає вашому товару за своїми характеристиками, якостями та властивостями';
$L['market_form_meta_h1'] = 'SEO заголовок H1';
$L['market_form_meta_h1_hint'] = 'Основний і видимий користувачеві заголовок сторінки. Використовується для виведення на сторінці картки товару замість звичайного заголовка/назви';
$L['market_form_owner'] = 'Власник товару';
$L['market_form_owner_hint'] = 'Він же продавець. Виступає як автор публікації та власник сторінки картки товару на сайті';
$L['market_form_item_title'] = 'Заголовок товару';
$L['market_form_item_title_hint'] = 'Звичайний заголовок товару, який видно у списках, замовленнях тощо.';
$L['market_form_item_desc'] = 'Вступний опис';
$L['market_form_item_desc_hint'] = 'Короткий вступний опис товару, який видно у списках під назвою товару';
$L['market_form_item_alias'] = 'Аліас у ЧПУ';
$L['market_form_item_alias_hint'] = 'Унікальний псевдонім як частина у складі загального посилання на товар. Заповнювати латинськими літерами та без спеціальних символів.';
$L['market_form_item_cost_usd'] = 'Вартість у USD';
$L['market_form_item_cost_usd_hint'] = 'Опціонально. Потрібно, якщо ціни у прайсах у доларах. Вводимо вартість у USD, автоматично конвертується й показує вартість у валюті сайту за поточним курсом. Копіюємо це значення та вставляємо в поле вартості вашого товару в місцевій (базовій) валюті вашої вітрини.';
$L['market_form_item_fieldmrkt_costdflt']  = 'Ціна/вартість у базовій валюті';
$L['market_form_item_price_base_after_rate']          = 'Вартість за поточним курсом';

$L['market_form_item_pcod']               = 'Код/Артикул';
$L['market_form_item_pcod_hint']          = 'Ваш код товару для швидкого пошуку на сайті. Зазвичай використовується при заповненні товарів на основі прайс-листів своїх постачальників';
$L['market_form_extrafield']               = 'Додаткові поля (екстраполя).';
$L['market_form_extrafield_not_found']     = 'Екстраполя для модуля маркет ще не створені.';
$L['market_form_extrafield_link'] = 
    'Редагувати' .
    '<a href="' . Cot::$cfg['mainurl'] . '/' . cot_url('admin', 'm=extrafields&n=' . Cot::$db_x . 'market', '', true) . '" target="_blank">' .
    '<strong> ' . $L['Extrafields'] . ' </strong></a>.';

$L['market_form_extrafield_hint']          = 'Екстраполя — це ваші додаткові користувацькі поля для вашого товару, щоб вносити й виводити будь-яку інформацію різних типів. Якщо їх більше, ніж декілька, — рекомендується використовувати окремий плагін <a href="https://github.com/webitproff/xtradbrowmarket-cotonti" target="_blank">Extrafields Market Custom i18n</a>.';

$L['market_goto_add_new_item_title'] = 'Додати товар';
$L['market_goto_add_new_item_title_hint'] = 'Пояснення про Додати товар (заготовка)';

$L['market_goto_edit_item_title'] = 'Редагувати товар';
$L['market_goto_edit_item_title_hint'] = 'Пояснення про Редагувати товар (заготовка)';


// ========================
// ПОМИЛКИ, ПІДТВЕРДЖЕННЯ ТА ПОВІДОМЛЕННЯ
// ========================
$L['market_aliascharacters'] = 'Недопустиме використання символів "+", "/", "?", "%", "#", "&" в аліасах';
$L['market_catmissing'] = 'Код категорії відсутній';
$L['market_clone'] = 'Клонувати товар';
$L['market_confirm_delete'] = 'Ви дійсно хочете видалити цей товар?';
$L['market_confirm_validate'] = 'Хочете затвердити цей товар?';
$L['market_confirm_unvalidate'] = 'Ви дійсно хочете відправити цей товар до черги на затвердження?';

$L['market_deleted'] = 'Товар видалено';
$L['market_deletedToTrash'] = 'Товар переміщено до кошика';
$L['market_drafts'] = 'Чернетки';
$L['market_drafts_desc'] = 'Товари, збережені в чернетках';
$L['market_notavailable'] = 'Товар буде опубліковано через';
$L['market_textmissing'] = 'Опис товару не повинен бути порожнім';
$L['market_titletooshort'] = 'Назва занадто коротка або відсутня';
$L['market_validation'] = 'Очікують затвердження';
$L['market_validation_desc'] = 'Ваші товари, які ще не затверджені адміністратором';
$L['market_savedasdraft'] = 'Товар збережено в чернетках';
$L['market_formhint'] = 'Після заповнення форми товар буде поміщено до черги на затвердження і буде приховано до затвердження адміністратором.';

// ========================
// УПРАВЛІННЯ ТОВАРОМ (ДІЇ)
// ========================
$L['market_pageid'] = 'ID товару';
$L['market_deletepage'] = 'Видалити товар';

$L['market_preview'] = 'Попередній перегляд';
$L['market_preview_notice'] = 'Це попередній перегляд. Зміни збережено як чернетку.';
$L['market_publish'] = 'Опублікувати';
$L['market_edit'] = 'Редагувати';

// ========================
// СТАТУСИ ТОВАРІВ
// ========================
$L['market_status_draft'] = 'Чернетка';
$L['market_status_pending'] = 'На розгляді';
$L['market_status_approved'] = 'Затверджено';
$L['market_status_published'] = 'Опубліковано';
$L['market_status_expired'] = 'Застаріло';

// ========================
// СПИСКИ ТА ЗАГАЛЬНЕ
// ========================
$L['market_linesperpage'] = 'Записів на сторінку';
$L['market_linesinthissection'] = 'Записів у розділі';
$L['market_date_published'] = 'Дата розміщення';
$L['market_latest_update'] = 'Оновлено';
$L['market_all_items'] = 'Усі товари';
$L['market_all_items_desc'] = 'Усі доступні товари магазину';
$L['market_contentAuthor'] = 'Товар розмістив';
$L['market_seller'] = 'Продавець товару';
$L['market_catalog'] = 'Каталог';
$L['market_price'] = 'Ціна';
$L['market_price_international'] = 'Ціна в міжнародній валюті (для конвертації)';
$L['market_price_base']          = 'Ціна/вартість у базовій валюті';
$L['market_go_to_catalog'] = 'Перейти до товарів';
$L['market_no_products'] = 'Немає товарів';
$L['market_catEmpty'] = 'У категорії поки немає товарів';

// ========================
// ВІДМІНКИ (МНОЖИННІ ФОРМИ)
// ========================
$Ls['pages'] = "товар,товари,товарів";
$Ls['unvalidated_market'] = "незатверджений товар,незатверджені товари,незатверджених товарів";
$Ls['market_in_drafts'] = "товар у чернетках,товари у чернетках,товарів у чернетках";

// ========================
// ОСОБИСТИЙ КАБІНЕТ І КАТАЛОГ
// ========================
$L['market_myproducts'] = 'Мої товари';

// ========================
// ПУБЛІЧНИЙ ПРОФІЛЬ І ТОВАРИ КОРИСТУВАЧА
// --- Рядки з market.userdetails.php ---
// ========================
$L['market_users_products'] = 'Товари користувача';
$L['market_load_more'] = 'Завантажити ще (сторінка %d з %d)';
$L['market_loading'] = '<i class="fa fa-spinner fa-spin"></i> Завантаження...';
$L['market_load_error'] = 'Помилка завантаження. Спробуйте ще раз.';

// ========================
// ФАЙЛИ ТА ЗАВАНТАЖЕННЯ
// ========================
$L['File'] = 'Файл';
$L['extf_onserver'] = 'на сервері';
$L['extf_replacefile'] = 'Замінити файл';
$L['extf_choosefile'] = 'Вибрати файл';
$L['extf_deletefile'] = 'Видалити поточний файл';
$L['extf_deletehint'] = 'поставте галочку та застосуйте зміни';

// ========================
// РЕЗУЛЬТАТИ ПОШУКУ
// ========================
$Ls['market_declen_items_sq_found'] = "товар,товари,товарів";
$L['market_search_found'] = 'Знайдено всього <span class="badge rounded-pill bg-primary bg-opacity-10 text-dark"> %1$s </span>, на цій сторінці: %2$s за запитом: <span class="badge rounded-pill bg-success"> %3$s </span>';
$L['market_search_none'] = 'За запитом %1$s нічого не знайдено';
$L['market_search_in_title']              = 'Шукати лише в назвах';
$L['market_search_in_title_and_descr']    = 'Шукати в назвах та описі';
$L['market_search_in_pcod']               = 'Шукати за кодом товару';


// ========================
// MAKET.MAIN | КАРТКА ТОВАРУ
// ========================
$L['market_get_by_id_as_recommend'] = 'З цим товаром люди часто замовляють';

// ========================
// ІНШІ ЕЛЕМЕНТИ ІНТЕРФЕЙСУ
// ========================
$L['market_read_more'] = 'Читати далі';
$L['market_collapse']  = 'Згорнути';

// ========================
// VENDOR
// ========================
$L['market_vendors_title'] = 'Продавці';
$L['market_vendors_total'] = 'Продавців усього';
$L['market_vendors_sort_title'] = 'Сортувати за';
$L['market_vendors_sort_last_item'] = 'За останнім товаром';
$L['market_vendors_sort_items_count'] = 'За кількістю товарів';
$L['market_vendors_sort_username'] = 'За іменем';
$L['market_vendors_sort_regdate'] = 'За датою реєстрації';
$L['market_vendors_sort_last_seen'] = 'За останньою активністю';
$L['market_vendors_items'] = 'товарів';
$L['market_vendors_categories'] = 'категорій';
$L['market_vendors_registered'] = 'Зареєстрований';
$L['market_vendors_goto_showcase'] = 'Вітрина';
$L['market_vendors_profile'] = 'Профіль';
$L['market_vendors_empty'] = 'Продавців поки не знайдено';
$L['market_vendor_page_title'] = 'Вітрина продавця %s';
$L['market_vendor_products'] = 'Товарів';
$L['market_vendor_categories'] = 'Категорій';
$L['market_vendor_registered'] = 'Дата реєстрації';
$L['market_vendor_categories_of'] = 'Категорії продавця';
$L['market_vendor_no_categories'] = 'У продавця поки немає категорій';
$L['market_vendor_empty'] = 'У продавця поки немає товарів';
$L['market_vendor_profile_link'] = 'профіль';
$L['market_owner_vendor_link'] = 'Вітрина продавця';
$L['market_vendors_search_username'] = 'Введіть логін продавця';

// Custom localization file for Cotonti using via function cot_langfile_custom() in system/functions.custom.php
// include File from Path: modules/market/lang/market.custom.uk.lang.php
// How it works:  https://github.com/webitproff/functions.custom.php-cotonti
// How it works:  https://abuyfile.com/ru/cotonti/reading/rukovodstvo-po-polzovatelskim-funkciyam-cotonti
if (function_exists('cot_langfile_custom')) {
    cot_langfile_custom('market', 'module');
}
