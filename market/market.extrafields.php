<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
==================== */

/**
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ХУКУ `admin.extrafields.first` И ФАЙЛУ market.extrafields.php
 * ============================================================
 *
 * Место вызова хука `admin.extrafields.first` (по факту из
 * /system/admin/admin.extrafields.php):
 *
 *   Файл содержит в начале:
 *       require_once cot_incfile('extrafields');
 *       $extra_blacklist = [ $db_auth, $db_cache, ... ];
 *       $extra_whitelist = [ $db_structure => [ ... ] ];
 *       $adminPath[] = ...;
 *       $adminTitle = Cot::$L['adm_extrafields'];
 *       $maxperpage = ...;
 *       $t = new XTemplate(cot_tplfile(['admin', 'extrafields', $n], 'core'));
 *
 *       /* === Hook === * /
 *       foreach (cot_getextplugins('admin.extrafields.first') as $pl) {
 *           include $pl;
 *       }
 *       /* ===== * /
 *
 *   То есть хук вызывается:
 *     — после того, как в области видимости admin.extrafields.php
 *       созданы массивы $extra_blacklist и $extra_whitelist;
 *     — до того, как начнётся ветвление между табличным списком
 *       (`if (empty($n) || in_array($n, $extra_blacklist))`) и
 *       редактированием extrafields конкретной таблицы (`else { ... }`).
 *
 *   Файлы-обработчики хука подключаются внутри admin.extrafields.php
 *   в её области видимости. Поэтому переменные $extra_whitelist и
 *   $extra_blacklist, изменяемые в обработчике, — это те же переменные,
 *   которые далее используются в admin.extrafields.php.
 *
 * Файл market.extrafields.php выполняет следующее:
 *
 *   1) defined('COT_CODE') or die('Wrong URL');
 *      — стандартная защита от прямого вызова.
 *
 *   2) require_once cot_incfile('market', 'module');
 *      — подключает основной файл функций модуля market:
 *            modules/market/inc/market.functions.php
 *        При этом выполняются инструкции, объявленные в нём на верхнем
 *        уровне:
 *            Cot::$db->registerTable('market');
 *            cot_extrafields_register_table('market');
 *            require_once cot_langfile('market', ExtensionsDictionary::TYPE_MODULE);
 *            require_once cot_incfile('market', ExtensionsDictionary::TYPE_MODULE, 'resources');
 *            require_once cot_incfile('forms');
 *            require_once cot_incfile('extrafields');
 *        В результате после require_once в области видимости доступна
 *        переменная $db_market (зарегистрированное имя таблицы market
 *        с префиксом БД) и зарегистрированная таблица в реестре
 *        Cot::$extrafields.
 *
 *   3) Добавляет запись в массив $extra_whitelist:
 *
 *          $extra_whitelist[$db_market] = [
 *              'name' => $db_market,
 *              'caption' => $L['Module'].' Market',
 *              'type' => 'module',
 *              'code' => 'market',
 *              'tags' => [
 *                  'market.list.tpl'          => '{LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}',
 *                  'market.tpl'               => '{MARKET_XXXXX}, {MARKET_XXXXX_TITLE}',
 *                  'market.add.tpl'           => '{MARKETADD_FORM_XXXXX}, {MARKETADD_FORM_XXXXX_TITLE}',
 *                  'market.edit.tpl'          => '{MARKETEDIT_FORM_XXXXX}, {MARKETEDIT_FORM_XXXXX_TITLE}',
 *                  'news.tpl'                 => '{MARKET_ROW_XXXXX}',
 *                  'recentitems.market.tpl'   => '{MARKET_ROW_XXXXX}',
 *              ],
 *          ];
 *
 *      Ключ массива — имя таблицы market с префиксом БД ($db_market).
 *
 *      Поля записи:
 *        • name    — имя таблицы в БД.
 *        • caption — отображаемое название. Формируется из
 *                    $L['Module'] (языковая строка Cotonti)
 *                    и строки ' Market'.
 *        • type    — 'module'. Значение используется в
 *                    admin.extrafields.php:
 *                        if ($type == 'module' || $type == 'plug') {
 *                            $ext_info = cot_get_extensionparams(
 *                                $extra_whitelist[$table]['code'],
 *                                $extra_whitelist[$table]['type'] == 'module'
 *                            );
 *                            ...
 *                        }
 *                    То есть по type='module' админка понимает, что
 *                    перед ней модуль market, и запрашивает у Cotonti
 *                    его параметры (название, иконку и т.п.) через
 *                    cot_get_extensionparams().
 *        • code    — код расширения 'market'. Передаётся в
 *                    cot_get_extensionparams() вторым аргументом
 *                    (через флаг is_module = true).
 *        • tags    — карта «файл шаблона → список доступных
 *                    extrafield-тегов». Используется в
 *                    admin.extrafields.php в блоке:
 *                        if (isset($extra_whitelist[$n])) {
 *                            if (is_array($extra_whitelist[$n]['tags'])) {
 *                                foreach ($extra_whitelist[$n]['tags'] as $ktags => $vtags) {
 *                                    $tags_list .= cot_rc('admin_exflds_array',
 *                                        ['tplfile' => $ktags, 'tags' => $vtags]);
 *                                    $tags_list_li .= '<li>' . cot_rc(
 *                                        'admin_exflds_array',
 *                                        ['tplfile' => $ktags, 'tags' => $vtags]
 *                                    ) . '</li>';
 *                                }
 *                            }
 *                        }
 *                    Значение XXXX в каждом теге заменяется именем
 *                    extrafield-а в JS шаблона admin.extrafields.tpl:
 *                        exhelper = exFLDHELPERS.replace(/XXXXX/g, exhelper);
 *                    То есть XXXX — это плейсхолдер для имени поля.
 *
 * Что делает admin.extrafields.php с $extra_whitelist после хука:
 *
 *   Ветка 1. Табличный список (когда $n пуст или в blacklist):
 *
 *       $sql = $db->query("SHOW TABLES");
 *       while ($row = $sql->fetch()) {
 *           $table = current($row);
 *           if (!in_array($table, $extra_blacklist)) {
 *               if (cot_import('alltables', 'G', 'BOL')) {
 *                   $tablelist[] = $table;
 *               } elseif (isset($extra_whitelist[$table])) {
 *                   $tablelist[] = $table;
 *               }
 *           }
 *       }
 *
 *       Далее для каждой таблицы из $tablelist:
 *           $icon = $extra_whitelist[$table]['icon'] ?? null;
 *           if (isset($extra_whitelist[$table])) {
 *               if (isset($extra_whitelist[$table]['type'])) {
 *                   $type = $extra_whitelist[$table]['type'];
 *               }
 *               if ($type == 'module' || $type == 'plug') {
 *                   $ext_info = cot_get_extensionparams(
 *                       $extra_whitelist[$table]['code'],
 *                       $extra_whitelist[$table]['type'] == 'module'
 *                   );
 *                   $name = $ext_info['name'];
 *                   if (!empty($ext_info['icon'])) {
 *                       $icon = $ext_info['icon'];
 *                   }
 *               }
 *               $name = (empty($name)) ? $extra_whitelist[$table]['caption'] : $name;
 *           }
 *
 *       В шаблоне admin.extrafields.tpl это выводится в блоке
 *       TABLELIST/ROW через теги:
 *           {ADMIN_EXTRAFIELDS_ROW_ICON}
 *           {ADMIN_EXTRAFIELDS_ROW_TABLENAME}
 *           {ADMIN_EXTRAFIELDS_ROW_TABLEURL}
 *
 *       Без записи market в $extra_whitelist таблица market
 *       НЕ попала бы в этот список при обычном режиме (без
 *       параметра alltables=1).
 *
 *   Ветка 2. Редактирование extrafields конкретной таблицы
 *   (когда $n = $db_market и $n не в blacklist):
 *
 *       — $adminPath[] дополняется:
 *             Cot::$L['adm_extrafields_table'] . ' ' . $n
 *             . ' - ' . $extra_whitelist[$n]['caption']
 *       — в конце формируется блок доступных тегов:
 *             $tags_list = '';
 *             $tags_list_li = '';
 *             if (isset($extra_whitelist[$n])) {
 *                 if (is_array($extra_whitelist[$n]['tags'])) {
 *                     foreach ($extra_whitelist[$n]['tags'] as $ktags => $vtags) {
 *                         ... cot_rc('admin_exflds_array', ...) ...
 *                     }
 *                 }
 *             }
 *             $t->assign('ADMIN_EXTRAFIELDS_TAGS', $tags_list);
 *       — help-блок для страницы:
 *             if (isset($extra_whitelist[$n]['help'])) {
 *                 $adminHelp = $extra_whitelist[$n]['help'];
 *             } else {
 *                 $adminHelp = Cot::$L['adm_help_info'];
 *                 if (!empty($tags_list)) {
 *                     $adminHelp .= Cot::$L['adm_help_newtags']
 *                         . '<ul class="follow">' . $tags_list_li . '</ul>';
 *                 }
 *             }
 *         В записи market.extrafields.php ключ 'help' отсутствует —
 *         значит для market используется ветка else (стандартная
 *         справка + список тегов).
 *
 * Список шаблонов в поле 'tags' (по факту из market.extrafields.php):
 *
 *   — market.list.tpl        — {LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}
 *   — market.tpl             — {MARKET_XXXXX}, {MARKET_XXXXX_TITLE}
 *   — market.add.tpl         — {MARKETADD_FORM_XXXXX}, {MARKETADD_FORM_XXXXX_TITLE}
 *   — market.edit.tpl        — {MARKETEDIT_FORM_XXXXX}, {MARKETEDIT_FORM_XXXXX_TITLE}
 *   — news.tpl               — {MARKET_ROW_XXXXX}
 *   — recentitems.market.tpl — {MARKET_ROW_XXXXX}
 *
 *   Значения XXXX в этих тегах подставляются именем extrafield-а.
 *   Список выводится на странице редактирования extrafields market
 *   как подсказка администратору: какие теги доступны в шаблонах.
 *
 * Область действия:
 *   — Файл выполняется один раз за запрос — при первом вызове
 *     cot_getextplugins('admin.extrafields.first') внутри
 *     /system/admin/admin.extrafields.php.
 *   — Файл не выполняется на фронтэнде: сам admin.extrafields.php
 *     защищён условием
 *         (defined('COT_CODE') && defined('COT_ADMIN')) or die(...);
 *   — Файл market.extrafields.php функций не объявляет и не
 *     возвращает значение. Его единственный побочный эффект —
 *     добавление записи в $extra_whitelist.
 *
 * Зависимости:
 *   — /system/admin/admin.extrafields.php — точка вызова хука
 *     `admin.extrafields.first` и потребитель массива $extra_whitelist;
 *   — /system/extrafields.php — предоставляет
 *     cot_extrafields_register_table(), cot_extrafields_add()/update()/remove(),
 *     cot_build_extrafields(), cot_import_extrafields(),
 *     cot_load_extrafields() и др., используемые admin.extrafields.php;
 *   — /modules/market/inc/market.functions.php — подключается через
 *     cot_incfile('market', 'module'); выполняет
 *     Cot::$db->registerTable('market') и
 *     cot_extrafields_register_table('market'), благодаря чему
 *     в области видимости появляется $db_market и таблица market
 *     регистрируется в реестре extrafields;
 *   — /system/admin/tpl/admin.extrafields.tpl — шаблон страницы,
 *     который выводит $tags_list через теги
 *     {ADMIN_EXTRAFIELDS_TAGS} в JS-блоке и через
 *     ADMIN_EXTRAFIELDS_ROW_* в табличном списке.
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

require_once cot_incfile('market', 'module');
$extra_whitelist[$db_market] = [
	'name' => $db_market,
	'caption' => $L['Module'].' Market',
	'type' => 'module',
	'code' => 'market',
	'tags' => [
		'market.list.tpl' => '{LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}',
		'market.tpl' => '{MARKET_XXXXX}, {MARKET_XXXXX_TITLE}',
		'market.add.tpl' => '{MARKETADD_FORM_XXXXX}, {MARKETADD_FORM_XXXXX_TITLE}',
		'market.edit.tpl' => '{MARKETEDIT_FORM_XXXXX}, {MARKETEDIT_FORM_XXXXX_TITLE}',
		'news.tpl' => '{MARKET_ROW_XXXXX}',
		'recentitems.market.tpl' => '{MARKET_ROW_XXXXX}',
	]
];