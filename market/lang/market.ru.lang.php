<?php
/**
 * Russian Language File for the Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Все текстовые строки, используемые модулем Market PRO в интерфейсе Cotonti:
 * - название и описание модуля (info_name, info_desc, info_notes)
 * - настройки в админ-панели (cfg_…)
 * - подсказки к полям (cfg_…_hint)
 * - строки интерфейса пользователя и администратора
 * - склонения для множественных чисел ($Ls)
 *
 * Filename: modules/market/lang/market.ru.lang.php
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
// ИНФОРМАЦИЯ О МОДУЛЕ (АДМИНКА)
// ========================
$L['info_name']  = 'Market PRO';
$L['info_desc']  = 'Модуль интернет-магазина, который может работать с одним продавцом или как торговая площадка с витринами продавцов.';
$L['info_notes'] = 'Подробнее: <a href="https://abuyfile.com/ru/market/cotonti/plugs/marketpro" target="_blank">документация и ссылки на неё</a>.';

$L['market_title']       = $L['info_name']; // backwards compatibility for old extentions
$L['market_desc']        = $L['info_desc']; // backwards compatibility for old extentions

$L['Market']       = $L['info_name']; // backwards compatibility for old extentions
$L['market']       = $L['info_name']; // backwards compatibility for old extentions


/* 
 * ======================================================
 * ► НАЧАЛО ◄
 * ------------------------------------------------------
 * НАСТРОЙКИ МОДУЛЯ (КОНФИГУРАЦИЯ)
 * ======================================================
*/
// =========================================
// BEGIN COT EXT CONFIG
// =========================================
$L['cfg_marketlist_default_title'] = 'Заголовок магазина по умолчанию';
$L['cfg_marketlist_default_title_hint'] = 'Заголовок магазина, отображаемый когда не выбрана категория или товар';

$L['cfg_marketlist_default_desc'] = 'Описание магазина по умолчанию';
$L['cfg_marketlist_default_desc_hint'] = 'Описание магазина, отображаемое когда не выбрана категория или товар';

// --- Валюта и курсы ---
$L['cfg_market_currency'] = '<strong>Базовая валюта</strong> - Денежная единица стоимости по умолчанию';
$L['cfg_market_currency_hint'] = 'Обычно это национальная валюта вашего государства. Название такой местной валюты писать можно полностью или сокращенно. Влияет только на публичную видимость стоимости товара в указанной единице стоимости. Значения такой стоимости (целое число) вносятся в поле <code>fieldmrkt_costdflt</code> таблицы модуля.';

$L['cfg_market_for_rate_base_currency_cost_usd'] = 'Твердая валюта для расчёта курса (например, USD)';
$L['cfg_market_for_rate_base_currency_cost_usd_hint'] = 'Международная валюта, по которой будем расчитывать стоимость товара в базовой валюте. Допустим, вы продаете товар в местной валюте, а поставщик поставляет вам товар только по оплате в долларах США';

$L['cfg_market_rate_value_fieldmrkt_costdflt_to_cost_usd'] = 'Курс базовой валюты к твердой валюте';
$L['cfg_market_rate_value_fieldmrkt_costdflt_to_cost_usd_hint'] = 'Значение курса вашей местной валюты к международной. Например местная валюта биткоин (BTC) и считаем сколько местной валюты нужно, что бы купить 1 доллар США. Сейчас это примерно 0,000012, но в качестве разделителя используем не запятую, а точку (0.000012). <br><a href="https://www.xe.com/currencyconverter/convert/?Amount=1&From=USD&To=EUR" target="_blank"><strong>Актуальные курсы мировых валют</strong></a>. Вставляем значение своей валюты';

// --- Код валюты Schema.org ---
$L['cfg_market_currency_schema_org']        = 'Код валюты Schema.org';
$L['cfg_market_currency_schema_org_hint']   = 'Код валюты по стандарту ISO 4217 для микроразметки Schema.org.';
$L['cfg_market_currency_schema_org_params'] = [
    'USD' => 'USD — Доллар США',
    'EUR' => 'EUR — Евро',
    'RUB' => 'RUB — Российский рубль',
    'UAH' => 'UAH — Украинская гривна',
    'KZT' => 'KZT — Казахстанский тенге',
    'BYN' => 'BYN — Белорусский рубль',
    'UZS' => 'UZS — Узбекский сум',
    'KGS' => 'KGS — Киргизский сом',
    'TJS' => 'TJS — Таджикский сомони',
    'TMT' => 'TMT — Туркменский манат',
    'AZN' => 'AZN — Азербайджанский манат',
    'AMD' => 'AMD — Армянский драм',
    'MDL' => 'MDL — Молдавский лей',
    'JPY' => 'JPY — Японская йена',
    'CNY' => 'CNY — Китайский юань',
    'BTC' => 'BTC — Биткоин',
];

