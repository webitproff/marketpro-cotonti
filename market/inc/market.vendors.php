<?php
// declare(strict_types = 1);   // при необходимости можно включить строгую типизацию

/**
 * Vendors 
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 * 
 * Filename: market.vendors.php 
 *
 * Path:    modules/market/inc/market.vendors.php
 *
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ФАЙЛУ market.vendors.php
 * ============================================================
 *
 * Назначение:
 *   Отображает каталог всех витрин продавцов модуля Market. Каждый
 *   продавец представлен карточкой с информацией о нём: аватар,
 *   никнейм, краткое описание, количество товаров, категории, в которых
 *   он торгует, дополнительные поля пользователя. Ссылка с карточки
 *   ведёт не на страницу профиля пользователя, а на страницу витрины
 *   конкретного продавца (market.vendor.php).
 *
 * Основные параметры URL:
 *   m=vendors               — режим каталога продавцов;
 *   sort=<поле>             — поле сортировки (last_item, items_count, username, regdate, last_seen);
 *   way=<asc|desc>          — направление сортировки;
 *   sq=<запрос>             — поиск по нику продавца;
 *   c=<код категории>       — фильтр по продавцам, торгующим в категории;
 *   d=<страница>            — номер страницы пагинации.
 *
 * Логика работы:
 *   1. Импортирует параметры запроса.
 *   2. Проверяет права пользователя на модуль Market.
 *   3. Формирует SQL-запрос к таблице cot_market с группировкой по
 *      полю fieldmrkt_ownerid — это даёт уникальный список продавцов.
 *   4. Для каждого продавца подсчитывается общее количество
 *      опубликованных товаров и список категорий, в которых он торгует.
 *   5. Загружает данные пользователей через прямой JOIN.
 *   6. Обрабатывает сортировку (по количеству товаров, по нику,
 *      по дате регистрации) и поиск.
 *   7. Генерирует пагинацию через cot_pagenav().
 *   8. Для каждого продавца формирует набор тегов VENDOR_ROW_* и
 *      парсит блок MAIN.VENDOR_ROW в шаблоне.
 *   9. Выводит сообщения, вызывает хуки, возвращает готовый HTML.
 *
 * Используемые классы и сервисы:
 *   MarketDictionary       — константы состояний товара;
 *   UsersRepository        — загрузка данных продавцов по ID;
 *   Cot::$db               — прямые запросы к базе данных;
 *   XTemplate              — шаблонизатор вывода;
 *   cot_pagenav()          — генерация пагинации;
 *   cot_generate_usertags() — теги пользователя (продавца);
 *   cot_extrafields_*      — работа с дополнительными полями.
 *
 * Хуки:
 *   market.vendors.first   — в начале страницы;
 *   market.vendors.query   — перед формированием SQL-запроса;
 *   market.vendors.loop    — внутри цикла вывода продавцов;
 *   market.vendors.tags    — перед финальным парсингом шаблона.
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 * API Extrafields:     https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Sep 10, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

/* =====================================================================
 * ПОДКЛЮЧЕНИЕ КЛАССОВ И ФАЙЛОВ
 * ---------------------------------------------------------------------
 * Импортируем необходимые классы, подключаем функции модуля и API форм.
 * ===================================================================== */

// Импорт словаря Market — константы состояний товара
use cot\modules\market\inc\MarketDictionary;

// Импорт репозитория пользователей — для загрузки данных продавцов
use cot\users\UsersRepository;

// Стандартная защита от прямого обращения к файлу
defined('COT_CODE') or die('Wrong URL.');

// Подключаем основной файл функций модуля Market (там находится весь API)
require_once cot_incfile('market', 'module');

// Подключаем API форм (нужен для cot_inputbox, cot_selectbox и т.п.)
require_once cot_incfile('forms');

/* =====================================================================
 * ПРОВЕРКА ПРАВ ДОСТУПА
 * ---------------------------------------------------------------------
 * Каталог продавцов доступен для просмотра всем, у кого есть право
 * на чтение модуля Market. Проверка на уровне «any» (любая категория).
 * ===================================================================== */

