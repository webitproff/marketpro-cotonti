<?php
/**
 * Installation handler for market 
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 * Adds all categories to cot_structure with automatic rights inheritance
 * Path:    modules/market/setup/market.install.php
 * Filename: market.install.php
 * Date: Aug 27, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('market', 'module');
require_once cot_incfile('structure');
// Подключаем глобальные объекты: $db (для запросов к БД) и $db_auth (имя таблицы прав). 
// $db_structure не нужен, т.к. cot_structure_add() сама работает с БД.

global $db, $db_auth; 

/**
 * =========================================================================
 * ДЕМОНСТРАЦИОННЫЕ КАТЕГОРИИ МОДУЛЯ MARKET
 * =========================================================================
 *
 * Каждая категория описывается массивом со следующими ключами:
 *
 * 'code'  — Уникальный символьный идентификатор категории (slug).
 *           Используется в URL адресах, для формирования ссылок,
 *           а также в таблице прав доступа (cot_auth.auth_option).
 *           Должен быть уникальным в пределах области 'market'.
 *           Пример: 'computers-components', 'gaming-desktops'.
 *
 * 'title' — Название категории, которое отображается пользователям
 *           на сайте (в меню, заголовках, хлебных крошках и т.д.).
 *           Может содержать пробелы и специальные символы.
 *           Пример: 'Computers & Components'.
 *
 * 'desc'  — Краткое описание категории. Поясняет, что именно
 *           размещается в данной категории. Выводится на странице
 *           категории и в подсказках.
 *           Пример: 'Desktop computers, components, and peripherals.'
 *
 * 'path'  — Строковое представление пути категории в иерархии.
 *           Определяет положение категории относительно других.
 *
 *           ФОРМАТ ПУТИ:
 *           Путь состоит из числовых сегментов, разделённых точкой.
 *           Количество сегментов указывает уровень вложенности:
 *               - 1 сегмент  → корневая категория (верхний уровень);
 *               - 2 сегмента → дочерняя категория (второй уровень);
 *               - 3 сегмента → внучатая категория (третий уровень) и т.д.
 *
 *           Каждый сегмент — это трёхзначный порядковый номер (001, 002, ...).
 *           Примеры:
 *               '001'           — корневая категория (Компьютеры и комплектующие);
 *               '001.001'       — дочерняя категория первого уровня (Настольные компьютеры);
 *               '001.001.001'   — внучатая категория второго уровня (Игровые ПК).
 *
 *           Такая структура позволяет Cotonti автоматически строить дерево
 *           категорий, вычислять родительские связи и наследовать права
 *           доступа от родительских категорий к дочерним.
 *
 *           Важно: первая цифра (001, 002, 003...) обычно соответствует
 *           корневой категории; вторая — порядковому номеру дочерней
 *           категории внутри родительской; третья — внучатой и так далее.
 *
 * =========================================================================
 */