// --- Категории и отображение ---
$L['cfg_marketmaxlistsperpage'] = 'Макс. количество категорий на странице';
$L['cfg_marketmaxlistsperpage_hint'] = 'Количество категорий, отображаемых на одной странице списка';

$L['cfg_marketmaxlistsperpageadmin'] = 'Макс. элементов на странице в админ-панели';
$L['cfg_marketmaxlistsperpageadmin_hint'] = 'Количество записей на странице списка товаров в административной панели';

$L['cfg_marketblacktreecatspage'] = 'Черный список категорий';
$L['cfg_marketblacktreecatspage_hint'] = 'Коды категорий, исключенные из дерева категорий на страницах (например: system, unvalidated)';

// --- Сортировка и структура ---
$L['cfg_market_main_order'] = 'Основная сортировка (главная)';
$L['cfg_market_main_order_hint'] = 'Поле и направление сортировки товаров на главной странице магазина';

// --- Основные настройки ---
$L['cfg_marketmarkup'] = 'Включить разметка в описании';
$L['cfg_marketmarkup_hint'] = 'Использовать или нет визуальный редактор текста в описании товара. Например: HTML или BBCode';

$L['cfg_marketparser'] = 'Парсер описания';
$L['cfg_marketparser_hint'] = 'Выберите парсер для обработки описания товара (например, BBCode, HTML и т.д.). Если доступен HTML - именно его и ставим.';

$L['cfg_marketcount_admin'] = 'Считать посещения администраторов';
$L['cfg_marketcount_admin_hint'] = 'Включить посещения администраторов в статистику посещаемости сайта';

$L['cfg_marketautovalidate'] = 'Автоматическое утверждение товаров';
$L['cfg_marketautovalidate_hint'] = 'Автоматически утверждать публикацию товаров, созданных пользователем с правом администрирования раздела';

$L['cfg_market_select2_custom_css'] = 'Подключать пользовательские стили Select2 из модуля Market';
$L['cfg_market_select2_custom_css_hint'] = 'Подключает файл <strong>modules/market/css/marketSelect2CustomStyles.css</strong> в котором лежат стили для кастомизации библиотеки Select2 через которую выводим категории в разных локациях. Рекомендуется стили перенести в файл стилей темы и эту опцию отключить.';

$L['cfg_market_select2_custom_js'] = 'Подключать пользовательские скрипты Select2 из модуля Market';
$L['cfg_market_select2_custom_js_hint'] = 'Подключает файл <strong>modules/market/js/marketSelect2CustomJS.js</strong> в котором лежат скрипты для кастомизации библиотеки Select2 через которую выводим категории в разных локациях. Рекомендуется эти скрипты перенести в отдельный файл скриптов в папку вашей темы и эту опцию отключить.';
// =========================================
// END COT EXT CONFIG
// =========================================

// =========================================
// BEGIN COT EXT CONFIG STRUCTURE
// =========================================
$L['cfg_marketorder'] = 'Поле сортировки';
$L['cfg_marketorder_hint'] = 'Поле, по которому сортируются товары в категории';
$L['cfg_marketorder_params'] = [];

$L['cfg_marketway'] = 'Направление сортировки';
$L['cfg_marketway_hint'] = 'Направление сортировки: по возрастанию или по убыванию';
$L['cfg_marketway_params'] = [$L['Ascending'], $L['Descending']];

$L['cfg_maxrowsperpage'] = 'Макс. элементов на странице списка';
$L['cfg_maxrowsperpage_hint'] = 'Количество товаров, отображаемых на одной странице списка';

$L['cfg_markettruncatetext'] = 'Ограничить размер текста в списках товаров';
$L['cfg_markettruncatetext_hint'] = '0 для отключения';

