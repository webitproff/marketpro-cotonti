<?php
// Определение начала PHP-файла

/**
 * Store item list
 * filename market.list.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */
// Документация: описание модуля Market, его назначение, автор и лицензия

use cot\modules\market\inc\MarketDictionary;
// Подключение пространства имен для использования класса MarketDictionary

defined('COT_CODE') or die('Wrong URL');
// Проверка, определена ли константа COT_CODE, иначе прерывание с ошибкой "Wrong URL"

const COT_LIST = true;
// Установка константы COT_LIST в true, указывающей, что это режим списка

Cot::$env['location'] = 'list';
// Установка значения 'list' для переменной окружения Cotonti, указывающей текущую локацию

$s = cot_import('s', 'G', 'ALP');
// Импорт параметра сортировки 's' (имя поля без префикса 'fieldmrkt_') из GET-запроса, ожидая алфавитное значение

$w = cot_import('w', 'G', 'ALP', 4);
// Импорт параметра направления сортировки 'w' (asc или desc) из GET-запроса, ожидая алфавитное значение длиной до 4 символов

$c = cot_import('c', 'G', 'TXT');
// Импорт кода категории 'c' из GET-запроса, ожидая текстовое значение

$o = cot_import('ord', 'G', 'ARR');
// Импорт массива имен полей для фильтрации 'ord' (без префикса 'fieldmrkt_') из GET-запроса, ожидая массив

$p = cot_import('p', 'G', 'ARR');
// Импорт массива значений для фильтрации 'p' из GET-запроса, ожидая массив

// Импорт поискового запроса
// Комментарий, описывающий следующий блок импорта поискового запроса

$sq = cot_import('sq', 'G', 'TXT');
// Импорт поискового запроса 'sq' из GET-запроса, ожидая текстовое значение

$sq = ($sq !== null) ? trim($sq) : '';
// Удаление пробелов из поискового запроса и установка пустой строки, если $sq равен null

$maxItemRowsPerPage = (int) Cot::$cfg['market']['cat___default']['marketmaxlistsperpage'];
// Установка максимального количества элементов на страницу из конфигурации по умолчанию для модуля Market

if ($maxItemRowsPerPage <= 0) {
    // Проверка, если значение $maxItemRowsPerPage меньше или равно нулю
    $maxItemRowsPerPage = Cot::$cfg['marketmaxlistsperpage'];
    // Установка значения из общей конфигурации модуля Market
}

if (
    !empty($c)
    && !empty(Cot::$cfg['market']['cat_' . $c])
    && !empty(Cot::$cfg['market']['cat_' . $c]['marketmaxlistsperpage'])
) {
    // Проверка, задана ли категория, существует ли конфигурация для этой категории и задано ли количество элементов на страницу
    $maxItemRowsPerPage = (int) Cot::$cfg['market']['cat_' . $c]['marketmaxlistsperpage'];
    // Установка количества элементов на страницу из конфигурации конкретной категории
}

// item number for items list
// Комментарий, описывающий импорт номера страницы для списка товаров

list($pg, $d, $durl) = cot_import_pagenav('d', $maxItemRowsPerPage);
// Импорт данных пагинации для списка товаров: $pg (номер страницы), $d (смещение), $durl (URL-параметр) с учетом $maxItemRowsPerPage

// item number for cats list
// Комментарий, описывающий импорт номера страницы для списка категорий

list($pgc, $dc, $dcurl) = cot_import_pagenav('dc', Cot::$cfg['market']['marketmaxlistsperpage']);
// Импорт данных пагинации для списка категорий: $pgc (номер страницы), $dc (смещение), $dcurl (URL-параметр) с учетом конфигурации модуля

// Проверяем права доступа
// Комментарий, описывающий блок проверки прав доступа

if ($c === 'all' || $c === 'system') {
    // Проверка, если выбрана категория 'all' или 'system'
    list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'a');
    // Установка прав доступа для администратора
    cot_block(Cot::$usr['isadmin']);
    // Блокировка, если пользователь не администратор
} elseif ($c === 'unvalidated' || $c === 'saved_drafts') {
    // Проверка, если выбрана категория 'unvalidated' или 'saved_drafts'
    list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');
    // Установка прав доступа для любой категории модуля Market
    cot_block(Cot::$usr['auth_write']);
    // Блокировка, если пользователь не имеет прав на запись
} elseif (!empty($c) && isset(Cot::$structure['market'][$c])) {
    // Проверка, если выбрана конкретная категория и она существует в структуре модуля
    list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', $c);
    // Установка прав доступа для выбранной категории
    cot_block(Cot::$usr['auth_read']);
    // Блокировка, если пользователь не имеет прав на чтение
} else {
    // Случай, если категория не выбрана или не существует
    // Если категория не выбрана или не существует, устанавливаем права для общего доступа
    list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');
    // Установка прав доступа для любой категории модуля Market
    cot_block(Cot::$usr['auth_read']);
    // Блокировка, если пользователь не имеет прав на чтение
}

/* === Hook === */
// Комментарий, описывающий подключение хуков для плагинов

