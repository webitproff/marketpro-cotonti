<?php
// declare(strict_types = 1);

/**
 * Edit store item.
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 * 
 * Filename: market.edit.php
 *
 * Path:    modules/market/inc/market.edit.php
 *
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ФАЙЛУ market.edit.php
 * ============================================================
 *
 * Назначение:
 *   Обрабатывает редактирование существующего товара модуля Market.
 *   Загружает данные товара по ID, проверяет права доступа (владелец или администратор),
 *   обрабатывает POST-запросы на обновление, предпросмотр или удаление,
 *   формирует форму редактирования с подстановкой актуальных данных,
 *   генерирует теги шаблона и выводит результат через XTemplate.
 *
 * Основные параметры URL:
 *   id=<числовой ID>        — обязательный идентификатор редактируемого товара;
 *   c=<код категории>       — опционально, для сохранения контекста категории;
 *   rparser=<код парсера>   — передаётся из POST для смены парсера текста;
 *   a=update                — действие обновления (отправка формы);
 *   preview=1               — предпросмотр: сохранить как черновик и перейти к просмотру;
 *   delete=1                — удалить товар (с подтверждением);
 *   ritemmarketdelete=1     — флаг удаления из POST-формы.
 *
 * Логика работы:
 *   1. Импортирует параметры id, c, rparser.
 *   2. Проверяет права на чтение модуля Market.
 *   3. Загружает товар по ID (если не найден — исключение NotFoundHttpException).
 *   4. Повторно проверяет права для конкретной категории товара.
 *   5. Определяет парсер (по умолчанию из конфига или 'html').
 *   6. Если пришёл POST с действием update:
 *      a) проверяет права на запись (владелец или админ);
 *      b) импортирует данные из POST через cot_market_import();
 *      c) обрабатывает предпросмотр (сохраняет как черновик и редиректит);
 *      d) обрабатывает удаление (через MarketControlService);
 *      e) валидирует данные;
 *      f) при отсутствии ошибок обновляет товар и редиректит в зависимости от статуса;
 *      g) при ошибках перенаправляет обратно на форму.
 *   7. Если действие не update — подготавливает данные для отображения формы:
 *      копирует $row_item в $item, дополняет мета-полями (с защитой от несуществующих ключей),
 *      определяет статус, загружает владельца.
 *   8. Проверяет права на редактирование (владелец или админ), иначе блокирует.
 *   9. Формирует хлебные крошки и набор тегов для формы (MARKETEDIT_*).
 *  10. Обрабатывает дополнительные поля (extrafields) с флагом MARKETEDIT_HAS_EXTRAFIELDS.
 *  11. Выводит сообщения, вызывает хуки, парсит шаблон и возвращает HTML.
 *
 * Используемые классы и сервисы:
 *   NotFoundHttpException  — исключение для ненайденного товара;
 *   MarketDictionary       — константы состояний товара (STATE_DRAFT и др.);
 *   MarketRepository       — репозиторий (в этом файле напрямую не используется,
 *                           но импортирован для возможных расширений);
 *   MarketControlService   — сервис удаления товара;
 *   UsersRepository        — получение данных пользователя (владельца);
 *   XTemplate              — шаблонизатор вывода формы.
 *
 * Хуки:
 *   market.edit.first          — в самом начале, до загрузки товара;
 *   market.edit.update.first   — перед обработкой POST-обновления;
 *   market.edit.update.import  — после импорта POST-данных;
 *   market.edit.update.error   — после валидации (если есть ошибки);
 *   market.edit.main           — после подготовки основных переменных и шаблона;
 *   market.edit.tags           — перед финальным парсингом шаблона.
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
 * Импортируем необходимые классы и подключаем API форм.
 * ===================================================================== */

// Импорт класса исключения для обработки случая, когда товар не найден
use cot\exceptions\NotFoundHttpException;

// Импорт словаря Market — константы состояний товара (STATE_PUBLISHED, STATE_DRAFT и др.)
use cot\modules\market\inc\MarketDictionary;

// Импорт репозитория товаров (может использоваться для дополнительных выборок)
use cot\modules\market\inc\MarketRepository;