$L['cfg_marketallowemptytext'] = 'Разрешить пустое описание товара';
$L['cfg_marketallowemptytext_hint'] = 'Разрешить публикацию товаров без описания';

// --- SEO по умолчанию ---
$L['cfg_marketmetatitle'] = 'Meta-заголовок';
$L['cfg_marketmetatitle_hint'] = 'Meta-тег title по умолчанию для страниц магазина';

$L['cfg_marketmetadesc'] = 'Meta-описание';
$L['cfg_marketmetadesc_hint'] = 'Meta-тег description по умолчанию для страниц магазина';

// --- Настройки в категориях (STRUCTURE) ---
$L['cfg_marketmaxlistsperpageincat'] = 'Макс. количество подкатегорий на странице категории';
$L['cfg_marketmaxlistsperpageincat_hint'] = 'Количество подкатегорий, отображаемых на странице внутри категории';

$L['cfg_marketkeywords'] = 'Ключевые слова';
$L['cfg_marketkeywords_hint'] = 'Meta-тег keywords по умолчанию для страниц магазина';

// --- Дубликат настройки, необходимый для категорий ---
$L['cfg_marketmaxlistsperpage'] = 'Макс. количество категорий на странице'; // duplicated in STRUCTURE section market.setup.php. It necessary for categories
// =========================================
// END COT EXT CONFIG STRUCTURE
// =========================================

/* 
 * ======================================================
 * НАСТРОЙКИ МОДУЛЯ (КОНФИГУРАЦИЯ)
 * ------------------------------------------------------
 * ► КОНЕЦ ◄
 * ======================================================
*/

// ========================
// ОСНОВНЫЕ СТРОКИ ИНТЕРФЕЙСА
// ========================
$L['market_title_general'] = 'Маркет';
$L['market_title_in_links'] = 'Витрины рынка';
$L['market_seller_vendors_title'] = 'Витрины и Продавцы';
$L['market_categories'] = 'Категории рынка';


// ========================
// АДМИН-ПАНЕЛЬ MARKET: ЛОКАЛИЗАЦИЯ ШАБЛОНА market.admin.tpl
// ========================
$L['adm_market_configuration'] = 'Конфигурация';
$L['adm_market_categories'] = 'Категории';
$L['adm_market_extrafields'] = 'Дополнительные поля';
$L['adm_market_search'] = 'Поиск';
$L['adm_market_category'] = 'Категория';
$L['adm_market_select_filter_options'] = 'Опции фильтра';
$L['adm_market_select_status_publication'] = 'Выбрать статус публикации';
$L['adm_market_sort'] = 'Сортировать';
$L['adm_market_select_filter_sorting_direction'] = 'Направление сортировки';
$L['adm_market_filter'] = 'Фильтр';
$L['adm_market_prune'] = 'Сбросить';
$L['adm_market_id'] = 'ID';
$L['adm_market_status'] = 'Статус';
$L['adm_market_title'] = 'Заголовок';
$L['adm_market_action'] = 'Действие';
$L['adm_market_validate'] = 'Утвердить';
$L['adm_market_delete'] = 'Удалить';
$L['adm_market_open'] = 'Открыть';
$L['adm_market_edit'] = 'Редактировать';
$L['adm_market_none'] = 'Нет';
$L['adm_market_total'] = 'Всего';
$L['adm_market_onpage'] = 'На странице';
$L['adm_market_text'] = 'Текст';

// ========================
// АДМИН-ПАНЕЛЬ: СТАТУСЫ, СПРАВКА, ФАЙЛЫ
// ========================
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

// ========================
// ВИДЖЕТ СТАТИСТИКИ (главная админки)
// ========================
$L['market_stats_activity'] = 'Активность';
$L['market_stats_today']    = 'Сегодня';
$L['market_stats_week']     = 'За 7 дней';
$L['market_stats_month']    = 'За 30 дней';
$L['market_stats_latest']   = 'Последние товары';
$L['market_stats_top_cats'] = 'Топ категорий';
$L['market_stats_top_viewed'] = 'Top viewed';

// ========================
// МАССОВЫЕ ДЕЙСТВИЯ И AJAX-ЗАГРУЗКА
// ========================
$L['market_adm_select_all'] = 'Выбрать все';
$L['market_adm_deselect_all'] = 'Снять все отметки';

