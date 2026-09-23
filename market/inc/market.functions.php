<?php
/**
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Файл основных функций и API модуля Market.
 *
 * Содержит функции для:
 *   - работы с шаблонами заголовков и подвалов категорий;
 *   - построения выпадающих списков категорий (обычных и с Select2);
 *   - подсчёта товаров в категориях (с учётом подкатегорий);
 *   - построения дерева категорий;
 *   - обрезки текста;
 *   - генерации тегов товаров для шаблонов;
 *   - настройки сортировки и статусов;
 *   - синхронизации структуры категорий;
 *   - импорта, валидации, добавления и обновления товаров;
 *   - формирования списков товаров (виджетов) для вывода на сайте.
 *
 * Полный список функций:
 *  1. market_cat_has_header_tpl()          – проверка существования общего шаблона заголовка категории
 *  2. market_cat_has_header_tpl_pageid()   – проверка существования шаблона заголовка страницы товара
 *  3. market_cat_has_footer_tpl()          – проверка существования общего шаблона подвала категории
 *  4. market_cat_has_footer_tpl_pageid()   – проверка существования шаблона подвала страницы товара
 *  5. cot_market_selectbox_structure_select2() – рендеринг выпадающего списка категорий с Select2
 *  6. cot_market_selectcat_select2()       – рендеринг выпадающего списка категорий для поиска с Select2
 *  7. cot_market_selectcat()               – рендеринг обычного выпадающего списка категорий
 *  8. cot_market_count_with_children()     – подсчёт товаров в категории и всех подкатегориях
 *  9. cot_market_count_active_in_cat()     – подсчёт активных товаров в конкретной категории
 * 10. cot_build_structure_market_tree()    – построение иерархического дерева категорий
 * 11. cot_cut_more_market()                – обрезка текста по тегу 'more' или первой странице
 * 12. cot_readraw_market()                 – чтение содержимого файла
 * 13. cot_generate_markettags()            – генерация всех тегов товара для шаблонов
 * 14. cot_market_config_order()            – возвращает возможные варианты сортировки товаров
 * 15. cot_market_status()                  – определение статуса товара (published, draft, pending)
 * 16. cot_market_sync()                    – возвращает количество товаров в категории
 * 17. cot_market_updateStructureCounters() – пересчитывает и обновляет счётчик структуры категории
 * 18. cot_market_updatecat()               – обновляет код категории у товаров
 * 19. cot_market_url()                     – формирует URL товара
 * 20. cot_market_auth()                    – возвращает права доступа для категории
 * 21. cot_market_import()                  – импортирует данные товара из запроса
 * 22. cot_market_validate()                – валидация данных товара
 * 23. cot_market_add()                     – добавление нового товара
 * 24. cot_market_update()                  – обновление товара
 * 25. cot_market_enum()                    – генерация виджета списка товаров
 * 26. cot_market_config_main_order()       – callback для настройки сортировки на главной
 * 27. cot_getmarketlist()                  – получение списка товаров для главной страницы
 *
 * Filename: market.functions.php
 *
 * Path:    modules/market/inc/market.functions.php
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 * API Extrafields:     https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Sep 06, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */
// Импортируем класс ExtensionsDictionary для получения констант типов расширений (например, TYPE_MODULE)
use cot\extensions\ExtensionsDictionary;

// Импортируем сервис расширений для проверки активности модулей/плагинов
use cot\extensions\ExtensionsService;

// Импортируем словарь модуля Market (константы статусов и источник)
use cot\modules\market\inc\MarketDictionary;

// Импортируем сервис комментариев для работы с комментариями товаров
use cot\plugins\comments\inc\CommentsService;

// Проверяем, что константа COT_CODE определена — защита от прямого вызова файла
defined('COT_CODE') or die('Wrong URL.');

/* =====================================================================
 * ПОДКЛЮЧЕНИЕ НЕОБХОДИМЫХ ФАЙЛОВ
 * ===================================================================== */

// Подключаем языковой файл модуля Market (market.*.lang.php)
require_once cot_langfile('market', ExtensionsDictionary::TYPE_MODULE);

// Подключаем файл ресурсов модуля Market (строки-шаблоны, иконки и т.п.)
require_once cot_incfile('market', ExtensionsDictionary::TYPE_MODULE, 'resources');

// Подключаем общие функции форм (cot_inputbox, cot_shield_protect и др.)
require_once cot_incfile('forms');

// Подключаем API дополнительных полей (extrafields)
require_once cot_incfile('extrafields');

/* =====================================================================
 * РЕГИСТРАЦИЯ ТАБЛИЦ И ДОПОЛНИТЕЛЬНЫХ ПОЛЕЙ
 * ===================================================================== */

// Регистрируем таблицу market в объекте Cot::$db, чтобы можно было обращаться
// к ней через Cot::$db->market и использовать в запросах
Cot::$db->registerTable('market');

// Регистрируем таблицу market как поддерживающую дополнительные поля (extrafields),
// чтобы Cotonti могла загрузить их и использовать для этой таблицы
cot_extrafields_register_table('market');

// Проверяем, что структура категорий Market загружена как массив.
// Если она пуста или не массив, инициализируем пустым массивом, чтобы избежать
// ошибок при обращении к несуществующему индексу.
if (empty(Cot::$structure['market'])) {
    Cot::$structure['market'] = [];
}

// Функции
/**
 * Функция проверяет существование общего файла шаблона заголовка для категории маркета.
 * Имя файла формируется как header.market.{код_категории}.tpl в папке текущей темы.
 *
 * @param string $catCode Код категории
 * @return bool true, если файл существует, иначе false.
 */
function market_cat_has_header_tpl(string $catCode): bool
{
    /**
     * Если код категории пустой, шаблон не может существовать — возвращаем false.
     */
    if (empty($catCode)) return false;
    /**
     * Формируем полный путь к файлу шаблона: каталог темы + имя файла header.market.{cat}.tpl.
     */
    $tplFile = Cot::$cfg['themes_dir'] . '/' . Cot::$cfg['defaulttheme']
             . '/header.market.' . $catCode . '.tpl';
    /**
     * Проверяем наличие файла в файловой системе и возвращаем результат.
     */
    return file_exists($tplFile);
}


/**
 * Функция проверяет существование специализированного шаблона заголовка для страницы товара.
 * Имя файла: header.market.{код_категории}.pagehasid.tpl
 *
 * @param string $catCode Код категории
 * @return bool true, если файл существует, иначе false.
 */
function market_cat_has_header_tpl_pageid(string $catCode): bool
{
    /**
     * Если код категории пустой, возвращаем false.
     */
    if (empty($catCode)) return false;
    /**
     * Формируем путь к файлу: директория темы + 'header.market.' + код категории + '.pagehasid.tpl'.
     */
    $tplFile = Cot::$cfg['themes_dir'] . '/' . Cot::$cfg['defaulttheme']
             . '/header.market.' . $catCode . '.pagehasid.tpl';
    /**
     * Возвращаем true, если файл существует.
     */
    return file_exists($tplFile);
}


/**
 * Проверяет существование кастомного подвала категории: footer.market.{cat}.tpl
 */
/**
 * Проверяет существование кастомного файла подвала для категории маркета.
 * Имя файла: footer.market.{код_категории}.tpl
 *
 * @param string $catCode Код категории
 * @return bool
 */
function market_cat_has_footer_tpl(string $catCode): bool
{
    if (empty($catCode)) {
        return false;
    }
    $tplFile = Cot::$cfg['themes_dir'] . '/' . Cot::$cfg['defaulttheme']
             . '/footer.market.' . $catCode . '.tpl';
    return file_exists($tplFile);
}


/**
 * Проверяет существование кастомного файла подвала для страницы товара в категории.
 * Имя файла: footer.market.{код_категории}.pagehasid.tpl
 *
 * @param string $catCode Код категории
 * @return bool
 */
/**
 * Проверяет существование кастомного подвала страницы товара: footer.market.{cat}.pagehasid.tpl
 */
function market_cat_has_footer_tpl_pageid(string $catCode): bool
{
    if (empty($catCode)) {
        return false;
    }
    $tplFile = Cot::$cfg['themes_dir'] . '/' . Cot::$cfg['defaulttheme']
             . '/footer.market.' . $catCode . '.pagehasid.tpl';
    return file_exists($tplFile);
}


/**
 * Renders structure dropdown for cot_market_selectbox_structure_select2 with Select2 support and indented subcategories
 * не забываем о кастомном js-select2.js и блок для $('select[name="ritemmarketcat"]').select2({
 *
 * @param string $extension Extension code (например, 'market')
 * @param string $check Selected value (код выбранной категории)
 * @param string $name Dropdown name (имя поля <select>)
 * @param string $subcat Show only subcats of selected category (код родительской категории для фильтрации)
 * @param bool $hidePrivate Hide private categories (скрывать категории без прав)
 * @param bool $isModule TRUE for modules, FALSE for plugins (режим проверки прав)
 * @param bool $addEmpty Allow empty choice (добавлять пустой вариант "---")
 * @param mixed $attrs Additional attributes as an associative array or a string (дополнительные атрибуты option)
 * @param string $customRC Custom resource string name (не используется в этой реализации)
 * @return string Полный HTML-код <select>
 */
function cot_market_selectbox_structure_select2(
    $extension,
    $check,
    $name,
    $subcat = '',
    $hidePrivate = true,
    $isModule = true,
    $addEmpty = false,
    $attrs = '',
    $customRC = ''
) {
    // Подключаем глобальные переменные i18n4marketpro для доступа к текущему языку и настройкам перевода категорий
    global $i18n4marketpro_enabled, $i18n4marketpro_read, $i18n4marketpro_notmain, $i18n4marketpro_locale;

    // Получаем значение настройки черного списка категорий из конфигурации (категории, которые не должны отображаться в селекте)
    $blacklist_cfg = Cot::$cfg['market']['marketblacktreecatspage'] ?? '';

    // Преобразуем строку черного списка в массив, удаляя лишние пробелы
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    // Получаем массив категорий для указанного расширения ($extension), если его нет — пустой массив
    $categories = is_array(Cot::$structure[$extension]) ? Cot::$structure[$extension] : [];

    // Инициализируем пустую строку для накопления всех <option>
    $options = '';

    // Если параметр $addEmpty = true — добавляем пустой вариант в начало списка (аналогично примеру)
    if ($addEmpty) {
        $options .= '<option value="">---</option>';
    }

    // Определяем, активен ли перевод категорий: плагин читает переводы и текущий язык не основной (defaultlang)
    $i18n_enabled = $i18n4marketpro_read && (!empty($i18n4marketpro_locale) && $i18n4marketpro_locale != Cot::$cfg['defaultlang']);

    // Перебираем все категории расширения
    foreach ($categories as $code => $category) {
        // Если код категории в черном списке — пропускаем её полностью
        if (in_array($code, $blacklist)) {
            continue;
        }

        // Проверяем права: для модулей ($isModule = true) проверяем право на запись ('W'), если $hidePrivate = true
        $display = ($hidePrivate && $isModule) ? cot_auth($extension, $code, 'W') : true;

        // Если указана родительская категория ($subcat) — фильтруем только её подкатегории или саму себя
        if ($display && !empty($subcat) && isset(Cot::$structure[$extension][$subcat])) {
            // Формируем префикс пути родителя с точкой на конце
            $mtch = Cot::$structure[$extension][$subcat]['path'] . '.';
            // Длина префикса
            $mtchlen = mb_strlen($mtch);
            // Сравниваем путь текущей категории с префиксом родителя или проверяем точное совпадение кода
            $display = (mb_substr($category['path'], 0, $mtchlen) == $mtch || $code === $subcat);
        }

        // Финальная проверка: права на чтение (для модулей), код не 'all', и категория должна отображаться
        if ((!$isModule || cot_auth($extension, $code, 'R')) && $code !== 'all' && $display) {
            // Берём оригинальное название категории из структуры
            $title = $category['title'];
			if (cot_plugin_active('i18n4marketpro')) {
				// Если перевод активен — пытаемся получить переведённое название через i18n4marketpro
				if ($i18n_enabled) {
					$translated_cat = cot_i18n4marketpro_get_cat($code, $i18n4marketpro_locale);
					// Если перевод существует и поле title не пустое — используем его
					if ($translated_cat && !empty($translated_cat['title'])) {
						$title = $translated_cat['title'];
					}
				}
			}
			

            // Вычисляем глубину вложенности категории по количеству точек в пути (для data-depth)
            $depth = substr_count($category['path'], '.');

            // Определяем, выбрана ли эта категория (добавляем атрибут selected)
            $selected = ($code === $check) ? ' selected' : '';

            // Преобразуем дополнительные атрибуты в строку (если массив — через cot_rc_attr_string)
            $attrs_str = is_array($attrs) ? cot_rc_attr_string($attrs) : $attrs;

            // Формируем полный тег <option> с value, data-depth, selected и атрибутами
            $options .= '<option value="' . htmlspecialchars($code) . '" data-depth="' . $depth . '"' . $selected . ' ' . $attrs_str . '>' .
                        htmlspecialchars($title) . '</option>';
        }
    }
    // Этот блок обязателен — Cotonti ищет плагины для хука 'selectBox.structure' и включает их
    // Удалять нельзя, иначе хуки не сработают
    /* === Hook === */ 
    foreach (cot_getextplugins('selectBox.structure') as $pl) {
        include $pl;
    }
    /* ===== */

    // Возвращаем готовый <select> с классом Bootstrap и всеми option внутри.
    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . $options . '</select>';
}