foreach (cot_getextplugins('market.list.first') as $pl) {
    // Цикл по плагинам, зарегистрированным для хука 'market.list.first'
    include $pl;
    // Подключение каждого плагина
}
/* ===== */
// Завершение блока хуков
// Инициализация пустого массива для хранения данных о категории
$cat = [];

// Проверка, указан ли код категории и существует ли она в структуре модуля Market
if (!empty($c) && isset(Cot::$structure['market'][$c])) {
    // Присваивание ссылки на данные категории из структуры
    $cat = &Cot::$structure['market'][$c];
} else {
    // Установка заголовка категории по умолчанию, если категория не указана
    $cat['title'] = $cfg['market']['marketlist_default_title'] ?: (Cot::$L['market_all_items'] ?: 'All Items');
    // Установка описания категории по умолчанию, если категория не указана
    $cat['desc'] = $cfg['market']['marketlist_default_desc'] ?: (Cot::$L['market_all_items_desc'] ?: 'All available store items');
    // Установка шаблона категории как 'all' для общего списка
    $cat['tpl'] = 'all';
}

// Получение порядка сортировки по умолчанию для текущей категории или общей настройки
$defaultOrder = !empty($c) && !empty(Cot::$cfg['market']['cat_' . $c]['marketorder'])
    ? Cot::$cfg['market']['cat_' . $c]['marketorder']
    : Cot::$cfg['market']['cat___default']['marketorder'];

// Если поле сортировки не указано, используется значение по умолчанию
if (empty($s)) {
    $s = $defaultOrder;
}

// Получение направления сортировки по умолчанию для текущей категории или общей настройки
$defaultOrderWay = !empty($c) && !empty(Cot::$cfg['market']['cat_' . $c]['marketway'])
    ? Cot::$cfg['market']['cat_' . $c]['marketway']
    : Cot::$cfg['market']['cat___default']['marketway'];

// Если направление сортировки не указано или некорректно, используется значение по умолчанию
if (empty($w) || !in_array($w, ['asc', 'desc'])) {
    $w = $defaultOrderWay;
}

// Установка длины обрезки текста для списка товаров из конфигурации по умолчанию
$itemListTruncateText = (int) Cot::$cfg['market']['cat___default']['markettruncatetext'];

// Проверка, указана ли категория и есть ли для неё настройка обрезки текста
if (
    !empty($c)
    && !empty(Cot::$cfg['market']['cat_' . $c])
    && isset(Cot::$cfg['market']['cat_' . $c]['markettruncatetext'])
    && ((string) Cot::$cfg['market']['cat_' . $c]['markettruncatetext'] !== '')
) {
    // Установка длины обрезки текста для текущей категории
    $itemListTruncateText = (int) Cot::$cfg['market']['cat_' . $c]['markettruncatetext'];
}

// Инициализация пустого массива для условий SQL-запроса
$where = [];

// Инициализация пустого массива для параметров SQL-запроса
$params = [];

// Установка базового условия для фильтрации по владельцу товара
$where_state = Cot::$usr['isadmin'] ? '1' : 'fieldmrkt_ownerid = ' . Cot::$usr['id'];

// Проверка, выбрана ли категория 'unvalidated' для неподтверждённых товаров
if ($c === 'unvalidated') {
    // Установка шаблона для неподтверждённых товаров
    $cat['tpl'] = 'unvalidated';
    // Добавление условия для фильтрации товаров в состоянии "на рассмотрении"
    $where['state'] = 'fieldmrkt_state = ' . MarketDictionary::STATE_PENDING;
    // Добавление условия для фильтрации по владельцу (для админов — все товары)
    $where['ownerid'] = Cot::$usr['isadmin'] ? '1' : 'fieldmrkt_ownerid = ' . Cot::$usr['id'];
    // Установка заголовка категории для неподтверждённых товаров
    $cat['title'] = Cot::$L['market_validation'];
    // Установка описания категории для неподтверждённых товаров
    $cat['desc'] = Cot::$L['market_validation_desc'];
    // Установка сортировки по дате
    $s = 'date';
    // Установка направления сортировки по убыванию
    $w = 'desc';
} elseif ($c === 'saved_drafts') {
    // Установка шаблона для черновиков
    $cat['tpl'] = 'unvalidated';
    // Добавление условия для фильтрации товаров в состоянии "черновик"
    $where['state'] = 'fieldmrkt_state = ' . MarketDictionary::STATE_DRAFT;
    // Добавление условия для фильтрации по владельцу (для админов — все товары)
    $where['ownerid'] = Cot::$usr['isadmin'] ? '1' : 'fieldmrkt_ownerid = ' . Cot::$usr['id'];
    // Установка заголовка категории для черновиков
    $cat['title'] = Cot::$L['market_drafts'];
    // Установка описания категории для черновиков
    $cat['desc'] = Cot::$L['market_drafts_desc'];
    // Установка сортировки по дате
    $s = 'date';
    // Установка направления сортировки по убыванию
    $w = 'desc';
} else {
    // Фильтр по категории + все подкатегории
    if (!empty($c) && isset(Cot::$structure['market'][$c])) {
        $catsub = cot_structure_children('market', $c, true);
        $catsub[] = $c; // добавляем саму категорию
        $catsub_quoted = array_map([Cot::$db, 'quote'], $catsub);
        $where['cat'] = "fieldmrkt_cat IN (" . implode(',', $catsub_quoted) . ")";
    } else {
        // Если категория не указана, берём все категории магазина
        $where['cat'] = "fieldmrkt_cat IN (SELECT structure_code FROM $db_structure WHERE structure_area = 'market')";
    }

    // Установка условия для фильтрации по состоянию товара
    if (Cot::$usr['isadmin']) {
        $where['state'] = '1';
    } else {
        $where['state'] = '(fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED
            . ' OR (fieldmrkt_state = ' . MarketDictionary::STATE_PENDING
            . ' AND fieldmrkt_ownerid = ' . Cot::$usr['id'] . '))';
    }
}

