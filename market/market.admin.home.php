<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.mainpanel
Order=5
[END_COT_EXT]
==================== */

/**
 * Market PRO — статистический виджет для главной страницы админ-панели.
 *
 * Filename: market.admin.home.php
 *
 * Path:    modules/market/market.admin.home.php
 *
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ФАЙЛУ market.admin.home.php
 * ============================================================
 *
 * Назначение:
 *   Выводит компактный статистический дашборд модуля Market на главной
 *   странице административной панели Cotonti. Виджет отображается как
 *   одна из панелей (mainpanel) и доступен только администраторам модуля.
 *
 *   Дашборд содержит:
 *     - счётчики товаров по состояниям (published / pending / drafts);
 *     - общее количество товаров и суммарные просмотры;
 *     - активность публикаций за сегодня / 7 / 30 дней;
 *     - прогресс-бар доли опубликованных товаров;
 *     - алерт-баннер очереди на утверждение (если есть pending);
 *     - топ-5 категорий по количеству товаров;
 *     - топ-5 продавцов по количеству товаров;
 *     - топ-10 самых просматриваемых опубликованных товаров;
 *     - ленту последних добавленных товаров;
 *     - быстрые ссылки на разделы модуля (настройки, структура, поля, добавление).
 *
 * Основные параметры URL:
 *   Хук admin.home.mainpanel не принимает внешних параметров — виджет
 *   вызывается автоматически при открытии admin.php без ?m=... .
 *   Права определяются по контексту текущего администратора.
 *
 * Логика работы:
 *   1. Проверяет права администратора модуля Market (область «any»).
 *   2. Собирает агрегированную статистику одним проходом по таблице товаров.
 *   3. Формирует независимые выборки: последние товары, топ категорий,
 *      топ продавцов, топ просмотров.
 *   4. Инициализирует XTemplate найденным файлом шаблона и передаёт путь
 *      к нему в тег TPL_PATH (для отладки).
 *   5. Передаёт в шаблон базовые теги, ссылки, счётчики, проценты и флаги
 *      состояний; парсит подблоки (RECENT_ITEMS, TOP_CATS, TOP_SELLERS,
 *      TOP_VIEWED) с обработкой пустых состояний.
 *   6. Вызывает хук market.admin.home.tags для плагинов и возвращает
 *      готовый HTML в переменную $line, которую родительский admin.php
 *      выводит в области MAINPANEL.
 *
 * Используемые классы и сервисы:
 *   MarketDictionary       — константы состояний товара (STATE_PUBLISHED,
 *                            STATE_PENDING, STATE_DRAFT);
 *   Cot::$db               — прямые SQL-запросы к таблицам cot_market,
 *                            cot_users;
 *   Cot::$structure        — дерево категорий модуля Market;
 *   Cot::$cfg, Cot::$usr, Cot::$sys, Cot::$L — общие ресурсы Cotonti;
 *   cot_auth()             — проверка прав администратора модуля;
 *   cot_url()              — генерация URL внутренних страниц;
 *   cot_market_url()       — генерация URL карточки товара (алиас либо ID);
 *   cot_date()             — форматирование дат для вывода;
 *   XTemplate              — шаблонизатор вывода дашборда;
 *   cot_getextplugins()    — выполнение зарегистрированных плагинов.
 *
 * Хуки:
 *   market.admin.home.tags — вызывается перед финальным парсингом шаблона,
 *                            позволяет плагинам добавить собственные теги
 *                            или модифицировать виджет.
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 10, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

// Импортируем словарь Market — константы состояний товара
use cot\modules\market\inc\MarketDictionary;

// Стандартная защита от прямого вызова файла
defined('COT_CODE') or die('Wrong URL');

// Подключаем главный файл модуля (функции, языковые строки, настройки)
require_once cot_incfile('market', 'module');

/* =====================================================================
 * ПРОВЕРКА ПРАВ ДОСТУПА
 * ---------------------------------------------------------------------
 * Виджет отображается только администраторам модуля Market.
 * Проверка выполняется на уровне «any» — без привязки к категории.
 * Если прав нет — файл молча завершается, виджет не выводится.
 * ===================================================================== */

// Получаем права текущего пользователя для модуля Market
list($auth_read, $auth_write, $isadmin) = cot_auth('market', 'any');