// Получаем права пользователя для модуля Market в целом
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');

// Блокируем доступ, если у пользователя нет права на чтение
cot_block(Cot::$usr['auth_read']);

/* =====================================================================
 * УСТАНОВКА ПЕРЕМЕННЫХ ОКРУЖЕНИЯ И ЗАГОЛОВКОВ
 * ===================================================================== */

// Определяем константу-маркер, что мы находимся на странице витрин
// (может использоваться в других файлах и плагинах)
defined('COT_VENDORS') or define('COT_VENDORS', true);

// Устанавливаем location для системы (используется в шаблонах темы)
Cot::$env['location'] = 'vendors';

// Устанавливаем подзаголовок страницы
Cot::$out['subtitle'] = Cot::$L['market_vendors_title'];

// Инициализируем head, если ещё не задан
if (!isset(Cot::$out['head'])) {
    Cot::$out['head'] = '';
}

// Убираем noindex (страница должна индексироваться поисковиками)
Cot::$sys['noindex'] = false;
Cot::$R['code_noindex'] = '';

// ПОКА ЗДЕСЬ
/* === Hook === */
// Хук market.vendors.first — начало обработки страницы,
// позволяет плагинам вмешаться до формирования запроса.
foreach (cot_getextplugins('market.vendors.first') as $pl) {
    include $pl;
}
/* ===== */
/* =====================================================================
 * ИМПОРТ ПАРАМЕТРОВ ЗАПРОСА
 * ===================================================================== */

// Импортируем поле сортировки из GET (только буквы)
//$s = cot_import('s', 'G', 'ALP');
$s = cot_import('s', 'G', 'TXT');
// Импортируем направление сортировки из GET (asc или desc)
$w = cot_import('w', 'G', 'ALP', 4);

// Импортируем код категории из GET (текстовая строка)
$c = cot_import('c', 'G', 'TXT');

// Импортируем поисковый запрос из GET (текстовая строка)
$sq = cot_import('sq', 'G', 'TXT');

// Обрезаем пробелы в поисковом запросе; если null — пустая строка
$sq = ($sq !== null) ? trim($sq) : '';

/* =====================================================================
 * КОНФИГУРАЦИЯ МОДУЛЯ (внутренние параметры файла)
 * ---------------------------------------------------------------------
 * Здесь настраиваются варианты сортировки и значения по умолчанию.
 * При необходимости можно добавлять свои варианты сортировки.
 * ===================================================================== */

// Массив доступных полей сортировки: ключ — внутреннее имя, значение — локализованное название
$vendorSortOptions = [
    'last_item'   => Cot::$L['market_vendors_sort_last_item'],   // по дате последнего добавленного товара
    'items_count' => Cot::$L['market_vendors_sort_items_count'], // по количеству товаров
    'username'    => Cot::$L['market_vendors_sort_username'],    // по имени продавца
    'regdate'     => Cot::$L['market_vendors_sort_regdate'],     // по дате регистрации
    'last_seen'   => Cot::$L['market_vendors_sort_last_seen'],   // по дате последней активности
];

// Сопоставление внутренних имён полей с SQL-выражениями (псевдонимы в запросе)
$vendorSortMap = [
    'last_item'   => 'last_item_date',
    'items_count' => 'items_count',
    'username'    => 'user_name',
    'regdate'     => 'user_regdate',
    'last_seen'   => 'user_lastlog',
];

// Сортировка по умолчанию — по дате последнего добавленного товара
$defaultSort = 'last_item';

// Направление сортировки по умолчанию — по убыванию (свежие сверху)
$defaultWay = 'desc';

// Если сортировка не задана или некорректна — применяем значение по умолчанию
if (empty($s) || !isset($vendorSortOptions[$s])) {
    $s = $defaultSort;
}

// Если направление сортировки не задано или некорректно — применяем дефолт
if (empty($w) || !in_array($w, ['asc', 'desc'])) {
    $w = $defaultWay;
}