$categories = [
    // ========== 1. Computers & Components ==========
    [
        'code'  => 'computers-components',
        'title' => 'Computers & Components',
        'desc'  => 'Desktop computers, components, and peripherals.',
        'path'  => '001',
    ],
    [
        'code'  => 'desktop-computers',
        'title' => 'Desktop Computers',
        'desc'  => 'Complete desktop systems.',
        'path'  => '001.001',
    ],
    [
        'code'  => 'gaming-desktops',
        'title' => 'Gaming Desktops',
        'desc'  => 'High-performance gaming PCs.',
        'path'  => '001.001.001',
    ],
    [
        'code'  => 'office-desktops',
        'title' => 'Office Desktops',
        'desc'  => 'Business and home office computers.',
        'path'  => '001.001.002',
    ],
    [
        'code'  => 'all-in-one-pcs',
        'title' => 'All-in-One PCs',
        'desc'  => 'Space-saving all-in-one computers.',
        'path'  => '001.001.003',
    ],
    [
        'code'  => 'computer-components',
        'title' => 'Computer Components',
        'desc'  => 'Internal parts for building and upgrading PCs.',
        'path'  => '001.002',
    ],
    [
        'code'  => 'processors',
        'title' => 'Processors',
        'desc'  => 'CPUs from Intel and AMD.',
        'path'  => '001.002.001',
    ],
    [
        'code'  => 'graphics-cards',
        'title' => 'Graphics Cards',
        'desc'  => 'GPUs for gaming and professional use.',
        'path'  => '001.002.002',
    ],
    [
        'code'  => 'motherboards',
        'title' => 'Motherboards',
        'desc'  => 'Mainboards for various platforms.',
        'path'  => '001.002.003',
    ],
    [
        'code'  => 'memory-ram',
        'title' => 'Memory (RAM)',
        'desc'  => 'DDR4 and DDR5 memory modules.',
        'path'  => '001.002.004',
    ],
    [
        'code'  => 'storage',
        'title' => 'Storage (SSD/HDD)',
        'desc'  => 'Solid-state drives and hard disk drives.',
        'path'  => '001.002.005',
    ],
    [
        'code'  => 'peripherals',
        'title' => 'Peripherals',
        'desc'  => 'Input and output devices for computers.',
        'path'  => '001.003',
    ],
    [
        'code'  => 'keyboards',
        'title' => 'Keyboards',
        'desc'  => 'Mechanical, membrane, and wireless keyboards.',
        'path'  => '001.003.001',
    ],
    [
        'code'  => 'mice',
        'title' => 'Mice',
        'desc'  => 'Optical and laser mice.',
        'path'  => '001.003.002',
    ],
    [
        'code'  => 'monitors',
        'title' => 'Monitors',
        'desc'  => 'Displays for work and gaming.',
        'path'  => '001.003.003',
    ],
    [
        'code'  => 'webcams',
        'title' => 'Webcams',
        'desc'  => 'HD and 4K webcams for video calls.',
        'path'  => '001.003.004',
    ],

    // ========== 2. Laptops ==========
    [
        'code'  => 'laptops',
        'title' => 'Laptops',
        'desc'  => 'Portable computers for every need.',
        'path'  => '002',
    ],
    [
        'code'  => 'gaming-laptops',
        'title' => 'Gaming Laptops',
        'desc'  => 'Laptops optimized for gaming performance.',
        'path'  => '002.001',
    ],
    [
        'code'  => 'high-end-gaming',
        'title' => 'High-End Gaming',
        'desc'  => 'Top-tier gaming laptops with RTX graphics.',
        'path'  => '002.001.001',
    ],
    [
        'code'  => 'mid-range-gaming',
        'title' => 'Mid-Range Gaming',
        'desc'  => 'Balanced performance and price.',
        'path'  => '002.001.002',
    ],
    [
        'code'  => 'budget-gaming',
        'title' => 'Budget Gaming',
        'desc'  => 'Affordable gaming laptops.',
        'path'  => '002.001.003',
    ],
    [
        'code'  => 'ultrabooks',
        'title' => 'Ultrabooks',
        'desc'  => 'Thin and light laptops.',
        'path'  => '002.002',
    ],
    [
        'code'  => 'business-ultrabooks',
        'title' => 'Business Ultrabooks',
        'desc'  => 'Secure and durable for professionals.',
        'path'  => '002.002.001',
    ],
    [
        'code'  => 'consumer-ultrabooks',
        'title' => 'Consumer Ultrabooks',
        'desc'  => 'Everyday ultraportables.',
        'path'  => '002.002.002',
    ],
    [
        'code'  => '2-in-1-convertibles',
        'title' => '2-in-1 Convertibles',
        'desc'  => 'Laptops that transform into tablets.',
        'path'  => '002.002.003',
    ],
    [
        'code'  => 'workstations',
        'title' => 'Workstations',
        'desc'  => 'High-performance laptops for professionals.',
        'path'  => '002.003',
    ],
    [
        'code'  => 'mobile-workstations',
        'title' => 'Mobile Workstations',
        'desc'  => 'Certified for CAD and 3D work.',
        'path'  => '002.003.001',
    ],
    [
        'code'  => 'desktop-replacement',
        'title' => 'Desktop Replacement',
        'desc'  => 'Large powerful laptops.',
        'path'  => '002.003.002',
    ],
    [
        'code'  => 'creator-laptops',
        'title' => 'Creator Laptops',
        'desc'  => 'Optimized for content creation.',
        'path'  => '002.003.003',
    ],

    // ========== 3. Smartphones ==========
    [
        'code'  => 'smartphones',
        'title' => 'Smartphones',
        'desc'  => 'Mobile phones and devices.',
        'path'  => '003',
    ],
    [
        'code'  => 'android-phones',
        'title' => 'Android Phones',
        'desc'  => 'Smartphones running Android OS.',
        'path'  => '003.001',
    ],
    [
        'code'  => 'samsung-galaxy',
        'title' => 'Samsung Galaxy',
        'desc'  => 'Samsung Galaxy series.',
        'path'  => '003.001.001',
    ],
    [
        'code'  => 'google-pixel',
        'title' => 'Google Pixel',
        'desc'  => 'Google Pixel smartphones.',
        'path'  => '003.001.002',
    ],
    [
        'code'  => 'xiaomi',
        'title' => 'Xiaomi',
        'desc'  => 'Xiaomi and Redmi devices.',
        'path'  => '003.001.003',
    ],
    [
        'code'  => 'oneplus',
        'title' => 'OnePlus',
        'desc'  => 'OnePlus smartphones.',
        'path'  => '003.001.004',
    ],
    [
        'code'  => 'iphones',
        'title' => 'iPhones',
        'desc'  => 'Apple iPhone models.',
        'path'  => '003.002',
    ],
    [
        'code'  => 'iphone-pro',
        'title' => 'iPhone Pro Models',
        'desc'  => 'iPhone Pro and Pro Max.',
        'path'  => '003.002.001',
    ],
    [
        'code'  => 'iphone-standard',
        'title' => 'iPhone Standard Models',
        'desc'  => 'iPhone standard series.',
        'path'  => '003.002.002',
    ],
    [
        'code'  => 'iphone-se',
        'title' => 'iPhone SE',
        'desc'  => 'Compact and affordable iPhone SE.',
        'path'  => '003.002.003',
    ],
    [
        'code'  => 'rugged-phones',
        'title' => 'Rugged Phones',
        'desc'  => 'Durable phones for harsh environments.',
        'path'  => '003.003',
    ],
    [
        'code'  => 'cat-phones',
        'title' => 'CAT Phones',
        'desc'  => 'Rugged phones from CAT.',
        'path'  => '003.003.001',
    ],
    [
        'code'  => 'blackview',
        'title' => 'Blackview',
        'desc'  => 'Rugged phones from Blackview.',
        'path'  => '003.003.002',
    ],
    [
        'code'  => 'ulefone',
        'title' => 'Ulefone',
        'desc'  => 'Rugged phones from Ulefone.',
        'path'  => '003.003.003',
    ],

    // ========== 4. Game Consoles ==========
    [
        'code'  => 'game-consoles',
        'title' => 'Game Consoles',
        'desc'  => 'Home and handheld gaming systems.',
        'path'  => '004',
    ],
    [
        'code'  => 'home-consoles',
        'title' => 'Home Consoles',
        'desc'  => 'Consoles connected to TV.',
        'path'  => '004.001',
    ],
    [
        'code'  => 'playstation',
        'title' => 'PlayStation',
        'desc'  => 'Sony PlayStation consoles.',
        'path'  => '004.001.001',
    ],
    [
        'code'  => 'xbox',
        'title' => 'Xbox',
        'desc'  => 'Microsoft Xbox consoles.',
        'path'  => '004.001.002',
    ],
    [
        'code'  => 'nintendo-switch',
        'title' => 'Nintendo Switch',
        'desc'  => 'Hybrid Nintendo Switch console.',
        'path'  => '004.001.003',
    ],
    [
        'code'  => 'handheld-consoles',
        'title' => 'Handheld Consoles',
        'desc'  => 'Portable gaming devices.',
        'path'  => '004.002',
    ],
    [
        'code'  => 'nintendo-switch-lite',
        'title' => 'Nintendo Switch Lite',
        'desc'  => 'Dedicated handheld Nintendo Switch.',
        'path'  => '004.002.001',
    ],
    [
        'code'  => 'steam-deck',
        'title' => 'Steam Deck',
        'desc'  => 'Valve Steam Deck handheld.',
        'path'  => '004.002.002',
    ],
    [
        'code'  => 'retro-handhelds',
        'title' => 'Retro Handhelds',
        'desc'  => 'Handhelds for retro gaming.',
        'path'  => '004.002.003',
    ],
    [
        'code'  => 'console-accessories',
        'title' => 'Console Accessories',
        'desc'  => 'Add-ons for game consoles.',
        'path'  => '004.003',
    ],
    [
        'code'  => 'controllers',
        'title' => 'Controllers',
        'desc'  => 'Gamepads and joysticks.',
        'path'  => '004.003.001',
    ],
    [
        'code'  => 'headsets',
        'title' => 'Headsets',
        'desc'  => 'Gaming headsets with microphone.',
        'path'  => '004.003.002',
    ],
    [
        'code'  => 'charging-docks',
        'title' => 'Charging Docks',
        'desc'  => 'Docks for controllers and consoles.',
        'path'  => '004.003.003',
    ],
    [
        'code'  => 'storage-expansion',
        'title' => 'Storage Expansion',
        'desc'  => 'Memory cards and external drives for consoles.',
        'path'  => '004.003.004',
    ],

    // ========== 5. Accessories ==========
    [
        'code'  => 'accessories',
        'title' => 'Accessories',
        'desc'  => 'Accessories for computers, smartphones, and gaming.',
        'path'  => '005',
    ],
    [
        'code'  => 'computer-accessories',
        'title' => 'Computer Accessories',
        'desc'  => 'Add-ons for desktops and laptops.',
        'path'  => '005.001',
    ],
    [
        'code'  => 'usb-hubs',
        'title' => 'USB Hubs',
        'desc'  => 'USB expansion hubs.',
        'path'  => '005.001.001',
    ],
    [
        'code'  => 'laptop-stands',
        'title' => 'Laptop Stands',
        'desc'  => 'Ergonomic stands for laptops.',
        'path'  => '005.001.002',
    ],
    [
        'code'  => 'cooling-pads',
        'title' => 'Cooling Pads',
        'desc'  => 'Cooling solutions for laptops.',
        'path'  => '005.001.003',
    ],
    [
        'code'  => 'cable-management',
        'title' => 'Cable Management',
        'desc'  => 'Organizers and sleeves for cables.',
        'path'  => '005.001.004',
    ],
    [
        'code'  => 'smartphone-accessories',
        'title' => 'Smartphone Accessories',
        'desc'  => 'Add-ons for mobile phones.',
        'path'  => '005.002',
    ],
    [
        'code'  => 'cases-covers',
        'title' => 'Cases & Covers',
        'desc'  => 'Protective cases and covers.',
        'path'  => '005.002.001',
    ],
    [
        'code'  => 'screen-protectors',
        'title' => 'Screen Protectors',
        'desc'  => 'Tempered glass and film protectors.',
        'path'  => '005.002.002',
    ],
    [
        'code'  => 'chargers-cables',
        'title' => 'Chargers & Cables',
        'desc'  => 'Wall chargers and USB cables.',
        'path'  => '005.002.003',
    ],
    [
        'code'  => 'power-banks',
        'title' => 'Power Banks',
        'desc'  => 'Portable battery chargers.',
        'path'  => '005.002.004',
    ],
    [
        'code'  => 'gaming-accessories',
        'title' => 'Gaming Accessories',
        'desc'  => 'Peripherals and gear for gamers.',
        'path'  => '005.003',
    ],
    [
        'code'  => 'gaming-keyboards',
        'title' => 'Gaming Keyboards',
        'desc'  => 'Mechanical keyboards with RGB.',
        'path'  => '005.003.001',
    ],
    [
        'code'  => 'gaming-mice',
        'title' => 'Gaming Mice',
        'desc'  => 'High-DPI gaming mice.',
        'path'  => '005.003.002',
    ],
    [
        'code'  => 'gaming-chairs',
        'title' => 'Gaming Chairs',
        'desc'  => 'Ergonomic chairs for gamers.',
        'path'  => '005.003.003',
    ],
    [
        'code'  => 'vr-headsets',
        'title' => 'VR Headsets',
        'desc'  => 'Virtual reality headsets.',
        'path'  => '005.003.004',
    ],
];