// Импорт сервиса управления товарами (удаление и прочие операции)
use cot\modules\market\inc\MarketControlService;

// Импорт репозитория пользователей (получение данных владельца)
// в частности используем UsersRepository::getInstance()->getById()
use cot\users\UsersRepository;

// Стандартная защита от прямого обращения к файлу
defined('COT_CODE') or die('Wrong URL');

// Подключаем файл функций для работы с формами (валидация, импорт полей и т.д.)
require_once cot_incfile('forms');

/* =====================================================================
 * ИМПОРТ ПАРАМЕТРОВ И ПЕРВИЧНАЯ ПРОВЕРКА
 * ---------------------------------------------------------------------
 * Извлекаем данные из запроса и определяем права пользователя.
 * ===================================================================== */

// Получаем ID товара из GET-запроса (целое число)
$id = cot_import('id', 'G', 'INT');

// Получаем код категории из GET-запроса (текстовая строка)
$c = cot_import('c', 'G', 'TXT');

// Получаем значение парсера из POST-запроса (только буквенные символы)
$item['fieldmrkt_parser'] = cot_import('rparser', 'P', 'ALP');

// Получаем права пользователя для модуля Market в целом (область 'any')
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');

/* === Hook === */
// Вызываем хук market.edit.first — ранний хук, позволяющий плагинам вмешаться до загрузки товара
foreach (cot_getextplugins('market.edit.first') as $pl) {
    include $pl;
}
/* ===== */

// Блокируем доступ, если у пользователя нет права на чтение модуля Market
cot_block(Cot::$usr['auth_read']);

/* =====================================================================
 * ЗАГРУЗКА ТОВАРА И ПРОВЕРКА СУЩЕСТВОВАНИЯ
 * ---------------------------------------------------------------------
 * Если ID некорректен или товар не найден, выбрасываем исключение 404.
 * ===================================================================== */

// Проверяем, что ID корректен (больше нуля)
if (!$id || $id < 0) {
    // Если ID невалиден, выбрасываем исключение "не найдено"
    throw new NotFoundHttpException();
}

// Загружаем данные товара из базы данных по ID (подготовленный запрос)
$row_item = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?', $id)->fetch();

// Если товар не найден (fetch вернул null), выбрасываем исключение
if ($row_item === null) {
    throw new NotFoundHttpException();
}

// Повторно получаем права, но уже для конкретной категории товара
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', $row_item['fieldmrkt_cat']);

/* =====================================================================
 * УСТАНОВКА ПАРСЕРА
 * ---------------------------------------------------------------------
 * Определяем парсер текста товара из POST или из конфигурации.
 * ===================================================================== */

// Устанавливаем парсер по умолчанию, если он пустой
if (empty($item['fieldmrkt_parser'])) {
    // Берём значение из конфигурации модуля, а если его нет, то 'html'
    $item['fieldmrkt_parser'] = $cfg['market']['marketparser'] ?: 'html';
}

// Получаем список всех доступных парсеров
// переменная в текущей версии документа не используется, но была проблема, не вспомню сейчас, но точно не просто так.
$parser_list = cot_get_parsers();

// Устанавливаем текущий парсер из данных товара
Cot::$sys['marketparser'] = $row_item['fieldmrkt_parser'];

/* =====================================================================
 * ОБРАБОТКА ДЕЙСТВИЯ 'update'
 * ---------------------------------------------------------------------
 * Если пользователь отправил форму редактирования, обрабатываем её.
 * ===================================================================== */