// Формируем SQL-выражение сортировки на основе выбранного поля и направления
$orderBy = ($vendorSortMap[$s] ?? 'last_item_date') . ' ' . $w;

/* =====================================================================
 * ПАГИНАЦИЯ
 * ---------------------------------------------------------------------
 * Количество продавцов на страницу берём из настроек модуля.
 * Если настройка не задана — используем значение по умолчанию 20.
 * ===================================================================== */

// Читаем количество продавцов на страницу из конфига модуля
$maxVendorsPerPage = (int) (Cot::$cfg['market']['marketmaxvendorsperpage'] ?? 20);

// Если значение некорректно (меньше или равно нулю) — сбрасываем на 20
if ($maxVendorsPerPage <= 0) {
    $maxVendorsPerPage = 20;
}

// Импортируем параметры пагинации: $pg — страница, $d — смещение, $durl — для URL
list($pg, $d, $durl) = cot_import_pagenav('d', $maxVendorsPerPage);

/* =====================================================================
 * ФОРМИРОВАНИЕ УСЛОВИЙ SQL-ЗАПРОСА
 * ---------------------------------------------------------------------
 * Собираем массив $where с SQL-условиями и массив $params с параметрами
 * для подготовленного запроса.
 * ===================================================================== */

// Инициализируем массив условий WHERE
$where = [];

// Инициализируем массив параметров для подготовленного запроса
$params = [];

// Если пользователь не администратор — показываем только опубликованные товары
// Для гостей и обычных пользователей — только опубликованные товары
// Для админов — все товары
// Для самого владельца — все свои товары (черновики, модерация, опубликованные)
// Товары без владельца не показываем
$where[] = 'p.fieldmrkt_ownerid > 0';

if (Cot::$usr['isadmin']) {
    // Админ видит всех продавцов со всеми их товарами
    // (без фильтра по статусу)

} elseif (Cot::$usr['id'] > 0) {
    // Авторизованный: чужие — только опубликованные,
    // свои — любые (черновики, модерация, опубликованные)
    $where[] = '(p.fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED
             . ' OR p.fieldmrkt_ownerid = ' . (int) Cot::$usr['id'] . ')';

} else {
    // Гость — только опубликованные
    $where[] = 'p.fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED;
}
if (!cot_plugin_active('multicatmarket')) {
// Фильтр по категории, если указана и существует в структуре
	if (!empty($c) && isset(Cot::$structure['market'][$c])) {

		// Получаем список категорий: указанная + все её подкатегории
		$catsub = cot_structure_children('market', $c, true);

		// Добавляем саму категорию (функция возвращает только дочерние)
		$catsub[] = $c;

		// Экранируем все коды категорий для безопасной подстановки в SQL
		$catsub_quoted = array_map([Cot::$db, 'quote'], $catsub);

		// Добавляем условие IN с списком категорий
		$where[] = 'p.fieldmrkt_cat IN (' . implode(',', $catsub_quoted) . ')';
	}
}
// Поиск по имени продавца (по частичному совпадению)
if (!empty($sq)) {

    // Экранируем поисковый шаблон
    $sq_escaped = Cot::$db->quote('%' . $sq . '%');

    // Добавляем условие LIKE по полю user_name
    $where[] = 'u.user_name LIKE ' . $sq_escaped;
}

/* === Hook === */
// Хук market.vendors.query — позволяет плагинам модифицировать запрос
foreach (cot_getextplugins('market.vendors.query') as $pl) {
    include $pl;
}
/* ===== */

// Формируем финальную SQL-строку WHERE (если условия есть)
$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';



/* =====================================================================
 * ОСНОВНОЙ SQL-ЗАПРОС: СПИСОК ПРОДАВЦОВ
 * ---------------------------------------------------------------------
 * Группируем товары по владельцу (fieldmrkt_ownerid) и считаем
 * агрегаты: количество товаров и дату последнего добавленного товара.
 * Это даёт уникальный список продавцов.
 * ===================================================================== */