// Прерываем выполнение, если пользователь не администратор модуля
if (!$isadmin) {
    // Выходим тихо, чтобы виджет не отрисовался в дашборде
    return;
}

/* =====================================================================
 * БАЗОВЫЕ ПЕРЕМЕННЫЕ И ГРАНИЦЫ ПЕРИОДОВ
 * ---------------------------------------------------------------------
 * Готовим имя таблицы товаров и три timestamp-границы, по которым
 * далее считается активность публикаций.
 * ===================================================================== */

// Регистрируем таблицу товаров в объекте БД для удобного доступа
$db_market = Cot::$db->market;

// Текущий timestamp сайта — используется для всех расчётов «за период»
$now = (int) Cot::$sys['now'];

// Начало текущего дня — для метрики «сегодня»
$dayStart = strtotime('today 00:00:00', $now);

// Граница 7 дней назад — для метрики «за неделю» (7 суток в секундах)
$weekStart = $now - 7 * 86400;

// Граница 30 дней назад — для метрики «за месяц» (30 суток в секундах)
$monthStart = $now - 30 * 86400;

/* =====================================================================
 * СБОР СТАТИСТИКИ
 * ---------------------------------------------------------------------
 * Формируем массив $stats со всеми числовыми метриками дашборда.
 * Счётчики состояний собираются одним запросом с GROUP BY, чтобы
 * не гонять три отдельных COUNT на больших каталогах.
 * ===================================================================== */

// Инициализируем структуру метрик значениями по умолчанию
$stats = [
    // Общее количество товаров во всех состояниях
    'total'     => 0,
    // Опубликованные товары (STATE_PUBLISHED)
    'published' => 0,
    // Товары в очереди на утверждение (STATE_PENDING)
    'pending'   => 0,
    // Черновики (STATE_DRAFT)
    'drafts'    => 0,
    // Товары без владельца (fieldmrkt_ownerid = 0)
    'orphaned'  => 0,
    // Количество товаров, добавленных сегодня
    'today'     => 0,
    // Количество товаров, добавленных за последние 7 дней
    'week'      => 0,
    // Количество товаров, добавленных за последние 30 дней
    'month'     => 0,
    // Суммарные просмотры всех товаров каталога
    'views'     => 0,
];

// Получаем количество товаров в разрезе состояний (одним запросом)
$rows = Cot::$db->query(
    "SELECT fieldmrkt_state, COUNT(*) AS cnt
     FROM $db_market
     GROUP BY fieldmrkt_state"
)->fetchAll();

// Раскладываем строки группировки по полям массива $stats
foreach ($rows as $r) {

    // Приводим значение счётчика к целому числу
    $cnt = (int) $r['cnt'];

    // Накапливаем общее количество товаров по всем состояниям
    $stats['total'] += $cnt;

    // Распределяем счётчик в нужную ячейку по коду состояния
    switch ((int) $r['fieldmrkt_state']) {

        // Опубликованные — кладём в published
        case MarketDictionary::STATE_PUBLISHED: $stats['published'] = $cnt; break;

        // На модерации — кладём в pending
        case MarketDictionary::STATE_PENDING:   $stats['pending']   = $cnt; break;

        // Черновики — кладём в drafts
        case MarketDictionary::STATE_DRAFT:     $stats['drafts']    = $cnt; break;
    }
}

// Отдельно считаем товары без владельца — индикатор «брошенных» записей
$stats['orphaned'] = (int) Cot::$db->query(
    "SELECT COUNT(*) FROM $db_market WHERE fieldmrkt_ownerid = 0"
)->fetchColumn();

// Активность: сколько товаров добавлено сегодня
$stats['today'] = (int) Cot::$db->query(
    "SELECT COUNT(*) FROM $db_market WHERE fieldmrkt_date >= ?", [$dayStart]
)->fetchColumn();

// Активность: сколько товаров добавлено за 7 дней
$stats['week'] = (int) Cot::$db->query(
    "SELECT COUNT(*) FROM $db_market WHERE fieldmrkt_date >= ?", [$weekStart]
)->fetchColumn();

// Активность: сколько товаров добавлено за 30 дней
$stats['month'] = (int) Cot::$db->query(
    "SELECT COUNT(*) FROM $db_market WHERE fieldmrkt_date >= ?", [$monthStart]
)->fetchColumn();