// Добавление условия для фильтрации по дате для неадминистраторов
if (!Cot::$usr['isadmin'] && $c !== 'unvalidated' && $c !== 'saved_drafts') {
    // Условие, чтобы показывались только товары с датой публикации не позже текущего времени
    $where['date'] = "fieldmrkt_date <= UNIX_TIMESTAMP()";
}

// Добавление условия для поискового запроса, если он указан
if (!empty($sq)) {
    // Условие для поиска по заголовку или тексту товара в рамках выбранной категории
    $sq_escaped = Cot::$db->quote("%$sq%");
    $where['search'] = "(fieldmrkt_title LIKE $sq_escaped OR fieldmrkt_text LIKE $sq_escaped)";
}

// Проверяем, существует ли поле для сортировки в таблице market
if (!Cot::$db->fieldExists(Cot::$db->market, "fieldmrkt_$s")) {
    // Если поле не существует, устанавливаем сортировку по умолчанию по полю 'title'
    $s = 'title';
}

// Формируем строку ORDER BY для SQL-запроса, используя поле сортировки и направление
$orderby = "fieldmrkt_$s $w";

// Финальный WHERE для запроса
$where_sql = 'WHERE ' . implode(' AND ', $where);


// Инициализируем пустой массив для параметров URL списка
$list_url_path = [];

if (!empty($sq)) {
    $list_url_path['sq'] = $sq; // Добавление sq в URL
}
// Если код категории указан, добавляем его в параметры URL
if (!empty($c)) {
    $list_url_path['c'] = $c;
}

// Если указаны поля для фильтрации, добавляем их в параметры URL
if (!empty($o)) {
    $list_url_path['ord'] = $o;
}

// Если указаны значения фильтров, добавляем их в параметры URL
if (!empty($p)) {
    $list_url_path['p'] = $p;
}

// Если поле сортировки отличается от значения по умолчанию, добавляем его в параметры URL
if ($s !== $defaultOrder) {
    $list_url_path['s'] = $s;
}

// Если направление сортировки отличается от значения по умолчанию, добавляем его в параметры URL
if ($w !== $defaultOrderWay) {
    $list_url_path['w'] = $w;
}

// Формируем URL для списка товаров на основе параметров
$list_url = cot_url('market', $list_url_path);

// Копируем параметры URL для использования в каноническом URL
$itemurl_params = $list_url_path;

// Если номер страницы для списка товаров больше 1, добавляем его в параметры канонического URL
if ($durl > 1) {
    $itemurl_params['d'] = $durl;
}

// Если номер страницы для списка категорий больше 1, добавляем его в параметры канонического URL
if ($dcurl > 1) {
    $itemurl_params['dc'] = $dcurl;
}

// Формируем массив пути категорий, если категория указана
$catpatharray = !empty($c) ? cot_structure_buildpath('market', $c) : [];

// Формируем строку хлебных крошек для категории или используем заголовок категории
$catpath = in_array($c, ['all', 'system', 'unvalidated', 'saved_drafts'], true) || empty($c)
    ? $cat['title']
    : cot_breadcrumbs($catpatharray, Cot::$cfg['homebreadcrumb'], true);

// Инициализируем массив для пути категорий в хлебных крошках
$marketCatpPath = [];

// Формируем базовый путь хлебных крошек с главной страницей и модулем Market
$marketCatpPath = [
    [cot_url('index'), $L['Main']],
    [cot_url('market'), $L['market_Market']]
];

// Если категория указана, добавляем путь текущей категории в хлебные крошки
if (!empty($c)) {
    $marketCatpPath = array_merge($marketCatpPath, cot_structure_buildpath('market', $c));
}

// Формируем HTML-код хлебных крошек на основе массива пути
$msCatpPath = cot_breadcrumbs($marketCatpPath, $cfg['homebreadcrumb'], true);

// Копируем массив пути категорий для создания укороченного пути
$shortpath = $catpatharray;

// Удаляем последний элемент из пути для создания укороченной версии хлебных крошек
array_pop($shortpath);

// Формируем укороченную версию хлебных крошек, если категория не специальная
$catpath_short = in_array($c, ['all', 'system', 'unvalidated', 'saved_drafts'], true) || empty($c)
    ? ''
    : cot_breadcrumbs($shortpath, Cot::$cfg['homebreadcrumb'], false);