// Проверяем, было ли действие 'update' (отправка формы редактирования)
if ($a == 'update') {

    /* === Hook === */
    // Хук перед обработкой обновления, позволяет плагинам вмешаться
    foreach (cot_getextplugins('market.edit.update.first') as $pl) {
        include $pl;
    }
    /* ===== */

    // Проверяем право на редактирование ПЕРЕД обработкой POST-запроса: либо администратор, либо владелец товара с правом записи
	// либо администратор, либо владелец товара с правом записи.
	// Это защита обработчика update от чужих POST-запросов (create/update/delete).
	// Вторая проверка (перед выводом формы ниже) защищает показ самой формы.
	// Обе проверки нужны — они закрывают разные точки входа.
    cot_block(Cot::$usr['isadmin'] || Cot::$usr['auth_write'] && Cot::$usr['id'] == $row_item['fieldmrkt_ownerid']);

    // Импортируем данные из POST, передавая $row_item как исходные данные товара
    $ritem = cot_market_import('POST', $row_item, Cot::$usr);

    /**
     * Обработка нажатия кнопки «Предпросмотр».
     *
     * Если в форме была нажата кнопка с именем "preview" (значение 1),
     * сохраняем товар как черновик (state = 2) и переходим на страницу предпросмотра.
     */
    if (cot_import('preview', 'P', 'BOL')) {
        // Устанавливаем состояние черновика
        $ritem['fieldmrkt_state'] = MarketDictionary::STATE_DRAFT;
        // Если ошибок нет, обновляем товар и редиректим на предпросмотр
        if (!cot_error_found()) {
            cot_market_update($id, $ritem);
            cot_redirect(cot_url('market', ['m' => 'preview', 'id' => $id], '', true));
            exit;
        }
    }

    // Определяем, был ли запрос на удаление
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Если POST, проверяем флаг ritemmarketdelete
        $ritemdelete = cot_import('ritemmarketdelete', 'P', 'BOL');
    } else {
        // Если GET, проверяем параметр delete и проверяем токен безопасности
        $ritemdelete = cot_import('delete', 'G', 'BOL');
        cot_check_xg();
    }

    // Если пришёл запрос на удаление
    if ($ritemdelete) {
        // Вызываем сервис удаления товара
        $resultOrMessage = MarketControlService::getInstance()->delete($id, $row_item);
        // Если удаление прошло успешно, выводим сообщение и редиректим в категорию
        if ($resultOrMessage !== false) {
            cot_message($resultOrMessage);
            cot_redirect(cot_url('market', ['c' => $row_item['fieldmrkt_cat']], '', true));
        }
    }

    /* === Hook === */
    // Хук после импорта POST-данных, позволяет плагинам модифицировать $ritem
    foreach (cot_getextplugins('market.edit.update.import') as $pl) {
        include $pl;
    }
    /* ===== */

    // Валидируем данные товара
    cot_market_validate($ritem);

    /* === Hook === */
    // Хук после валидации, если есть ошибки (можно их обработать)
    foreach (cot_getextplugins('market.edit.update.error') as $pl) {
        include $pl;
    }
    /* ===== */

    // Если ошибок нет
    if (!cot_error_found()) {
        // Обновляем товар в базе данных
        cot_market_update($id, $ritem);

        // В зависимости от состояния товара формируем URL для редиректа
        switch ($ritem['fieldmrkt_state']) {
            case MarketDictionary::STATE_PUBLISHED:
                // Если товар опубликован, редиректим на его страницу
                $r_url = cot_market_url($ritem, [], '', true);
                break;

            case MarketDictionary::STATE_PENDING:
                // Если отправлен на модерацию, показываем стандартное сообщение
                $r_url = cot_url('message', 'msg=300', '', true);
                break;

            case MarketDictionary::STATE_DRAFT:
                // Если сохранён как черновик, выводим сообщение и возвращаемся к редактированию
                cot_message(Cot::$L['market_savedasdraft']);
                $r_url = cot_url('market', 'm=edit&id=' . $id, '', true);
                break;
        }
        // Выполняем редирект
        cot_redirect($r_url);
    } else {
        // Если были ошибки валидации, возвращаемся на форму редактирования
        cot_redirect(cot_url('market', "m=edit&id=$id", '', true));
    }
}
/* =====================================================================
 * КОПИРОВАНИЕ ДАННЫХ ТОВАРА: РАЗДЕЛЕНИЕ ОРИГИНАЛА И РАБОЧЕЙ КОПИИ
 * ---------------------------------------------------------------------
 * В этом месте мы создаём две переменные с данными товара:
 *   $row_item — оригинальный массив, полученный из БД. Его НЕЛЬЗЯ изменять,
 *               потому что он может потребоваться позже (например, для сравнения
 *               при обновлении, в хуках, при проверке изменения категории).
 *   $item     — рабочая копия, которую можно безопасно модифицировать:
 *               добавлять недостающие ключи, подменять значения, подготавливать
 *               данные для формы и т.д. Все изменения касаются только $item.
 *
 * Это стандартный приём в Cotonti, чтобы избежать случайного изменения
 * исходных данных, которые могут использоваться в других частях кода или плагинах.
 * ===================================================================== */

