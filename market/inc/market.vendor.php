<?php
// declare(strict_types = 1);   // при необходимости можно включить строгую типизацию

/**
 * Single vendor showcase.
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 * 
 * Filename: market.vendor.php
 *
 * Path:    modules/market/inc/market.vendor.php
 *
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ФАЙЛУ market.vendor.php
 * ============================================================
 *
 * Назначение:
 *   Отображает витрину конкретного продавца — страницу с товарами,
 *   принадлежащими только этому пользователю. Логика вывода списка
 *   товаров полностью совпадает с market.list.php: поиск, фильтрация
 *   по категориям, сортировка, пагинация, AJAX-подгрузка. Отличие —
 *   жёсткая фильтрация по владельцу (fieldmrkt_ownerid).
 *
 *   Сверху витрины выводится шапка с информацией о продавце:
 *   аватар, никнейм, краткое описание, статистика (всего товаров,
 *   количество категорий, дата регистрации), ссылка на профиль.
 *
 * Основные параметры URL:
 *   m=vendor                — режим витрины продавца;
 *   u=<username>            — имя пользователя (продавца);
 *   uid=<user_id>           — ID пользователя (альтернативный способ);
 *   c=<код категории>       — фильтр по категории внутри витрины;
 *   s=<поле сортировки>     — поле сортировки товаров;
 *   w=<asc|desc>            — направление сортировки;
 *   sq=<запрос>             — поиск по товарам продавца;
 *   search_in=<область>     — область поиска: title, full, pcod;
 *   d=<страница>            — номер страницы пагинации.
 *
 * Поддерживаемые варианты URL:
 *   /market/vendor/<username>             — предпочтительный вариант;
 *   /market/vendor/<user_id>              — если передан числовой ID;
 *   /market/vendor-<user_id>-<username>   — комбинированный вариант;
 *   ?e=market&m=vendor&u=<username>       — обычный query-string.
 *
 * Логика работы:
 *   1. Импортирует идентификатор продавца (u или uid) из URL.
 *   2. Загружает данные продавца через UsersRepository.
 *   3. Если продавец не найден — 404.
 *   4. Проверяет, что у продавца есть опубликованные товары (или
 *      пользователь — владелец/админ, чтобы видеть и неопубликованные).
 *   5. Настраивает параметры сортировки, поиска, фильтрации (как в
 *      market.list.php, но с жёсткой привязкой к ownerid).
 *   6. Формирует SQL-условие с полем fieldmrkt_ownerid = <ID продавца>.
 *   7. Категории показываются только те, в которых у продавца есть
 *      товары (динамический подсчёт).
 *   8. Выводит шапку витрины с данными продавца (теги VENDOR_*).
 *   9. Выводит список товаров продавца (теги LIST_ROW_*), как в
 *      market.list.php.
 *  10. Генерирует пагинацию для товаров и для категорий.
 *  11. Выводит сообщения, вызывает хуки, возвращает HTML.
 *
 * Используемые классы и сервисы:
 *   MarketDictionary       — константы состояний товара;
 *   UsersRepository        — загрузка данных продавца;
 *   Cot::$db               — прямые запросы к базе данных;
 *   XTemplate              — шаблонизатор вывода;
 *   cot_pagenav()          — пагинация;
 *   cot_generate_markettags() — теги товара;
 *   cot_generate_usertags()   — теги продавца;
 *   cot_extrafields_*      — работа с дополнительными полями.
 *
 * Хуки:
 *   market.vendor.first         — в начале страницы;
 *   market.vendor.query         — перед формированием SQL-запроса;
 *   market.vendor.main          — после подготовки данных продавца;
 *   market.vendor.before_loop   — перед циклом вывода товаров;
 *   market.vendor.loop          — внутри цикла товаров;
 *   market.vendor.tags          — перед финальным парсингом шаблона.
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
 * ===================================================================== */

// Импорт словаря Market — константы состояний товара
use cot\modules\market\inc\MarketDictionary;

// Импорт репозитория пользователей — для загрузки данных продавца
use cot\users\UsersRepository;

// Стандартная защита от прямого обращения к файлу
defined('COT_CODE') or die('Wrong URL.');

// Подключаем основной файл функций модуля Market
require_once cot_incfile('market', 'module');

// Подключаем API форм
require_once cot_incfile('forms');

