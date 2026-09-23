<?php
/* ====================
[BEGIN_COT_EXT]
Code=market
Name=Market PRO
Category=commerce
Description=Store Items and Categories
Version=5.7.9
Date=2026-09-10
Author=webitproff
Copyright=(c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
Notes=
Auth_guests=R
Lock_guests=A
Auth_members=RW1
Lock_members=
Requires_modules=
Requires_plugins=
Recommends_modules=
Recommends_plugins=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
marketlist_default_title=10:string::Заголовок магазина по-умолчанию, когда не выбрана категория или товар:
marketlist_default_desc=11:textarea::Описание магазина по-умолчанию, когда не выбрана категория или товар:
market_currency=21:string::грн.:
market_for_rate_base_currency_cost_usd=22:string::USD:
market_rate_value_fieldmrkt_costdflt_to_cost_usd=23:string::44.50:
market_currency_schema_org=25:select:USD,EUR,RUB,UAH,KZT,BYN,UZS,KGS,TJS,TMT,AZN,AMD,MDL,JPY,CNY,BTC:BTC:
marketmaxlistsperpage=30:select:5,6,7,8,9,10,15,50,100:10:
marketmaxlistsperpageadmin=31:select:10,15,25,30,40,50,100:10:Items in Admin List
marketblacktreecatspage=40:text:::Category codes (black list codes page structure as system, unvalidated e.t.c)
market_main_order=42:callback:cot_market_config_main_order():fieldmrkt_updated DESC:
marketmarkup=60:radio::1:
marketparser=61:callback:cot_get_parsers():none:
marketcount_admin=70:radio::0:
marketautovalidate=71:radio::1:
[END_COT_EXT_CONFIG]

[BEGIN_COT_EXT_CONFIG_STRUCTURE]
marketorder=01:callback:cot_market_config_order():title:
marketway=02:select:asc,desc:asc:
maxrowsperpage=03:string::8:
markettruncatetext=04:string::0:
marketallowemptytext=05:radio::0:
marketmetatitle=07:string:::
marketmetadesc=08:string:::
marketmaxlistsperpageincat=09:select:5,6,7,8,9,10,15,9:10:
[END_COT_EXT_CONFIG_STRUCTURE]
==================== */

/**
 * ============================================================
 *
 * Filename: market.setup.php
 * Path:     modules/market/market.setup.php
 *
 * Market PRO v.5+ for Cotonti v.1+, PHP 8.5+, MySQL 8.4
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 19, 2026
 *
 * @package    market
 * @subpackage Setup
 * @version    5.7.9
 * @author     webitproff
 * @copyright  Copyright (c) webitproff 2026
 * @license    BSD
 * ============================================================
 */
 