// Создаём рабочую копию массива $row_item.
// В PHP массивы копируются по значению, поэтому $item становится независимой копией.
$item = $row_item;

/* Пример для понимания:
 * Допустим, $row_item содержит:
 * [
 *     'fieldmrkt_id'    => 123,
 *     'fieldmrkt_title' => 'Мой товар',
 *     'fieldmrkt_cat'   => 'electronics',
 * ]
 *
 * После $item = $row_item; в $item будут те же данные.
 * Теперь можно добавить новый ключ:
 * $item['fieldmrkt_metah1'] = '';
 *
 * Результат:
 * $row_item['fieldmrkt_metah1'] — НЕ существует (оригинал не тронут)
 * $item['fieldmrkt_metah1']     — существует и равен ''
 *
 * Это позволяет без опасений дополнять $item нужными для формы полями,
 * не влияя на $row_item, который может понадобиться, например, в хуках
 * или при вызове функций обновления, где ожидаются исходные данные.
 */

// Далее мы будем работать только с $item, а $row_item останется нетронутым.
 
/* =====================================================================
 * ЗАЩИТА ОТ ВАРНИНГОВ И ОПРЕДЕЛЕНИЕ СТАТУСА
 * ---------------------------------------------------------------------
 * Дополняем массив $item недостающими ключами, чтобы избежать
 * предупреждений PHP при обращении к ним. Затем определяем
 * текстовый статус товара.
 * ===================================================================== */

// Если ключ fieldmrkt_metah1 отсутствует, присваиваем пустую строку (защита от Undefined index)
$item['fieldmrkt_metah1'] = $item['fieldmrkt_metah1'] ?? '';
// Аналогично для мета-заголовка
$item['fieldmrkt_metatitle'] = $item['fieldmrkt_metatitle'] ?? '';
// И для мета-описания
$item['fieldmrkt_metadesc'] = $item['fieldmrkt_metadesc'] ?? '';

// Получаем статус товара в текстовом виде ('published', 'draft', 'pending')
$item['fieldmrkt_status'] = cot_market_status($item['fieldmrkt_state']);

/* =====================================================================
 * ПОЛУЧЕНИЕ ДАННЫХ ВЛАДЕЛЬЦА
 * ---------------------------------------------------------------------
 * Загружаем полную информацию о владельце товара через UsersRepository.
 * Если пользователь не найден, используем заглушку 'Deleted'.
 * ===================================================================== */

// Инициализируем переменную для данных владельца
$owner_info = null;
// Если ID владельца задан, загружаем его данные
if (!empty($item['fieldmrkt_ownerid'])) {
    $owner_info = UsersRepository::getInstance()->getById((int)$item['fieldmrkt_ownerid']);
}
// Определяем имя владельца: если данные найдены и есть user_name, берём его, иначе 'Deleted'
$owner_name = ($owner_info && isset($owner_info['user_name'])) ? $owner_info['user_name'] : Cot::$L['Deleted'];

/* =====================================================================
 * ПРОВЕРКА ПРАВ НА РЕДАКТИРОВАНИЕ
 * ---------------------------------------------------------------------
 * Убеждаемся, что текущий пользователь может редактировать товар:
 * либо это администратор, либо владелец с правом записи.
 * Если прав нет, cot_block() перенаправит на страницу ошибки.
 * ===================================================================== */
// Проверяем право на редактирование ПЕРЕД показом формы.
// Это защита страницы формы от открытия чужого товара по прямой ссылке
// (GET ?m=edit&id=N). Первая проверка (внутри блока update выше) защищает
// обработчик POST-запросов. Удалять одну из проверок нельзя (ПЕРВАЯ в внутри блока if ($a == 'update')).
// Блокируем доступ, если пользователь не админ и не владелец с правом записи