// Подзапрос: уникальные продавцы с агрегатами
$sql_vendors = "
    SELECT
        p.fieldmrkt_ownerid AS user_id,
        COUNT(DISTINCT p.fieldmrkt_id) AS items_count,
        MAX(p.fieldmrkt_date) AS last_item_date
    FROM " . Cot::$db->market . " AS p
    LEFT JOIN " . Cot::$db->users . " AS u ON u.user_id = p.fieldmrkt_ownerid
    $where_sql
    GROUP BY p.fieldmrkt_ownerid
";



// SQL-запрос для подсчёта общего количества продавцов (без LIMIT)
$sql_count = "SELECT COUNT(*) FROM ($sql_vendors) AS t";

// Выполняем запрос подсчёта и сохраняем результат
$totalitems = Cot::$db->query($sql_count, $params)->fetchColumn();


// SQL-запрос для выборки текущей страницы продавцов с их данными из таблицы users.
// Собираем строку через конкатенацию, чтобы избежать путаницы с интерполяцией
// вложенных переменных внутри двойных кавычек. Такой подход безопасен, читабелен
// и не вызывает ложных подсветок синтаксиса в редакторах и IDE.
$sql_query = "SELECT t.*, u.*"
    . " FROM (" . $sql_vendors . ") AS t"
    . " LEFT JOIN " . Cot::$db->users . " AS u ON u.user_id = t.user_id"
    . " ORDER BY " . $orderBy
    . " LIMIT " . (int) $d . ", " . (int) $maxVendorsPerPage;

// Выполняем основной запрос и получаем массив продавцов
$vendors = Cot::$db->query($sql_query, $params)->fetchAll();

/* =====================================================================
 * ФОРМИРОВАНИЕ URL ДЛЯ ПАГИНАЦИИ И СОРТИРОВКИ
 * ===================================================================== */

// Базовые параметры URL (без пагинации и без сортировки)
$list_url_path = [];

// Добавляем поисковый запрос, если он был
if (!empty($sq)) {
    $list_url_path['sq'] = $sq;
}

// Добавляем фильтр по категории, если он был
if (!empty($c)) {
    $list_url_path['c'] = $c;
}

// Если сортировка отличается от дефолтной — сохраняем её в URL
if ($s !== $defaultSort) {
    $list_url_path['s'] = $s;
}

// Если направление отличается от дефолтного — сохраняем его в URL
if ($w !== $defaultWay) {
    $list_url_path['w'] = $w;
}
$list_url_path['l'] = Cot::$usr['lang'];
// Полный URL текущего списка (для ссылок и канонической)
$list_url = cot_url('market', array_merge(['m' => 'vendors'], $list_url_path));

/* =====================================================================
 * СОЗДАНИЕ ШАБЛОНА
 * ===================================================================== */

// Определяем файл шаблона
$mskin = cot_tplfile(['market', 'vendors.list']);
if (empty($mskin)) cot_error('Шаблон не найден');

// Формируем путь к файлу шаблона
$tpl_Path = $sys['abs_url'] . $mskin;

// Создаём объект XTemplate
$t = new XTemplate($mskin);

/* =====================================================================
 * ПЕРЕДАЧА ОБЩИХ ТЕГОВ В ШАБЛОН
 * ===================================================================== */