// Суммарные просмотры всех товаров каталога (COALESCE — защита от NULL)
$stats['views'] = (int) Cot::$db->query(
    "SELECT COALESCE(SUM(fieldmrkt_count), 0) FROM $db_market"
)->fetchColumn();

// Замыкание для расчёта процента от общего количества товаров.
// Используется в тегах прогресс-баров; при пустом каталоге возвращает 0.
$pct = function ($n) use ($stats) {
    // При пустом каталоге возвращаем 0, чтобы не делить на ноль
    return $stats['total'] > 0 ? round($n / $stats['total'] * 100, 1) : 0;
};

/* =====================================================================
 * ИНИЦИАЛИЗАЦИЯ ШАБЛОНА
 * ---------------------------------------------------------------------
 * Определяем абсолютный путь к файлу шаблона через cot_tplfile().
 * Если шаблон не найден — фиксируем ошибку и продолжаем работу,
 * чтобы администратор увидел диагностическое сообщение.
 * Путь к шаблону передаётся в тег TPL_PATH — так же, как в админке модуля.
 * ===================================================================== */

// Полный путь к файлу шаблона (третий параметр true — абсолютный путь)
$tpl_Path = cot_tplfile('market.admin.home', 'module', true);

// Если шаблон не найден — фиксируем ошибку для вывода администратору
if (empty($tpl_Path)) {
    // Регистрируем сообщение об отсутствии файла шаблона
    cot_error('Шаблон не найден');
}

// Создаём объект XTemplate найденным файлом
$tt = new XTemplate($tpl_Path);

// Передаём путь в шаблон для отладки (тот же тег, что и в market.admin.tpl)
$tt->assign('TPL_PATH', Cot::$sys['abs_url'] . $tpl_Path);

/* =====================================================================
 * БАЗОВЫЕ ТЕГИ ШАБЛОНА
 * ---------------------------------------------------------------------
 * Передаём в шаблон ссылки, счётчики, проценты и флаги состояния.
 * Эти теги используются во всех блоках виджета: в шапке, плитках,
 * прогрессе и в условных конструкциях шаблона.
 * ===================================================================== */

// Передаём в шаблон базовые теги одним вызовом
$tt->assign([

    // Ссылка на админ-раздел модуля
    'ADMIN_HOME_URL'             => cot_url('admin', 'm=market'),

    // Ссылка на страницу настроек модуля Market
    'ADMIN_HOME_CONFIG_URL'      => cot_url('admin', 'm=config&n=edit&o=module&p=market'),

    // Ссылка на управление структурой категорий Market
    'ADMIN_HOME_STRUCTURE_URL'   => cot_url('admin', 'm=structure&n=market'),

    // Ссылка на управление дополнительными полями товаров
    'ADMIN_HOME_EXTRAFIELDS_URL' => cot_url('admin', 'm=extrafields&n=' . Cot::$db->market),

    // Ссылка на публичную форму добавления нового товара
    'ADMIN_HOME_ADD_URL'         => cot_url('market', 'm=add'),

    // Ссылка на очередь модерации в админ-разделе модуля
    'ADMIN_HOME_QUEUE_URL'       => cot_url('admin', 'm=market&filter=valqueue'),

    // Общее количество товаров во всех состояниях
    'ADMIN_HOME_TOTAL'      => $stats['total'],

    // Количество опубликованных товаров
    'ADMIN_HOME_PUBLISHED'  => $stats['published'],

    // Количество товаров в очереди на утверждение
    'ADMIN_HOME_PENDING'    => $stats['pending'],

    // Количество черновиков
    'ADMIN_HOME_DRAFTS'     => $stats['drafts'],

    // Количество товаров без владельца
    'ADMIN_HOME_ORPHANED'   => $stats['orphaned'],

    // Количество товаров, добавленных сегодня
    'ADMIN_HOME_TODAY'      => $stats['today'],

    // Количество товаров, добавленных за 7 дней
    'ADMIN_HOME_WEEK'       => $stats['week'],

    // Количество товаров, добавленных за 30 дней
    'ADMIN_HOME_MONTH'      => $stats['month'],

    // Суммарные просмотры в формате с пробелом-разделителем (1 234)
    'ADMIN_HOME_VIEWS'      => number_format($stats['views'], 0, '.', ' '),

    // Дубль счётчика очереди под старым именем — совместимость с кастомными шаблонами
    'ADMIN_HOME_MARKETQUEUED' => $stats['pending'],

    // Процент опубликованных от общего количества
    'ADMIN_HOME_PUBLISHED_PCT' => $pct($stats['published']),

    // Процент товаров на модерации от общего количества
    'ADMIN_HOME_PENDING_PCT'   => $pct($stats['pending']),

    // Процент черновиков от общего количества
    'ADMIN_HOME_DRAFTS_PCT'    => $pct($stats['drafts']),

    // Флаг: есть ли товары в очереди на модерации (для алерта)
    'ADMIN_HOME_HAS_PENDING' => $stats['pending'] > 0,

    // Флаг: каталог полностью пуст (для пустого состояния)
    'ADMIN_HOME_IS_EMPTY'    => $stats['total'] === 0,

    // Флаг: каталог содержит хотя бы один товар
    'ADMIN_HOME_HAS_ITEMS'   => $stats['total'] > 0,
]);