// =========================================================================
// =========================================================================
//     ВСЁ ТО, ЧТО НИЖЕ НЕ РЕДАКТИРОВАТЬ, 
//     ЕСЛИ ДОСТОВЕРНО НЕ ЗНАЕТЕ, 
//     ЧТО КОНКРЕТНО ДЕЛАЕТЕ И ЗАЧЕМ !!!
// =========================================================================
// =========================================================================


/**
 * Флаг, определяющий, какие права использовать для категорий:
 * false (по умолчанию) — использовать свои права из массивов $authPermit и $authLock;
 * true — использовать стандартные права, автоматически добавляемые Cotonti.
 */
 
$useDefaultAuth = false;

/**
 * Права доступа для групп пользователей к категориям модуля market.
 *
 * Система прав Cotonti использует символьные маски, где каждый символ
 * соответствует определённому биту прав доступа:
 *   R — чтение (Read), бит 1: разрешает просмотр категории и элементов в ней.
 *   W — запись (Write), бит 2: разрешает добавление и редактирование элементов.
 *   1 — специальный уровень 1, бит 4: в модуле Market используется для права
 *       публиковать (создавать) объявления.
 *   2 — специальный уровень 2, бит 8: зарезервировано, в Market не используется.
 *   3 — специальный уровень 3, бит 16: зарезервировано, в Market не используется.
 *   4 — специальный уровень 4, бит 32: зарезервировано, в Market не используется.
 *   5 — специальный уровень 5, бит 64: зарезервировано, в Market не используется.
 *   A — администрирование (Admin), бит 128: разрешает управление правами доступа
 *       к категории, а также выполнение административных действий.
 *
 *
 * Пояснение по уровням 1..5:
 *   Это пять дополнительных битов прав, зарезервированных в ядре Cotonti
 *   для использования модулями. Они не имеют фиксированного назначения
 *   и могут интерпретироваться каждым модулем по-своему.
 *   В контексте модуля Market можно, например, задать:
 *       Уровень 1 — право публиковать объявления;
 *       Уровень 2 — право редактировать чужие объявления;
 *       Уровень 3 — право модерировать (подтверждать/отклонять);
 *       Уровень 4 — право управлять категориями;
 *       Уровень 5 — право на расширенные настройки модуля.
 *
 *   В текущей конфигурации для гостей и обычных пользователей
 *   задействован только уровень 1 (маски R1, RW1). Уровни 2–5
 *   оставлены для будущего расширения функциональности.
 *
 * Массив $authPermit задаёт разрешённые права для каждой группы.
 * Массив $authLock задаёт права, которые заблокированы от изменения
 * (администратор не сможет их снять через интерфейс управления правами).
 *
 * Для дополнительных групп (ID > 5), кроме явно указанных,
 * используются значения из COT_GROUP_DEFAULT (0).
 * Это гарантирует, что вновь создаваемые группы получат разумные права по умолчанию.
 *
 * Определения:
 *   Гости (COT_GROUP_GUESTS) = 1
 *   Неактивные (COT_GROUP_INACTIVE) = 2
 *   Забаненные (COT_GROUP_BANNED) = 3
 *   Пользователи (COT_GROUP_MEMBERS) = 4
 *   Администраторы (COT_GROUP_SUPERADMINS) = 5
 *   Модераторы (COT_GROUP_MODERATORS) = 6
 *   Default (шаблон для новых групп) = 0
 */