cot_block(Cot::$usr['isadmin'] || Cot::$usr['auth_write'] && Cot::$usr['id'] == $item['fieldmrkt_ownerid']);

// Если очень хочется «убрать визуальный дубль» — можно вынести условие в отдельную переменную:
// Права на редактирование текущего товара (для переиспользования в двух точках входа)
/* 
    $canEdit = Cot::$usr['isadmin'] || Cot::$usr['auth_write'] && Cot::$usr['id'] == $row_item['fieldmrkt_ownerid'];
 */
// И далее в двух местах: 
/* 
	cot_block($canEdit); 
*/
// Это уберёт «визуальный дубль» условия, но обе точки проверки останутся. Такой вариант — компромисс между читаемостью и защитой. 


/* =====================================================================
 * УСТАНОВКА ЗАГОЛОВКА СТРАНИЦЫ И ХЛЕБНЫХ КРОШЕК
 * ---------------------------------------------------------------------
 * Задаём заголовок и мета-информацию для страницы редактирования.
 * ===================================================================== */

// Устанавливаем подзаголовок страницы
Cot::$out['subtitle'] = Cot::$L['market_form_item_edit_title'];

// Если свойство head ещё не определено, инициализируем пустой строкой
if (!isset(Cot::$out['head'])) {
    Cot::$out['head'] = '';
}
// Добавляем мета-тег noindex, чтобы страница не индексировалась
Cot::$out['head'] .= Cot::$R['code_noindex'];

// Устанавливаем подраздел (название категории товара)
Cot::$sys['sublocation'] = Cot::$structure['market'][$item['fieldmrkt_cat']]['title'];

/* =====================================================================
 * ВЫБОР И ЗАГРУЗКА ШАБЛОНА
 * ---------------------------------------------------------------------
 * Формируем имя шаблона: market.edit.<tpl_категории>.tpl или
 * market.edit.tpl, если для категории не задан отдельный шаблон.
 * ===================================================================== */

// Код модуля, используется как первая часть имени файла шаблона
$tpl_ExtCode = 'market';

// Часть имени, обозначающая действие (редактирование)
$tpl_PartExt = 'edit';

// Часть имени шаблона категории (если задан, то используется специализированный шаблон)
$tpl_PartExtSecond = Cot::$structure['market'][$item['fieldmrkt_cat']]['tpl'];

// Загружаем файл шаблона с учётом возможного специфичного шаблона категории
$extTplFile = cot_tplfile(
    [
        $tpl_ExtCode,
        $tpl_PartExt,
        $tpl_PartExtSecond
    ],
    'module',
    true
);

// Если файл шаблона не найден — регистрируем ошибку и прерываем выполнение
// с кодом 500 (без шаблона дальнейшая работа невозможна).
if (empty($extTplFile)) {
    cot_error('Шаблон не найден');
    cot_die_message(500);
}

// Формируем абсолютный путь к шаблону для отладки (передаётся в шаблон)
$tpl_Path = $sys['abs_url'] . $extTplFile;

// Хук после подготовки основных переменных и шаблона
/* === Hook === */
foreach (cot_getextplugins('market.edit.main') as $pl) {
    include $pl;
}
/* ===== */

// Создаём объект шаблона
$t = new XTemplate($extTplFile);

/* =====================================================================
 * ФОРМИРОВАНИЕ ХЛЕБНЫХ КРОШЕК
 * ---------------------------------------------------------------------
 * Строим цепочку: категория -> товар -> "Редактирование".
 * ===================================================================== */

// Получаем путь категории
$breadcrumbs = cot_structure_buildpath('market', $item['fieldmrkt_cat']);

// Добавляем ссылку на сам товар
$breadcrumbs[] = [cot_market_url($item), $item['fieldmrkt_title']];

// Добавляем текущий пункт "Редактирование"
$breadcrumbs[] = Cot::$L['market_form_item_edit_title'];