// ========================
// ФОРМЫ: ДОБАВЛЕНИЕ И РЕДАКТИРОВАНИЕ ТОВАРА
// ========================
$L['market_form_add_item_title'] = 'Создание карточки товара';
$L['market_form_add_item_subtitle'] = 'Заполните как минимум обязательные &#128681; поля и сохраните данные формы. При редактировани товара вы сможете заполнить остальные.';
$L['market_form_item_edit_title'] = 'Редактируем свойства товара';
$L['market_form_item_edit_subtitle'] = 'Измените и заполните необходимые поля и сохраните данные формы';
$L['market_form_parser'] = 'Разметка (парсер)';
$L['market_form_parser_hint'] = 'Визуальный тектовый редактор, если он настроен. Если доступен для выбора <code>HTML</code> - именно его всегда и ставим';
$L['market_form_text_full'] = 'Описание товара';
$L['market_form_text_full_hint'] = 'Без ссылок, спама и муссора';
$L['market_form_date_now'] = 'Актуализировать дату товара';
$L['market_form_meta_title'] = 'Meta-заголовок';
$L['market_form_meta_title_hint'] = 'Заголовок во вкладку браузера. До 70 символов.';
$L['market_form_meta_desc'] = 'Meta-описание';
$L['market_form_meta_desc_hint'] = 'Объясняем поисковым системам что это за товар. До 155 символов.';
$L['market_form_category'] = 'Категория товара';
$L['market_form_category_hint'] = 'Выберите раздел структуры витрины, который наиболее точно соответствует вашему товару по своим характеристикам, качествам и свойствам';
$L['market_form_meta_h1'] = 'SEO заголовок H1';
$L['market_form_meta_h1_hint'] = 'Основной и видимый пользователю заголовок страницы. Используется для вывода на странице карточки товара вместо обычного заголовка/названия';
$L['market_form_owner'] = 'Владелец товара';
$L['market_form_owner_hint'] = 'Он же продавец. Выступает как автор публикации и владелец страницы карточки товара на сайте';
$L['market_form_item_title'] = 'Заголовок товара';
$L['market_form_item_title_hint'] = 'Обычный заголовок товара, который видим в списках, заказах и т.д.';
$L['market_form_item_desc'] = 'Вступительное описание';
$L['market_form_item_desc_hint'] = 'Краткое вступительное описание товара, которое видим в списках под названием товара';
$L['market_form_item_alias'] = 'Алиас в ЧПУ';
$L['market_form_item_alias_hint'] = 'Уникальный псевдоним как часть в составе общей ссылки на товар. Заполнять латинскими буквами и без специальных символов.';
$L['market_form_item_cost_usd'] = 'Стоимость в USD';
$L['market_form_item_cost_usd_hint'] = 'Опционально. Нужно, если цены в прайсах в долларах. Вводим стоимость в USD, автоматически конвертируется и показывает стоимость в валюте сайта по текущему курсу. Копируем это значение и вставляем в поле стоимости вашего товара в местной (базовой) валюте вашей витрины.';
$L['market_form_item_fieldmrkt_costdflt']  = 'Цена/стоимость в базовой валюте';
$L['market_form_item_price_base_after_rate']          = 'Стоимость по текущему курсу';

$L['market_form_item_pcod']               = 'Код/Артикул';
$L['market_form_item_pcod_hint']          = 'Ваш код товара для быстрого поиска по сайту. Обычно используется при заполнении товаров на основе прайс-листов своих поставщиков';
$L['market_form_extrafield']               = 'Дополнительные поля (экстраполя).';
$L['market_form_extrafield_not_found']     = 'Экстраполя для модуля маркет еще не созданы.';
$L['market_form_extrafield_link'] = 
    'Редактировать' .
    '<a href="' . Cot::$cfg['mainurl'] . '/' . cot_url('admin', 'm=extrafields&n=' . Cot::$db_x . 'market', '', true) . '" target="_blank">' .
    '<strong> ' . $L['Extrafields'] . ' </strong></a>.';

$L['market_form_extrafield_hint']          = 'Экстраполя - это ваши дополнительные пользовательские поля для вашего товара, что бы вносить и выводить любую информацию, различных типов. Если их больше, чем несколько, - рекомендуется использовать отдельный плагин <a href="https://github.com/webitproff/xtradbrowmarket-cotonti" target="_blank">Extrafields Market Custom i18n</a>.';