/* =====================================================================
 * ПОЛУЧЕНИЕ ИДЕНТИФИКАТОРА ПРОДАВЦА
 * ---------------------------------------------------------------------
 * Поддерживаем несколько способов передачи продавца:
 *   - u=<username>  — предпочтительный (никнейм);
 *   - uid=<user_id> — числовой ID (fallback);
 *   - vendor_user   — если включена строгая ЧПУ-маршрутизация.
 * ===================================================================== */

// Импортируем имя пользователя (никнейм) из GET
$u = cot_import('u', 'G', 'TXT');

// Импортируем числовой ID пользователя из GET
$uid = cot_import('uid', 'G', 'INT');

// Если передан ник — используем его для загрузки
// Иначе, если передан ID — используем ID (позже подгрузим ник из БД)
$vendor_user_id = 0;

// Если задан ник — ищем пользователя по имени
if (!empty($u)) {

    // Ищем пользователя по никнейму в БД через подготовленный запрос
    $vendor_user = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->users . ' WHERE user_name = ? LIMIT 1',
        [$u]
    )->fetch();

    // Если пользователь не найден — 404
    if (!$vendor_user) {
        cot_die_message(404);
    }

    // Получаем ID пользователя
    $vendor_user_id = (int) $vendor_user['user_id'];

// Иначе, если задан числовой ID
} elseif (!empty($uid)) {

    // Загружаем пользователя через UsersRepository
    $vendor_user = UsersRepository::getInstance()->getById((int) $uid);

    // Если пользователь не найден — 404
    if (!$vendor_user) {
        cot_die_message(404);
    }

    // Получаем ID пользователя
    $vendor_user_id = (int) $vendor_user['user_id'];

// Иначе — идентификатор не передан, 404
} else {
    cot_die_message(404);
}

/* =====================================================================
 * ПРОВЕРКА ПРАВ ДОСТУПА
 * ===================================================================== */

// Получаем права пользователя для модуля Market в целом
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');

// Блокируем доступ, если у пользователя нет права на чтение модуля
cot_block(Cot::$usr['auth_read']);

/* =====================================================================
 * ОПРЕДЕЛЕНИЕ, ЯВЛЯЕТСЯ ЛИ ПОЛЬЗОВАТЕЛЬ ВЛАДЕЛЬЦЕМ ВИТРИНЫ
 * ---------------------------------------------------------------------
 * Это влияет на то, какие товары показывать: только опубликованные
 * (для гостей и чужих пользователей) или все (для владельца и админа).
 * ===================================================================== */

// Является ли текущий пользователь владельцем витрины
$isOwner = (Cot::$usr['id'] === $vendor_user_id);

// Является ли текущий пользователь администратором модуля
$isAdmin = (bool) Cot::$usr['isadmin'];

// Может ли пользователь видеть неопубликованные товары этого продавца
$canSeeAll = ($isOwner || $isAdmin);

/* =====================================================================
 * ЗАГРУЗКА ДОПОЛНИТЕЛЬНЫХ ДАННЫХ ПРОДАВЦА
 * ---------------------------------------------------------------------
 * Подсчитываем количество опубликованных товаров и категорий, чтобы
 * показать эти данные в шапке витрины, а также проверить, что витрина
 * вообще существует (есть хотя бы один товар).
 * ===================================================================== */

// SQL-запрос: количество опубликованных товаров продавца
$sql_count_published = Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->market . '
     WHERE fieldmrkt_ownerid = ?
       AND fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED,
    [$vendor_user_id]
);

// Получаем число опубликованных товаров
$vendor_products_published = (int) $sql_count_published->fetchColumn();

// SQL-запрос: общее количество товаров продавца (включая черновики и модерацию)
$sql_count_total = Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->market . '
     WHERE fieldmrkt_ownerid = ?',
    [$vendor_user_id]
);

// Получаем общее число товаров
$vendor_products_total = (int) $sql_count_total->fetchColumn();

// Если у продавца нет ни одного товара и пользователь не владелец/админ — 404
if ($vendor_products_total === 0 && !$canSeeAll) {
    cot_die_message(404);
}

// SQL-запрос: количество категорий, в которых у продавца есть товары
$sql_count_cats = Cot::$db->query(
    'SELECT COUNT(DISTINCT fieldmrkt_cat) FROM ' . Cot::$db->market . '
     WHERE fieldmrkt_ownerid = ?
       AND fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED,
    [$vendor_user_id]
);