// Формируем массив общих тегов и передаём их в шаблон
$t->assign([

    // Общее количество продавцов (после применения фильтров и поиска)
    'VENDORS_TOTAL'        => $totalitems,

    // Текущее поле сортировки — для подсветки активного пункта в дропдауне
    'VENDORS_SORT_CURRENT' => $s,

    // Текущее направление сортировки — для подсветки активного пункта в дропдауне
    'VENDORS_WAY_CURRENT'  => $w,

    // ---------------------------------------------------------------------
    // Готовые классы подсветки активного пункта дропдауна.
    // Вычисляются в PHP и передаются в шаблон, потому что CoTemplate
    // не умеет вставлять IF-условия внутрь атрибута class.
    // Если условие истинно — в шаблоне подставится "active", иначе пустая строка.
    // ---------------------------------------------------------------------
    'VENDORS_SORT_LAST_ITEM_ACTIVE'       => ($s === 'last_item'   && $w === 'desc') ? 'active' : '',
    'VENDORS_SORT_LAST_ITEM_ACTIVE_UP'    => ($s === 'last_item'   && $w === 'asc')  ? 'active' : '',
    'VENDORS_SORT_ITEMS_COUNT_ACTIVE'     => ($s === 'items_count' && $w === 'desc') ? 'active' : '',
    'VENDORS_SORT_ITEMS_COUNT_ACTIVE_UP'  => ($s === 'items_count' && $w === 'asc')  ? 'active' : '',
    'VENDORS_SORT_USERNAME_ACTIVE'        => ($s === 'username'    && $w === 'asc')  ? 'active' : '',
    'VENDORS_SORT_USERNAME_ACTIVE_UP'     => ($s === 'username'    && $w === 'desc') ? 'active' : '',

    // ---------------------------------------------------------------------
    // URL сортировки по каждому полю и направлению.
    // Пятый аргумент cot_url() = true — «ignoreAppendix»: запрещает Cotonti
    // автоматически приклеивать текущий $_GET к ссылке. Без него старое
    // s/w из URL перебивает новое значение, и сортировка не меняется.
    // ---------------------------------------------------------------------

    // URL сортировки по дате последнего добавленного товара (по возрастанию)
    'VENDORS_SORT_LAST_ITEM_ASC'    => cot_url('market', array_merge($list_url_path, ['m' => 'vendors', 's' => 'last_item',   'w' => 'asc']),  '', false, true),

    // URL сортировки по дате последнего добавленного товара (по убыванию)
    'VENDORS_SORT_LAST_ITEM_DESC'   => cot_url('market', array_merge($list_url_path, ['m' => 'vendors', 's' => 'last_item',   'w' => 'desc']), '', false, true),

    // URL сортировки по количеству товаров у продавца (по возрастанию)
    'VENDORS_SORT_ITEMS_COUNT_ASC'  => cot_url('market', array_merge($list_url_path, ['m' => 'vendors', 's' => 'items_count', 'w' => 'asc']),  '', false, true),

    // URL сортировки по количеству товаров у продавца (по убыванию)
    'VENDORS_SORT_ITEMS_COUNT_DESC' => cot_url('market', array_merge($list_url_path, ['m' => 'vendors', 's' => 'items_count', 'w' => 'desc']), '', false, true),

    // URL сортировки по имени продавца (A→Z)
    'VENDORS_SORT_USERNAME_ASC'     => cot_url('market', array_merge($list_url_path, ['m' => 'vendors', 's' => 'username',    'w' => 'asc']),  '', false, true),

    // URL сортировки по имени продавца (Z→A)
    'VENDORS_SORT_USERNAME_DESC'    => cot_url('market', array_merge($list_url_path, ['m' => 'vendors', 's' => 'username',    'w' => 'desc']), '', false, true),

    // URL формы поиска продавцов (GET-запрос на этот же контроллер)
    'VENDORS_SEARCH_ACTION_URL'  => cot_url('market', ['m' => 'vendors']),

    // Выпадающий список категорий для формы поиска (с поддержкой Select2)
    'VENDORS_SEARCH_CAT_SELECT2' => cot_market_selectcat_select2($c, 'c'),

    // Поле ввода поискового запроса с сохранением текущего значения
    // 'VENDORS_SEARCH_SQ'          => cot_inputbox('text', 'sq', !empty($sq) ? htmlspecialchars($sq) : '', 'class="form-control"'),

	'VENDORS_SEARCH_SQ' => cot_inputbox(
		'text',
		'sq',
		!empty($sq) ? htmlspecialchars($sq) : '',
		'class="form-control" placeholder="' . htmlspecialchars(Cot::$L['market_vendors_search_username']) . '"'
	),

    // URL текущего списка продавцов (для ссылок и канонического URL)
    'VENDORS_LIST_URL' => $list_url,
]);

// Устанавливаем канонический URL для поисковых систем
Cot::$out['canonical_uri'] = $list_url;