// Инициализируем или используем существующие дополнительные столбцы для SQL-запроса
$join_columns = isset($join_columns) ? $join_columns : '';

// Инициализируем или используем существующие условия соединения для SQL-запроса
$join_condition = isset($join_condition) ? $join_condition : '';

/* === Hook === */
// Комментарий, описывающий подключение хуков для плагинов перед формированием SQL-запроса

foreach (cot_getextplugins('market.list.query') as $pl) {
    // Цикл по плагинам, зарегистрированным для хука 'market.list.query'
    include $pl;
    // Подключение каждого плагина
}
/* ===== */
// Завершение блока хуков
// Проверка, заданы ли поля фильтрации ($o) и их значения ($p)
if ($o && $p) {
    // Проверка, является ли $o массивом, если нет — преобразуем в массив
    if (!is_array($o)) {
        $o = [$o];
    }
    // Проверка, является ли $p массивом, если нет — преобразуем в массив
    if (!is_array($p)) {
        $p = [$p];
    }
    // Создание ассоциативного массива фильтров, где ключи — это поля, а значения — их значения
    $filters = array_combine($o, $p);
    // Перебор фильтров для обработки каждого ключа и значения
    foreach ($filters as $key => $val) {
        // Импорт ключа фильтра с очисткой, ожидается алфавитно-цифровое значение длиной до 16 символов
        $key = cot_import($key, 'D', 'ALP', 16);
        // Импорт значения фильтра с очисткой, ожидается текстовое значение длиной до 16 символов
        $val = cot_import($val, 'D', 'TXT', 16);
        // Проверка, что ключ и значение заданы и поле существует в таблице market
        if ($key && $val && Cot::$db->fieldExists(Cot::$db->market, "fieldmrkt_$key")) {
            // Добавление значения фильтра в массив параметров для SQL-запроса
            $params[$key] = $val;
            // Добавление условия фильтрации в массив условий
            $where['filter'][] = "fieldmrkt_$key = :$key";
        }
    }
    // Очистка временного массива фильтров
    $filters = [];
// Проверка, есть ли условия фильтрации
if (!empty($where['filter'])) {
    // Добавление условий фильтрации в массив $filters
    $filters[] = $where['filter'];
}
// Проверка, есть ли условие поиска
if (!empty($where['search'])) {
    // Добавление условия поиска в массив $filters
    $filters[] = $where['search'];
}
// Формирование SQL-условия WHERE, если есть фильтры
$where_sql = ($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';
}

// Проверка, пустая ли строка основного SQL-запроса
if (empty($sql_item_string)) {
    // Удаление пустых условий из массива $where
    $where = array_filter($where);
    // Формирование SQL-условия WHERE, если есть непустые условия
    $where_sql = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Формирование SQL-запроса для подсчета общего количества уникальных товаров
    $sql_item_count = "SELECT COUNT(DISTINCT p.fieldmrkt_id)
        FROM $db_market AS p
        $join_condition
        LEFT JOIN $db_users AS u ON u.user_id = p.fieldmrkt_ownerid
        $where_sql";

    // Формирование основного SQL-запроса для получения списка товаров без дублирования
    $sql_item_string = "SELECT p.*, u.* $join_columns
        FROM $db_market AS p
        $join_condition
        LEFT JOIN $db_users AS u ON u.user_id = p.fieldmrkt_ownerid
        $where_sql
        GROUP BY p.fieldmrkt_id
        ORDER BY $orderby
        LIMIT $d, $maxItemRowsPerPage";
}

// Выполнение SQL-запросов в блоке try-catch для обработки ошибок
try {
    // Выполнение запроса для подсчета общего количества товаров
    $totallines = $db->query($sql_item_count, $params)->fetchColumn();
    // Выполнение основного запроса для получения списка товаров
    $sqllist = $db->query($sql_item_string, $params);
} catch (Exception $e) {
    // Логирование ошибки SQL-запроса
    cot_log("SQL error in market list: " . $e->getMessage(), 'error', 'market', 'query');
    // Вывод сообщения об ошибке сервера (500)
    cot_die_message(500);
}

// Проверка условий для редиректа в случае некорректной пагинации
if (
    (
        // Проверка, если пагинация не упрощена, страница больше 0 и смещение некорректно
        !Cot::$cfg['easypagenav']
        && $durl > 0
        && $maxItemRowsPerPage > 0
        && $durl % $maxItemRowsPerPage > 0
    )
    || ($d > 0 && $d >= $totallines)
) {
    // Перенаправление на корректный URL списка с сохранением параметров
    cot_redirect(cot_url('market', $list_url_path + ['dc' => $dcurl]));
}

// Формирование данных для пагинации
$pagenav = cot_pagenav(
    'market',
    $list_url_path + ['dc' => $dcurl],
    $d,
    $totallines,
    $maxItemRowsPerPage
);
// Очистка и экранирование заголовка категории для безопасного вывода
$catTitle = htmlspecialchars(strip_tags($cat['title']));

// Очистка и экранирование описания категории для использования в мета-теге description
Cot::$out['desc'] = htmlspecialchars(strip_tags($cat['desc']));

// Проверка, указан ли код категории
if (!empty($c)) {
    // Установка подзаголовка страницы: заголовок категории + строка из языкового файла
    Cot::$out['subtitle'] = $catTitle . ' ' . Cot::$L['maintitle_in_list_c_empty'];
} else {
    // Очистка основного заголовка в конфигурации, если категория не указана
    Cot::$cfg['maintitle'] = '';
    // Установка подзаголовка страницы: строка из языкового файла + заголовок категории
    Cot::$out['subtitle'] = Cot::$L['maintitle_in_list_c_empty'] . ' ' . $catTitle;
}

// Проверка, указана ли категория и есть ли для неё ключевые слова в конфигурации
if (!empty($c) && !empty(Cot::$cfg['market']['cat_' . $c]['keywords'])) {
    // Установка ключевых слов для страницы из конфигурации текущей категории
    Cot::$out['keywords'] = Cot::$cfg['market']['cat_' . $c]['keywords'];
} elseif (!empty(Cot::$cfg['market']['cat___default']['keywords'])) {
    // Установка ключевых слов из конфигурации по умолчанию, если для категории они не заданы
    Cot::$out['keywords'] = Cot::$cfg['market']['cat___default']['keywords'];
}

// Проверка, указана ли категория и есть ли для неё мета-описание в конфигурации
if (!empty($c) && !empty(Cot::$cfg['market']['cat_' . $c]['metadesc'])) {
    // Установка мета-описания для страницы из конфигурации текущей категории
    Cot::$out['desc'] = Cot::$cfg['market']['cat_' . $c]['metadesc'];
}

// Проверка, пустое ли мета-описание и есть ли значение по умолчанию
if (empty(Cot::$out['desc']) && !empty(Cot::$cfg['market']['cat___default']['metadesc'])) {
    // Установка мета-описания из конфигурации по умолчанию с добавлением заголовка категории
    Cot::$out['desc'] = Cot::$cfg['market']['cat___default']['metadesc'] . ' - ' . $catTitle;
}

// Формирование канонического URL страницы на основе параметров
Cot::$out['canonical_uri'] = cot_url('market', $itemurl_params);

// Сохранение текущего кода категории в сессии
$_SESSION['cat'] = $c;

// Определение пути к файлу шаблона для отображения списка с учетом шаблона категории
$mskin = cot_tplfile(['market', 'list', $cat['tpl']]);

// Добавление номера страницы в подзаголовок, если указана страница категорий больше 1
if (!empty($pgc) && $pgc > 1) {
    Cot::$out['subtitle'] .= ' (' . $pgc . ')';
}

// Установка флага, чтобы страница индексировалась поисковиками
Cot::$sys['noindex'] = false;

// Очистка тега для отключения noindex
Cot::$R['code_noindex'] = '';

// Подключение хуков для плагинов, зарегистрированных на событие 'market.list.main'
foreach (cot_getextplugins('market.list.main') as $pl) {
    // Включение каждого плагина
    include $pl;
}

// Инициализация шаблонизатора XTemplate с указанным шаблоном
$t = new XTemplate($mskin);

// Формирование иконки категории, если она задана
$categoryIcon = !empty($cat['icon'])
    ? cot_rc(
        'img_structure_cat',
        [
            'icon' => $cat['icon'],
            'title' => htmlspecialchars($cat['title']),
            'desc' => htmlspecialchars($cat['desc']),
        ]
    )
    : '';

// Назначение переменных шаблона для отображения информации о категории
$t->assign([
    // Код текущей категории
    'LIST_CAT_CODE' => $c,
    // Экранированный заголовок категории
    'LIST_CAT_TITLE' => htmlspecialchars($cat['title']),
    // URL для RSS-ленты текущей категории
    'LIST_CAT_RSS' => cot_url('rss', ['c' => $c]),
    // Полный путь хлебных крошек для категории
    'LIST_CAT_PATH' => $catpath,
    // Укороченный путь хлебных крошек
    'LIST_CAT_PATH_SHORT' => $catpath_short,
    // URL текущего списка товаров
    'LIST_CAT_URL' => cot_url('market', $list_url_path),
    // Описание категории
    'LIST_CAT_DESCRIPTION' => $cat['desc'],
    // HTML-код иконки категории
    'LIST_CAT_ICON' => $categoryIcon,
    // Путь к файлу иконки категории
    'LIST_CAT_ICON_SRC' => !empty($cat['icon']) ? $cat['icon'] : '',
    // Полный путь хлебных крошек, включая главную страницу и модуль
    'LIST_BREADCRUMBS_FULL' => $msCatpPath,
    // Полный путь хлебных крошек для категории
    'LIST_BREADCRUMBS' => $catpath,
    // Укороченный путь хлебных крошек
    'LIST_BREADCRUMBS_SHORT' => $catpath_short,
]);

// Назначение переменных шаблона для формы поиска
$t->assign([
    // URL действия формы поиска
    "MARKET_SEARCH_ACTION_URL" => cot_url('market', '', '', true),
    // Поле ввода поискового запроса с сохранением текущего значения
    "MARKET_SEARCH_SQ" => cot_inputbox(
        'text',
        'sq',
        !empty($sq) ? htmlspecialchars($sq) : '',
        'class="schstring form-control" autofocus'
    ),
    // Выпадающий список категорий с поддержкой Select2
    "MARKET_SEARCH_CAT_SELECT2" => cot_market_selectcat_select2($c, 'c'),
]);

// Назначение тегов пагинации для шаблона
$t->assign(cot_generatePaginationTags($pagenav));

// Проверка прав на добавление товаров и отсутствие специальных категорий
if (Cot::$usr['auth_write'] && $c != 'all' && $c != 'unvalidated' && $c != 'saved_drafts') {
    // Формирование URL для добавления нового товара
    $submitNewItemUrl = cot_url('market', ['c' => $c, 'm' => 'add']);
    // Назначение переменных шаблона для кнопки добавления товара
    $t->assign([
        // HTML-код кнопки добавления нового товара
        'LIST_SUBMIT_NEW_ITEM' => cot_rc('market_submitnewitem', ['sub_url' => $submitNewItemUrl]),
        // URL для добавления нового товара
        'LIST_SUBMIT_NEW_ITEM_URL' => $submitNewItemUrl,
    ]);
}
// Проверка наличия дополнительных полей для структуры категорий
if (isset(Cot::$extrafields[Cot::$db->structure])) {
    // Перебор всех дополнительных полей структуры
    foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
        // Формирование имени дополнительного поля в верхнем регистре
        $uname = strtoupper($exfld['field_name']);
        // Получение заголовка дополнительного поля с префиксом 'structure_'
        $exfld_title = cot_extrafield_title($exfld, 'structure_');
        // Назначение тегов шаблона для дополнительного поля категории
        $t->assign([
            // Заголовок дополнительного поля
            'LIST_CAT_' . $uname . '_TITLE' => $exfld_title,
            // Форматированное значение дополнительного поля для категории
            'LIST_CAT_' . $uname => cot_build_extrafields_data('structure', $exfld, $cat[$exfld['field_name']] ?? ''),
            // Исходное значение дополнительного поля категории
            'LIST_CAT_' . $uname . '_VALUE' => $cat[$exfld['field_name']] ?? '',
        ]);
    }
}