// Получаем число категорий
$vendor_categories_count = (int) $sql_count_cats->fetchColumn();

/* =====================================================================
 * УСТАНОВКА ПЕРЕМЕННЫХ ОКРУЖЕНИЯ И ЗАГОЛОВКОВ
 * ===================================================================== */

// Определяем константу-маркер витрины продавца
defined('COT_VENDOR') or define('COT_VENDOR', true);

// Устанавливаем location
Cot::$env['location'] = 'market.vendor';

// Устанавливаем подзаголовок страницы: "Витрина <ник>"
Cot::$out['subtitle'] = sprintf(
    Cot::$L['market_vendor_page_title'],
    htmlspecialchars($vendor_user['user_name'])
);

// Инициализируем head, если ещё не задан
if (!isset(Cot::$out['head'])) {
    Cot::$out['head'] = '';
}

// Убираем noindex (витрина должна индексироваться)
Cot::$sys['noindex'] = false;
Cot::$R['code_noindex'] = '';

/* =====================================================================
 * ИМПОРТ ПАРАМЕТРОВ ФИЛЬТРАЦИИ, ПОИСКА, СОРТИРОВКИ
 * ===================================================================== */

// Импортируем код категории из GET
$c = cot_import('c', 'G', 'TXT');

// Если категория пуста или отсутствует в структуре — сбрасываем в пустую строку
/* if ($c === '' || !isset(Cot::$structure['market'][$c])) {
    $c = '';
} */
if (!is_string($c) || $c === '' || !isset(Cot::$structure['market'][$c])) {
    $c = '';
}
// Импортируем поле сортировки
$s = cot_import('s', 'G', 'ALP');

// Импортируем направление сортировки
$w = cot_import('w', 'G', 'ALP', 4);

// Импортируем поисковый запрос
$sq = cot_import('sq', 'G', 'TXT');
$sq = ($sq !== null) ? trim($sq) : '';

// Импортируем область поиска (title/full/pcod)
$search_in = cot_import('search_in', 'G', 'ALP', 8);
if (!in_array($search_in, ['title', 'full', 'pcod'])) {
    $search_in = 'title';
}

/* =====================================================================
 * НАСТРОЙКА СОРТИРОВКИ ПО УМОЛЧАНИЮ
 * ===================================================================== */

// Значения по умолчанию
$defaultOrder = Cot::$cfg['market']['cat___default']['marketorder'] ?? 'date';
$defaultOrderWay = Cot::$cfg['market']['cat___default']['marketway'] ?? 'desc';

// Если сортировка не задана — берём из конфига категории или общий дефолт
if (empty($s)) {
    $s = !empty($c) && !empty(Cot::$cfg['market']['cat_' . $c]['marketorder'])
        ? Cot::$cfg['market']['cat_' . $c]['marketorder']
        : $defaultOrder;
}

// Если направление не задано — берём из конфига категории или общий дефолт
if (empty($w) || !in_array($w, ['asc', 'desc'])) {
    $w = !empty($c) && !empty(Cot::$cfg['market']['cat_' . $c]['marketway'])
        ? Cot::$cfg['market']['cat_' . $c]['marketway']
        : $defaultOrderWay;
}

// Проверяем, что поле сортировки существует в таблице market
if (!Cot::$db->fieldExists(Cot::$db->market, "fieldmrkt_$s")) {
    $s = 'date';
}

// Формируем SQL-выражение сортировки
$orderby = "fieldmrkt_$s $w";

/* =====================================================================
 * ПАГИНАЦИЯ ТОВАРОВ
 * ---------------------------------------------------------------------
 * Количество товаров на страницу берём из конфига категории или общий
 * дефолт модуля.
 * ===================================================================== */

// Количество товаров на страницу по умолчанию
$perPage = (int) (Cot::$cfg['market']['cat___default']['marketmaxlistsperpageincat']
    ?? Cot::$cfg['market']['cat___default']['marketmaxlistsperpage']
    ?? 10);

// Если задана категория — берём её настройку (если есть)
if (!empty($c) && !empty(Cot::$cfg['market']['cat_' . $c]['marketmaxlistsperpageincat'])) {
    $perPage = (int) Cot::$cfg['market']['cat_' . $c]['marketmaxlistsperpageincat'];
}