/**
 * Select page cat for search form. Используется с Select2
 *
 * @global array $structure
 * @param string $check Selected category code
 * @param string $name Name of the select input
 * @param string $subcat Parent category code for filtering subcategories
 * @param bool $hideprivate Hide private categories
 * @return string
 */
function cot_market_selectcat_select2($check, $name, $subcat = '', $hideprivate = true)
{
    global $structure, $cfg;
    // ДОПОЛНЕНИЕ: глобальные переменные i18n4marketpro для проверки и перевода категорий
    global $i18n4marketpro_enabled, $i18n4marketpro_read, $i18n4marketpro_notmain, $i18n4marketpro_locale;

    // Получаем черный список категорий из конфигурации
    $blacklist_cfg = Cot::$cfg['market']['marketblacktreecatspage'] ?? '';
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    // Проверяем, что массив категорий существует
    $structure['market'] = is_array($structure['market']) ? $structure['market'] : [];

    // Переменная для накопления всех option'ов
    $options = '';

    // Добавляем пустой вариант для "Все категории"
    $options .= '<option value=""' . (empty($check) ? ' selected' : '') . '>Все категории</option>';

    // ДОПОЛНЕНИЕ: определяем, включён ли перевод для категорий структуры
    $i18n_enabled = $i18n4marketpro_read && (!empty($i18n4marketpro_locale) && $i18n4marketpro_locale != Cot::$cfg['defaultlang']);

    // Перебираем все категории в разделе 'market'
    foreach ($structure['market'] as $i => $x) {
        // Пропускаем категории из черного списка
        if (in_array($i, $blacklist)) {
            continue;
        }

        // Проверяем права на чтение категории
        $display = $hideprivate ? cot_auth('market', $i, 'R') : true;

        // Фильтрация по родительской категории (если указана)
        if ($display && !empty($subcat) && isset($structure['market'][$subcat])) {
            $mtch = $structure['market'][$subcat]['path'] . ".";
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        // Пропускаем системную категорию 'all'
        if ($display && $i !== 'all') {
            // ДОПОЛНЕНИЕ: перевод названия категории, если i18n активен и есть перевод
            $title = $x['title']; // оригинал из структуры
			if (cot_plugin_active('i18n4marketpro')) {
				// Если перевод активен — пытаемся получить переведённое название через i18n4marketpro
				if ($i18n_enabled) {
					$translated_cat = cot_i18n4marketpro_get_cat($i, $i18n4marketpro_locale);
					if ($translated_cat && !empty($translated_cat['title'])) {
						$title = $translated_cat['title']; // используем переведённое название
					}
				}
			}


            // Глубина вложенности для отступов в Select2
            $depth = substr_count($x['path'], '.');

            // Выбрана ли категория
            $selected = ($i == $check) ? ' selected' : '';

            // Формируем option
            $options .= '<option value="' . htmlspecialchars($i) . '" data-depth="' . $depth . '"' . $selected . '>' .
                        htmlspecialchars($title) . '</option>';
        }
    }

    // Возвращаем полный <select> с классом Bootstrap
    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . $options . '</select>';
}


/**
 * Select page cat for search form
 * 
 * @global array $structure
 * @param type $check
 * @param type $name
 * @param type $subcat
 * @param type $hideprivate
 * @return string
 */
function cot_market_selectcat($check, $name, $subcat = '', $hideprivate = true)
{
    global $structure;

    // Load blacklist from configuration
    $blacklist_cfg = $cfg['market']['marketblacktreecatspage'] ?? '';
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    $structure['market'] = (is_array($structure['market'])) ? $structure['market'] : array();

    $result_array = array();
    foreach ($structure['market'] as $i => $x)
    {
        // Skip categories in blacklist
        if (in_array($i, $blacklist)) {
            continue;
        }

        $display = ($hideprivate) ? cot_auth('market', $i, 'R') : true;
        if ($display && !empty($subcat) && isset($structure['market'][$subcat]))
        {
            $mtch = $structure['market'][$subcat]['path'].".";
            $mtchlen = mb_strlen($mtch);
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        if (cot_auth('market', $i, 'R') && $i != 'all' && $display) {
            $result_array[$i] = $x['tpath'];
        }
    }

    return cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), true);
}

/**
 * Считает количество товаров в категории и во всех её потомках
 *
 * @param string $cat Код категории
 * @return int
 *
 * Cotonti / PHP 8.5+
 */
function cot_market_count_with_children($cat)
{
    global $structure, $db, $db_market;

    // базовая валидация
    if (
        empty($cat)
        || empty($structure['market'])
        || !isset($structure['market'][$cat])
        || empty($structure['market'][$cat]['path'])
    ) {
        return 0;
    }

    $parentPath = (string) $structure['market'][$cat]['path'];

    // собираем все дочерние категории
    $cats = [$cat];
    foreach ($structure['market'] as $code => $data) {

        // защита от null / отсутствующих path
        if (empty($data['path'])) {
            continue;
        }

        // дочерние категории по path
        if (strpos((string)$data['path'], $parentPath . '.') === 0) {
            $cats[] = $code;
        }
    }

    if (empty($cats)) {
        return 0;
    }

    // плейсхолдеры
    $placeholders = implode(',', array_fill(0, count($cats), '?'));

    $sql = "
        SELECT COUNT(*)
        FROM $db_market
        WHERE fieldmrkt_state = 0
          AND fieldmrkt_cat IN ($placeholders)
    ";

    return (int) $db->query($sql, $cats)->fetchColumn();
}

/**
 * Считает количество товаров в конкретной категории 
 *
 * @param string $cat Код категории
 * @return int
 *
 * Cotonti / PHP 8.5+
 */

function cot_market_count_active_in_cat($cat)
{
    global $db, $db_market;

    if (empty($cat)) {
        return 0;
    }

    return (int) $db->query(
        "SELECT COUNT(*) 
         FROM $db_market 
         WHERE fieldmrkt_state = 0 
           AND fieldmrkt_cat = ?",
        [$cat]
    )->fetchColumn();
}



/**
 * Формирует иерархическую структуру дерева категорий для модуля market
 *
 * @param string $parent Код родительской категории, пустой для корневого уровня
 * @param string|array $selected Код(ы) выбранной категории для подсветки (строка или массив)
 * @param int $level Текущий уровень в иерархии категорий
 * @param string $template Файл шаблона для использования (зарезервировано)
 * @return string|bool Отрендеренный HTML для дерева категорий или false, если нет дочерних элементов
 */
function cot_build_structure_market_tree($parent = '', $selected = '', $level = 0, $template = '')
{
    global $structure, $cfg, $db, $sys, $cot_extrafields, $db_structure, $db_market;
    global $i18n4marketpro_notmain, $i18n4marketpro_locale, $i18n4marketpro_write, $i18n4marketpro_admin, $i18n4marketpro_read, $db_i18n4marketpro_pages;

    $blacklist_cfg = $cfg['market']['marketblacktreecatspage'] ?? '';
	
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    $urlparams = [];

    /* === Hook === */
    foreach (cot_getextplugins('market.tree.first') as $pl)
    {
        include $pl;
    }
    /* ===== */

    if (empty($parent))
    {
        $i18n4marketpro_enabled = $i18n4marketpro_read;
        $children = [];
        $allcat = cot_structure_children('market', '');
        foreach ($allcat as $x)
        {
            if (
                mb_substr_count($structure['market'][$x]['path'], ".") == 0 &&
                !in_array($x, $blacklist)
            ) {
                $children[] = $x;
            }
        }
    }
    else
    {
        $i18n4marketpro_enabled = $i18n4marketpro_read && cot_i18n4marketpro_enabled($parent);
        $children = array_filter($structure['market'][$parent]['subcats'] ?? [], function($cat) use ($blacklist) {
            return !in_array($cat, $blacklist);
        });
    }

    $mskin = cot_tplfile(['market', 'tree', $template], 'module');
    $t1 = new XTemplate($mskin);

    /* === Hook === */
    foreach (cot_getextplugins('market.tree.main') as $pl)
    {
        include $pl;
    }
    /* ===== */

    if (count($children) == 0)
    {
        return false;
    }

    $total_count = 0;
    if ($db->tableExists($db_market)) {
        $result = $db->query("SELECT COUNT(*) AS total FROM $db_market WHERE fieldmrkt_state = 0")->fetch();
        $total_count = $result['total'] ?? 0;
    }

    $title = '';
    $desc = '';
    $count = 0;
    $icon = '';
    if (!empty($parent) && isset($structure['market'][$parent])) {
        $title = $structure['market'][$parent]['title'];
        $desc  = $structure['market'][$parent]['desc'];
        $count = $structure['market'][$parent]['count'];
        $icon  = $structure['market'][$parent]['icon'];
    }

    $t1->assign([
        "TITLE" => htmlspecialchars($title),
        "DESC" => $desc,
        "COUNT" => $count,
        "ICON" => $icon,
        "HREF" => cot_url("market", $urlparams + ['c' => $parent]),
        "LEVEL" => $level,
        "TOTAL_COUNT" => $total_count,
    ]);

    $jj = 0;

    /* === Hook - Part1 : Set === */
    $extp = cot_getextplugins('market.tree.loop');
    /* ===== */

    foreach ($children as $row)
    {
        if (in_array($row, $blacklist)) {
            continue;
        }

        $jj++;
        $urlparams['c'] = $row;
        $subcats = !empty($structure['market'][$row]['subcats']) ? array_filter($structure['market'][$row]['subcats'], function($cat) use ($blacklist) {
            return !in_array($cat, $blacklist);
        }) : [];
		
		//Считает количество товаров в категории и во всех её потомках fieldmrkt_state = 0
		$parent_count = cot_market_count_with_children($row);
		
		// Считает количество товаров в конкретной категории fieldmrkt_state = 0
		$row_count = cot_market_count_active_in_cat($row);

        $t1->assign([
            "ROW_ID" => $row,
            "ROW_TITLE" => htmlspecialchars($structure['market'][$row]['title']),
            "ROW_DESC" => $structure['market'][$row]['desc'],
			"ROW_COUNT" => $row_count,
			"ROW_PARENT_COUNT" => $parent_count ? $parent_count : '',
            "ROW_ICON" => $structure['market'][$row]['icon'],
            "ROW_HREF" => cot_url("market", $urlparams),
            "ROW_SELECTED" => (!empty($selected) && (strpos($selected, $row) === 0 || $selected === $row)) ? 1 : 0,
			"ROW_SUBCAT" => !empty($subcats) ? cot_build_structure_market_tree($row, $selected, $level + 1, $template) : '',
            "ROW_LEVEL" => $level,
            "ROW_ODDEVEN" => cot_build_oddeven($jj),
            "ROW_JJ" => $jj
        ]);

        foreach ($cot_extrafields[$db_structure] as $exfld)
        {
            $uname = strtoupper($exfld['field_name']);
            $t1->assign([
                'ROW_'.$uname.'_TITLE' => isset($L['structure_'.$exfld['field_name'].'_title']) ? $L['structure_'.$exfld['field_name'].'_title'] : $exfld['field_description'],
                'ROW_'.$uname => cot_build_extrafields_data('structure', $exfld, $structure['market'][$row][$exfld['field_name']]),
                'ROW_'.$uname.'_VALUE' => $structure['market'][$row][$exfld['field_name']],
            ]);
        }

        if ($i18n4marketpro_enabled && $i18n4marketpro_notmain){
            $x_i18n4marketpro = cot_i18n4marketpro_get_cat($row, $i18n4marketpro_locale);
            if ($x_i18n4marketpro){
                if(!$cfg['plugin']['i18n']['omitmain'] || $i18n4marketpro_locale != $cfg['defaultlang']){
                    $urlparams['l'] = $i18n4marketpro_locale;
                }
                $t1->assign([
                    'ROW_URL' => cot_url('market', $urlparams),
                    'ROW_TITLE' => $x_i18n4marketpro['title'],
                    'ROW_DESC' => $x_i18n4marketpro['desc'],
                ]);
            }
        }

        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl)
        {
            include $pl;
        }
        /* ===== */

        $t1->parse("MAIN.CATS");
    }

    if ($jj == 0)
    {
        return false;
    }

    $t1->parse("MAIN");
    return $t1->text("MAIN");
}