$authPermit = [
    COT_GROUP_DEFAULT      => 'RW1',      // Шаблон для новых групп: чтение, запись, уровень 1
    COT_GROUP_GUESTS       => 'R1',       // Гости: чтение и уровень 1
    COT_GROUP_INACTIVE     => 'R',        // Неактивные: только чтение
    COT_GROUP_BANNED       => '',         // Забаненные: никаких прав
    COT_GROUP_MEMBERS      => 'RW1',      // Обычные пользователи: чтение, запись, уровень 1
    COT_GROUP_SUPERADMINS  => 'RW12345A', // Администраторы: все права
    COT_GROUP_MODERATORS   => 'RW1A',     // Модераторы: чтение, запись, уровень 1, администрирование
];

$authLock = [
    COT_GROUP_DEFAULT      => '0',        // Шаблон: ничего не заблокировано
    COT_GROUP_GUESTS       => 'W2345A',   // Гости: запрещено изменять всё, кроме R и 1
    COT_GROUP_INACTIVE     => 'W12345A',  // Неактивные: запрещено изменять всё, кроме R
    COT_GROUP_BANNED       => 'RW12345A', // Забаненные: все права зафиксированы на нуле
    COT_GROUP_MEMBERS      => '0',        // Пользователи: ничего не заблокировано
    COT_GROUP_SUPERADMINS  => 'RW12345A', // Администраторы: права зафиксированы полностью
    COT_GROUP_MODERATORS   => '0',        // Модераторы: ничего не заблокировано
];