/* =====================================================================
 * ПОСЛЕДНИЕ ДОБАВЛЕННЫЕ ТОВАРЫ
 * ---------------------------------------------------------------------
 * Выбираем 5 самых свежих товаров независимо от состояния — чтобы
 * администратор сразу видел, что нового происходит в каталоге.
 * Для каждого товара формируем теги RECENT_ITEM_*, включая ссылку на
 * карточку через cot_market_url() (алиас товара с фолбэком на ID).
 * ===================================================================== */

// Запрашиваем 5 последних товаров вместе с ником владельца
$recentItems = Cot::$db->query(
    "SELECT p.fieldmrkt_id, p.fieldmrkt_alias, p.fieldmrkt_title, p.fieldmrkt_cat,
            p.fieldmrkt_state, p.fieldmrkt_date, p.fieldmrkt_ownerid,
            u.user_name
     FROM $db_market AS p
     LEFT JOIN " . Cot::$db->users . " AS u ON u.user_id = p.fieldmrkt_ownerid
     ORDER BY p.fieldmrkt_date DESC
     LIMIT 5"
)->fetchAll();

// Карта состояний: CSS-класс Bootstrap и ключ языковой строки для бейджа
$stateMap = [

    // Опубликован — зелёный
    MarketDictionary::STATE_PUBLISHED => ['class' => 'success',   'lang' => 'market_status_published'],

    // На модерации — жёлтый
    MarketDictionary::STATE_PENDING   => ['class' => 'warning',   'lang' => 'market_status_pending'],

    // Черновик — серый
    MarketDictionary::STATE_DRAFT     => ['class' => 'secondary', 'lang' => 'market_status_draft'],
];

// Если товары найдены — формируем строки, иначе парсим блок «пусто»
if (!empty($recentItems)) {

    // Перебираем найденные товары
    foreach ($recentItems as $item) {

        // Приводим состояние к целому для поиска в карте
        $state = (int) $item['fieldmrkt_state'];

        // Достаём CSS-класс и ключ языковой строки; при неизвестном состоянии — черновик
        $map = $stateMap[$state] ?? ['class' => 'secondary', 'lang' => 'market_status_draft'];

        // Название категории из структуры; если категории нет — код как есть
        $catTitle = Cot::$structure['market'][$item['fieldmrkt_cat']]['title']
            ?? $item['fieldmrkt_cat'];

        // Передаём в шаблон теги одной строки «последние товары»
        $tt->assign([
            // ID товара
            'RECENT_ITEM_ID'          => $item['fieldmrkt_id'],
            // Название с HTML-экранированием
            'RECENT_ITEM_TITLE'       => htmlspecialchars($item['fieldmrkt_title']),
            // URL карточки товара (алиас с фолбэком на ID)
            'RECENT_ITEM_URL'         => cot_market_url($item),
            // Название категории с экранированием
            'RECENT_ITEM_CAT'         => htmlspecialchars($catTitle),
            // Дата добавления в коротком формате
            'RECENT_ITEM_DATE'        => cot_date('datetime_short', $item['fieldmrkt_date']),
            // CSS-класс бейджа состояния
            'RECENT_ITEM_STATE_CLASS' => $map['class'],
            // Локализованное название состояния
            'RECENT_ITEM_STATE_LANG'  => Cot::$L[$map['lang']] ?? '',
            // Ник владельца товара с экранированием
            'RECENT_ITEM_OWNER'       => htmlspecialchars($item['user_name'] ?? ''),
        ]);

        // Парсим одну строку блока последних товаров
        $tt->parse('MAIN.RECENT_ITEMS.RECENT_ROW');
    }

    // Парсим контейнер блока последних товаров
    $tt->parse('MAIN.RECENT_ITEMS');

} else {

    // Пустое состояние — ни одного товара в каталоге
    $tt->parse('MAIN.RECENT_ITEMS.RECENT_EMPTY');

    // Парсим контейнер блока последних товаров
    $tt->parse('MAIN.RECENT_ITEMS');
}