/**
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО SETUP-ФАЙЛУ market.setup.php
 * ============================================================
 *
 * market.setup.php — файл модуля Market PRO для Cotonti.
 *
 * Смотреть function cot_extension_install() 
 * https://github.com/Cotonti/Cotonti/blob/master/system/extensions.php#L187
 *
 * Смотреть function cot_infoget() 
 * https://github.com/Cotonti/Cotonti/blob/master/system/extensions.php#L709
 *
 * Смотреть function cot_extension_add()
 * https://github.com/Cotonti/Cotonti/blob/master/system/extensions.php#L781
 *
 * Смотреть function cot_plugin_add()
 * https://github.com/Cotonti/Cotonti/blob/master/system/extensions.php#L972
 * cot_plugin_add - Registers a plugin or module in hook registry !!!
 *
 * Содержит:
 *   — блок BEGIN_COT_EXT с парами ключ=значение
 *     (Code, Name, Category, Description, Version, Date, Author,
 *      Copyright, Notes, Auth_guests, Lock_guests, Auth_members,
 *      Lock_members, Requires_modules, Requires_plugins,
 *      Recommends_modules, Recommends_plugins);
 *   — блок BEGIN_COT_EXT_CONFIG с параметрами глобальной
 *     конфигурации в формате имя=порядок:тип:варианты:default:текст;
 *   — блок BEGIN_COT_EXT_CONFIG_STRUCTURE с параметрами,
 *     переопределяемыми для отдельной категории, в том же формате.
 *
 * Эти блоки лежат внутри PHP-комментария /* ... *\/. При обычном
 * выполнении PHP они не исполняются.
 *
 * Читает их функция cot_infoget() (system/extensions.php):
 * находит BEGIN_<limiter> и END_<limiter> в тексте файла,
 * берёт строки между ними, делит каждую по первому «=» на ключ
 * и значение, собирает в массив.
 *
 * Вызывается cot_infoget() из cot_extension_install() при установке
 * или обновлении расширения — с limiter'ами 'COT_EXT',
 * 'COT_EXT_CONFIG', 'COT_EXT_CONFIG_STRUCTURE'.
 *
 * Во время работы сайта (после установки) этот файл ядром
 * не подключается.
 *
 * Метаданные модуля (секция COT_EXT):
 *
 *   Code               — код модуля (совпадает с именем папки и файлов).
 *   Name               — отображаемое имя в списке расширений админки.
 *   Category           — группа в админке (языковой ключ ext_cat_<код>).
 *   Description        — краткое описание модуля.
 *   Version            — версия в формате A.B.C (сравнивается с ct_version
 *                        при обновлении).
 *   Date               — дата релиза.
 *   Author             — авторы расширения.
 *   Copyright          — копирайт.
 *   Notes              — сведения о лицензии.
 *   Auth_guests        — права групп-гостей (R = read, W = write).
 *   Lock_guests        — список групп, для которых право заблокировано.
 *   Auth_members       — права участников.
 *   Lock_members       — список заблокированных групп для участников.
 *   Requires_modules   — обязательные модули (через запятую).
 *   Recommends_plugins — рекомендуемые плагины.
 *
 * Параметры глобальной конфигурации (секция COT_EXT_CONFIG):
 *
 * Формат setup-строки:
 *   имя=порядок:тип:варианты:default:текст
 *
 *   имя      — имя параметра в таблице config (колонка config_name).
 *   порядок  — позиция в списке (config_order).
 *   тип      — тип поля (см. COT_CONFIG_TYPE_* в system/configuration.php):
 *                string   — однострочное поле;
 *                text     — textarea (по умолчанию);
 *                select   — выпадающий список;
 *                radio    — переключатель да/нет;
 *                callback — список из callback-функции;
 *                hidden   — скрытое значение;
 *                range    — целочисленный диапазон;
 *                custom   — пользовательский тип.
 *   варианты — список значений для select / radio / callback.
 *   default  — значение по умолчанию (config_value и config_default).
 *   текст    — описание (используется как fallback, если нет ключа
 *              cfg_<имя> в языковом файле).
 *
 * Глобальные параметры модуля Market PRO (23 шт.):
 *
 *   1) marketmarkup  — включать ли визуальный редактор в описании товара.
 *   2) marketparser  — выбор парсера описания (HTML, BBCode и т.д.).
 *   3) marketcount_admin — учитывать ли посещения администраторов
 *                           в счётчике просмотров.
 *   4) marketautovalidate — автоматически утверждать товары, созданные
 *                            администратором раздела.
 *   5) marketmaxlistsperpage — количество категорий на странице.
 *   6) marketmaxlistsperpageadmin — записей на странице в админ-панели.
 *   7) markettitle_page — шаблон meta-заголовка страницы товара.
 *   8) marketlist_default_title — заголовок магазина по умолчанию.
 *   9) marketlist_default_desc  — описание магазина по умолчанию.
 *  10) marketblacktreecatspage — коды категорий, исключаемых из дерева.
 *  11) market_currency — валюта по умолчанию (информационно).
 *  12) market_currency_schema_org — код валюты по стандарту Schema.org.
 *  13) market_for_rate_base_currency_cost_usd — базовая валюта для курса.
 *  14) market_rate_value_fieldmrkt_costdflt_to_cost_usd — курс базовой валюты
 *       к гривне (UAH) для пересчёта цен.
 *  15) marketorder — поле сортировки товаров в категории.
 *  16) marketway — направление сортировки (по возрастанию/убыванию).
 *  17) market_main_order — сортировка на главной странице магазина.
 *  18) maxrowsperpage — максимальное количество товаров на странице.
 *  19) markettruncatetext — ограничение длины текста в списках (0 — off).
 *  20) marketallowemptytext — разрешать публикацию товаров без описания.
 *  21) marketkeywords — meta-тег keywords по умолчанию.
 *  22) marketmetatitle — meta-тег title по умолчанию.
 *  23) marketmetadesc — meta-тег description по умолчанию.
 *
 * Параметры структуры (секция COT_EXT_CONFIG_STRUCTURE):
 *
 * Эти параметры могут быть переопределены для каждой категории отдельно
 * через панель администратора:
 *
 *   1) marketmaxlistsperpageincat — максимальное количество подкатегорий,
 *       отображаемых внутри страницы категории.
 *   2) marketmaxlistsperpage — максимальное количество категорий
 *       на странице (дублирует глобальный параметр, но используется
 *       именно для настроек конкретной категории).
 *
 * ВАЖНО. Значения default в секции — однострочные. Многострочные
 * значения (например, списки кодов категорий) записываются
 * в одну строку через запятую. Финальные многострочные значения
 * при необходимости устанавливаются после cot_config_add() из
 * setup/market.install.php.
 *
 * Файл подключается ядром Cotonti при установке или обновлении модуля
 * в функции cot_extension_install(). Прямой запуск через браузер
 * запрещён — в конце файла стоит проверка defined('COT_CODE').
 *
 * ============================================================
 * ВЗАИМОДЕЙСТВИЕ С БАЗОЙ ДАННЫХ
 * ============================================================
 *
 * Сам файл xxxx.setup.php в базу данных НИЧЕГО НЕ ПИШЕТ. Он является
 * только источником данных для ядра. Читает его функция cot_infoget(),
 * а запись в таблицы выполняет ядро:
 *
 *   — при установке/обновлении:  cot_extension_install()  (system/extensions.php)
 *   — при удалении:              cot_extension_uninstall()
 *   — при смене статуса хуков:   ExtensionsControlService::pause()/resume()
 *
 * Таблицы БД, которые участвуют при установке/обновлении:
 *
 *   cot_core
 *     Регистрация расширения.
 *     Пишется функциями cot_extension_add() / cot_extension_update().
 *     Колонки:
 *       ct_code     = Code из COT_EXT
 *       ct_title    = Name из COT_EXT
 *       ct_version  = Version из COT_EXT
 *       ct_plug     = 0 для модуля, 1 для плагина
 *       ct_state    = 1 (активно) / 0 (выключено); выставляется через
 *                     ExtensionsControlService::checkIsActive().
 *
 *   cot_auth
 *     Права групп из метаданных Auth_guests / Lock_guests /
 *     Auth_members / Lock_members.
 *     Вставка: $db->insert($db_auth, $insert_rows).
 *     Колонки:
 *       auth_groupid      — ID группы (GUESTS, MEMBERS, …)
 *       auth_code         — для модуля = код модуля,
 *                           для плагина = строка 'plug'
 *       auth_option       — для модуля = 'a',
 *                           для плагина = код плагина
 *       auth_rights       — маска из Auth_guests / Auth_members
 *       auth_rights_lock  — маска из Lock_guests / Lock_members
 *       auth_setbyuserid  — ID того, кто установил
 *     Маски разбираются через cot_auth_getvalue().
 *     После вставки ядро делает UPDATE cot_users SET user_auth = ''
 *     (сброс кэша прав у всех пользователей).
 *
 *   cot_config
 *     Параметры из секций COT_EXT_CONFIG и COT_EXT_CONFIG_STRUCTURE.
 *     Парсинг: cot_config_parse(), запись: cot_config_add() /
 *     cot_config_update().
 *     Колонки:
 *       config_owner    = 'module' или 'plug'
 *       config_cat      = код расширения (Code)
 *       config_name     = имя параметра (например, marketmarkup)
 *       config_value    = текущее значение
 *       config_default  = значение по умолчанию из setup-строки
 *       config_type     = COT_CONFIG_TYPE_* (radio / select / string / …)
 *       config_order    = порядок в списке
 *       config_subcat   = '' (для глобальных) или '__default'
 *                         (для структуры категорий)
 *       config_variants = список вариантов (для select / radio)
 *       config_text     = текст описания (fallback cfg_<имя>)
 *
 *   cot_plugins
 *     Привязки частей модуля/плагина к хукам.
 *     Источник — блок [BEGIN_COT_EXT] в каждой части
 *     <code>.<part>.php (поля Hooks и Order).
 *     Запись: cot_plugin_add(); при обновлении предварительно
 *     cot_plugin_remove().
 *     Колонки:
 *       pl_hook    — имя хука (например, market.main)
 *       pl_code    — код расширения (Code)
 *       pl_part    — имя части (main, rss, header, …)
 *       pl_title   = Name из COT_EXT
 *       pl_file    — путь вида code/part.php
 *       pl_order   — приоритет, по умолчанию COT_PLUGIN_DEFAULT_ORDER = 10
 *       pl_active  = 1 / 0 (активна ли часть)
 *       pl_module  = 0 / 1 (модуль или плагин)
 *
 *   cot_users
 *     UPDATE cot_users SET user_auth = '' — сброс кэша прав
 *     после любых изменений в cot_auth / cot_plugins.
 *
 *   Таблицы модуля
 *     Создаются и удаляются SQL-скриптами из папки setup/:
 *       setup/market.install.sql       — при установке
 *       setup/patch_X.Y.Z.sql          — при обновлении (пошагово)
 *       setup/patch_X.Y.Z.inc          — PHP-патч при обновлении
 *       setup/market.uninstall.sql     — при удалении
 *     Запускаются через $db->runScript() и cot_apply_patches().
 *     Именно здесь создаются рабочие таблицы модуля Market PRO:
 *       market_items, market_categories и прочие — в зависимости
 *       от состава install.sql.
 *
 */