/**
 * Cuts the store item after 'more' tag or after the first page (if multipage)
 *
 * @param string $html Store item body
 * @return string
 */
function cot_cut_more_market($html)
{
	$mpos = mb_strpos($html, '<!--more-->');
	if ($mpos === false) {
		$mpos = mb_strpos($html, '[more]');
	}
	if ($mpos === false) {
        if (preg_match('#<hr *class="more" */?>#', $html, $matches, PREG_OFFSET_CAPTURE)) {
            $mpos = $matches[0][1];
        }
	}
	if ($mpos !== false) {
		$html = mb_substr($html, 0, $mpos);
	}
	$mpos = mb_strpos($html, '[newpage]');
	if ($mpos !== false) {
		$html = mb_substr($html, 0, $mpos);
	}
	if (mb_strpos($html, '[title]')) {
		$html = preg_replace('#\[title\](.*?)\[/title\][\s\r\n]*(<br />)?#i', '', $html);
	}
	return $html;
}

/**
 * Reads raw data from file
 *
 * @param string $file File path
 * @return string
 */
function cot_readraw_market($file)
{
	return (mb_strpos($file, '..') === false && file_exists($file)) ? file_get_contents($file) : 'File not found : '.$file; // TODO need translate
}

/**
 * Returns all store item tags for coTemplate
 *
 * @param int|array $item_data Store item Info Array or ID
 * @param string $tag_prefix Prefix for tags
 * @param int $textLength Text truncate
 * @param bool $admin_rights Store item Admin Rights
 * @param bool $pagepath_home Add home link for store item path
 * @param string $emptytitle Store item title text if item does not exist
 * @param string $backUrl BackUrl for store item validate actions
 *
 * @return array|null
 * @global CotDB $db
 */
function cot_generate_markettags(
    $item_data,
    $tag_prefix = '',
    $textLength = 0,
    $admin_rights = null,
    $pagepath_home = false,
    $emptytitle = '',
    $backUrl = null
) {
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R, $cfg;

	global $db, $cot_extrafields, $db_market, $usr, $sys, $cot_yesno, $structure, $db_structure;

	static $extp_first = null, $extp_main = null;
	static $market_auth = [];

	if (is_null($extp_first)) {
		$extp_first = cot_getextplugins('markettags.first');
		$extp_main = cot_getextplugins('markettags.main');
	}

	/* === Hook === */
	foreach ($extp_first as $pl) {
		include $pl;
	}
	/* ===== */

	if (!empty($item_data) && !is_array($item_data)) {
        $itemID = (int) $item_data;
        $item_data = null;
        if ($itemID > 0) {
            $sql = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ? LIMIT 1', $itemID);
            $item_data = $sql->fetch();
        }
	}

    if (empty($item_data)) {
        return null;
    }

	if ($item_data['fieldmrkt_id'] > 0 && !empty($item_data['fieldmrkt_title'])) {
		if (is_null($admin_rights)) {
			if (!isset($market_auth[$item_data['fieldmrkt_cat']])) {
				$market_auth[$item_data['fieldmrkt_cat']] = cot_auth('market', $item_data['fieldmrkt_cat'], 'RWA1');
			}
			$admin_rights = (bool) $market_auth[$item_data['fieldmrkt_cat']][2];
		}
		$pagepath = cot_structure_buildpath('market', $item_data['fieldmrkt_cat']);
		$catpath = cot_breadcrumbs($pagepath, $pagepath_home, false);
        $item_data['fieldmrkt_pageurl'] = cot_market_url($item_data);
		$pageLink = [[$item_data['fieldmrkt_pageurl'], $item_data['fieldmrkt_title']]];
		$breadcrumbs = cot_breadcrumbs(array_merge($pagepath, $pageLink), $pagepath_home);


		$date_format = 'datetime_medium';

		$text = cot_parse($item_data['fieldmrkt_text'], $cfg['market']['marketmarkup'], $item_data['fieldmrkt_parser']);
		$text_cut = cot_cut_more_market($text);
		if ($textLength > 0 && mb_strlen($text_cut) > $textLength) {
			$text_cut = cot_string_truncate($text_cut, $textLength);
		}
		$cutted = mb_strlen($text) > mb_strlen($text_cut);

		$cat_url = cot_url('market', ['c' => $item_data['fieldmrkt_cat']]);

        $urlParams = [
            'm' => 'market',
            'a' => 'validate',
            'id' => $item_data['fieldmrkt_id'],
            'x' => Cot::$sys['xk'],
        ];
        if (!empty($backUrl)) {
            $urlParams['back'] = base64_encode($backUrl);
        }
		$validate_url = cot_url('admin', $urlParams);

        $urlParams['a'] = 'unvalidate';
		$unvalidate_url = cot_url('admin', $urlParams);

		$edit_url = cot_url('market', "m=edit&id={$item_data['fieldmrkt_id']}");
		$delete_url = cot_url('market', "m=edit&a=update&delete=1&id={$item_data['fieldmrkt_id']}&x={$sys['xk']}");

		$item_data['fieldmrkt_status'] = cot_market_status(
			$item_data['fieldmrkt_state'],
		);

        $catTitle = isset($structure['market'][$item_data['fieldmrkt_cat']]['title'])
            ? htmlspecialchars($structure['market'][$item_data['fieldmrkt_cat']]['title'])
            : '';
        $catDescription = isset($structure['market'][$item_data['fieldmrkt_cat']]['desc'])
            ? $structure['market'][$item_data['fieldmrkt_cat']]['desc']
            : '';
        $itemDescription = (isset($item_data['fieldmrkt_desc']) && $item_data['fieldmrkt_desc'] !== '')
            ? htmlspecialchars($item_data['fieldmrkt_desc'])
            : '';
        $temp_array = [
			'URL' => $item_data['fieldmrkt_pageurl'],
			'ID' => $item_data['fieldmrkt_id'],
			'TITLE' => htmlspecialchars($item_data['fieldmrkt_title'], ENT_COMPAT, 'UTF-8', false),
			'META_H1' => htmlspecialchars($item_data['fieldmrkt_metah1'], ENT_COMPAT, 'UTF-8', false),
			'META_TITLE' => htmlspecialchars($item_data['fieldmrkt_metatitle'], ENT_COMPAT, 'UTF-8', false),
            'BREADCRUMBS' => $breadcrumbs,
			'BREADCRUMBS_ITEM' => cot_breadcrumbs(
				array_merge(
					[[cot_url('index'), Cot::$L['Main']]],
					[[cot_url('market'), Cot::$L['market_title_general']]],
					cot_structure_buildpath('market', $item_data['fieldmrkt_cat']),
					[htmlspecialchars($item_data['fieldmrkt_title'], ENT_QUOTES, 'UTF-8')]
				),
				$pagepath_home,
				false
			),
			'ALIAS' => $item_data['fieldmrkt_alias'],
			'PCOD' => $item_data['fieldmrkt_pcod'],
			'STATE' => $item_data['fieldmrkt_state'],
			'STATUS' => $item_data['fieldmrkt_status'],
			'LOCAL_STATUS' => $L['market_status_' . $item_data['fieldmrkt_status']],
			'CAT' => $item_data['fieldmrkt_cat'],
			'CAT_URL' => $cat_url,
			'CAT_TITLE' => $catTitle,
			'CAT_PATH' => $catpath,
			'CAT_PATH_SHORT' => cot_rc_link($cat_url, $catTitle),
			'CAT_DESCRIPTION' => $catDescription,
			'CAT_ICON' => !empty($structure['market'][$item_data['fieldmrkt_cat']]['icon'])
                ? cot_rc(
                    'img_structure_cat',
                    [
                        'icon' => $structure['market'][$item_data['fieldmrkt_cat']]['icon'],
                        'title' => $catTitle,
                        'desc' => htmlspecialchars($catDescription),
                    ]
                )
                : '',
            'CAT_ICON_SRC' => isset($structure['market'][$item_data['fieldmrkt_cat']]['icon'])
                ? $structure['market'][$item_data['fieldmrkt_cat']]['icon']
                : '',

			'DESCRIPTION' => $itemDescription,
			'TEXT' => $text,
			'TEXT_SHORT' => cot_cutstring(strip_tags($item_data['fieldmrkt_text']), 250),
			'TEXT_CUT' => $text_cut,
			'TEXT_IS_CUT' => $cutted,
			'DESCRIPTION_OR_TEXT' => $itemDescription !== '' ? $itemDescription : $text,
			'DESCRIPTION_OR_TEXT_CUT' => $itemDescription !== '' ? $itemDescription : $text_cut,
			'MORE' => ($cutted) ? cot_rc('list_more', ['page_url' => $item_data['fieldmrkt_pageurl']]) : '',
			'AUTHOR' => (isset($item_data['fieldmrkt_author']) && $item_data['fieldmrkt_author'] != '')
                ? htmlspecialchars($item_data['fieldmrkt_author'])
                : '',
			'OWNER_ID' => $item_data['fieldmrkt_ownerid'],
			'OWNER_NAME' => (isset($item_data['user_name']) && $item_data['user_name'] != '')
                ? htmlspecialchars($item_data['user_name'])
                : '',
			'COSTDFLT' => (floor($item_data['fieldmrkt_costdflt']) != $item_data['fieldmrkt_costdflt']) ? number_format($item_data['fieldmrkt_costdflt'], '2', '.', ' ') : number_format($item_data['fieldmrkt_costdflt'], '0', '.', ' '),
			'COST_USD' => (float)$item_data['fieldmrkt_cost_usd'],
			'COST_USD_RAW' => rtrim(rtrim(number_format((float)$item_data['fieldmrkt_cost_usd'], 2, '.', ''), '0'), '.'),
			'COST_USD_FORMATTED' => $item_data['fieldmrkt_cost_usd'] > 0 
				? number_format($item_data['fieldmrkt_cost_usd'], 2, '.', ' ') 
				: '',
            'CREATED' => cot_date($date_format, $item_data['fieldmrkt_date']),
			'UPDATED' => cot_date($date_format, $item_data['fieldmrkt_updated']),
            'CREATED_STAMP' => $item_data['fieldmrkt_date'],
			'UPDATED_STAMP' => $item_data['fieldmrkt_updated'],
			'HITS' => $item_data['fieldmrkt_count'],
            'ADMIN' => $admin_rights
                ? cot_rc('list_row_admin', ['unvalidate_url' => $unvalidate_url, 'edit_url' => $edit_url])
                : '',
		];

		// Admin tags
		if ($admin_rights) {
			$validate_confirm_url = cot_confirm_url($validate_url, 'market', 'market_confirm_validate');
			$unvalidate_confirm_url = cot_confirm_url($unvalidate_url, 'market', 'market_confirm_unvalidate');
			$delete_confirm_url = cot_confirm_url($delete_url, 'market', 'market_confirm_delete');
			$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, Cot::$L['Edit']);
			$temp_array['ADMIN_EDIT_URL'] = $edit_url;
			$temp_array['ADMIN_UNVALIDATE'] = $item_data['fieldmrkt_state'] == MarketDictionary::STATE_PENDING
                ? cot_rc_link($validate_confirm_url, Cot::$L['Validate'], 'class="confirmLink"')
                : cot_rc_link($unvalidate_confirm_url, Cot::$L['Putinvalidationqueue'], 'class="confirmLink"');
			$temp_array['ADMIN_UNVALIDATE_URL'] = $item_data['fieldmrkt_state'] == 1 ?
				$validate_confirm_url : $unvalidate_confirm_url;
			$temp_array['ADMIN_DELETE'] = cot_rc_link($delete_confirm_url, $L['Delete'], 'class="confirmLink"');
			$temp_array['ADMIN_DELETE_URL'] = $delete_confirm_url;
		} elseif ($usr['id'] == $item_data['fieldmrkt_ownerid']) {
			$temp_array['ADMIN_EDIT'] = cot_rc_link($edit_url, $L['Edit']);
			$temp_array['ADMIN_EDIT_URL'] = $edit_url;
		}

		if (cot_auth('market', 'any', 'W')) {
			$clone_url = cot_url('market', "m=add&c={$item_data['fieldmrkt_cat']}&clone={$item_data['fieldmrkt_id']}");
			$temp_array['ADMIN_CLONE'] = cot_rc_link($clone_url, $L['market_clone']);
			$temp_array['ADMIN_CLONE_URL'] = $clone_url;
		}

		// Extrafields
        if (!empty(Cot::$extrafields[Cot::$db->market])) {
            foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
				$tag = mb_strtoupper($exfld['field_name']);
                $exfld_title = cot_extrafield_title($exfld, 'market_');

				$temp_array[$tag . '_TITLE'] = $exfld_title;
                $temp_value = null;
                if (isset($item_data['fieldmrkt_'.$exfld['field_name']])) {
                    $temp_value = $item_data['fieldmrkt_'.$exfld['field_name']];
                }
				$temp_array[$tag] = cot_build_extrafields_data('market', $exfld, $temp_value, $item_data['fieldmrkt_parser']);
				$temp_array[$tag . '_VALUE'] = $temp_value;
			}
		}

		// Extra fields for structure
		if (isset(Cot::$extrafields[Cot::$db->structure])) {
			foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
				$tag = mb_strtoupper($exfld['field_name']);
                $exfld_title = cot_extrafield_title($exfld, 'structure_');

				$temp_array['CAT_' . $tag . '_TITLE'] = $exfld_title;
                $temp_value = null;
                if (isset(Cot::$structure['market'][$item_data['fieldmrkt_cat']][$exfld['field_name']])) {
                    $temp_value = Cot::$structure['market'][$item_data['fieldmrkt_cat']][$exfld['field_name']];
                }
				$temp_array['CAT_' . $tag] = cot_build_extrafields_data('structure', $exfld, $temp_value);
				$temp_array['CAT_' . $tag.'_VALUE'] = $temp_value;
			}
		}

		/* === Hook === */
		foreach ($extp_main as $pl) {
			include $pl;
		}
		/* ===== */

	} else {
		$temp_array = [
			'TITLE' => (!empty($emptytitle)) ? $emptytitle : Cot::$L['Deleted'],
		];
	}

	$return_array = [];
	foreach ($temp_array as $key => $val) {
		$return_array[$tag_prefix . $key] = $val;
	}

	return $return_array;
}

