<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=structure.extensions
[END_COT_EXT]
==================== */

/**
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ХУКУ `structure.extensions` И ФАЙЛУ market.structure.php
 * ============================================================
 *
 * Место вызова хука `structure.extensions` (по факту из /system/functions.php):
 *
 *   Функция: cot_getExtensionsWithStructure()
 *
 *   Тело функции:
 *       static $extensionsWithStructure = [];
 *       static $loaded = false;
 *
 *       if ($loaded) {
 *           return $extensionsWithStructure;
 *       }
 *
 *       /* === Hook === * /
 *       foreach (cot_getextplugins('structure.extensions') as $pl) {
 *           include $pl;
 *       }
 *       /* ===== * /
 *
 *       $loaded = true;
 *
 *       return $extensionsWithStructure;
 *
 *   Файлы-обработчики хука `structure.extensions` подключаются внутри
 *   функции cot_getExtensionsWithStructure() в её локальной области
 *   видимости. Поэтому переменная $extensionsWithStructure внутри
 *   включаемого файла — это та же статическая переменная, которую
 *   возвращает функция.
 *
 *   Функция кэширует результат на время запроса (static $loaded = false),
 *   обработчики подключаются один раз за запрос.
 *
 * Потребитель результата (по факту из /system/admin/admin.structure.php):
 *
 *   $extensionsWithStructure = cot_getExtensionsWithStructure();
 *
 *   Далее массив используется так:
 *
 *     1) Если в массиве ровно один элемент и соответствующее расширение
 *        активно (cot_plugin_active() или cot_module_active()) — происходит
 *        редирект на страницу структуры этого расширения.
 *
 *     2) Иначе строится список расширений: перебирается $extensionsWithStructure,
 *        для каждого кода проверяется активность, и только активные
 *        расширения выводятся в списке.
 *
 *     3) При открытии структуры конкретного расширения (параметр n):
 *            if (!in_array($n, $extensionsWithStructure, true)) {
 *                cot_die_message(404);
 *            }
 *        Если кода нет в массиве — отдаётся 404.
 *
 * Файл market.structure.php содержит одну строку:
 *
 *   $extensionsWithStructure[] = 'market';
 *
 *   Она добавляет код 'market' в массив $extensionsWithStructure.
 *   За счёт этого модуль market попадает в список расширений, для
 *   которых в админке доступна страница управления структурой
 *   (/system/admin/admin.structure.php).
 *
 * Связь с /system/structure.php:
 *
 *   Файл /system/structure.php содержит две функции, которые работают
 *   со структурой расширений и вызывают функции самого расширения
 *   по имени, собранному из его кода:
 *
 *   1) cot_structure_add($extension, $data, $is_module)
 *      — при добавлении категории:
 *          • вызывает хук `structure.add`;
 *          • вызывает cot_auth_add_item($extension, $code, ...)
 *            при $is_module === true;
 *          • вызывает функцию cot_<extension>_addcat($code),
 *            если она существует:
 *                $area_addcat = 'cot_' . $extension . '_addcat';
 *                (function_exists($area_addcat))
 *                    ? $area_addcat($data['structure_code'])
 *                    : FALSE;
 *
 *   2) cot_structure_update($extension, $id, $old_data, $new_data, $is_module)
 *      — при обновлении категории:
 *          • вызывает хук `structure.update`;
 *          • при смене кода категории обновляет auth (для модуля),
 *            config (для модуля) и вызывает функцию
 *            cot_<extension>_updatecat($old_code, $new_code),
 *            если она существует:
 *                $area_updatecat = 'cot_' . $extension . '_updatecat';
 *          • перед сохранением пересчитывает structure_count через
 *            функцию cot_<extension>_sync($code), если она существует:
 *                $area_sync = 'cot_' . $extension . '_sync';
 *                $new_data['structure_count'] =
 *                    (function_exists($area_sync))
 *                        ? $area_sync($new_data['structure_code'])
 *                        : 0;
 *          • вызывает хук `structure.update.done` после обновления.
 *
 * Функции market-модуля, соответствующие этим вызовам ядра
 * (объявлены в /modules/market/inc/market.functions.php):
 *
 *   • cot_market_updatecat($oldcat, $newcat)
 *       — вызывается ядром из cot_structure_update() при смене кода
 *         категории market. Обновляет в таблице market поле
 *         fieldmrkt_cat у всех товаров, которые были в старой категории:
 *             $db->update($db_market,
 *                 ['fieldmrkt_cat' => $newcat],
 *                 "fieldmrkt_cat = '<oldcat>'"
 *             );
 *
 *   • cot_market_sync($category)
 *       — вызывается ядром из cot_structure_update() для пересчёта
 *         structure_count категории market. Возвращает число товаров
 *         в указанной категории:
 *             SELECT COUNT(*) FROM market WHERE fieldmrkt_cat = ?
 *         Значение пишется ядром в structure_count структуры.
 *
 *   • cot_market_addcat($code)
 *       — ядро в cot_structure_add() ищет функцию с таким именем и,
 *         если она существует, вызывает её при добавлении новой
 *         категории market. В файле /modules/market/inc/market.functions.php
 *         (полный листинг которого был предоставлен) функция
 *         cot_market_addcat() отсутствует. Значит при добавлении
 *         категории market ядро пропускает этот вызов
 *         (function_exists() вернёт false).
 *
 *   Дополнительно, market-модуль содержит функцию
 *   cot_market_updateStructureCounters($category), которая обновляет
 *   structure_count той же категории в таблице structure. Она НЕ
 *   вызывается ядром из /system/structure.php — по коду видно, что
 *   она вызывается самим market-модулем в cot_market_add() и
 *   cot_market_update() при добавлении/обновлении товара.
 *
 * Область действия:
 *   — Файл market.structure.php выполняется один раз за запрос —
 *     при первом вызове cot_getExtensionsWithStructure().
 *   — Результат (наличие 'market' в массиве) используется
 *     /system/admin/admin.structure.php.
 *   — Сам файл market.structure.php функций cot_market_* не содержит.
 *     Он только регистрирует расширение в списке для админки.
 *   — Функции cot_market_updatecat() и cot_market_sync() определены
 *     в /modules/market/inc/market.functions.php и вызываются ядром
 *     из /system/structure.php только при работе со структурой market.
 *
 * Зависимости:
 *   — /system/functions.php (cot_getExtensionsWithStructure());
 *   — /system/admin/admin.structure.php (потребитель массива);
 *   — /system/structure.php (cot_structure_add, cot_structure_update) —
 *     ядро, которое вызывает функции расширения по имени
 *     cot_<extension>_addcat / cot_<extension>_updatecat / cot_<extension>_sync;
 *   — /modules/market/inc/market.functions.php — определяет
 *     cot_market_updatecat() и cot_market_sync().
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 26, 2026
 *
 * @package    market
 * @subpackage Setup
 * @version    5.7.9
 * @author     webitproff
 * @copyright  Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license    BSD
 * ============================================================
 */



defined('COT_CODE') or die('Wrong URL');

$extensionsWithStructure[] = 'market';