// Инициализация массива для хранения стрелок сортировки
$arrows = [];
// Перебор дополнительных полей таблицы market и стандартных полей (title, key, date, author, owner, count, filecount)
foreach (Cot::$extrafields[Cot::$db->market] + ['title' => 'title', 'key' => 'key', 'date' => 'date', 'author' => 'author', 'owner' => 'owner', 'count' => 'count', 'filecount' => 'filecount'] as $row_k => $row_p) {
    // Формирование имени поля в верхнем регистре
    $uname = strtoupper($row_k);
    // Формирование URL для сортировки по возрастанию
    $url_asc = cot_url('market', ['s' => $row_k, 'w' => 'asc'] + $list_url_path);
    // Формирование URL для сортировки по убыванию
    $url_desc = cot_url('market', ['s' => $row_k, 'w' => 'desc'] + $list_url_path);
    // Установка иконки стрелки вниз для сортировки по возрастанию
    $arrows[$row_k]['asc'] = Cot::$R['icon_down'];
    // Установка иконки стрелки вверх для сортировки по убыванию
    $arrows[$row_k]['desc'] = Cot::$R['icon_up'];
    // Проверка, является ли текущее поле активным для сортировки
    if ($s == $row_k) {
        // Установка активной иконки для текущего направления сортировки
        $arrows[$s][$w] = Cot::$R['icon_vert_active'][$w];
    }
    // Проверка, является ли поле стандартным (title, key, date, author, owner, count, filecount)
    if (in_array($row_k, ['title', 'key', 'date', 'author', 'owner', 'count', 'filecount'])) {
        // Назначение тега шаблона для стандартного поля с иконками сортировки
        $t->assign([
            'LIST_TOP_' . $uname => cot_rc("list_link_$row_k", [
                'cot_img_down' => $arrows[$row_k]['asc'],
                'cot_img_up' => $arrows[$row_k]['desc'],
                'list_link_url_down' => $url_asc,
                'list_link_url_up' => $url_desc
            ])
        ]);
    } else {
        // Получение заголовка для пользовательского дополнительного поля
        $extratitle = isset($L['market_' . $row_k . '_title']) ? $L['market_' . $row_k . '_title'] : $row_p['field_description'];
        // Назначение тега шаблона для пользовательского поля с иконками сортировки
        $t->assign([
            'LIST_TOP_' . $uname => cot_rc('list_link_field_name', [
                'cot_img_down' => $arrows[$row_k]['asc'],
                'cot_img_up' => $arrows[$row_k]['desc'],
                'list_link_url_down' => $url_asc,
                'list_link_url_up' => $url_desc
            ])
        ]);
    }
    // Назначение URL-тегов для сортировки по возрастанию и убыванию
    $t->assign([
        'LIST_TOP_' . $uname . '_URL_ASC' => $url_asc,
        'LIST_TOP_' . $uname . '_URL_DESC' => $url_desc
    ]);
}