/**
 * Possible values for category sorting order
 * @param bool $adminpart Call from admin part
 * @return array
 */
function cot_market_config_order($adminpart = false)
{
	global $cot_extrafields, $L, $db_market;

	$options_sort = [
		'id' => $L['Id'],
		'title' => $L['Title'],
		'desc' => $L['Description'],
		'text' => $L['Body'],
		'ownerid' => $L['Owner'],
		'date' => $L['Date'],
		'count' => $L['Count'],
		'updated' => $L['Updated'],
		'cat' => $L['Category']
	];

	foreach($cot_extrafields[$db_market] as $exfld) {
		$options_sort[$exfld['field_name']] = isset($L['market_'.$exfld['field_name'].'_title']) ? $L['market_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	}

	if ($adminpart || version_compare('0.9.19', Cot::$cfg['version']) < 1) {
		return $options_sort;
	} else {
		// old style trick, will be removed in next versions
		$L['cfg_order_params'] = array_values($options_sort);
		return array_keys($options_sort);
	}
}
/**
 * Determines store item status
 *
 * @param int $fieldmrkt_state
 * @return string 'draft', 'pending' or 'published'
 */
function cot_market_status($fieldmrkt_state)
{
	if ($fieldmrkt_state == 0) {
		return 'published';
	} elseif ($fieldmrkt_state == 2) {
		return 'draft';
	}
	return 'pending';
}

/**
 * Returns store item category counter
 * Used in Admin/Structure/Resync All
 *
 * @param string $category Category code
 * @return int
 */
function cot_market_sync($category)
{
    if (empty($category)) {
        return 0;
    }

    return (int) Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->market) .
        ' WHERE fieldmrkt_cat=?',
        $category
    )->fetchColumn();
}

/**
 * Recalculate and update structure counters
 * @param string $category Category code
 * @return void
 */
function cot_market_updateStructureCounters($category)
{
    if (empty($category) || empty(Cot::$structure['market'][$category])) {
        return;
    }

    $count = cot_market_sync($category);

    Cot::$db->query('UPDATE ' . Cot::$db->quoteTableName(Cot::$db->structure) .
        ' SET structure_count = ' . $count .
        " WHERE structure_area='market' AND structure_code = :category", ['category' => $category]);

    if (Cot::$cache) {
        Cot::$cache->db->remove('structure', 'system');
    }
}

/**
 * Update store item category code
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 * @global CotDB $db
 */
function cot_market_updatecat($oldcat, $newcat)
{
	global $db, $db_structure, $db_market;
	return (bool) $db->update($db_market, ["fieldmrkt_cat" => $newcat], "fieldmrkt_cat='".$db->prep($oldcat)."'");
}


/**
 * Url address of the store item
 *
 * @param array $data Store item data as array
 * @param array $params Additional URL Parameters
 * @param string $tail URL postfix, e.g. anchor
 * @param bool $htmlspecialcharsBypass If TRUE, will not convert & to & and so on.
 * @param bool $ignoreAppendix If TRUE, $cot_url_appendix will be ignored for this URL
 * @return string Valid HTTP URL
 */
function cot_market_url($data, $params = [], $tail = '', $htmlspecialcharsBypass = false, $ignoreAppendix = false)
{
    $urlParams = ['c' => $data['fieldmrkt_cat']];
    if (!empty($data['fieldmrkt_alias'])) {
        $urlParams['al'] = $data['fieldmrkt_alias'];
    } elseif (!empty($data['fieldmrkt_id'])) {
        $id = (int) $data['fieldmrkt_id'];
        if ($id <= 0) {
            return '';
        }
        $urlParams['id'] = $id;
    } else {
        return '';
    }

    if (!empty($params)) {
        $urlParams = array_merge($urlParams, $params);
    }

    return cot_url('market', $urlParams, $tail, $htmlspecialcharsBypass, $ignoreAppendix);
}

/**
 * Returns permissions for a store item category.
 * @param  string $cat Category code
 * @return array       Permissions array with keys: 'auth_read', 'auth_write', 'isadmin', 'auth_download'
 */
function cot_market_auth($cat = null)
{
	if (empty($cat)) {
		$cat = 'any';
	}
	$auth = [];
	[$auth['auth_read'], $auth['auth_write'], $auth['isadmin'], $auth['auth_download']] = cot_auth('market', $cat, 'RWA1');
	return $auth;
}
/**
 * Импортирует данные элемента хранилища из параметров запроса.
 * @param строка $source - метод запроса источника для параметров
 * @param массив $ritem - данные существующего элемента хранилища из базы данных
 * @param массив $массив разрешений auth
 * @return массив - данные элемента хранилища
 */
/**
 * Imports store item data from request parameters.
 * @param string $source Source request method for parameters
 * @param array $ritem  Existing store item data from database
 * @param array $auth   Permissions array
 * @return array Store item data
 */
function cot_market_import($source = 'POST', $ritem = [], $auth = [])
{
	global $cfg, $db_market, $cot_extrafields, $usr, $sys;

	if (count($auth) == 0) {
		$auth = cot_market_auth($ritem['fieldmrkt_cat']);
	}

	if ($source == 'D' || $source == 'DIRECT') {
		// A trick so we don't have to affect every line below
		global $_PATCH;
		$_PATCH = $ritem;
		$source = 'PATCH';
	}

	$ritem['fieldmrkt_cat']      = cot_import('ritemmarketcat', $source, 'TXT', 255);
	$ritem['fieldmrkt_alias']    = cot_import('ritemmarketalias', $source, 'TXT', 255);
	$ritem['fieldmrkt_title']    = cot_import('ritemmarkettitle', $source, 'TXT', 255);
	$ritem['fieldmrkt_desc']     = cot_import('ritemmarketdesc', $source, 'TXT', 255);
	$ritem['fieldmrkt_text']     = cot_import('ritemmarkettext', $source, 'HTM');
	$ritem['fieldmrkt_parser']   = cot_import('ritemmarketparser', $source, 'ALP', 64);
	$ritem['fieldmrkt_pcod']     = cot_import('ritemmarketpcod', $source, 'TXT', 64);
	$ritem['fieldmrkt_costdflt'] = cot_import('ritemmarketcostdflt', $source, 'NUM');
    $ritem['fieldmrkt_cost_usd']  = cot_import('ritemmarketcostusd', $source, 'NUM'); // новое поле
	$ritemmarketdatenow           = cot_import('ritemmarketdatenow', $source, 'BOL');
	$ritem['fieldmrkt_date']     = cot_import_date('ritemmarketdate', true, false, $source);
	$ritem['fieldmrkt_date']     = ($ritemmarketdatenow || is_null($ritem['fieldmrkt_date'])) ? $sys['now'] : (int) $ritem['fieldmrkt_date'];
	
	$ritem['fieldmrkt_updated']  = $sys['now'];
	$ritem['fieldmrkt_metah1'] = cot_import('ritemmarketmetah1', $source, 'TXT', 255);
	$ritem['fieldmrkt_metatitle'] = cot_import('ritemmarketmetatitle', $source, 'TXT', 255);
	$ritem['fieldmrkt_metadesc'] = cot_import('ritemmarketmetadesc', $source, 'TXT', 255);

	$rmspublish               = cot_import('rmspublish', $source, 'ALP'); // For backwards compatibility
	$ritem['fieldmrkt_state']    = ($rmspublish == 'OK') ? 0 : cot_import('ritemmarketstate', $source, 'INT');

	if ($auth['isadmin'] && isset($ritem['fieldmrkt_ownerid'])) {
		$ritem['fieldmrkt_count']     = cot_import('ritemmarketcount', $source, 'INT');
		$ritem['fieldmrkt_ownerid']   = cot_import('ritemmarketownerid', $source, 'INT');
	} else {
		$ritem['fieldmrkt_ownerid'] = Cot::$usr['id'];
	}

	$parser_list = cot_get_parsers();

	if (
        empty($ritem['fieldmrkt_parser'])
        || !in_array($ritem['fieldmrkt_parser'], $parser_list)
        || $ritem['fieldmrkt_parser'] != 'none'
        && !cot_auth('plug', $ritem['fieldmrkt_parser'], 'W')
    ) {
		$ritem['fieldmrkt_parser'] = isset(Cot::$sys['marketparser']) ? Cot::$sys['marketparser'] : Cot::$cfg['market']['marketparser'];
	}

	// Extra fields
    if (!empty(Cot::$extrafields[Cot::$db->market])) {
        foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
            $value = isset($ritem['fieldmrkt_' . $exfld['field_name']]) ? $ritem['fieldmrkt_' . $exfld['field_name']] : null ;
            $ritem['fieldmrkt_' . $exfld['field_name']] = cot_import_extrafields('ritemmarket' . $exfld['field_name'], $exfld,
                $source, $value, 'market_');
        }
    }

	return $ritem;
}
/**
 * Validates store item data.
 * @param  array   $ritem Imported store item data
 * @return boolean        TRUE if validation is passed or FALSE if errors were found
 */
function cot_market_validate($ritem)
{
	global $structure;

	cot_check(empty($ritem['fieldmrkt_cat']), 'market_catmissing', 'ritemmarketcat');
	if (!empty($ritem['fieldmrkt_cat']) && isset($structure['market'][$ritem['fieldmrkt_cat']]['locked']) && $structure['market'][$ritem['fieldmrkt_cat']]['locked']) {
		global $L;
		require_once cot_langfile('message', 'core');
		cot_error('msg602_body', 'ritemmarketcat');
	}
	cot_check(mb_strlen($ritem['fieldmrkt_title']) < 2, 'market_titletooshort', 'ritemmarkettitle');

	cot_check(!empty($ritem['fieldmrkt_alias']) && preg_match('`[+/?%#&]`', $ritem['fieldmrkt_alias']), 'market_aliascharacters', 'ritemmarketalias');

	$allowemptytext = Cot::$cfg['market']['cat_' . $ritem['fieldmrkt_cat']]['marketallowemptytext']
        ?? Cot::$cfg['market']['cat___default']['marketallowemptytext'];

	cot_check(!$allowemptytext && empty($ritem['fieldmrkt_text']), 'market_textmissing', 'ritemmarkettext');

	return !cot_error_found();
}

/**
 * Adds a new store item to the CMS.
 * @param array $ritem Store item data
 * @param array $auth Permissions array
 * @return ?int New store item ID or NULL on error
 */
function cot_market_add(&$ritem, $auth = [])
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	if (cot_error_found()) {
		return false;
	}

	if (count($auth) == 0) {
		$auth = cot_market_auth($ritem['fieldmrkt_cat']);
	}

	if (!empty($ritem['fieldmrkt_alias'])) {
		$item_count = Cot::$db->query(
            'SELECT COUNT(*) FROM ' . Cot::$db->market . ' WHERE fieldmrkt_alias = ?',
            $ritem['fieldmrkt_alias']
        )->fetchColumn();
		if ($item_count > 0) {
			$ritem['fieldmrkt_alias'] = $ritem['fieldmrkt_alias'] . rand(1000, 9999);
		}
	}

	if (
        $ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED
        && !($auth['isadmin'] && Cot::$cfg['market']['marketautovalidate'])
    ) {
        $ritem['fieldmrkt_state'] = MarketDictionary::STATE_PENDING;
	}

	/* === Hook === */
	foreach (cot_getextplugins('market.add.add.query') as $pl) {
		include $pl;
	}
	/* ===== */

	if (Cot::$db->insert(Cot::$db->market, $ritem)) {
		$id = (int) Cot::$db->lastInsertId();
		cot_extrafield_movefiles();
        cot_market_updateStructureCounters($ritem['fieldmrkt_cat']);
	} else {
		$id = null;
	}

	/* === Hook === */
	foreach (cot_getextplugins('market.add.add.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if ($ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED && Cot::$cache) {
		if (Cot::$cfg['cache_market']) {
            Cot::$cache->static->clearByUri(cot_market_url($ritem));
            Cot::$cache->static->clearByUri(cot_url('market', ['c' => $ritem['fieldmrkt_cat']]));
		}
		if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
		}
	}

	cot_shield_update(30, "r market");
	cot_log('Add store item #' . $id, 'market', 'add', 'done');

	return $id;
}

