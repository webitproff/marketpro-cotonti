<?php
/**
 * Предпросмотр карточки товара.
 *
 * Файл: modules/market/inc/market.preview.php
 *
 * После нажатия кнопки «Предпросмотр» на странице редактирования товар
 * сохраняется как черновик (state=2) и открывается эта страница.
 * Пользователь может опубликовать товар или вернуться к редактированию.
 *
 * @package Market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

// Подключаем классы для работы 
// Импорт класса MarketDictionary, содержащего константы состояний товара.
use cot\modules\market\inc\MarketDictionary;
// Импорт класса UsersRepository для загрузки данных пользователя по ID.
use cot\users\UsersRepository;

// Проверка, что файл запущен в контексте Cotonti; иначе блокировка.
defined('COT_CODE') or die('Wrong URL');

/**
 * Получаем ID товара из GET-параметра.
 * cot_import('id', 'G', 'INT') — извлекает значение 'id' из массива $_GET,
 * валидирует как целое число и возвращает его.
 * Если параметр отсутствует или некорректен, будет 0.
 *
 * @var int $id
 */
$id = cot_import('id', 'G', 'INT');

/**
 * Проверяем право на запись в целом (для модуля).
 * cot_auth('market', 'any') возвращает массив прав:
 * [auth_read, auth_write, isadmin].
 * Здесь мы получаем права на уровне модуля без привязки к конкретной категории.
 */
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');

// Если у пользователя нет права на запись (auth_write = false), блокируем доступ.
cot_block(Cot::$usr['auth_write']);

/**
 * Загружаем товар вместе с именем владельца.
 *
 * SQL-запрос:
 * SELECT m.*, u.user_name
 * FROM cot_market AS m
 * LEFT JOIN cot_users AS u ON u.user_id = m.fieldmrkt_ownerid
 * WHERE m.fieldmrkt_id = ?
 *
 * Параметр [$id] привязывается к плейсхолдеру '?' в WHERE.
 * LEFT JOIN гарантирует, что даже если владелец удалён,
 * user_name будет NULL (или пустая строка).
 * Метод fetch() возвращает одну строку результата в виде ассоциативного массива.
 */
$item = Cot::$db->query(
    'SELECT m.*, u.user_name
     FROM ' . Cot::$db->market . ' AS m
     LEFT JOIN ' . Cot::$db->users . ' AS u ON u.user_id = m.fieldmrkt_ownerid
     WHERE m.fieldmrkt_id = ?',
    [$id]
)->fetch();

// Если товар не найден (fetch вернул false), показываем страницу 404.
if (!$item) {
    cot_die_message(404);
}

/**
 * Добавляем поле user_id в массив $item.
 * Оно равно ownerid товара.
 * Это нужно для шаблона, где может использоваться {PHP.item.user_id}.
 */
$item['user_id'] = $item['fieldmrkt_ownerid'];

/**
 * Загружаем полный профиль владельца через UsersRepository.
 * Это необходимо для корректной генерации тегов пользователя
 * (cot_generate_usertags), которой нужны все поля user_*.
 * getById() возвращает массив данных пользователя или null, если пользователь не найден.
 */
$owner_info = UsersRepository::getInstance()->getById((int)$item['fieldmrkt_ownerid']);

/**
 * Проверяем права на конкретную категорию товара.
 * Теперь мы знаем категорию товара ($item['fieldmrkt_cat']),
 * поэтому получаем права именно для неё.
 * cot_auth('market', $item['fieldmrkt_cat']) возвращает [auth_read, auth_write, isadmin]
 * для данной категории.
 */
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', $item['fieldmrkt_cat']);

// Доступ к предпросмотру имеют:
// - администратор (isadmin = true)
// - или пользователь с правом записи в этой категории (auth_write = true) и владелец товара.
cot_block(Cot::$usr['isadmin'] || (Cot::$usr['auth_write'] && Cot::$usr['id'] == $item['fieldmrkt_ownerid']));
// Для остальных (гостей, других пользователей, поисковых роботов) будет показана страница ошибки доступа (403), а не предпросмотр.
// Поисковики не смогут проиндексировать страницу, так как не авторизованы и получат 403. К тому же мы добавляем noindex для подстраховки.
// Предпросмотр виден только ПРОДАВЦУ(владельцу) или администратору. Чужие не увидят ни товар, ни его черновик.


/**
 * Обработка нажатия кнопки «Опубликовать».
 * Если в URL передан параметр a=save (GET-запрос),
 * меняем статус товара на «Опубликован» (state=0) или «На модерации» (state=1)
 * в зависимости от прав и настроек.
 */