// Цикл установки категорий
// Проверяем результат cot_structure_add(): если категория успешно создана (true),
// то добавляем права. Если категория уже существует (вернётся массив с ошибкой или false),
// повторная вставка прав приведёт к дублированию и SQL-ошибке из-за уникального ключа.
// Поэтому права добавляются только при успешном создании категории и при условии,
// что используются собственные права ($useDefaultAuth === false).
// Переменная $result создаётся для сохранения возвращаемого значения функции cot_structure_add()
// Без этой проверки при повторном запуске установки (когда категории уже существуют) попытка вставить дубликаты прав вызвала бы SQL-ошибку.
// $result может содержать:
// true — категория успешно добавлена;
// массив ['adm_cat_exists', 'rstructurecode'] — категория уже существует;
// false — ошибка (например, не заполнены обязательные поля).
foreach ($categories as $cat) {
    // Добавляем категорию в структуру
    // Функция cot_structure_add() возвращает:
    //   true — категория успешно создана;
    //   массив с ошибкой (например, ['adm_cat_exists', ...]) — категория уже существует;
    //   false — другая ошибка (например, не заполнены обязательные поля).
    $result = cot_structure_add('market', [
        'structure_area'   => 'market',
        'structure_code'   => $cat['code'],
        'structure_title'  => $cat['title'],
        'structure_desc'   => $cat['desc'],
        'structure_path'   => $cat['path'],
        'structure_locked' => 0,
        'structure_count'  => 0,
        'structure_tpl'    => '',
        'structure_icon'   => ''
    ], $useDefaultAuth);

    // Добавляем права только если:
    // 1. Категория была успешно создана ($result === true)
    // 2. Используются собственные права ($useDefaultAuth === false)
    // В противном случае права не добавляются, чтобы избежать дублирования записей
    // и возможных SQL-ошибок из-за нарушения уникальности ключа (auth_groupid, auth_code, auth_option).
    if ($result === true && !$useDefaultAuth) {
        // cot_auth_add_item() автоматически:
        //   - дополняет переданные массивы правами по умолчанию из $cot_auth_default_permit и $cot_auth_default_lock,
        //   - вставляет записи в таблицу cot_auth для всех групп, у которых нет skiprights,
        //   - пересортировывает таблицу и очищает кэш прав.
        cot_auth_add_item('market', $cat['code'], $authPermit, $authLock);
    }
}

// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// 							ЕСТЬ ДРУГОЙ ВАРИАНТ НО ЕЩЕ ОБКАТАН:
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------


// =========================================================================
// -------------------------------------------------------------------------
//     ВСЁ ТО, ЧТО НИЖЕ НЕ РЕДАКТИРОВАТЬ, 
//     ЕСЛИ ДОСТОВЕРНО НЕ ЗНАЕТЕ, 
//     ЧТО КОНКРЕТНО ДЕЛАЕТЕ И ЗАЧЕМ !!!
// -------------------------------------------------------------------------
// =========================================================================


// Флаг, определяющий, какие права использовать для категорий:
// false (по умолчанию) — использовать свои права из массива $authRights;
// true — использовать стандартные права, автоматически добавляемые Cotonti.

/* 
$useDefaultAuth = false;
 */
/*
 * Права доступа для групп пользователей к категориям модуля market.
 *
 * Система прав Cotonti использует битовую маску:
 *   R (Read)        = 1   — чтение/просмотр
 *   W (Write)       = 2   — добавление/изменение
 *   1               = 4   — специальное право уровня 1
 *   2               = 8   — специальное право уровня 2
 *   3               = 16  — специальное право уровня 3
 *   4               = 32  — специальное право уровня 4
 *   5               = 64  — специальное право уровня 5
 *   A (Admin)       = 128 — администрирование/управление правами
 *
 * Значение "rights" — это сумма битов разрешённых действий.
 * Значение "lock"   — это сумма битов, которые запрещено изменять
 *                    (блокировка прав для данной группы).
 *
 * Например, rights = 5 означает R + 1 = 1 + 4 = 5.
 * lock = 250 означает, что заблокированы биты: W(2), 2(8), 3(16), 4(32), 5(64), A(128),
 * т.е. можно менять только R(1) и 1(4). Остальные права фиксированы.
 */

// Группа 1 (Гости):
//   rights = 5 → R + 1 (1+4) — гости могут просматривать категорию и использовать специальное право уровня 1,
//              но не могут добавлять, редактировать или управлять правами.
//   lock   = 250 → заблокированы W,2,3,4,5,A — гостям нельзя изменить ничего, кроме R и 1.