/**
 * Updates a store item in the CMS.
 * @param int $id Store item ID
 * @param array $ritem Store item data
 * @param array $auth  Permissions array
 * @return bool TRUE on success, FALSE on error
 */
function cot_market_update($id, &$ritem, $auth = [])
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

    if (cot_error_found()) {
		return false;
	}

	if (count($auth) == 0) {
		$auth = cot_market_auth($ritem['fieldmrkt_cat']);
	}

	if (!empty($ritem['fieldmrkt_alias'])) {
		$item_count = Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
            ' WHERE fieldmrkt_alias = ? AND fieldmrkt_id != ?', array($ritem['fieldmrkt_alias'], $id))->fetchColumn();
		if ($item_count > 0) {
			$ritem['fieldmrkt_alias'] = $ritem['fieldmrkt_alias'] . rand(1000, 9999);
		}
	}

	$row_item = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?', $id)->fetch();

    if (
        $ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED
        && !($auth['isadmin'] && Cot::$cfg['market']['marketautovalidate'])
    ) {
        $ritem['fieldmrkt_state'] = MarketDictionary::STATE_PENDING;
    }

    Cot::$cache && Cot::$cache->db->remove('structure', 'system');

	if (!Cot::$db->update(Cot::$db->market, $ritem, 'fieldmrkt_id = ?', $id)) {
		return false;
	}
	cot_log("Edited store item #" . $id, 'market', 'edit', 'done');

	cot_extrafield_movefiles();

	/* === Hook === */
	foreach (cot_getextplugins('market.edit.update.done') as $pl) {
		include $pl;
	}
	/* ===== */

	if (
        ($ritem['fieldmrkt_state'] == MarketDictionary::STATE_PUBLISHED  || $ritem['fieldmrkt_cat'] != $row_item['fieldmrkt_cat'])
        && Cot::$cache
    ) {
		if (Cot::$cfg['cache_market']) {
            Cot::$cache->static->clearByUri(cot_market_url($ritem));
            Cot::$cache->static->clearByUri(cot_url('market', ['c' => $ritem['fieldmrkt_cat']]));

			if ($ritem['fieldmrkt_cat'] != $row_item['fieldmrkt_cat']) {
                Cot::$cache->static->clearByUri(cot_market_url($row_item));
                Cot::$cache->static->clearByUri(cot_url('market', ['c' => $row_item['fieldmrkt_cat']]));
			}
		}
		if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
		}
	}

	return true;
}

/**
 * Generates store item list widget
 * @param string|string[] $categories Custom parent categories code
 * @param int $count Number of items to show. 0 - all items
 * @param string $template Path for template file
 * @param string $order Sorting order (SQL)
 * @param string $condition Custom selection filter (SQL)
 * @param bool $active_only Custom parent category code
 * @param bool $use_subcat Include subcategories TRUE/FALSE
 * @param bool $exclude_current Exclude the current store item from the rowset for items.
 * @param string $blacklist Category black list, semicolon separated
 * @param string $pagination Pagination symbol
 * @param int $cache_ttl Cache lifetime in seconds, 0 disables cache
 * @return string Parsed HTML
 */
/*  
	
$market_widget_html = cot_market_enum(
    $categories,      // категории (строка/массив кодов или '' для всех)
    $count,           // количество товаров (0 = все или постранично)
    $template,        // файл шаблона (если '' — стандартный market.enum.tpl)
    $order,           // SQL-сортировка (например, 'fieldmrkt_date DESC')
    $condition,       // дополнительное SQL-условие (без WHERE)
    $active_only,     // показывать только опубликованные активные (true/false)
    $use_subcat,      // включать подкатегории (true/false)
    $exclude_current, // исключить текущий товар (true/false)
    $blacklist,       // чёрный список категорий (строка или массив)
    $pagination,      // имя GET-параметра для пагинации ('' = отключить)
    $cache_ttl        // время кэша в секундах (0/null = без кэша)
);
echo $market_widget_html;
// Где-то в логике (контроллере)
$market_widget_html = cot_market_enum('', 5, '', 'fieldmrkt_date DESC');

// Передаём в шаблон
$t->assign('MARKET_WIDGET', $market_widget_html);
*/
 
function cot_market_enum(
    $categories = '',
    $count = 0,
    $template = '',
    $order = '',
    $condition = '',
	$active_only = true,
    $use_subcat = true,
    $exclude_current = false,
    $blacklist = '',
    $pagination = '',
    $cache_ttl = null
) {
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	global $db, $db_market, $db_users, $structure, $cfg, $sys, $lang, $cache;

	// Compile lists
	if (!is_array($blacklist)) {
		$blacklist = str_replace(' ', '', $blacklist);
		$blacklist = (!empty($blacklist)) ? explode(',', $blacklist) : array();
	}

	// Get the cats
	if (!empty($categories)) {
		if (!is_array($categories)) {
			$categories = str_replace(' ', '', $categories);
			$categories = explode(',', $categories);
		}
		$categories = array_unique($categories);
		if ($use_subcat) {
			$total_categories = [];
			foreach ($categories as $cat) {
				$cats = cot_structure_children('market', $cat, $use_subcat);
				$total_categories = array_merge($total_categories, $cats);
			}
			$categories = array_unique($total_categories);
		}
		$categories = (count($blacklist) > 0 ) ? array_diff($categories, $blacklist) : $categories;
		$where['cat'] = "fieldmrkt_cat IN ('" . implode("','", $categories) . "')";
	} elseif (count($blacklist)) {
		$where['cat_black'] = "fieldmrkt_cat NOT IN ('" . implode("','", $blacklist) . "')";
	}

	$where['condition'] = $condition;

	if ($exclude_current && defined('COT_MARKET') && !defined('COT_LIST')) {
		global $id;
        $tmp = 0;
        if (!empty($id)) {
            $tmp = (int) $id;
        }
		if (!empty($tmp)) {
            $where['fieldmrkt_id'] = "fieldmrkt_id != $tmp";
        }
	}
	if ($active_only) {
		$where['state'] = 'fieldmrkt_state = ' . MarketDictionary::STATE_PUBLISHED;
		$where['date'] = "fieldmrkt_begin <= {$sys['now']} AND (fieldmrkt_expire = 0 OR fieldmrkt_expire > {$sys['now']})";
	}

	// Get pagination number if necessary
	if (!empty($pagination)) {
		[$pg, $d, $durl] = cot_import_pagenav($pagination, $count);
	} else {
		$d = 0;
	}

	// Display the items
	$mskin = (!empty($template) && file_exists($template)) ?
        $template : cot_tplfile(array('market', 'enum', $template), 'module');

    $cns_join_tables = '';
	$cns_join_columns = '';

	/* === Hook === */
	foreach (cot_getextplugins('market.enum.query') as $pl) {
		include $pl;
	}
	/* ===== */

    // Todo move it to comments plugin
	if (cot_plugin_active('comments')) {
		global $db_com;
		require_once cot_incfile('comments', 'plug');
		$cns_join_columns .= ", (SELECT COUNT(*) FROM `$db_com` WHERE com_area = 'market' AND com_code = p.fieldmrkt_id) AS com_count";
	}
	$sql_order = empty($order) ? 'ORDER BY fieldmrkt_date DESC' : "ORDER BY $order";
	$sql_limit = ($count > 0) ? "LIMIT $d, $count" : '';
	$where = array_filter($where);
	$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';

	$sql_total = "SELECT COUNT(*) FROM $db_market AS p $cns_join_tables $where";
	$sql_query = "SELECT p.*, u.* $cns_join_columns FROM $db_market AS p LEFT JOIN $db_users AS u ON p.fieldmrkt_ownerid = u.user_id
			$cns_join_tables $where $sql_order $sql_limit";

	$t = new XTemplate($mskin);

	isset($md5hash) || $md5hash = 'market_enum_'.md5(str_replace($sys['now'], '_time_', $mskin.$lang.$sql_query));

	if ($cache && (int) $cache_ttl > 0) {
		$item_query_html = $cache->disk->get($md5hash, 'market', (int) $cache_ttl);

		if (!empty($item_query_html)) {
			return $item_query_html;
		}
	}

	$totalitems = $db->query($sql_total)->fetchColumn();
	$sql = $db->query($sql_query);

	$sql_rowset = $sql->fetchAll();
	$jj = 0;
	foreach ($sql_rowset as $item) {
		$jj++;
		$t->assign(cot_generate_markettags($item, 'MARKET_ROW_', Cot::$cfg['market']['cat___default']['markettruncatetext']));

		$t->assign([
			'MARKET_ROW_NUM' => $jj,
			'MARKET_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'MARKET_ROW_RAW' => $item,
		]);

		$t->assign(cot_generate_usertags($item, 'MARKET_ROW_OWNER_'));

		/* === Hook === */
		foreach (cot_getextplugins('market.enum.loop') as $pl) {
			include $pl;
		}
		/* ===== */

		if (cot_plugin_active('comments')) {
			$itemUrlParams = empty($item['fieldmrkt_alias'])
                ? ['c' => $item['fieldmrkt_cat'], 'id' => $item['fieldmrkt_id']]
                : ['c' => $item['fieldmrkt_cat'], 'al' => $item['fieldmrkt_alias']];
			$t->assign([
				'MARKET_ROW_COMMENTS_LINK' => cot_commentsLink(
                    'market',
                    $itemUrlParams,
                    MarketDictionary::SOURCE_MARKET,
                    $item['fieldmrkt_id'],
                    $item['fieldmrkt_cat'],
                    $item
                ),
				'MARKET_ROW_COMMENTS_COUNT' => CommentsService::getInstance()
                    ->getCount(MarketDictionary::SOURCE_MARKET, $item['fieldmrkt_id'], $item),
			]);
		}

		$t->parse("MAIN.MARKET_ROW");
	}

	// Render pagination
	$url_params = $_GET;
    if (isset($url_params['rwr'])) {
        unset($url_params['rwr']);
    }
	$url_area = 'index';
    $extensionService = ExtensionsService::getInstance();
	$extensionCode = cot_import('e', 'G', 'ALP');
    if (!empty($extensionCode)) {
        if ($extensionService->isModuleActive($extensionCode)) {
            $url_area = $url_params['e'];
            unset($url_params['e']);
        } elseif ($extensionService->isPluginActive($extensionCode)) {
            $url_area = 'plug';
        }
    }
	unset($url_params[$pagination]);

    $pagenav = [
        'main' => null,
        'prev' => null,
        'next' => null,
        'first' => null,
        'last' => null,
        'current' => 1,
        'total' => 1,
    ];

	if (!empty($pagination)) {
		$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $count, $pagination);
	}

    $t->assign(cot_generatePaginationTags($pagenav));

	/* === Hook === */
	foreach (cot_getextplugins('market.enum.tags') as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse("MAIN");
	$item_query_html = $t->text("MAIN");

	if ($cache && (int) $cache_ttl > 0) {
		$cache->disk->store($md5hash, $item_query_html, 'market');
	}
	return $item_query_html;
}
/**
 * Callback-функция для настройки сортировки товаров на главной странице
 *
 * Используется в конфигурации модуля market (market.setup.php)
 * для формирования выпадающего списка вариантов сортировки.
 *
 * @return array Ассоциативный массив [SQL-выражение => Название]
 */