$L['market_goto_add_new_item_title'] = 'Добавить товар';
$L['market_goto_add_new_item_title_hint'] = 'Пояснение про Добавить товар (заготовка)';

$L['market_goto_edit_item_title'] = 'Редактировать товар';
$L['market_goto_edit_item_title_hint'] = 'Пояснение про Редактировать товар (заготовка)';


// ========================
// ОШИБКИ, ПОДТВЕРЖДЕНИЯ И УВЕДОМЛЕНИЯ
// ========================
$L['market_aliascharacters'] = 'Недопустимо использование символов "+", "/", "?", "%", "#", "&" в алиасах';
$L['market_catmissing'] = 'Код категории отсутствует';
$L['market_clone'] = 'Клонировать товар';
$L['market_confirm_delete'] = 'Вы действительно хотите удалить этот товар?';
$L['market_confirm_validate'] = 'Хотите утвердить этот товар?';
$L['market_confirm_unvalidate'] = 'Вы действительно хотите отправить этот товар в очередь на утверждение?';

$L['market_deleted'] = 'Товар удален';
$L['market_deletedToTrash'] = 'Товар удален в корзину';
$L['market_drafts'] = 'Черновики';
$L['market_drafts_desc'] = 'Товары, сохраненные в черновиках';
$L['market_notavailable'] = 'Товар будет опубликован через';
$L['market_textmissing'] = 'Описание товара не должно быть пустым';
$L['market_titletooshort'] = 'Название слишком короткое либо отсутствует';
$L['market_validation'] = 'Ожидают утверждения';
$L['market_validation_desc'] = 'Ваши товары, которые еще не утверждены администратором';
$L['market_savedasdraft'] = 'Товар сохранён в черновиках';
$L['market_formhint'] = 'После заполнения формы товар будет помещён в очередь на утверждение и будет скрыт до утверждения администратором.';

// ========================
// УПРАВЛЕНИЕ ТОВАРОМ (ДЕЙСТВИЯ)
// ========================
$L['market_pageid'] = 'ID товара';
$L['market_deletepage'] = 'Удалить товар';

$L['market_preview'] = 'Предпросмотр';
$L['market_preview_notice'] = 'Это предпросмотр. Изменения сохранены как черновик.';
$L['market_publish'] = 'Опубликовать';
$L['market_edit'] = 'Редактировать';

// ========================
// СТАТУСЫ ТОВАРОВ
// ========================
$L['market_status_draft'] = 'Черновик';
$L['market_status_pending'] = 'На рассмотрении';
$L['market_status_approved'] = 'Утверждён';
$L['market_status_published'] = 'Опубликован';
$L['market_status_expired'] = 'Устарел';

// ========================
// СПИСКИ И ОБЩЕЕ
// ========================
$L['market_linesperpage'] = 'Записей на страницу';
$L['market_linesinthissection'] = 'Записей в разделе';
$L['market_date_published'] = 'Дата размещения';
$L['market_latest_update'] = 'Обновлено';
$L['market_all_items'] = 'Все товары';
$L['market_all_items_desc'] = 'Все доступные товары магазина';
$L['market_contentAuthor'] = 'Товар разместил';
$L['market_seller'] = 'Продавец товара';
$L['market_catalog'] = 'Каталог';
$L['market_price'] = 'Цена';
$L['market_price_international'] = 'Цена в международной валюте (для конвертации)';
$L['market_price_base']          = 'Цена/стоимость в базовой валюте';
$L['market_price_converted_label'] = $L['market_price_base'];
$L['market_go_to_catalog'] = 'Перейти к товарам';
$L['market_no_products'] = 'Нет товаров';
$L['market_catEmpty'] = 'В категории пока нет товаров';

// ========================
// СКЛОНЕНИЯ (МНОЖЕСТВЕННЫЕ ФОРМЫ)
// ========================
$Ls['pages'] = "товар,товара,товаров";
$Ls['unvalidated_market'] = "неутверждённый товар,неутверждённые товары,неутверждённых товаров";
$Ls['market_in_drafts'] = "товар в черновиках,товары в черновиках,товаров в черновиках";