// Инициализация счетчика для подкатегорий
$kk = 0;
// Получение списка подкатегорий для текущей категории
$allsub = cot_structure_children('market', $c ?: '', false, false, true, false);
// Выборка подкатегорий для текущей страницы с учетом пагинации
$subcat = array_slice($allsub, $dc, Cot::$cfg['market']['marketmaxlistsperpage']);

/* === Hook === */
// Подключение хуков для плагинов, зарегистрированных на событие 'market.list.rowcat.first'
foreach (cot_getextplugins('market.list.rowcat.first') as $pl) {
    // Включение каждого плагина
    include $pl;
}
/* ===== */
// Завершение блока хуков

/* === Hook - Part1 : Set === */
// Получение списка плагинов для хука 'market.list.rowcat.loop'
$extp = cot_getextplugins('market.list.rowcat.loop');
/* ===== */
// Завершение инициализации хуков
// Перебор подкатегорий текущей категории для отображения в списке
foreach ($subcat as $x) {
    // Увеличение счетчика подкатегорий
    $kk++;
    // Получение списка дочерних категорий для текущей подкатегории
    $cat_childs = cot_structure_children('market', $x);
    // Инициализация счетчика количества элементов в дочерних категориях
    $subCategoriesCount = 0;
    // Перебор дочерних категорий для подсчета общего количества элементов
    foreach ($cat_childs as $cat_child) {
        // Добавление количества элементов в дочерней категории к общему счетчику
        $subCategoriesCount += (int) ($structure['market'][$cat_child]['count'] ?? 0);
    }

    // Копирование параметров URL для подкатегории
    $sub_url_path = $list_url_path;
    // Установка кода текущей подкатегории в параметры URL
    $sub_url_path['c'] = $x;
    // Назначение тегов шаблона для текущей подкатегории
    $t->assign([
        // ID подкатегории из структуры
        'LIST_CAT_ROW_ID' => $structure['market'][$x]['id'] ?? 0,
        // URL подкатегории
        'LIST_CAT_ROW_URL' => cot_url('market', $sub_url_path),
        // Экранированный заголовок подкатегории
        'LIST_CAT_ROW_TITLE' => htmlspecialchars($structure['market'][$x]['title'] ?? ''),
        // Описание подкатегории
        'LIST_CAT_ROW_DESCRIPTION' => $structure['market'][$x]['desc'] ?? '',
        // Иконка подкатегории, если она задана
        'LIST_CAT_ROW_ICON' => !empty($structure['market'][$x]['icon'])
            ? cot_rc(
                'img_structure_cat',
                [
                    'icon' => $structure['market'][$x]['icon'],
                    'title' => htmlspecialchars($structure['market'][$x]['title'] ?? ''),
                    'desc' => htmlspecialchars($structure['market'][$x]['desc'] ?? ''),
                ]
            )
            : '',
        // Путь к файлу иконки подкатегории
        'LIST_CAT_ROW_ICON_SRC' => !empty($structure['market'][$x]['icon']) ? $structure['market'][$x]['icon'] : '',
        // Количество элементов в подкатегории и её дочерних категориях
        'LIST_CAT_ROW_COUNT' => $subCategoriesCount,
        // Порядковый номер подкатегории в списке
        'LIST_CAT_ROW_NUM' => $kk,
    ]);

    // Проверка наличия дополнительных полей для структуры категорий
    if (!empty(Cot::$extrafields[Cot::$db->structure])) {
        // Перебор всех дополнительных полей структуры
        foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
            // Формирование имени дополнительного поля в верхнем регистре
            $uname = strtoupper($exfld['field_name']);
            // Получение заголовка дополнительного поля с префиксом 'structure_'
            $exfld_title = cot_extrafield_title($exfld, 'structure_');
            // Назначение тегов шаблона для дополнительного поля подкатегории
            $t->assign([
                // Заголовок дополнительного поля
                'LIST_CAT_ROW_' . $uname . '_TITLE' => $exfld_title,
                // Форматированное значение дополнительного поля для подкатегории
                'LIST_CAT_ROW_' . $uname => cot_build_extrafields_data('structure', $exfld,
                    Cot::$structure['market'][$x][$exfld['field_name']] ?? ''),
                // Исходное значение дополнительного поля подкатегории
                'LIST_CAT_ROW_' . $uname . '_VALUE' => Cot::$structure['market'][$x][$exfld['field_name']] ?? '',
            ]);
        }
    }

    // Подключение хуков для плагинов, зарегистрированных на событие 'market.list.rowcat.loop'
    foreach ($extp as $pl) {
        // Включение каждого плагина
        include $pl;
    }
    // Завершение блока хуков

    // Парсинг блока шаблона для текущей подкатегории
    $t->parse('MAIN.LIST_CAT_ROW');
}