// Импортируем параметры пагинации
list($pg, $d, $durl) = cot_import_pagenav('d', $perPage);

/* =====================================================================
 * ФОРМИРОВАНИЕ SQL-УСЛОВИЙ ДЛЯ ВЫБОРКИ ТОВАРОВ
 * ===================================================================== */

// Массив условий WHERE
$where = [];

// Жёсткая привязка к продавцу — главное отличие от market.list.php
$where['owner'] = 'fieldmrkt_ownerid = ' . (int) $vendor_user_id;

// Ограничение по статусу
if ($canSeeAll) {

    // Владелец и админ видят все товары продавца — ограничений по статусу нет
    // (кроме тех, что придут из плагинов через хук ниже)

} else {

    // Гости и чужие пользователи видят только опубликованные товары
    $where['state'] = 'fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED;
}

// Фильтр по категории, если задана
if (!empty($c) && isset(Cot::$structure['market'][$c])) {

    // Получаем список категорий: указанная + все её подкатегории
    $catsub = cot_structure_children('market', $c, true);
    $catsub[] = $c;

    // Экранируем коды категорий и формируем условие IN
    $catsub_quoted = array_map([Cot::$db, 'quote'], $catsub);
    $where['cat'] = 'fieldmrkt_cat IN (' . implode(',', $catsub_quoted) . ')';
}

// Условие по датам активности (для неадминов)
if (!$canSeeAll) {
    $where['date'] = 'fieldmrkt_date <= UNIX_TIMESTAMP()';
}

// Поиск по товарам
if (!empty($sq)) {

    // Экранированный шаблон поиска
    $sq_escaped = Cot::$db->quote("%$sq%");

    // Базовое условие в зависимости от области поиска
    if ($search_in === 'title') {
        $base_condition = "fieldmrkt_title LIKE $sq_escaped";
    } elseif ($search_in === 'full') {
        $base_condition = "(fieldmrkt_title LIKE $sq_escaped OR fieldmrkt_text LIKE $sq_escaped)";
    } else { // pcod
        $base_condition = "fieldmrkt_pcod LIKE $sq_escaped";
    }

    // Учёт переводов i18n4marketpro
    $use_i18n_search = false;
    $i18n_condition = '';

    if (cot_plugin_active('i18n4marketpro')) {

        // Регистрируем таблицу переводов, если она ещё не зарегистрирована
        if (!isset(Cot::$db->i18n4marketpro_pages)) {
            Cot::$db->registerTable('i18n4marketpro_pages');
        }
        $i18n_table = Cot::$db->i18n4marketpro_pages;

        // Текущий язык
        $current_locale = Cot::$usr['lang'] ?: Cot::$cfg['defaultlang'];

        // Если язык не основной — ищем также в переводах
        if ($current_locale !== Cot::$cfg['defaultlang']) {
            $use_i18n_search = true;

            if ($search_in === 'title') {
                $i18n_condition = "EXISTS (SELECT 1 FROM $i18n_table WHERE ipage_id = p.fieldmrkt_id AND ipage_locale = " . Cot::$db->quote($current_locale) . " AND ipage_title LIKE $sq_escaped)";
            } elseif ($search_in === 'full') {
                $i18n_condition = "EXISTS (SELECT 1 FROM $i18n_table WHERE ipage_id = p.fieldmrkt_id AND ipage_locale = " . Cot::$db->quote($current_locale) . " AND (ipage_title LIKE $sq_escaped OR ipage_desc LIKE $sq_escaped OR ipage_text LIKE $sq_escaped))";
            }
        }
    }

    // Итоговое условие поиска
    $where['search'] = ($use_i18n_search && !empty($i18n_condition))
        ? "($base_condition OR $i18n_condition)"
        : $base_condition;
}

/* === Hook === */
// Хук market.vendor.query — позволяет плагинам модифицировать условия выборки
foreach (cot_getextplugins('market.vendor.query') as $pl) {
    include $pl;
}
/* ===== */

// Убираем пустые элементы (если плагины что-то удалили)
$where = array_filter($where);

// Формируем финальное SQL-выражение WHERE
$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

