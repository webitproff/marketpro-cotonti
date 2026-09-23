<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Market module main entry point.
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Filename: market.php
 *
 * Path:    modules/market/market.php
 *
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ФАЙЛУ market.php
 * ============================================================
 *
 * Назначение:
 *   Главный файл-точка входа модуля Market. Именно этот файл
 *   подключается движком Cotonti при переходе пользователя на
 *   страницы модуля Market (index.php?e=market, ЧПУ-адреса вида
 *   /market/..., /market/electronics/..., /market/electronics/noutbuk и т.п.).
 *
 *   Задача файла — определить, какой сценарий требуется пользователю,
 *   подключить соответствующий обработчик из папки inc/ и вывести
 *   результат через стандартные header.php и footer.php Cotonti.
 *
 * Общая логика работы:
 *   1. Регистрирует модуль в системе Cotonti:
 *      - определяет константу COT_MARKET (маркер того, что мы внутри
 *        модуля Market; используется другими частями модуля и плагинами);
 *      - устанавливает Cot::$env['location'] = 'market'.
 *
 *   2. Подключает обязательные API и функции модуля:
 *      - extrafields — API системы дополнительных полей;
 *      - market (module) — основной файл функций модуля
 *        market.functions.php с полным API.
 *
 *   3. Определяет режим $m (какое действие выполнять):
 *      - если $m не из списка 'add', 'edit', 'counter', 'preview' —
 *        автоматически выбирает 'main' (если в URL есть id или al)
 *        или 'list' (в остальных случаях);
 *      - если $m явно указан (add/edit/counter/preview) —
 *        остаётся как есть.
 *
 *   4. Подключает соответствующий сценарий из папки inc/:
 *      - market.list.php     — список товаров (каталог);
 *      - market.main.php     — страница отдельного товара;
 *      - market.add.php      — форма добавления нового товара;
 *      - market.edit.php     — форма редактирования товара;
 *      - market.preview.php  — предпросмотр товара;
 *      - market.counter.php  — AJAX-обработчик счётчика просмотров.
 *
 *   5. Подключает стандартную шапку сайта (header.php).
 *   6. Выводит сгенерированный HTML модуля из переменной $moduleBody
 *      (каждый сценарий в inc/ формирует эту переменную).
 *   7. Подключает стандартный подвал сайта (footer.php).
 *
 * Механизм выбора режима:
 *   - Параметр $m приходит из URL (например, ?m=add или через ЧПУ-правила).
 *   - Если $m явно не задан для специальных действий, движок Cotonti
 *     обычно передаёт пустое значение или значение по умолчанию.
 *   - В таких случаях логика файла выбирает:
 *       • 'main' — когда в URL присутствует id (ID товара) или al (алиас);
 *       • 'list' — во всех остальных ситуациях (главная страница модуля,
 *         страница категории, результаты поиска и т.п.).
 *
 * Почему это важно:
 *   Один файл-точка входа обеспечивает единое поведение модуля для
 *   всех сценариев, упрощает маршрутизацию и позволяет движку
 *   применять единые настройки (кэш, права, тема оформления).
 *   Именно такой подход принят в Cotonti для большинства модулей.
 *
 * Специальные константы:
 *   COT_MARKET — определяется только внутри этого модуля.
 *                Используется в других файлах для проверки, что мы
 *                находимся именно в контексте Market (например,
 *                в market.main.php для определения переменных,
 *                доступных только на странице товара).
 *
 * Взаимодействие с другими файлами модуля:
 *   - Файл не выполняет бизнес-логику сам — он только подготавливает
 *     окружение и делегирует управление в inc/market.<m>.php.
 *   - Каждый inc-сценарий формирует переменную $moduleBody с готовым HTML.
 *   - Плагины могут подключаться через хуки (например, 'market.first'
 *     внутри market.main.php) — но сам market.php хуков не вызывает.
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
 *
 * @var string $m  Режим работы модуля (какое действие выполнять).
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_MARKET', TRUE);
$env['location'] = 'market';

// Additional API requirements
require_once cot_incfile('extrafields');

// Self requirements
require_once cot_incfile('market', 'module');

// Mode choice
/* if (!in_array($m, ['add', 'edit', 'counter', 'preview'])) {
	if (isset($_GET['id']) || isset($_GET['al']))
	{
		$m = 'main';
	} else {
		$m = 'list';
	}
} */
/* =====================================================================
 * ОПРЕДЕЛЕНИЕ РЕЖИМА РАБОТЫ МОДУЛЯ (mode choice)
 * ---------------------------------------------------------------------
 * Если в URL явно указан режим ($m) — используем его как есть.
 * Иначе определяем автоматически:
 *   - наличие id или al → страница товара (main);
 *   - наличие u или uid → витрина продавца (vendor);
 *   - всё остальное     → список товаров (list).
 * ===================================================================== */

// Список разрешённых «прямых» режимов
$allowedModes = ['add', 'edit', 'counter', 'preview', 'vendors', 'vendor'];

// Если $m не входит в список разрешённых — определяем его автоматически
if (!in_array($m, $allowedModes)) {

    // Если в URL есть id или al — это страница товара
    if (isset($_GET['id']) || isset($_GET['al'])) {
        $m = 'main';

    // Если в URL есть u или uid — это витрина продавца
    } elseif (isset($_GET['u']) || isset($_GET['uid'])) {
        $m = 'vendor';

    // Иначе — список товаров
    } else {
        $m = 'list';
    }
}

// Подключаем соответствующий обработчик из папки inc/

require_once cot_incfile('market', 'module', $m);

// Подключаем шапку сайта (header.tpl)
require_once $cfg['system_dir'].'/header.php';

// Выводим сгенерированное тело модуля (определяется в ранее подключённом файле)
echo $moduleBody;

// Подключаем футер сайта (footer.tpl)
require_once $cfg['system_dir'].'/footer.php';