function cot_market_config_main_order()
{
    // Базовые варианты сортировки по системным полям
    $options = [
        'fieldmrkt_updated DESC' => 'Updated (newest first)',
        'fieldmrkt_date DESC'    => 'Date (newest first)',
        'fieldmrkt_date ASC'     => 'Date (oldest first)',
        'fieldmrkt_title ASC'    => 'Title (A-Z)',
        'fieldmrkt_title DESC'   => 'Title (Z-A)',
        'fieldmrkt_costdflt ASC'  => 'Price (low to high)',
        'fieldmrkt_costdflt DESC' => 'Price (high to low)',
        'fieldmrkt_count DESC'   => 'Count (highest first)',
        'fieldmrkt_count ASC'    => 'Count (lowest first)',
        'fieldmrkt_id DESC'      => 'ID (newest first)',
    ];

    // Добавляем сортировку по дополнительным полям, если они есть
    global $cot_extrafields, $db_market;
    if (!empty($cot_extrafields[$db_market])) {
        foreach ($cot_extrafields[$db_market] as $exfld) {
            $options['fieldmrkt_' . $exfld['field_name'] . ' DESC'] = $exfld['field_description'] . ' (desc)';
            $options['fieldmrkt_' . $exfld['field_name'] . ' ASC']  = $exfld['field_description'] . ' (asc)';
        }
    }
    return $options;
}
/**
 * Возвращает список товаров market для отображения на главной
 *
 * @param string $template Шаблон для вывода (по умолчанию 'index')
 * @param int $count Количество товаров для отображения (по умолчанию 5)
 * @param string $sqlsearch Дополнительные условия WHERE для SQL (по умолчанию '')
 * @param string $order Порядок сортировки SQL (по умолчанию 'fieldmrkt_updated DESC')
 * @return string Сформированный HTML код для вывода
 */
function cot_getmarketlist($template = 'index', $count = 5, $sqlsearch = '', $order = null)
{
    global $db, $db_market, $cfg, $db_users;

    // Если сортировка не передана явно, берём из настроек модуля
    if ($order === null) {
        $order = isset($cfg['market']['market_main_order']) 
                 ? $cfg['market']['market_main_order'] 
                 : 'fieldmrkt_updated DESC';
    }

    // Проверка прав доступа пользователя для модуля market
    list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('market', 'any', 'RWA');

    // Инициализация шаблона
    $t = new XTemplate(cot_tplfile(['market', $template], 'module'));

    // Подготовка дополнительных условий поиска
    $sqlsearch = !empty($sqlsearch) ? ' AND ' . $sqlsearch : '';

    // Длина текста для обрезки: если не задана в конфиге, ставим значение по умолчанию
    $truncateLen = isset($cfg['market']['markettruncatetext']) ? (int)$cfg['market']['markettruncatetext'] : 200;

    // Основной запрос к базе market с JOIN на таблицу пользователей
    $sqllist = $db->query("SELECT p.*, u.user_name 
        FROM $db_market AS p 
        LEFT JOIN $db_users AS u ON u.user_id = p.fieldmrkt_ownerid 
        WHERE p.fieldmrkt_state = 0 $sqlsearch 
        ORDER BY $order 
        LIMIT " . (int)$count);

    // Получение всех результатов запроса
    $sqllist_rowset = $sqllist->fetchAll();

    // Собираем ID и алиасы товаров (может понадобиться для дальнейшей обработки)
    $sqllist_idset = [];
    foreach ($sqllist_rowset as $item) {
        $sqllist_idset[$item['fieldmrkt_id']] = $item['fieldmrkt_alias'];
    }

    // Нумерация для чередования классов (odd/even)
    $jj = 0;
    foreach ($sqllist_rowset as $item) {
        $jj++;

        // Присвоение тегов владельца товара
        $t->assign(cot_generate_usertags($item, 'MARKET_ROW_OWNER_'));

        // Присвоение тегов товара с учетом длины обрезки текста
        $t->assign(cot_generate_markettags($item, 'MARKET_ROW_', $truncateLen, 
                                           $usr['isadmin'], $cfg['homebreadcrumb']));

        // Чередование классов для строк (odd/even)
        $t->assign([
            'MARKET_ROW_ODDEVEN' => cot_build_oddeven($jj),
        ]);

        // Парсинг одной строки товара
        $t->parse('MARKET.MARKET_ROW');
    }

    // Парсинг всего блока товаров
    $t->parse('MARKET');

    // Возвращаем готовый HTML
    return $t->text('MARKET');
}


/**
 * Собирает HTML-дерево категорий витрины продавца.
 *
 * Принимает плоский список категорий продавца (как из модуля Market,
 * так и дополненный плагином Multicat) и возвращает готовый HTML
 * иерархического дерева.
 *
 * @param array  $vendorCats     Список: [['fieldmrkt_cat' => 'код', 'items_count' => N], ...]
 * @param string $vendorUserName Никнейм продавца (для ссылок)
 * @param string $selectedCode   Код активной категории (для подсветки)
 * @return string                HTML дерева или пустая строка
 */
function cot_market_build_vendor_categories_html($vendorCats, $vendorUserName, $selectedCode = '')
{
    // 1. Индекс [код => количество] с объединением дублей через MAX.
    //    Плагин Multicat может добавить тот же код, что уже есть
    //    в модуле — берём большее значение.
    $itemsCountByCode = [];
    foreach ($vendorCats as $row) {
        $code = (string) ($row['fieldmrkt_cat'] ?? '');
        if ($code === '' || !isset(Cot::$structure['market'][$code])) {
            continue;
        }
        $count = (int) ($row['items_count'] ?? 0);
        $itemsCountByCode[$code] = max($itemsCountByCode[$code] ?? 0, $count);
    }

    if (empty($itemsCountByCode)) {
        return '';
    }

    // 2. Добавляем всех родителей, чтобы дерево было целым.
    $allCats     = Cot::$structure['market'];
    $codesToShow = [];

    foreach (array_keys($itemsCountByCode) as $code) {
        $codesToShow[$code] = true;

        $path = (string) ($allCats[$code]['path'] ?? '');
        if ($path !== '' && mb_substr_count($path, '.') > 0) {
            $parentsPaths = explode('.', $path);
            array_pop($parentsPaths);
            foreach ($allCats as $cCode => $cData) {
                if (in_array((string) $cData['path'], $parentsPaths, true)) {
                    $codesToShow[$cCode] = true;
                }
            }
        }
    }

    // 3. Индекс «родитель → дети».
    $childrenByCode = [];
    foreach (array_keys($codesToShow) as $code) {
        $path       = (string) ($allCats[$code]['path'] ?? '');
        $parentCode = '';

        if ($path !== '' && mb_substr_count($path, '.') > 0) {
            $parentPath = mb_substr($path, 0, mb_strrpos($path, '.'));
            foreach ($allCats as $pCode => $pData) {
                if ((string) $pData['path'] === $parentPath) {
                    $parentCode = $pCode;
                    break;
                }
            }
        }
        $childrenByCode[$parentCode][] = $code;
    }

    // 4. Сортировка детей по названию.
    foreach ($childrenByCode as $parentCode => $codes) {
        usort($codes, function ($a, $b) use ($allCats) {
            return strcmp((string) $allCats[$a]['title'], (string) $allCats[$b]['title']);
        });
        $childrenByCode[$parentCode] = $codes;
    }

    // 5. Переводы (если активен i18n4marketpro).
    $i18nActive = cot_plugin_active('i18n4marketpro')
        && !empty(Cot::$usr['lang'])
        && Cot::$usr['lang'] !== Cot::$cfg['defaultlang'];
    $currentLocale = Cot::$usr['lang'] ?: Cot::$cfg['defaultlang'];

    // 6. Путь к шаблону (modules/market/tpl/market.vendor.categories.tpl
    //    или themes/<theme>/modules/market/market.vendor.categories.tpl).
    $tplPath = cot_tplfile(['market', 'vendor', 'categories']);

    // 7. Рекурсивный рендер.
    return cot_market_render_vendor_categories_tree(
        '',
        0,
        $childrenByCode,
        $allCats,
        $itemsCountByCode,
        $selectedCode,
        $vendorUserName,
        $i18nActive,
        $currentLocale,
        $tplPath
    );
}

/**
 * Рекурсивный рендер одного уровня дерева категорий.
 */
function cot_market_render_vendor_categories_tree(
    $parentCode,
    $level,
    $childrenByCode,
    $allCats,
    $itemsCountByCode,
    $selectedCode,
    $vendorUserName,
    $i18nActive,
    $currentLocale,
    $tplPath
) {
    $children = $childrenByCode[$parentCode] ?? [];
    if (empty($children)) {
        return '';
    }

    $tpl = new XTemplate($tplPath);
    $jj  = 0;

    foreach ($children as $code) {
        $jj++;
        $cData = $allCats[$code];
        $title = (string) $cData['title'];

        if ($i18nActive) {
            $tr = cot_i18n4marketpro_get_cat($code, $currentLocale);
            if ($tr && !empty($tr['title'])) {
                $title = $tr['title'];
            }
        }

        $hasSubcats = !empty($childrenByCode[$code]);
        $linkClass  = 'text-decoration-none fw-medium';
        if ($selectedCode === $code) {
            $linkClass .= ' text-primary fw-bold';
        }

        $tpl->assign([
            'ROW_ID'          => 'lvl' . $level . '-' . $jj . '-' . preg_replace('/[^a-z0-9_]/i', '_', $code),
            'ROW_TITLE'       => htmlspecialchars($title),
            'ROW_HREF'        => cot_url('market', ['m' => 'vendor', 'u' => $vendorUserName, 'c' => $code]),
            'ROW_COUNT'       => (int) ($itemsCountByCode[$code] ?? 0),
            'ROW_LEVEL'       => $level,
            'ROW_PADDING'     => 8 + ($level * 14),
            'ROW_LINK_CLASS'  => $linkClass,
            'ROW_HAS_SUBCATS' => $hasSubcats ? 1 : 0,
        ]);

        if ($hasSubcats) {
            $tpl->assign('ROW_SUBCAT', cot_market_render_vendor_categories_tree(
                $code,
                $level + 1,
                $childrenByCode,
                $allCats,
                $itemsCountByCode,
                $selectedCode,
                $vendorUserName,
                $i18nActive,
                $currentLocale,
                $tplPath
            ));
        }

        $tpl->parse('MAIN.CATS');
    }

    $tpl->parse('MAIN');
    return $tpl->text('MAIN');
}


/**
 * ============================================================================
 * cot_market_get_by_id_as_recommend()
 * ============================================================================
 *
 * Назначение:
 *   Автоматически обрабатывает теги вида MARKET_CHECK_<ID>_* в шаблоне.
 *   Функция сканирует уже назначенные шаблонизатору теги, находит все
 *   идентификаторы товаров, встречающиеся в конструкциях:
 *
 *       {MARKET_CHECK_65_ID}
 *       {MARKET_CHECK_65_URL}
 *       {MARKET_CHECK_65_TITLE}
 *
 *   Для каждого найденного ID функция:
 *     1. Проверяет, существует ли опубликованный товар с таким ID.
 *     2. При необходимости получает перевод заголовка через i18n4marketpro.
 *     3. Формирует корректный URL товара.
 *     4. Передаёт в XTemplate теги:
 *          MARKET_CHECK_<ID>_ID
 *          MARKET_CHECK_<ID>_URL
 *          MARKET_CHECK_<ID>_TITLE
 *
 *   Если товар не найден или не опубликован, соответствующие теги остаются
 *   пустыми — шаблон не ломается.
 *
 * Типовой пример использования в шаблоне:
 *
 *   <!-- IF {MARKET_CHECK_65_ID} == '65' -->
 *   <a href="{MARKET_CHECK_65_URL}">{MARKET_CHECK_65_TITLE}</a>
 *   <!-- ENDIF -->
 *
 * Параметры:
 *   @param XTemplate $t  Объект шаблонизатора, в который будут назначены теги.
 *
 * Возвращает:
 *   @return void        Функция ничего не возвращает, только назначает теги.
 *
 * Зависимости:
 *   - Cot::$db          — подключение к базе данных Cotonti.
 *   - Cot::$usr         — данные текущего пользователя.
 *   - Cot::$cfg         — конфигурация Cotonti.
 *   - cot_plugin_active() — проверка активности плагина i18n4marketpro.
 *   - cot_market_url()  — построение URL товара модуля Market.
 *   - cot_url_check()   — проверка URL.
 *   - COT_ABSOLUTE_URL  — абсолютный базовый URL сайта.
 *
 * Особенности:
 *   - Поиск ID выполняется регулярным выражением по всем тегам шаблона.
 *   - Одинаковые ID обрабатываются один раз благодаря массиву-ключу.
 *   - Выбираются только товары с fieldmrkt_state = 0, то есть опубликованные.
 *   - Поддержка i18n4marketpro: перевод заголовка берётся из таблицы
 *     i18n4marketpro_pages по ipage_id и ipage_locale.
 *
 * ============================================================================
 */
function cot_market_get_by_id_as_recommend($t)
{
	
    // Массив для хранения уникальных ID товаров, найденных в тегах шаблона.
    $ids = [];

    // Получаем все теги, которые уже встречаются в переданном шаблоне.
    foreach ($t->getTags() as $tag) {
        // Ищем теги вида MARKET_CHECK_65_ANYTHING.
        if (preg_match('/^MARKET_CHECK_(\d+)_/', $tag, $m)) {
            // Приводим ID к целому числу и используем его как ключ массива,
            // чтобы автоматически убрать возможные дубли.
            $ids[(int) $m[1]] = true;
        }
    }

    // Если в шаблоне нет ни одного тега MARKET_CHECK_* — дальше работать не нужно.
    if (empty($ids)) return;

    // Массив тегов, которые позже будут переданы в XTemplate.
    $tags = [];

    // Перебираем только уникальные ID товаров.
    foreach (array_keys($ids) as $id) {
        // Формируем префикс для тегов конкретного товара.
        // Например, для ID 65 это будет MARKET_CHECK_65_.
        $prefix = 'MARKET_CHECK_' . $id . '_';

        // Инициализируем теги пустыми значениями.
        // Это нужно, чтобы шаблон не падал, если товар не найден
        // или не опубликован.
        $tags[$prefix . 'ID']    = '';
        $tags[$prefix . 'URL']   = '';
        $tags[$prefix . 'TITLE'] = '';

        // Получаем товар из базы данных.
        // Условие fieldmrkt_state = 0 означает, что берём только опубликованные товары.
        $item = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->market .
            ' WHERE fieldmrkt_id = ? AND fieldmrkt_state = 0 LIMIT 1', $id
        )->fetch();

        // Если товар не найден — оставляем пустые теги и переходим к следующему ID.
        if (!$item) continue;

        // Берём оригинальный заголовок товара из базы.
        $title = $item['fieldmrkt_title'];

        // Если активен плагин i18n4marketpro — пытаемся подставить перевод заголовка.
        if (cot_plugin_active('i18n4marketpro')) {
            // Определяем текущую локаль пользователя.
            // Если язык не задан, используем язык по умолчанию.
            $locale = Cot::$usr['lang'] ?: Cot::$cfg['defaultlang'];

            // Перевод нужен только тогда, когда текущий язык отличается от основного.
            if ($locale != Cot::$cfg['defaultlang']) {
                // Регистрируем таблицу переводов, если она ещё не зарегистрирована.
                if (!isset(Cot::$db->i18n4marketpro_pages)) {
                    Cot::$db->registerTable('i18n4marketpro_pages');
                }

                // Ищем перевод заголовка товара для текущей локали.
                $tr = Cot::$db->query(
                    'SELECT ipage_title FROM ' . Cot::$db->i18n4marketpro_pages .
                    ' WHERE ipage_id = ? AND ipage_locale = ?', [$id, $locale]
                )->fetch();

                // Если перевод найден и заголовок не пустой — используем его.
                if ($tr && !empty($tr['ipage_title'])) {
                    $title = $tr['ipage_title'];
                }
            }
        }

        // Формируем URL товара штатной функцией модуля Market.
        $url = cot_market_url($item);

        // Если URL не проходит проверку как абсолютный — добавляем базовый URL сайта.
        if (!cot_url_check($url)) $url = COT_ABSOLUTE_URL . $url;

        // Заполняем теги конкретного ID товара.
        $tags[$prefix . 'ID']    = $id;
        $tags[$prefix . 'URL']   = $url;
        $tags[$prefix . 'TITLE'] = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    }

    // Передаём сформированные теги в шаблонизатор.
    $t->assign($tags);
}