/* =====================================================================
 * ОСНОВНЫЕ SQL-ЗАПРОСЫ
 * ===================================================================== */

// Колонки и условия JOIN, которые могут быть расширены плагинами
$join_columns = $join_columns ?? '';
$join_condition = $join_condition ?? '';


// SQL-запрос для подсчёта общего количества товаров продавца (для пагинации).
// Собираем строку через конкатенацию, чтобы избежать интерполяции переменных
// внутри многострочных литералов — это устраняет ложные подсветки синтаксиса
// в редакторах и делает подставляемые фрагменты явными.
$sql_item_count = "SELECT COUNT(DISTINCT p.fieldmrkt_id)"
    . " FROM " . Cot::$db->market . " AS p"
    . " " . $join_condition
    . " LEFT JOIN " . Cot::$db->users . " AS u ON u.user_id = p.fieldmrkt_ownerid"
    . " " . $where_sql;

// SQL-запрос для выборки товаров текущей страницы
// SQL-запрос для выборки товаров текущей страницы витрины продавца.
// Также собираем через конкатенацию: каждая переменная и каждый SQL-фрагмент
// подставляются явно, что безопасно и не вызывает ложных срабатываний
// статических анализаторов.
$sql_item_string = "SELECT p.*, u.* " . $join_columns
    . " FROM " . Cot::$db->market . " AS p"
    . " " . $join_condition
    . " LEFT JOIN " . Cot::$db->users . " AS u ON u.user_id = p.fieldmrkt_ownerid"
    . " " . $where_sql
    . " GROUP BY p.fieldmrkt_id"
    . " ORDER BY " . $orderby
    . " LIMIT " . (int) $d . ", " . (int) $perPage;

// Выполняем запросы с обработкой исключений
try {

    // Общее количество товаров (для пагинации)
    $totallines = (int) Cot::$db->query($sql_item_count)->fetchColumn();

    // Массив товаров текущей страницы
    $sqllist = Cot::$db->query($sql_item_string);

} catch (Exception $e) {

    // Логируем ошибку
    cot_log('SQL error in market.vendor: ' . $e->getMessage(), 'error', 'market', 'query');

    // Отдаём 500
    cot_die_message(500);
}

// Получаем все строки товаров
$sqllist_rowset = $sqllist->fetchAll();

/* =====================================================================
 * ФОРМИРОВАНИЕ URL ДЛЯ ПАГИНАЦИИ И ФИЛЬТРОВ
 * ===================================================================== */

// Базовые параметры URL витрины (без пагинации)
$list_url_path = ['m' => 'vendor', 'u' => $vendor_user['user_name']];

// Добавляем поисковый запрос
if (!empty($sq)) {
    $list_url_path['sq'] = $sq;
    $list_url_path['search_in'] = $search_in;
}

// Добавляем фильтр по категории
if (!empty($c)) {
    $list_url_path['c'] = $c;
}

// Добавляем сортировку, если она отличается от дефолтной
if ($s !== $defaultOrder) {
    $list_url_path['s'] = $s;
}

// Добавляем направление сортировки
if ($w !== $defaultOrderWay) {
    $list_url_path['w'] = $w;
}

// URL текущей витрины
$list_url = cot_url('market', $list_url_path);

/* =====================================================================
 * КАТЕГОРИИ ПРОДАВЦА
 * ---------------------------------------------------------------------
 * Показываем только те категории, в которых у продавца есть товары.
 * ===================================================================== */

// SQL-запрос: список категорий продавца с количеством товаров
$sql_vendor_cats = Cot::$db->query(
    'SELECT fieldmrkt_cat, COUNT(fieldmrkt_id) AS items_count
     FROM ' . Cot::$db->market . '
     WHERE fieldmrkt_ownerid = ?
       AND fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED . '
     GROUP BY fieldmrkt_cat
     ORDER BY items_count DESC',
    [$vendor_user_id]
)->fetchAll();

/* =====================================================================
 * СОЗДАНИЕ ШАБЛОНА
 * ===================================================================== */

// Определяем шаблон: market.vendor.tpl или специфичный для категории
$tplPartSecond = !empty($c) && isset(Cot::$structure['market'][$c]['tpl'])
    ? Cot::$structure['market'][$c]['tpl']
    : '';