/* =====================================================================
 * ТОП-5 КАТЕГОРИЙ
 * ---------------------------------------------------------------------
 * Группируем товары по коду категории и берём 5 категорий с наибольшим
 * количеством товаров. Ссылка ведёт в админ-список с фильтром по
 * выбранной категории.
 * ===================================================================== */

// Запрашиваем 5 категорий с наибольшим числом товаров
$topCats = Cot::$db->query(
    "SELECT fieldmrkt_cat, COUNT(*) AS cnt
     FROM $db_market
     GROUP BY fieldmrkt_cat
     ORDER BY cnt DESC
     LIMIT 5"
)->fetchAll();

// Если категории найдены — формируем строки, иначе парсим блок «пусто»
if (!empty($topCats)) {

    // Перебираем найденные категории
    foreach ($topCats as $cat) {

        // Название категории из структуры; если категории нет — код как есть
        $catTitle = Cot::$structure['market'][$cat['fieldmrkt_cat']]['title']
            ?? $cat['fieldmrkt_cat'];

        // Передаём в шаблон теги одной строки топ-категорий
        $tt->assign([
            // Код категории
            'TOP_CAT_CODE'  => $cat['fieldmrkt_cat'],
            // Название категории с экранированием
            'TOP_CAT_TITLE' => htmlspecialchars($catTitle),
            // Количество товаров в категории
            'TOP_CAT_COUNT' => $cat['cnt'],
            // Ссылка на админ-список с фильтром по этой категории
            'TOP_CAT_URL'   => cot_url('admin', 'm=market&c=' . urlencode($cat['fieldmrkt_cat'])),
        ]);

        // Парсим одну строку блока топ-категорий
        $tt->parse('MAIN.TOP_CATS.TOP_CAT_ROW');
    }

    // Парсим контейнер блока топ-категорий
    $tt->parse('MAIN.TOP_CATS');

} else {

    // Пустое состояние — категорий с товарами нет
    $tt->parse('MAIN.TOP_CATS.TOP_CATS_EMPTY');

    // Парсим контейнер блока топ-категорий
    $tt->parse('MAIN.TOP_CATS');
}

/* =====================================================================
 * ТОП-5 ПРОДАВЦОВ
 * ---------------------------------------------------------------------
 * Группируем товары по владельцу и берём 5 продавцов с наибольшим
 * количеством товаров. Товары без владельца (ownerid = 0) исключаются.
 * Ссылка ведёт в профиль пользователя.
 * ===================================================================== */

// Запрашиваем 5 продавцов с наибольшим числом товаров
$topSellers = Cot::$db->query(
    "SELECT p.fieldmrkt_ownerid, COUNT(*) AS cnt, u.user_name
     FROM $db_market AS p
     LEFT JOIN " . Cot::$db->users . " AS u ON u.user_id = p.fieldmrkt_ownerid
     WHERE p.fieldmrkt_ownerid > 0
     GROUP BY p.fieldmrkt_ownerid
     ORDER BY cnt DESC
     LIMIT 5"
)->fetchAll();