// Группа 2 (Неактивные):
//   rights = 1 → только R (чтение) — неактивные пользователи могут лишь просматривать категорию.
//   lock   = 254 → заблокированы все права, кроме R (биты W,1,2,3,4,5,A = 2+4+8+16+32+64+128=254).
//                То есть R фиксировано, остальное менять нельзя.

// Группа 3 (Забаненные):
//   rights = 0 → нет никаких прав — забаненные не видят и не могут ничего.
//   lock   = 255 → заблокированы все биты (R,W,1,2,3,4,5,A) — права полностью зафиксированы на нуле.

// Группа 4 (Пользователи):
//   rights = 7 → R + W + 1 (1+2+4) — пользователи могут просматривать, добавлять/изменять и использовать уровень 1.
//   lock   = 0 → никакие права не заблокированы — администратор может менять права этой группы.

// Группа 5 (Администраторы):
//   rights = 255 → все права (R,W,1,2,3,4,5,A) — полный доступ.
//   lock   = 255 → все права заблокированы — права администраторов не могут быть изменены никем.

// Группа 6 (Модераторы):
//   rights = 135 → A + 1 + W + R (128+4+2+1) — модераторы могут администрировать (A),
//                использовать уровень 1, писать (W) и читать (R). Нет уровней 2-5.
//   lock   = 0 → никакие права не заблокированы — администратор может менять права модераторов.
/* 
$authRights = [
    1 => ['rights' => 5,   'lock' => 250],
    2 => ['rights' => 1,   'lock' => 254],
    3 => ['rights' => 0,   'lock' => 255],
    4 => ['rights' => 7,   'lock' => 0],
    5 => ['rights' => 255, 'lock' => 255],
    6 => ['rights' => 135, 'lock' => 0],
];
 */
// Проверяем результат cot_structure_add(): если категория успешно создана (true),
// то добавляем права. Если категория уже существует (вернётся массив с ошибкой или false),
// повторная вставка прав приведёт к дублированию и SQL-ошибке из-за уникального ключа.
// Поэтому права добавляются только при успешном создании категории и при условии,
// что используются собственные права ($useDefaultAuth === false).
// Переменная $result создаётся для сохранения возвращаемого значения функции cot_structure_add()
// Без этой проверки при повторном запуске установки (когда категории уже существуют) попытка вставить дубликаты прав вызвала бы SQL-ошибку.
// $result может содержать:
// true — категория успешно добавлена;
// массив ['adm_cat_exists', 'rstructurecode'] — категория уже существует;
// false — ошибка (например, не заполнены обязательные поля).
/* 
foreach ($categories as $cat) {   // Перебираем все категории из массива $categories
    $result = cot_structure_add('market', [   // Вызываем API-функцию для добавления категории в структуру (таблица cot_structure)
        'structure_area' => 'market',   // Область (area) — 'market'
        'structure_code' => $cat['code'], // Код категории (например 'computers-components')
        'structure_title' => $cat['title'], // Название категории
        'structure_desc' => $cat['desc'],   // Описание категории
        'structure_path' => $cat['path'],   // Путь для вложенности (например '001.001')
        'structure_locked' => 0,            // Не заблокирована
        'structure_count' => 0,             // Начальный счётчик = 0
        'structure_tpl' => '',              // Шаблон пустой
        'structure_icon' => ''              // Иконка пустая
    ], $useDefaultAuth); // Если false — отключаем авто-права И СТАВИМ СВОИ, ИНАЧЕ, если true — позволяем Cotonti добавить стандартные
	
	// Проверка $result === true && !$useDefaultAuth гарантирует добавление прав только для успешно созданных категорий 
	// и при использовании собственных прав.
    // Добавляем свои права только если флаг выключен и категория действительно создана
    if ($result === true && !$useDefaultAuth) {
        foreach ($authRights as $groupId => $auth) {   // Для каждой группы из массива прав
            $db->insert($db_auth, [                     // Вставляем запись в таблицу cot_auth
                'auth_groupid'     => $groupId,          // ID группы
                'auth_code'        => 'market',          // Модуль — 'market'
                'auth_option'      => $cat['code'],      // Категория, к которой относятся права
                'auth_rights'      => $auth['rights'],   // Права (число)
                'auth_rights_lock' => $auth['lock'],     // Блокировка прав (число)
                'auth_setbyuserid' => 1                  // Кто установил права (1 = система)
            ]);
        }   // Конец цикла по группам
    }
}   // Конец цикла по категориям


 */