// Формируем путь к файлу шаблона
$mskin = cot_tplfile(['market', 'vendor', $tplPartSecond]);
if (empty($mskin)) cot_error('Шаблон не найден');
$tpl_Path = $sys['abs_url'] . $mskin;

// Создаём объект XTemplate
$t = new XTemplate($mskin);

/* =====================================================================
 * ШАПКА ВИТРИНЫ: ДАННЫЕ ПРОДАВЦА
 * ===================================================================== */

// Формируем ссылку на профиль продавца
$vendorProfileUrl = cot_url('users', [
    'm' => 'details',
    'id' => $vendor_user_id,
    'u' => $vendor_user['user_name'],
]);

// Формируем подпись (описание) продавца из extrafields (если есть)
// просто что бы не забыть. но лучше использовать плагин 'xtradbrowusers'
// $vendorDescription = !empty($vendor_user['user_description'])
//     ? htmlspecialchars($vendor_user['user_description'])
//    : '';

// Ссылка на страницу всех витрин
$vendorsListUrl = cot_url('market', ['m' => 'vendors']);

// Формируем массив тегов шапки витрины и передаём в шаблон
$t->assign([

    // ID продавца
    'VENDOR_USER_ID' => $vendor_user_id,

    // Никнейм продавца
    'VENDOR_USERNAME' => htmlspecialchars($vendor_user['user_name']),

    // Аватар продавца что бы не забыть. просто выводим {VENDOR_USER_AVATAR} в шаблоне
    // 'VENDOR_AVATAR' => cot_build_user($vendor_user_id, $vendor_user['user_name']),

    // Описание продавца. то же самое, просто что бы не забыть. но лучше использовать плагин 'xtradbrowusers'
    // 'VENDOR_DESCRIPTION' => $vendorDescription,

    // Ссылка на витрину (текущая страница)
    'VENDOR_SHOWCASE_URL' => $list_url,

    // Ссылка на профиль пользователя
    'VENDOR_PROFILE_URL' => $vendorProfileUrl,

    // Ссылка на список всех витрин
    'VENDOR_VENDORS_LIST_URL' => $vendorsListUrl,

    // Количество опубликованных товаров
    'VENDOR_PRODUCTS_PUBLISHED' => $vendor_products_published,

    // Общее количество товаров (с учётом черновиков и модерации)
    'VENDOR_PRODUCTS_TOTAL' => $vendor_products_total,

    // Количество категорий, в которых торгует продавец
    'VENDOR_CATEGORIES_COUNT' => $vendor_categories_count,

    // Дата регистрации продавца
    'VENDOR_REGDATE' => cot_date('datetime_medium', $vendor_user['user_regdate']),
    'VENDOR_REGDATE_STAMP' => (int) $vendor_user['user_regdate'],

    // Дата последней активности
    'VENDOR_LAST_SEEN' => !empty($vendor_user['user_lastlog'])
        ? cot_date('datetime_medium', $vendor_user['user_lastlog'])
        : '',
    'VENDOR_LAST_SEEN_STAMP' => (int) $vendor_user['user_lastlog'],

    // Форма поиска
    'VENDOR_SEARCH_ACTION_URL' => cot_url('market', ['m' => 'vendor', 'u' => $vendor_user['user_name']]),
    'VENDOR_SEARCH_SQ' => cot_inputbox(
        'text',
        'sq',
        !empty($sq) ? htmlspecialchars($sq) : '',
        'class="form-control"'
    ),
    'VENDOR_SEARCH_IN_SELECT' => cot_selectbox(
        $search_in,
        'search_in',
        ['title', 'full', 'pcod'],
        [Cot::$L['market_search_in_title'], Cot::$L['market_search_in_title_and_descr'], Cot::$L['market_search_in_pcod']],
        false,
        'class="form-select"'
    ),

	// Хлебные крошки: Главная → Market → Список продавцов → Ник продавца
	'VENDOR_BREADCRUMBS' => cot_breadcrumbs([
		[cot_url('index'), Cot::$L['Main']],
		[cot_url('market'), Cot::$L['market_title_general']],
		[cot_url('market', ['m' => 'vendors']), Cot::$L['market_vendors_title']],
		htmlspecialchars($vendor_user['user_name']),
	], Cot::$cfg['homebreadcrumb'], true),
]);

// Передаём дополнительные поля пользователя (extrafields)
$t->assign(cot_generate_usertags($vendor_user, 'VENDOR_USER_'));