// Формирование данных пагинации для списка подкатегорий
$pagenav_cat = cot_pagenav(
    'market',
    $list_url_path + ['d' => $durl],
    $dc,
    count($allsub),
    Cot::$cfg['market']['marketmaxlistsperpage'],
    'dc'
);

// Назначение тегов пагинации для шаблона подкатегорий с префиксом 'LIST_CAT_'
$t->assign(cot_generatePaginationTags($pagenav_cat, 'LIST_CAT_'));

// Инициализация счетчика для товаров в списке
$jj = 0;

// Инициализация хуков для плагинов, зарегистрированных на событие 'market.list.loop'
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('market.list.loop');
// Получение списка плагинов для хука 'market.list.loop'
/* ===== */
// Завершение инициализации хуков

// Получение всех строк результата SQL-запроса для списка товаров
$sqllist_rowset = $sqllist->fetchAll();

// Генерация сообщения о результатах поиска после выполнения всех операций
// === Генерация сообщения о поиске один раз после всех операций ===
if (!empty($sq)) {
    // Проверка, задан ли поисковый запрос
    $countResults = count($sqllist_rowset);
    // Подсчет количества найденных строк
    if ($countResults > 0) {
        // Если найдены результаты, формируем сообщение с правильным склонением
        $searchMsg = cot_declension($countResults, ['позиция', 'позиции', 'позиций']) 
            . ' найдено по запросу: <strong>' . htmlspecialchars($sq) . '</strong>';
    } else {
        // Если результаты не найдены, формируем сообщение об отсутствии результатов
        $searchMsg = 'По запросу <strong>' . htmlspecialchars($sq) . '</strong> ничего не найдено';
    }
} else {
    // Если поисковый запрос не задан, устанавливаем пустое сообщение
    $searchMsg = '';
}