/* =====================================================================
 * ФОРМИРОВАНИЕ МАССИВА ТЕГОВ ДЛЯ ФОРМЫ
 * ---------------------------------------------------------------------
 * Создаём массив $itemedit_array с тегами MARKETEDIT_* для шаблона.
 * Каждый тег соответствует полю формы или служебной информации.
 * ===================================================================== */

// Инициализируем массив тегов формы
$itemedit_array = [
    // Хлебные крошки
    'MARKETEDIT_BREADCRUMBS' => cot_breadcrumbs($breadcrumbs, Cot::$cfg['homebreadcrumb']),

    // URL отправки формы (action)
    'MARKETEDIT_FORM_SEND' => cot_url('market', ['m' => 'edit', 'a' => 'update', 'id' => $item['fieldmrkt_id']]),

    // Служебные поля: ID, числовой статус, текстовый статус, локализованный статус
    'MARKETEDIT_FORM_ID' => $item['fieldmrkt_id'],
    'MARKETEDIT_FORM_STATE' => $item['fieldmrkt_state'],
    'MARKETEDIT_FORM_STATUS' => $item['fieldmrkt_status'],
    'MARKETEDIT_FORM_LOCAL_STATUS' => Cot::$L['market_status_' . $item['fieldmrkt_status']],

    // Готовая ссылка на профиль владельца с именем
    'MARKETEDIT_FORM_OWNER_FULL' => cot_rc_link(
        cot_url('users', [
            'm' => 'details',
            'id' => $item['fieldmrkt_ownerid'],
            'u' => htmlspecialchars($owner_name)
        ]),
        htmlspecialchars($owner_name)
    ),

    // Чистый URL на профиль владельца
    'MARKETEDIT_FORM_OWNER_URL' => cot_url('users', [
        'm' => 'details',
        'id' => $item['fieldmrkt_ownerid'],
        'u' => htmlspecialchars($owner_name)
    ]),

    // Просто никнейм владельца (текст)
    'MARKETEDIT_FORM_OWNER_NAME' => htmlspecialchars($owner_name),

    // Категории: стандартный select и Select2, с учётом возможного фильтра $c
    'MARKETEDIT_FORM_CAT' => cot_selectbox_structure('market', $item['fieldmrkt_cat'], 'ritemmarketcat'),
    'MARKETEDIT_FORM_CAT_SHORT' => cot_selectbox_structure('market', $item['fieldmrkt_cat'], 'ritemmarketcat', $c),
    'MARKETEDIT_FORM_CAT_S2' => cot_market_selectbox_structure_select2('market', $item['fieldmrkt_cat'], 'ritemmarketcat'),
    'MARKETEDIT_FORM_CAT_SHORT_S2' => cot_market_selectbox_structure_select2('market', $item['fieldmrkt_cat'], 'ritemmarketcat', $c),

    // SEO-поля: мета-заголовок, мета-описание, H1
    'MARKETEDIT_FORM_META_TITLE' => cot_inputbox('text', 'ritemmarketmetatitle', $item['fieldmrkt_metatitle'], ['maxlength' => '255']),
    'MARKETEDIT_FORM_META_DESC' => cot_textarea('ritemmarketmetadesc', $item['fieldmrkt_metadesc'], 2, 64, ['maxlength' => '255']),
    'MARKETEDIT_FORM_META_H1' => cot_inputbox('text', 'ritemmarketmetah1', $item['fieldmrkt_metah1'], ['maxlength' => '255']),

    // Основные поля
    'MARKETEDIT_FORM_ALIAS' => cot_inputbox('text', 'ritemmarketalias', $item['fieldmrkt_alias'], ['maxlength' => '255']),
    'MARKETEDIT_FORM_PCOD' => cot_inputbox('text', 'ritemmarketpcod', $item['fieldmrkt_pcod'], ['maxlength' => '64']),
    'MARKETEDIT_FORM_TITLE' => cot_inputbox('text', 'ritemmarkettitle', $item['fieldmrkt_title'], ['maxlength' => '255']),
    'MARKETEDIT_FORM_DESCRIPTION' => cot_textarea('ritemmarketdesc', $item['fieldmrkt_desc'], 2, 64, ['maxlength' => '255']),

    // Даты: дата добавления с чекбоксом "обновить дату", дата последнего обновления
    'MARKETEDIT_FORM_DATE' => cot_selectbox_date($item['fieldmrkt_date'], 'long', 'ritemmarketdate') . ' ' . Cot::$usr['timetext'],
    'MARKETEDIT_FORM_DATENOW' => cot_checkbox(0, 'ritemmarketdatenow'),
    'MARKETEDIT_FORM_UPDATED' => cot_date('datetime_full', $item['fieldmrkt_updated']) . ' ' . Cot::$usr['timetext'],

    // Контент: текст с редактором и выбор парсера
    'MARKETEDIT_FORM_TEXT' => cot_textarea('ritemmarkettext', $item['fieldmrkt_text'], 24, 120, '', 'input_textarea_editor'),
    'MARKETEDIT_FORM_PARSER' => cot_selectbox($item['fieldmrkt_parser'], 'ritemmarketparser', cot_get_parsers(), cot_get_parsers(), false),

    // Цены
    'MARKETEDIT_FORM_COSTDFLT' => cot_inputbox('text', 'ritemmarketcostdflt', $item['fieldmrkt_costdflt'], 'size="10"'),
    'MARKETEDIT_FORM_COST_USD' => cot_inputbox('text', 'ritemmarketcostusd', $item['fieldmrkt_cost_usd'], 'size="10"'),

    // Радиокнопки для удаления (Да/Нет)
    'MARKETEDIT_FORM_DELETE' => cot_radiobox(0, 'ritemmarketdelete', [1, 0], [Cot::$L['Yes'], Cot::$L['No']]),
];

