<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=global
 * [END_COT_EXT]
 */

/**
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ХУКУ `global` И ФАЙЛУ market.global.php
 * ============================================================
 *
 * Хук `global` в Cotonti вызывается в /system/common.php
 * в самом конце файла, после инициализации системы,
 * после загрузки конфигурации, структуры, данных пользователя,
 * языковых файлов и ресурсов темы:
 *
 *     foreach (cot_getextplugins('global') as $pl) {
 *         include $pl;
 *     }
 *
 * Точное место вызова (по факту из /system/common.php):
 *   — после блока `/* ============ Head Resources ===========* /`;
 *   — после инициализации XTemplate (`if (class_exists('XTemplate'))`);
 *   — перед завершением выполнения common.php.
 *
 * В /index.php хука `global` нет. В /system/common.php — есть, в указанном месте.
 *
 * Назначение хука `global`:
 *   — для кода, который должен быть выполнен при каждом запросе,
 *     сразу после инициализации системы;
 *   — хук вызывается практически всегда и строго в определённом
 *     порядке относительно других основных хуков: input → rc → global;
 *   — используется для регистрации глобальных переменных, доступных
 *     на всех страницах сайта;
 *   — подходит для кода, который должен загружаться всегда, а не
 *     только на определённых страницах.
 *
 * Порядок выполнения:
 *   — обработчики хука сортируются по значению Order (по умолчанию 10);
 *   — при одинаковом Order — в порядке установки плагинов;
 *   — файл с наименьшим Order выполняется первым.
 *
 * Файл market.global.php содержит следующую логику:
 *
 *   1) require_once cot_incfile('market', 'module');
 *      — Подключает основной файл модуля Market. Выполняется
 *        безусловно при каждом запросе, где срабатывает хук global.
 *
 *   2) if (cot_module_active('payments')) {
 *          require_once cot_incfile('payments', 'module');
 *      }
 *      — Проверяет активность модуля Payments через cot_module_active().
 *      — Если модуль Payments активен, подключает его основной файл
 *        через cot_incfile(). Если не активен — блок пропускается.
 *
 * Область действия:
 *   — Файл выполняется при каждом запросе к сайту.
 *   — Файл выполняется для всех локаций: главная, модули, плагины,
 *     админка (если не отсечено отдельно условием внутри кода).
 *   — В данном файле нет условий, ограничивающих выполнение
 *     по локации, модулю или типу запроса.
 *
 * Зависимости:
 *   — modules/market/market.php (через cot_incfile('market', 'module'));
 *   — modules/payments/payments.php (через cot_incfile('payments', 'module')).
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
 
 
defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('market', 'module');
if (cot_module_active('payments')) {
    require_once cot_incfile('payments', 'module');
}