// Инициализация флага для альтернативного набора строк результатов
$sqllist_rowset_other = false;

// Подключение хуков для плагинов перед началом цикла обработки товаров
/* === Hook === */
foreach (cot_getextplugins('market.list.before_loop') as $pl) {
    // Цикл по плагинам, зарегистрированным для хука 'market.list.before_loop'
    include $pl;
    // Включение каждого плагина
}
/* ===== */
// Завершение блока хуков

// Проверка, не используется ли альтернативный набор строк результатов
if (!$sqllist_rowset_other) {
    // Комментарий: действия валидации/отмены валидации находятся в контроллере админки, требуется редирект
    // Validate/Unvalidate item actions are in admin controller. We need to redirect back.
    $urlParams = $list_url_path;
    // Копирование параметров URL для списка товаров
    if ($durl > 1) {
        // Добавление параметра пагинации товаров, если страница больше 1
        $urlParams['d'] = $durl;
    }
    if ($dcurl > 1) {
        // Добавление параметра пагинации категорий, если страница больше 1
        $urlParams['dc'] = $dcurl;
    }
    // Формирование URL для возврата после действий
    $backUrl = cot_url('market', $urlParams, '', true);

    // Перебор строк результата SQL-запроса для товаров
    foreach ($sqllist_rowset as $item) {
        // Увеличение счетчика товаров
        $jj++;
        // Назначение тегов шаблона для текущего товара
        $t->assign(
            cot_generate_markettags(
                $item,
                'LIST_ROW_',
                $itemListTruncateText,
                Cot::$usr['isadmin'],
                false,
                '',
                $backUrl
            )
        );
        // Назначение дополнительных тегов для владельца товара и стиля строки
        $t->assign([
            // Информация о владельце товара
            'LIST_ROW_OWNER' => cot_build_user($item['fieldmrkt_ownerid'], $item['user_name']),
            // Стиль четной/нечетной строки
            'LIST_ROW_ODDEVEN' => cot_build_oddeven($jj),
            // Порядковый номер строки
            'LIST_ROW_NUM' => $jj,
        ]);
        // Назначение пользовательских тегов для владельца товара
        $t->assign(cot_generate_usertags($item, 'LIST_ROW_OWNER_'));

        // Подключение хуков для плагинов внутри цикла обработки товаров
        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl) {
            // Включение каждого плагина из хука 'market.list.loop'
            include $pl;
        }
        /* ===== */
        // Завершение блока хуков

        // Парсинг блока шаблона для текущего товара
        $t->parse('MAIN.LIST_ROW');
    }
}

// Обработка ошибок и сообщений для вывода в шаблоне
cot_display_messages($t);

// Подключение хуков для плагинов, зарегистрированных на событие 'market.list.tags'
/* === Hook === */
foreach (cot_getextplugins('market.list.tags') as $pl) {
    // Включение каждого плагина
    include $pl;
}
/* ===== */
// Завершение блока хуков

// Назначение тега шаблона для сообщения о результатах поиска
$t->assign('MARKET_SEARCH_RESULT_MSG', $searchMsg);

// Парсинг основного блока шаблона
$t->parse('MAIN');
// Получение готового HTML-кода шаблона
$moduleBody = $t->text('MAIN');

// Проверка условий для кэширования страницы
if (Cot::$cache && $usr['id'] === 0 && Cot::$cfg['cache_market']) {
    // Запись страницы в кэш для неавторизованных пользователей
    Cot::$cache->static->write();
}