/* =====================================================================
 * ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ ДЛЯ АДМИНИСТРАТОРА
 * ---------------------------------------------------------------------
 * Если пользователь администратор, добавляем поля для изменения
 * владельца и счётчика просмотров.
 * ===================================================================== */

// Если текущий пользователь — администратор
if (Cot::$usr['isadmin']) {
    // Добавляем в массив тегов поля для ID владельца и счётчика просмотров
    $itemedit_array += [
        'MARKETEDIT_FORM_OWNER_ID' => cot_inputbox('text', 'ritemmarketownerid', $item['fieldmrkt_ownerid'], ['maxlength' => '24']),
        'MARKETEDIT_FORM_HITS' => cot_inputbox('text', 'ritemmarketcount', $item['fieldmrkt_count'], ['maxlength' => '8']),
    ];
}

/* =====================================================================
 * ПЕРЕДАЧА ТЕГОВ В ШАБЛОН
 * ---------------------------------------------------------------------
 * Присваиваем все сформированные теги объекту XTemplate.
 * ===================================================================== */

// Передаём массив тегов формы в шаблон
$t->assign($itemedit_array);

// Предупреждение для разработчика (комментарий-подсказка)
// если видим
// Warning: Undefined array key "fieldmrkt_file" in /...../modules/market/inc/market.edit.php on line 190
// идем в экстраполя Управление сайтом Прочее Экстраполя cot_market - Модуль Market и удаляем fieldmrkt_file

/* =====================================================================
 * ОБРАБОТКА ДОПОЛНИТЕЛЬНЫХ ПОЛЕЙ (EXTRAFIELDS)
 * ---------------------------------------------------------------------
 * Если для таблицы market есть дополнительные поля, выводим их в шаблон.
 * Используем флаг MARKETEDIT_HAS_EXTRAFIELDS для условного отображения.
 * ===================================================================== */

// Передаём флаг наличия дополнительных полей в шаблон
$t->assign('MARKETEDIT_HAS_EXTRAFIELDS', !empty(Cot::$extrafields[Cot::$db->market]));