// Устанавливаем канонический URL страницы
Cot::$out['canonical_uri'] = $list_url;

/* === Hook === */
// Хук market.vendor.main — после подготовки данных продавца и шапки
foreach (cot_getextplugins('market.vendor.main') as $pl) {
    include $pl;
}
/* ===== */

/* =====================================================================
 * ПЕРЕДАЧА КАТЕГОРИЙ ПРОДАВЦА В ШАБЛОН
 * ---------------------------------------------------------------------
 * Строим HTML-дерево из $sql_vendor_cats. К этому моменту массив
 * может быть дополнен плагином Multicat — категориями, привязанными
 * через мультикатегории. Готовый HTML уходит в тег
 * {VENDOR_CATEGORIES_TREE}.
 * ===================================================================== */

$t->assign(
    'VENDOR_CATEGORIES_TREE',
    cot_market_build_vendor_categories_html(
        $sql_vendor_cats,
        $vendor_user['user_name'],
        $c
    )
);

/* =====================================================================
 * ПАГИНАЦИЯ ТОВАРОВ
 * ===================================================================== */

// Формируем пагинацию
$pagenav = cot_pagenav(
    'market',
    $list_url_path,
    $d,
    $totallines,
    $perPage
);

// Передаём теги пагинации в шаблон
$t->assign(cot_generatePaginationTags($pagenav));

/* =====================================================================
 * ЦИКЛ ПО ТОВАРАМ ПРОДАВЦА
 * ---------------------------------------------------------------------
 * Логика полностью совпадает с market.list.php, но выборка уже
 * отфильтрована по конкретному продавцу.
 * ===================================================================== */

// Счётчик товаров
$jj = 0;

// Список плагинов для хука market.vendor.loop
$extp = cot_getextplugins('market.vendor.loop');

// URL для возврата после операций (например, редактирования)
$backUrl = cot_url('market', array_merge($list_url_path, ['d' => $durl ?: 1]), '', true);

/* === Hook === */
// Хук market.vendor.before_loop — перед циклом вывода товаров
foreach (cot_getextplugins('market.vendor.before_loop') as $pl) {
    include $pl;
}
/* ===== */

// Перебираем товары
foreach ($sqllist_rowset as $item) {

    // Увеличиваем счётчик
    $jj++;

    // Генерируем теги товара (те же, что в market.list.php)
    $t->assign(
        cot_generate_markettags(
            $item,
            'LIST_ROW_',
            0,                    // Без обрезки текста на витрине
            Cot::$usr['isadmin'],
            false,                // Не добавлять главную в хлебные крошки
            '',
            $backUrl
        )
    );

    // Дополнительные теги для витрины продавца
    $t->assign([
        // Информация о владельце
        'LIST_ROW_OWNER' => cot_build_user($item['fieldmrkt_ownerid'], $item['user_name']),

        // Стилизация строки
        'LIST_ROW_ODDEVEN' => cot_build_oddeven($jj),

        // Порядковый номер
        'LIST_ROW_NUM' => $jj,
        'LIST_ROW_ABS_NUM' => $jj + $d,
    ]);

    // Теги пользователя
    $t->assign(cot_generate_usertags($item, 'LIST_ROW_OWNER_'));

    /* === Hook - Part2 : Include === */
    foreach ($extp as $pl) {
        include $pl;
    }
    /* ===== */

    // Парсим блок строки товара
    $t->parse('MAIN.LIST_ROW');
}

// Если товаров не найдено
if ($jj === 0) {
    // Парсим блок с сообщением
    $t->parse('MAIN.LIST_EMPTY');
}

// в шаблоне показываем путь к нему
$t->assign('TPL_PATH', $tpl_Path); 

/* =====================================================================
 * ВЫВОД СООБЩЕНИЙ И ФИНАЛЬНЫЙ ПАРСИНГ
 * ===================================================================== */

// Выводим накопленные сообщения
cot_display_messages($t);

/* === Hook === */
// Хук market.vendor.tags — перед финальным парсингом
foreach (cot_getextplugins('market.vendor.tags') as $pl) {
    include $pl;
}
/* ===== */

// Парсим основной блок
$t->parse('MAIN');

// Получаем готовый HTML
$moduleBody = $t->text('MAIN');