// Если продавцы найдены — формируем строки, иначе парсим блок «пусто»
if (!empty($topSellers)) {

    // Перебираем найденных продавцов
    foreach ($topSellers as $seller) {

        // Передаём в шаблон теги одной строки топ-продавцов
        $tt->assign([
            // ID продавца (пользователя)
            'TOP_SELLER_ID'    => $seller['fieldmrkt_ownerid'],
            // Ник или фолбэк #ID, с экранированием
            'TOP_SELLER_NAME'  => htmlspecialchars($seller['user_name'] ?? ('#' . $seller['fieldmrkt_ownerid'])),
            // Количество товаров продавца
            'TOP_SELLER_COUNT' => $seller['cnt'],
            // Ссылка на профиль продавца в системе пользователей
            'TOP_SELLER_URL'   => cot_url('users', 'm=details&id=' . $seller['fieldmrkt_ownerid']),
        ]);

        // Парсим одну строку блока топ-продавцов
        $tt->parse('MAIN.TOP_SELLERS.TOP_SELLER_ROW');
    }

    // Парсим контейнер блока топ-продавцов
    $tt->parse('MAIN.TOP_SELLERS');

} else {

    // Пустое состояние — продавцов нет
    $tt->parse('MAIN.TOP_SELLERS.TOP_SELLERS_EMPTY');

    // Парсим контейнер блока топ-продавцов
    $tt->parse('MAIN.TOP_SELLERS');
}

/* =====================================================================
 * ТОП-10 ПРОСМАТРИВАЕМЫХ ТОВАРОВ
 * ---------------------------------------------------------------------
 * Выбираем 10 опубликованных товаров с наибольшим счётчиком просмотров
 * (fieldmrkt_count). Черновики и товары на модерации в топ не попадают.
 * Ссылка на карточку строится через cot_market_url() — с приоритетом
 * алиаса над ID.
 * ===================================================================== */

// Запрашиваем 10 самых просматриваемых опубликованных товаров
$topViewed = Cot::$db->query(
    "SELECT fieldmrkt_id, fieldmrkt_alias, fieldmrkt_title, fieldmrkt_cat, fieldmrkt_count
     FROM $db_market
     WHERE fieldmrkt_state = " . MarketDictionary::STATE_PUBLISHED . "
     ORDER BY fieldmrkt_count DESC
     LIMIT 10"
)->fetchAll();

// Если товары найдены — формируем строки, иначе парсим блок «пусто»
if (!empty($topViewed)) {

    // Перебираем найденные товары
    foreach ($topViewed as $item) {

        // Название категории из структуры; если категории нет — код как есть
        $catTitle = Cot::$structure['market'][$item['fieldmrkt_cat']]['title']
            ?? $item['fieldmrkt_cat'];

        // Передаём в шаблон теги одной строки топа просмотров
        $tt->assign([
            // ID товара
            'TOP_VIEWED_ID'    => $item['fieldmrkt_id'],
            // Название с HTML-экранированием
            'TOP_VIEWED_TITLE' => htmlspecialchars($item['fieldmrkt_title']),
            // URL карточки товара (алиас с фолбэком на ID)
            'TOP_VIEWED_URL'   => cot_market_url($item),
            // Название категории с экранированием
            'TOP_VIEWED_CAT'   => htmlspecialchars($catTitle),
            // Количество просмотров с пробелом-разделителем
            'TOP_VIEWED_COUNT' => number_format((int) $item['fieldmrkt_count'], 0, '.', ' '),
        ]);

        // Парсим одну строку блока топа просмотров
        $tt->parse('MAIN.TOP_VIEWED.TOP_VIEWED_ROW');
    }

    // Парсим контейнер блока топа просмотров
    $tt->parse('MAIN.TOP_VIEWED');

} else {

    // Пустое состояние — просмотров пока нет
    $tt->parse('MAIN.TOP_VIEWED.TOP_VIEWED_EMPTY');

    // Парсим контейнер блока топа просмотров
    $tt->parse('MAIN.TOP_VIEWED');
}

/* === Hook === */

// Хук market.admin.home.tags — позволяет плагинам добавить свои теги
// в шаблон виджета перед финальным парсингом
foreach (cot_getextplugins('market.admin.home.tags') as $pl) {

    // Подключаем каждый зарегистрированный плагин
    include $pl;
}
/* ===== */

/* =====================================================================
 * ФИНАЛЬНЫЙ ПАРСИНГ ШАБЛОНА
 * ---------------------------------------------------------------------
 * Парсим основной блок MAIN и сохраняем готовый HTML в переменную $line,
 * которую родительский admin.php подставит в область ADMIN_HOME_MAINPANEL.
 * ===================================================================== */

// Парсим корневой блок шаблона
$tt->parse('MAIN');

// Получаем готовый HTML в переменную для родительского admin.php
$line = $tt->text('MAIN');