// Если дополнительные поля существуют
if (!empty(Cot::$extrafields[Cot::$db->market])) {
    // Перебираем каждое дополнительное поле
    foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
        // Преобразуем имя поля в верхний регистр для использования в тегах
        $uname = strtoupper($exfld['field_name']);
        // Генерируем HTML-элемент для поля
        $extrafieldElement = cot_build_extrafields(
            'ritemmarket' . $exfld['field_name'],
            $exfld,
            $item['fieldmrkt_' . $exfld['field_name']] // x-line 190
        );
        // Получаем заголовок поля
        $extrafieldTitle = cot_extrafield_title($exfld, 'market_');

        // Присваиваем теги для конкретного поля и общие теги для цикла EXTRAFLD
        // Передаём в шаблон набор тегов для текущего дополнительного поля.
        // Используются две группы тегов:
        //   1. Уникальные теги с именем поля: MARKETEDIT_FORM_<ИМЯ> и _TITLE
        //      (для прямого вывода конкретного поля в шаблоне).
        //   2. Общие теги внутри цикла EXTRAFLD: MARKETEDIT_FORM_EXTRAFLD и _TITLE
        //      (для автоматического перебора всех полей в шаблоне).
        $t->assign([
			// индивидуально выводим заголовок и значения(форму) поля:
			// Готовый HTML-элемент поля
            'MARKETEDIT_FORM_' . $uname => $extrafieldElement,
			// Заголовок поля
            'MARKETEDIT_FORM_' . $uname . '_TITLE' => $extrafieldTitle,
			
			// Универсальные теги (выводим все поля автоматом в шаблоне) для вывода внутри цикла:
			//<!-- BEGIN: EXTRAFLD --> {MARKETEDIT_FORM_EXTRAFLD_TITLE} {MARKETEDIT_FORM_EXTRAFLD}<!-- END: EXTRAFLD -->
            'MARKETEDIT_FORM_EXTRAFLD' => $extrafieldElement,
            'MARKETEDIT_FORM_EXTRAFLD_TITLE' => $extrafieldTitle,
			
			// Машинное имя поля (например, 'product_status') — для отладки и т.п.
            'MARKETEDIT_FORM_EXTRAFLD_CODENAME' => $exfld['field_name'],
        ]);
        // Парсим блок MAIN.EXTRAFLD для вывода текущего поля
        // При каждой итерации цикла создаётся новый экземпляр блока,
        // который затем выводится в месте, где объявлен <!-- BEGIN: EXTRAFLD --> ... <!-- END: EXTRAFLD -->.
        $t->parse('MAIN.EXTRAFLD');
    }
}

/* =====================================================================
 * ПЕРЕДАЧА ПУТИ К ШАБЛОНУ (ДЛЯ ОТЛАДКИ)
 * ===================================================================== */
// в шаблоне показываем путь к нему: <!-- IF {PHP.usr.isadmin} -->{TPL_PATH}<!-- ENDIF -->
$t->assign('TPL_PATH', $tpl_Path); 

/* =====================================================================
 * ВЫВОД НАКОПЛЕННЫХ СООБЩЕНИЙ
 * ===================================================================== */
cot_display_messages($t);

/* === Hook === */
// Хук перед финальным парсингом шаблона
foreach (cot_getextplugins('market.edit.tags') as $pl) {
    include $pl;
}
/* ===== */

/* =====================================================================
 * ОПРЕДЕЛЕНИЕ ВОЗМОЖНОСТИ ПУБЛИКАЦИИ И ПАРСИНГ БЛОКА ADMIN
 * ---------------------------------------------------------------------
 * Переменная $usr_can_publish используется в шаблоне для показа
 * кнопки "Опубликовать". Также парсим блок ADMIN (если он есть).
 * ===================================================================== */

// По умолчанию публикация запрещена
$usr_can_publish = false;

// Если пользователь администратор
if (Cot::$usr['isadmin']) {
    // Если включена автовалидация, разрешаем публикацию
    if (Cot::$cfg['market']['marketautovalidate']) {
        $usr_can_publish = true;
    }
    // Парсим блок ADMIN (если он определён в шаблоне)
    $t->parse('MAIN.ADMIN');
}

/* =====================================================================
 * ФИНАЛЬНЫЙ ПАРСИНГ ШАБЛОНА
 * ---------------------------------------------------------------------
 * Парсим основной блок MAIN и сохраняем готовый HTML в $moduleBody.
 * ===================================================================== */

// Парсим основной блок
$t->parse('MAIN');

// Получаем готовый HTML-код модуля
$moduleBody = $t->text('MAIN');