/* =====================================================================
 * ГЕНЕРАЦИЯ ПАГИНАЦИИ
 * ===================================================================== */

// Формируем пагинацию через стандартную функцию Cotonti
$pagenav = cot_pagenav(
    'market',
    array_merge(['m' => 'vendors'], $list_url_path),
    $d,
    $totalitems,
    $maxVendorsPerPage
);

// Передаём теги пагинации в шаблон
$t->assign(cot_generatePaginationTags($pagenav));

/* =====================================================================
 * ЦИКЛ ПО ПРОДАВЦАМ
 * ---------------------------------------------------------------------
 * Для каждого продавца:
 *   - подгружаем список категорий, в которых у него есть товары;
 *   - формируем ссылку на его витрину и на профиль;
 *   - передаём теги VENDOR_ROW_* в шаблон.
 * ===================================================================== */

// Счётчик порядковых номеров строк
$jj = 0;

/* === Hook - Part1 : Set === */
// Готовим список плагинов для хука, который выполнится внутри цикла
$extp = cot_getextplugins('market.vendors.loop');
/* ===== */

// Перебираем всех продавцов из результата SQL-запроса
foreach ($vendors as $vendor) {

    // Увеличиваем счётчик
    $jj++;

    // SQL-запрос: список категорий, в которых у продавца есть опубликованные товары
    $sql_vendor_cats = "
        SELECT DISTINCT fieldmrkt_cat
        FROM " . Cot::$db->market . "
        WHERE fieldmrkt_ownerid = ?
          AND fieldmrkt_state = " . MarketDictionary::STATE_PUBLISHED;

    // Выполняем запрос с подготовленным параметром user_id
    $vendor_cats_codes = Cot::$db->query($sql_vendor_cats, [$vendor['user_id']])->fetchAll(PDO::FETCH_COLUMN);

    // Формируем HTML-список ссылок на категории с названиями
    $vendor_cats_html = [];
    foreach ($vendor_cats_codes as $catCode) {
        // Если категория существует в структуре — формируем ссылку с названием
        if (isset(Cot::$structure['market'][$catCode])) {
            // Получаем локализованное название (если активен i18n4marketpro)
            $catTitle = Cot::$structure['market'][$catCode]['title'];
            if (cot_plugin_active('i18n4marketpro')) {
                $currentLang = Cot::$usr['lang'] ?: Cot::$cfg['defaultlang'];
                if ($currentLang !== Cot::$cfg['defaultlang']) {
                    $translatedCat = cot_i18n4marketpro_get_cat($catCode, $currentLang);
                    if ($translatedCat && !empty($translatedCat['title'])) {
                        $catTitle = $translatedCat['title'];
                    }
                }
            }
            // Формируем ссылку на витрину продавца с фильтром по этой категории
            $vendor_cats_html[] = cot_rc_link(
                cot_url('market', ['m' => 'vendor', 'u' => $vendor['user_name'], 'c' => $catCode]),
                htmlspecialchars($catTitle)
            );
        }
    }

    // Ссылка на витрину продавца (используется в карточке)
    $showcaseUrl = cot_url('market', ['m' => 'vendor', 'u' => $vendor['user_name']]);

    // Ссылка на страницу профиля пользователя в системе
    $profileUrl = cot_url('users', [
        'm' => 'details',
        'id' => $vendor['user_id'],
        'u' => $vendor['user_name'],
    ]);

    // Формируем дополнительное описание из extrafields пользователя (если есть)
    // Пример: если у таблицы users есть поле user_description — берём его
    $vendorDescription = !empty($vendor['user_description'])
        ? htmlspecialchars($vendor['user_description'])
        : '';

    // Собираем массив тегов для одного продавца и передаём в шаблон
    $t->assign([

        // ID продавца в системе
        'VENDOR_ROW_USER_ID' => $vendor['user_id'],

        // Никнейм продавца (с HTML-экранированием)
        'VENDOR_ROW_USERNAME' => htmlspecialchars($vendor['user_name'] ?? ''),

        // Аватар продавца (стандартная функция Cotonti)
        'VENDOR_ROW_AVATAR' => cot_build_user($vendor['user_id'], $vendor['user_name']),

        // Описание продавца (из extrafields, если есть)
        'VENDOR_ROW_DESCRIPTION' => $vendorDescription,

        // Общее количество опубликованных товаров у продавца
        'VENDOR_ROW_PRODUCTS_COUNT' => $vendor['items_count'],

        // HTML-список категорий, в которых торгует продавец
        'VENDOR_ROW_CATEGORIES' => implode(', ', $vendor_cats_html),

        // Количество категорий у продавца
        'VENDOR_ROW_CATEGORIES_COUNT' => count($vendor_cats_html),

        // Дата последнего добавленного товара
        'VENDOR_ROW_LAST_ITEM_DATE' => !empty($vendor['last_item_date'])
            ? cot_date('datetime_medium', $vendor['last_item_date'])
            : '',

        // Timestamp последнего товара (для микроразметки)
        'VENDOR_ROW_LAST_ITEM_STAMP' => (int) $vendor['last_item_date'],

        // URL витрины продавца (главная ссылка карточки)
        'VENDOR_ROW_SHOWCASE_URL' => $showcaseUrl,

        // URL профиля продавца в системе
        'VENDOR_ROW_PROFILE_URL' => $profileUrl,

        // Дата регистрации продавца
        'VENDOR_ROW_REGDATE' => cot_date('datetime_medium', $vendor['user_regdate']),

        // Timestamp регистрации
        'VENDOR_ROW_REGDATE_STAMP' => (int) $vendor['user_regdate'],

        // Дата последней активности продавца
        'VENDOR_ROW_LAST_SEEN' => !empty($vendor['user_lastlog'])
            ? cot_date('datetime_medium', $vendor['user_lastlog'])
            : '',

        // Timestamp последней активности
        'VENDOR_ROW_LAST_SEEN_STAMP' => (int) $vendor['user_lastlog'],

        // Порядковый номер строки в списке
        'VENDOR_ROW_NUM' => $jj,

        // Чётность строки для CSS-стилизации (odd / even)
        'VENDOR_ROW_ODDEVEN' => cot_build_oddeven($jj),
    ]);

    // Передаём дополнительные поля пользователя (extrafields)
    // для их вывода в карточке продавца
    $t->assign(cot_generate_usertags($vendor, 'VENDOR_ROW_USER_'));

    /* === Hook - Part2 : Include === */
    // Выполняем плагины, привязанные к хуку market.vendors.loop
    foreach ($extp as $pl) {
        include $pl;
    }
    /* ===== */

    // Парсим блок VENDOR_ROW — добавляем текущую карточку в общий вывод
    $t->parse('MAIN.VENDOR_ROW');
}

/* =====================================================================
 * ОБРАБОТКА ПУСТОГО СПИСКА
 * ---------------------------------------------------------------------
 * Если продавцов не найдено — показываем сообщение об этом.
 * ===================================================================== */

// Если ни одного продавца не найдено
if ($jj === 0) {
    // Парсим блок VENDOR_EMPTY (сообщение об отсутствии продавцов)
    $t->parse('MAIN.VENDOR_EMPTY');
}

// в шаблоне показываем путь к нему
$t->assign('TPL_PATH', $tpl_Path); 

/* =====================================================================
 * ВЫВОД НАКОПЛЕННЫХ СООБЩЕНИЙ
 * ===================================================================== */
cot_display_messages($t);

/* === Hook === */
// Хук market.vendors.tags — вызывается перед финальным парсингом
foreach (cot_getextplugins('market.vendors.tags') as $pl) {
    include $pl;
}
/* ===== */

/* =====================================================================
 * ФИНАЛЬНЫЙ ПАРСИНГ ШАБЛОНА
 * ===================================================================== */

// Парсим основной блок MAIN
$t->parse('MAIN');

// Получаем готовый HTML для вывода в родительском файле market.php
$moduleBody = $t->text('MAIN');