/**
 * Проверяет, что текущий ID НЕ входит в список исключений.
 * Используется в шаблонах через callback CoTemplate:
 *     {MARKET_ID|cot_market_not_in($this, 29, 71)}
 * Возвращает true, если $value НЕ входит в список $ids.
 *
 * @param mixed $value  Значение, которое проверяем (обычно текущий ID).
 * @param mixed ...$ids Список ID для сравнения.
 * @return bool
 */
function cot_market_not_in($value, ...$ids)
{
    // Приводим всё к int и проверяем строго.
    return !in_array((int) $value, array_map('intval', $ids), true);
}

/**
 * Проверяет, существует ли опубликованный товар Market с указанным ID.
 * Используется в шаблонах в IF-условиях, чтобы не вызывать рендер дважды.
 *
 * Пример:
 *   <!-- IF {PHP|cot_market_exists(71)} AND {MARKET_ID|cot_market_not_in($this,71)} -->
 */
function cot_market_exists($id)
{
    $id = (int)$id;
    if ($id <= 0) {
        return false;
    }
    $cnt = Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->market .
        ' WHERE fieldmrkt_id = ? AND fieldmrkt_state = 0',
        [$id]
    )->fetchColumn();
    return $cnt > 0;
}

/**
 * ============================================================================
 * МОДУЛЬ MARKET — ФУНКЦИИ ВЫВОДА ТОВАРА ПО ID ИЗ ПРОИЗВОЛЬНОГО ШАБЛОНА
 * ============================================================================
 *
 * Файл:      modules/market/inc/market.functions.php
 *
 *   1) cot_market_get_by_id_owner_tpl()  — главная функция, вызывается
 *      из любого шаблона сайта, подгружает нужный TPL-файл и возвращает HTML.
 *
 *   2) cot_market_build_check_tags()     — вспомогательная функция,
 *      формирует массив тегов MARKET_CHECK_<ID>_* для набора ID.
 *
 * ============================================================================
 */


/**
 * ============================================================================
 * cot_market_get_by_id_owner_tpl()
 * ============================================================================
 *
 * НАЗНАЧЕНИЕ:
 *   Универсальная функция вывода одного или нескольких товаров Market по их ID
 *   с использованием произвольного TPL-шаблона. Вызывается из любого шаблона
 *   сайта (index.tpl, page.tpl, шаблонов сторонних модулей и т.д.) через
 *   конструкцию {PHP|имя_функции(...)}.
 *
 *   Функция сама:
 *     • принимает имя шаблона и ID товара(ов);
 *     • ищет файл шаблона в теме:
 *         market.<template>.<id>.tpl   (приоритет 1, для одного ID)
 *         market.<template>.tpl        (приоритет 2, общий под любой ID)
 *     • собирает теги MARKET_CHECK_<ID>_ID / _URL / _TITLE через
 *       вспомогательную функцию cot_market_build_check_tags();
 *     • рендерит шаблон и возвращает готовый HTML.
 *
 *   Также поддерживает СТАРЫЙ режим для обратной совместимости:
 *     если первым аргументом передан объект XTemplate — функция работает
 *     как раньше: сканирует уже назначенные теги вида MARKET_CHECK_<ID>_*
 *     в этом объекте и подставляет им значения.
 *
 * ----------------------------------------------------------------------------
 * СИГНАТУРА:
 *   cot_market_get_by_id_owner_tpl(
 *       XTemplate|string $templateOrTpl = 'getbyid',
 *       int|string|array $ids = 0,
 *       string $prefix = 'MARKET_CHECK_'
 *   ): string|void
 * ----------------------------------------------------------------------------
 *
 * ПАРАМЕТРЫ:
 *   @param XTemplate|string $templateOrTpl
 *          Режим 1 (совместимость): объект XTemplate.
 *          Режим 2 (основной):      имя шаблона без префикса market. и без .tpl.
 *                                   Например: 'getbyid', 'recommend', 'related'.
 *
 *   @param int|string|array $ids
 *          Только для режима 2. ID товара или список ID.
 *
 *          ⚠️ Из шаблона (CoTemplate/XTemplate) доступны только:
 *              • одиночное число:         29
 *              • одиночная строка:        '29'
 *              • строка через запятую:    '29,71,105'
 *              • строка через пробел:     '29 71 105'
 *
 *          НЕ работают из шаблона:
 *              • массив [29, 71, 105]
 *              • строка с "|" — '29|71|105'  (режется раньше аргументов,
 *                см. Cotpl_var::__construct())
 *
 *          Из PHP доступны все форматы, включая массив и "|".
 *
 *   @param string $prefix
 *          Префикс тегов (по умолчанию 'MARKET_CHECK_').
 *          Можно переопределить, если в шаблоне нужен свой префикс,
 *          например 'RECOMMEND_'.
 *
 * ВОЗВРАЩАЕТ:
 *   @return string|void
 *          Режим 2 — готовый HTML-код (string).
 *          Режим 1 — ничего (void), теги назначаются прямо в переданный $t.
 *
 * ----------------------------------------------------------------------------
 * ПРИМЕРЫ ВЫЗОВА ИЗ ШАБЛОНА (режим 2):
 * ----------------------------------------------------------------------------
 *
 *   Пример 1. Один товар, шаблон 'getbyid'
 *              (ищется market.getbyid.29.tpl, при отсутствии — market.getbyid.tpl):
 *
 *     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29)}
 *
 *   Пример 2. Несколько товаров — строка через запятую:
 *
 *     {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29,71,105')}
 *
 *   Пример 3. Несколько товаров — строка через пробел:
 *
 *     {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29 71 105')}
 *
 *   Пример 4. Кастомный префикс тегов 'RECO_':
 *
 *     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29, 'RECO_')}
 *
 *   Пример 5. Обёрнуто в проверку активности модуля (рекомендуется):
 *
 *     <!-- IF {PHP|cot_module_active('market')} -->
 *     <div class="px-0 px-md-3 py-4">
 *         {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29)}
 *     </div>
 *     <!-- ENDIF -->
 *
 *   Пример 6. Старый вызов из PHP (совместимость) — передаём XTemplate:
 *
 *     cot_market_get_by_id_owner_tpl($t);   // сканирует теги в $t
 *
 * ----------------------------------------------------------------------------
 * ПРИМЕРЫ ВЫЗОВА ИЗ PHP (в обход ограничений TPL):
 * ----------------------------------------------------------------------------
 *
 *   Здесь МОЖНО использовать массивы и любой формат, включая "|":
 *
 *     // один товар
 *     $html = cot_market_get_by_id_owner_tpl('getbyid', 29);
 *
 *     // массив ID
 *     $html = cot_market_get_by_id_owner_tpl('getbyid', [29, 71, 105]);
 *
 *     // строка с любым разделителем из [,\|\s]
 *     $html = cot_market_get_by_id_owner_tpl('getbyid', '29|71|105');
 *     $html = cot_market_get_by_id_owner_tpl('getbyid', '29,71,105');
 *     $html = cot_market_get_by_id_owner_tpl('getbyid', '29 71 105');
 *
 *     // с кастомным префиксом тегов
 *     $html = cot_market_get_by_id_owner_tpl('getbyid', [29, 71], 'RECO_');
 *
 *     // и даже так — с уже существующим $t (совместимость)
 *     cot_market_get_by_id_owner_tpl($t);
 *
 * ----------------------------------------------------------------------------
 * ПРИМЕР СОДЕРЖИМОГО ШАБЛОНА market.getbyid.29.tpl:
 * ----------------------------------------------------------------------------
 *
 *   <!-- BEGIN: MAIN -->
 *   <!-- IF {MARKET_CHECK_29_ID} == '29' -->
 *   <div class="alert alert-info mt-5">
 *       {PHP.L.market_get_by_id_as_recommend}:
 *       <strong>
 *           <a href="{MARKET_CHECK_29_URL}"
 *              title="{MARKET_CHECK_29_TITLE}">{MARKET_CHECK_29_TITLE}</a>
 *       </strong>
 *   </div>
 *   <!-- ENDIF -->
 *   <!-- END: MAIN -->
 *
 *   Примечание: внешний блок должен называться MAIN — это стандарт XTemplate,
 *   функция парсит только MAIN. Внутренние блоки можно называть как угодно.
 *
 * ----------------------------------------------------------------------------
 * ЗАВИСИМОСТИ:
 *   • Cot::$db              — соединение с БД.
 *   • Cot::$usr             — текущий пользователь.
 *   • Cot::$cfg             — конфигурация Cotonti.
 *   • cot_tplfile()         — поиск файла шаблона в теме.
 *   • cot_plugin_active()   — проверка активности плагина i18n4marketpro.
 *   • cot_market_url()      — построение URL товара.
 *   • cot_url_check()       — валидация URL.
 *   • COT_ABSOLUTE_URL      — абсолютный базовый URL сайта.
 *   • XTemplate             — шаблонизатор Cotonti.
 *
 * ----------------------------------------------------------------------------
 * ОСОБЕННОСТИ РЕАЛИЗАЦИИ:
 *   • Все запрошенные ID собираются в ОДИН SQL-запрос (нет N+1).
 *   • Выбираются только опубликованные товары (fieldmrkt_state = 0).
 *   • Если товар не найден — его теги остаются пустыми, шаблон не ломается.
 *   • Поддерживается мультиязычность через плагин i18n4marketpro.
 *   • Парсится только корневой блок MAIN (для своего собственного шаблона),
 *		пример 
 *		 market.getbyid.29.tpl 
 *		 market.getbyid.marketpro.tpl
 *		 market.getbyid.tpl
 * ============================================================================
 */