if (cot_import('a', 'G', 'ALP') === 'save') {
    // Проверяем защитный токен XG для предотвращения CSRF-атак.
    cot_check_xg();

    // Определяем новый статус товара.
    // Если пользователь администратор и разрешена автопубликация (marketautovalidate),
    // то статус = PUBLISHED, иначе PENDING (на модерации).
    $state = (Cot::$usr['isadmin'] && Cot::$cfg['market']['marketautovalidate'])
        ? MarketDictionary::STATE_PUBLISHED
        : MarketDictionary::STATE_PENDING;

    // Обновляем поле fieldmrkt_state в таблице товаров.
    Cot::$db->update(
        Cot::$db->market,
        ['fieldmrkt_state' => $state],
        'fieldmrkt_id = ?',
        [$id]
    );

    // Формируем URL для перенаправления на страницу товара.
    // Если у товара есть алиас, используем его, иначе используем ID.
    $redirectParams = ['c' => $item['fieldmrkt_cat']];
    if (!empty($item['fieldmrkt_alias'])) {
        $redirectParams['al'] = $item['fieldmrkt_alias'];
    } else {
        $redirectParams['id'] = $id;
    }

    // Перенаправляем браузер на сформированный URL.
    cot_redirect(cot_url('market', $redirectParams, '', true));
    exit;
}

/**
 * Определяем шаблон отображения товара (как в market.main.php).
 * Получаем структуру категории, чтобы узнать имя шаблона.
 */
$cat = Cot::$structure['market'][$item['fieldmrkt_cat']];

// Получаем путь к файлу шаблона. Если для категории задан свой шаблон (tpl), используется он.
$mskin = cot_tplfile(['market', $cat['tpl']], 'module', true);

// Если шаблон не найден — выводим сообщение об ошибке.
if (empty($mskin)) {
    cot_error('Шаблон не найден');
}

// Формируем абсолютный URL к файлу шаблона (для отладки или отображения админу).
$tpl_Path = Cot::$cfg['mainurl'] . '/' . $mskin;

// Создаём объект XTemplate для обработки шаблона.
$t = new XTemplate($mskin);


// $c = $item['fieldmrkt_cat']; // если кто-то вместо {MARKET_CAT} написал в market.tpl тег {PHP.c}

/**
 * Устанавливаем заголовок страницы и мета-теги.
 * noindex — предпросмотр не должен индексироваться поисковиками.
 */
Cot::$out['subtitle'] = $item['fieldmrkt_title'];
Cot::$out['head'] = Cot::$R['code_noindex'];

/**
 * Генерируем теги товара. Админ-кнопки не показываем.
 */
// Добавляем URL товара в массив $item для корректной генерации тегов.
$item['fieldmrkt_pageurl'] = cot_market_url($item, [], '', true);

// Генерируем теги товара и присваиваем их шаблону.
// Параметры:
// $item — данные товара,
// 'MARKET_' — префикс тегов,
// 0 — не обрезать текст,
// false — не показывать админ-кнопки,
// Cot::$cfg['homebreadcrumb'] — добавлять ли ссылку на главную в хлебные крошки,
// '' — пустая строка для backUrl.
$t->assign(cot_generate_markettags(
    $item,
    'MARKET_',
    0,
    false,
    Cot::$cfg['homebreadcrumb'],
    '',
    ''
));

/**
 * Добавляем теги владельца.
 * Используем полный профиль $owner_info, а не $item.
 */
if ($owner_info) {
    // Генерируем HTML-ссылку на профиль пользователя.
    $t->assign('MARKET_OWNER', cot_build_user($owner_info['user_id'], $owner_info['user_name']));

    // Генерируем дополнительные теги пользователя (например, MARKET_OWNER_NAME, MARKET_OWNER_ID и т.д.).
    $t->assign(cot_generate_usertags($owner_info, 'MARKET_OWNER_'));
}


/**
 * Передаём В ШАБЛОН флаг предпросмотра и ссылки для кнопок.
 */
$t->assign([
    // Флаг, указывающий шаблону, что мы находимся в режиме предпросмотра.
    // Если true, в шаблоне можно выводить предупреждение и кнопки «Опубликовать»/«Редактировать».
    'MARKET_IS_PREVIEW' => true,

    // URL для кнопки «Опубликовать».
    // cot_url() генерирует внутреннюю ссылку Cotonti.
    // Параметры:
    //   'market' — модуль,
    //   'm=preview&a=save&id=' . $item['fieldmrkt_id'] . '&' . cot_xg() — строка GET-параметров.
    // cot_xg() добавляет защитный токен для предотвращения CSRF.
    'MARKET_PREVIEW_SAVE_URL' => cot_url('market', 'm=preview&a=save&id=' . $item['fieldmrkt_id'] . '&' . cot_xg()),

    // URL для кнопки «Редактировать».
    // Ведёт на страницу редактирования товара с его ID.
    'MARKET_PREVIEW_EDIT_URL' => cot_url('market', 'm=edit&id=' . $item['fieldmrkt_id']),
]);

/**
 * Хук для дополнительной обработки тегов.
 * Позволяет другим плагинам модифицировать теги перед выводом.
 */
/* === Hook === */
foreach (cot_getextplugins('market.tags') as $pl) {
    include $pl;
}
/* ===== */

// Парсим основной блок шаблона.
$t->parse('MAIN');

// Получаем готовый HTML-код страницы.
$moduleBody = $t->text('MAIN');