// ========================
// ЛИЧНЫЙ КАБИНЕТ И КАТАЛОГ
// ========================
$L['market_myproducts'] = 'Мои товары';

// ========================
// ПУБЛИЧНЫЙ ПРОФИЛЬ И ТОВАРЫ ПОЛЬЗОВАТЕЛЯ
// --- Строки из market.userdetails.php ---
// ========================
$L['market_users_products'] = 'Товары пользователя';
$L['market_load_more'] = 'Загрузить ещё (страница %d из %d)';
$L['market_loading'] = '<i class="fa fa-spinner fa-spin"></i> Загрузка...';
$L['market_load_error'] = 'Ошибка загрузки. Попробуйте ещё раз.';

// ========================
// ФАЙЛЫ И ЗАГРУЗКИ
// ========================
$L['File'] = 'Файл';
$L['extf_onserver'] = 'на сервере';
$L['extf_replacefile'] = 'Заменить файл';
$L['extf_choosefile'] = 'Выбрать файл';
$L['extf_deletefile'] = 'Удалить текущий файл';
$L['extf_deletehint'] = 'поставьте галочку и примените изменения';

// ========================
// РЕЗУЛЬТАТЫ ПОИСКА
// ========================
$Ls['market_declen_items_sq_found'] = "товар,товара,товаров";
$L['market_search_found'] = 'Найдено всего <span class="badge rounded-pill bg-primary bg-opacity-10 text-dark"> %1$s </span>, на этой странице: %2$s по запросу: <span class="badge rounded-pill bg-success"> %3$s </span>';
$L['market_search_none'] = 'По запросу %1$s ничего не найдено';
$L['market_search_in_title']              = 'Искать только в названиях';
$L['market_search_in_title_and_descr']    = 'Искать в названиях и описании';
$L['market_search_in_pcod']               = 'Искать по коду товара';


// ========================
// MAKET.MAIN | КАРТОЧКА ТОВАРА
// ========================
$L['market_get_by_id_as_recommend'] = 'С этим товаром, люди часто заказывают';

// ========================
// ПРОЧИЕ ЭЛЕМЕНТЫ ИНТЕРФЕЙСА
// ========================
$L['market_read_more'] = 'Читать далее';
$L['market_collapse']  = 'Свернуть';

// ========================
// VENDOR
// ========================
$L['market_vendors_title'] = 'Продавцы';
$L['market_vendors_total'] = 'Продавцов всего';
$L['market_vendors_sort_title'] = 'Сортировать по';
$L['market_vendors_sort_last_item'] = 'По последнему товару';
$L['market_vendors_sort_items_count'] = 'По количеству товаров';
$L['market_vendors_sort_username'] = 'По имени';
$L['market_vendors_sort_regdate'] = 'По дате регистрации';
$L['market_vendors_sort_last_seen'] = 'По последней активности';
$L['market_vendors_items'] = 'товаров';
$L['market_vendors_categories'] = 'категорий';
$L['market_vendors_registered'] = 'Зарегистрирован';
$L['market_vendors_goto_showcase'] = 'Витрина';
$L['market_vendors_profile'] = 'Профиль';
$L['market_vendors_empty'] = 'Продавцы пока не найдены';
$L['market_vendor_page_title'] = 'Витрина продавца %s';
$L['market_vendor_products'] = 'Товаров';
$L['market_vendor_categories'] = 'Категорий';
$L['market_vendor_registered'] = 'Дата регистрации';
$L['market_vendor_categories_of'] = 'Категории продавца';
$L['market_vendor_no_categories'] = 'У продавца пока нет категорий';
$L['market_vendor_empty'] = 'У продавца пока нет товаров';
$L['market_vendor_profile_link'] = 'профиль';
$L['market_owner_vendor_link'] = 'Витрина продавца';
$L['market_vendors_search_username'] = 'Введите логин продавца';

// Custom localization file for Cotonti using via function cot_langfile_custom() in system/functions.custom.php
// include File from Path: modules/market/lang/market.custom.ru.lang.php
// How it works:  https://github.com/webitproff/functions.custom.php-cotonti
// How it works:  https://abuyfile.com/ru/cotonti/reading/rukovodstvo-po-polzovatelskim-funkciyam-cotonti
if (function_exists('cot_langfile_custom')) {
    cot_langfile_custom('market', 'module');
}