function cot_market_get_by_id_owner_tpl($templateOrTpl = 'getbyid', $ids = 0, $prefix = 'MARKET_CHECK_')
{
    // ------------------------------------------------------------------------
    // РЕЖИМ 1. Совместимость со старым вызовом.
    // Если первым аргументом пришёл объект XTemplate — работаем как раньше:
    // сканируем уже назначенные ему теги вида MARKET_CHECK_<ID>_* и
    // подставляем в них актуальные значения из БД.
    // ------------------------------------------------------------------------
    if ($templateOrTpl instanceof XTemplate) {

        // Ссылка на переданный шаблонизатор (короткое имя для удобства).
        $t = $templateOrTpl;

        // Массив найденных уникальных ID (ключ = ID, значение = true).
        $foundIds = [];

        // Регулярное выражение для поиска тега вида <prefix><digits>_
        // Например, при prefix = 'MARKET_CHECK_' ловим 'MARKET_CHECK_29_ANYTHING'.
        $re = '/^' . preg_quote($prefix, '/') . '(\d+)_/';

        // Проходим по всем тегам, уже зарегистрированным в шаблоне.
        foreach ($t->getTags() as $tag) {

            // Проверяем, соответствует ли тег шаблону поиска.
            if (preg_match($re, $tag, $m)) {

                // Приводим ID к int и записываем в массив как ключ (убирает дубли).
                $foundIds[(int)$m[1]] = true;
            }
        }

        // Если подходящих тегов нет — выходим без изменений.
        if (empty($foundIds)) {
            return;
        }

        // Собираем теги для найденных ID одним вызовом вспомогательной функции.
        $tags = cot_market_build_check_tags(array_keys($foundIds), $prefix);

        // Назначаем теги в переданный шаблонизатор.
        $t->assign($tags);

        // Возвращаем void — рендер делает вызывающий код (market.main.php).
        return;
    }

    // ------------------------------------------------------------------------
    // РЕЖИМ 2. Основной режим: имя шаблона + ID(ы) → HTML.
    // ------------------------------------------------------------------------

    // Приводим имя шаблона к строке (защита от случайных типов).
    $template = (string)$templateOrTpl;

    // -------- Нормализация входных ID --------------------------------------

    // Если ID пришли строкой — разбиваем по запятой, вертикальной черте,
    // пробелу или табуляции. Пустые куски отбрасываются.
    if (is_string($ids)) {
        $ids = preg_split('/[,\|\s]+/', $ids, -1, PREG_SPLIT_NO_EMPTY);
    }

    // Если после разбора это не массив — оборачиваем в массив.
    if (!is_array($ids)) {
        $ids = [$ids];
    }

    // Оставляем только положительные целые, убираем дубли, переиндексируем.
    $ids = array_values(array_unique(array_filter(
        array_map('intval', $ids),
        function ($v) { return $v > 0; }
    )));

    // Если после нормализации ничего не осталось — возвращаем пустую строку.
    if (empty($ids)) {
        return '';
    }

    // -------- Поиск файла шаблона ------------------------------------------

    // Переменная для пути найденного шаблона.
    $tplFile = '';

    // Если ID ровно один — сначала пробуем шаблон с ID в имени файла,
    // например market.getbyid.29.tpl.
    // if (count($ids) === 1) {
    //     $tplFile = cot_tplfile(['market', $template . '.' . $ids[0]], 'module', true);
    // }

    // Если персональный шаблон не найден — пробуем общий market.<template>.tpl.
    // if (empty($tplFile)) {
    //     $tplFile = cot_tplfile(['market', $template], 'module', true);
    // }

/* 
 *	вызов	что ищется (по порядку)
 *	'alias-some', 31	market.alias-some.tpl → если нет → market.alias-some.31.tpl
 *	'getbyid.31', 31	market.getbyid.31.tpl (сразу найдёт)
 *	'getbyid', 31	market.getbyid.tpl → если нет → market.getbyid.31.tpl
 *	'getbyid', 31,71	market.getbyid.tpl 
*/

    // Приоритет 1: точное имя как передано.
    // market.alias-some.tpl, market.getbyid.31.tpl и т.п.
    if (cot_tplfile(['market', $template], 'module', true)) {
        $tplFile = cot_tplfile(['market', $template], 'module', true);
    }

    // Приоритет 2: если точного имени нет и ID один —
    // пробуем market.<template>.<id>.tpl
    if (empty($tplFile) && count($ids) === 1) {
        if (cot_tplfile(['market', $template . '.' . $ids[0]], 'module', true)) {
            $tplFile = cot_tplfile(['market', $template . '.' . $ids[0]], 'module', true);
        }
    }
    // Если и общий шаблон не найден — тихо выходим с пустой строкой.
    if (empty($tplFile)) {
        return '';
    }

    // -------- Подготовка тегов ---------------------------------------------

    // Формируем массив тегов MARKET_CHECK_<ID>_* через вспомогательную функцию.
    $tags = cot_market_build_check_tags($ids, $prefix);

    // Если теги не сформированы — возвращаем пустую строку.
    if (empty($tags)) {
        return '';
    }

    // -------- Рендер шаблона -----------------------------------------------

    // Создаём объект XTemplate на найденном файле.
    // Защита от рекурсии: не рендерим один и тот же шаблон внутри себя
    static $stack = [];
    $key = $tplFile . '|' . implode(',', $ids);
    if (isset($stack[$key])) {
        return '';
    }
    $stack[$key] = true;

    // Создаём объект XTemplate на найденном файле.
    $t = new XTemplate($tplFile);

    // Назначаем теги товаров в шаблон.
    $t->assign($tags);

    // Парсим ТОЛЬКО корневой блок MAIN.
    // Всё остальное (вложенные блоки) должно быть вложено в MAIN в самом TPL.
    $t->parse('MAIN');

    // Получаем готовый HTML.
    $html = $t->text('MAIN');

    unset($stack[$key]);

    // Возвращаем результат вызывающему коду.
    return $html;
}



/**
 * ============================================================================
 * cot_market_build_check_tags()
 * ============================================================================
 *
 * НАЗНАЧЕНИЕ:
 *   Вспомогательная функция. Формирует массив тегов вида:
 *
 *       MARKET_CHECK_<ID>_ID
 *       MARKET_CHECK_<ID>_URL
 *       MARKET_CHECK_<ID>_TITLE
 *
 *   для переданного набора ID товаров. Используется как основной функцией
 *   cot_market_get_by_id_owner_tpl(), так и может вызываться напрямую,
 *   если нужно назначить теги в уже существующий XTemplate-объект.
 *
 * ----------------------------------------------------------------------------
 * СИГНАТУРА:
 *   cot_market_build_check_tags(array $ids, string $prefix = 'MARKET_CHECK_'): array
 * ----------------------------------------------------------------------------
 *
 * ПАРАМЕТРЫ:
 *   @param array  $ids     Массив целых положительных ID товаров.
 *   @param string $prefix  Префикс тегов (по умолчанию 'MARKET_CHECK_').
 *
 * ВОЗВРАЩАЕТ:
 *   @return array          Ассоциативный массив вида:
 *                            ['MARKET_CHECK_29_ID'    => 29,
 *                             'MARKET_CHECK_29_URL'   => 'https://...',
 *                             'MARKET_CHECK_29_TITLE' => 'Название']
 *                          Для ненайденных/неопубликованных ID значения
 *                          остаются пустыми строками.
 *
 * ----------------------------------------------------------------------------
 * ПРИМЕРЫ ВЫЗОВА:
 * ----------------------------------------------------------------------------
 *
 *   Пример 1. Получить теги для одного товара:
 *
 *     $tags = cot_market_build_check_tags([29]);
 *     // → ['MARKET_CHECK_29_ID' => 29, 'MARKET_CHECK_29_URL' => '...', ...]
 *
 *   Пример 2. Несколько товаров и свой префикс:
 *
 *     $tags = cot_market_build_check_tags([29, 71, 105], 'RECO_');
 *     // → ['RECO_29_ID' => 29, ..., 'RECO_71_ID' => 71, ...]
 *
 *   Пример 3. Назначение тегов в существующий шаблонизатор:
 *
 *     $t = new XTemplate(cot_tplfile('market.myblock', 'module'));
 *     $t->assign(cot_market_build_check_tags([29, 71]));
 *     $t->parse('MAIN');
 *
 * ----------------------------------------------------------------------------
 * ОСОБЕННОСТИ РЕАЛИЗАЦИИ:
 *   • Один SQL-запрос на весь массив ID (нет N+1).
 *   • Используются подготовленные выражения (placeholders) — защита от SQL-инъекций.
 *   • Выбираются только опубликованные товары (fieldmrkt_state = 0).
 *   • Поддержка i18n4marketpro: перевод заголовка берётся из таблицы
 *     i18n4marketpro_pages по ipage_id и ipage_locale.
 *   • Значения TITLE экранируются htmlspecialchars() — безопасно для HTML.
 * ============================================================================
 */
function cot_market_build_check_tags(array $ids, $prefix = 'MARKET_CHECK_')
{
    // Итоговый массив тегов. Изначально пустой.
    $tags = [];

    // Если ID не переданы — сразу возвращаем пустой массив.
    if (empty($ids)) {
        return $tags;
    }

    // ------------------------------------------------------------------------
    // 1. Одним запросом забираем все опубликованные товары из переданных ID.
    // ------------------------------------------------------------------------

    // Формируем строку вида "?,?,?" — количество плейсхолдеров равно числу ID.
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Выполняем подготовленный запрос:
    //   - только строки с нужными ID
    //   - только опубликованные (fieldmrkt_state = 0)
    $rows = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->market .
        ' WHERE fieldmrkt_id IN (' . $placeholders . ') AND fieldmrkt_state = 0',
        $ids
    )->fetchAll();

    // Индексируем полученные строки по ID — так проще искать нужный товар ниже.
    $byId = [];
    foreach ($rows as $row) {
        $byId[(int)$row['fieldmrkt_id']] = $row;
    }

    // ------------------------------------------------------------------------
    // 2. Подготовка i18n4marketpro (если активен и язык отличается от основного).
    // ------------------------------------------------------------------------

    // Флаг активности плагина мультиязычности.
    $i18nActive = cot_plugin_active('i18n4marketpro');

    // Переменная для текущей локали (заполняется, если i18n активен).
    $locale = null;

    // Если плагин активен — уточняем локаль и проверяем необходимость перевода.
    if ($i18nActive) {

        // Текущая локаль пользователя или локаль по умолчанию.
        $locale = Cot::$usr['lang'] ?: Cot::$cfg['defaultlang'];

        // Если язык совпадает с основным — переводы не нужны, отключаем i18n.
        if ($locale == Cot::$cfg['defaultlang']) {
            $i18nActive = false;
        }
        // Иначе — регистрируем таблицу переводов (если ещё не зарегистрирована).
        elseif (!isset(Cot::$db->i18n4marketpro_pages)) {
            Cot::$db->registerTable('i18n4marketpro_pages');
        }
    }

    // ------------------------------------------------------------------------
    // 3. Формируем теги для каждого ID.
    // ------------------------------------------------------------------------

    // Проходим по всем запрошенным ID в том порядке, в каком они пришли.
    foreach ($ids as $id) {

        // Префикс конкретного товара, например 'MARKET_CHECK_29_'.
        $p = $prefix . $id . '_';

        // По умолчанию — пустые теги.
        // Это гарантирует, что шаблон с <!-- IF {MARKET_CHECK_29_ID} --> не сломается,
        // если товар не найден или не опубликован.
        $tags[$p . 'ID']    = '';
        $tags[$p . 'URL']   = '';
        $tags[$p . 'TITLE'] = '';

        // Если товара нет в выборке — переходим к следующему ID.
        if (!isset($byId[$id])) {
            continue;
        }

        // Ссылка на строку товара из БД.
        $item = $byId[$id];

        // Берём оригинальный заголовок товара.
        $title = $item['fieldmrkt_title'];

        // Если активен i18n — пытаемся заменить заголовок переводом.
        if ($i18nActive) {

            // Ищем перевод по ID и локали.
            $tr = Cot::$db->query(
                'SELECT ipage_title FROM ' . Cot::$db->i18n4marketpro_pages .
                ' WHERE ipage_id = ? AND ipage_locale = ?',
                [$id, $locale]
            )->fetch();

            // Если перевод найден и не пуст — используем его.
            if ($tr && !empty($tr['ipage_title'])) {
                $title = $tr['ipage_title'];
            }
        }

        // Формируем URL товара штатной функцией модуля Market.
        $url = cot_market_url($item);

        // Если URL относительный — добавляем абсолютный базовый URL сайта.
        if (!cot_url_check($url)) {
            $url = COT_ABSOLUTE_URL . $url;
        }

        // Заполняем теги конкретного ID.
        $tags[$p . 'ID']    = $id;                                        // числовой ID
        $tags[$p . 'URL']   = $url;                                       // абсолютный URL
        $tags[$p . 'TITLE'] = htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); // безопасный заголовок
    }

    // Возвращаем готовый массив тегов.
    return $tags;
}


