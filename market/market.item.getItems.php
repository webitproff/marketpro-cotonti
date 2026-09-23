<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.getItems
[END_COT_EXT]
==================== */
/**
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Файл-обработчик хука `item.getItems` для модуля Market.
 *
 * Назначение:
 *   Этот файл вызывается автоматически, когда система Cotonti через общий
 *   механизм элементов (Item) запрашивает данные о товарах модуля Market.
 *   Он работает аналогично market.itemService.getItems.php, но предназначен
 *   для другого хука (item.getItems) и имеет небольшие отличия в формировании
 *   DTO: добавляется подтип 'market', а результат сохраняется по ключу $dto->id.
 *
 * Что делает:
 *   1. Проверяет, что запрос относится к модулю Market (source === 'market')
 *      и что передан непустой список идентификаторов товаров.
 *   2. Преобразует ID товаров в целые числа, удаляет дубликаты.
 *   3. Получает данные товаров из репозитория MarketRepository по условию IN.
 *   4. Для каждого товара формирует объект ItemDto с дополнительным параметром
 *      'market' (подтип), заполняя его полями (ID, тип, заголовок, описание,
 *      URL, владелец).
 *   5. Если запрошены полные данные, добавляет их в DTO.
 *   6. Заполняет информацию о категории (код, заголовок, URL), если она существует.
 *   7. Добавляет готовый DTO в общий результат по ключу $dto->id.
 *   8. Предоставляет возможность другим плагинам повлиять на результат через хук
 *      `market.item.getItems.done`.
 *
 * Почему нужен:
 *   Обеспечивает интеграцию товаров Market с общими функциями Cotonti,
 *   которые используют хук item.getItems (например, некоторые плагины или
 *   системные механизмы). Без этого файла товары не будут отображаться
 *   в соответствующих выборках.
 *
 * Filename: market.item.getItems.php
 *
 * Path:    modules/market/market.item.getItems.php
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
 */



// Включаем строгую типизацию
declare(strict_types = 1);

// Импортируем класс ItemDto для создания DTO объектов элементов
use cot\dto\ItemDto;

// Импортируем словарь расширений для указания типа модуля
use cot\extensions\ExtensionsDictionary;

// Импортируем константы Market (источник и статусы)
use cot\modules\market\inc\MarketDictionary;

// Импортируем репозиторий товаров для выборки данных из БД
use cot\modules\market\inc\MarketRepository;

// Защита от прямого вызова файла
defined('COT_CODE') or die('Wrong URL');

// Проверяем, что запрос относится к модулю Market и есть ID для обработки
if ($source !== MarketDictionary::SOURCE_MARKET || empty($sourceIds)) {
    // Если нет — выходим, ничего не делая
    return;
}

// Объявляем глобальные переменные языка (используются внутри файла)
global $L, $R, $Ls;

// Подключаем функции модуля Market (необходимо для работы cot_market_url и др.)
require_once cot_incfile('market', ExtensionsDictionary::TYPE_MODULE);

// Инициализируем массив для валидных целочисленных ID
$marketIds = [];

// Проходим по всем полученным ID
foreach ($sourceIds as $id) {
    // Приводим ID к целому числу
    $id = (int) $id;
    // Если ID больше нуля, добавляем его в массив
    if ($id > 0) {
        $marketIds[] = $id;
    }
}

// Убираем возможные дубликаты ID
$marketIds = array_unique($marketIds);

// Если после фильтрации не осталось ни одного ID, выходим
if (empty($marketIds)) {
    return;
}

// Формируем SQL-условие для выборки товаров по списку ID
$condition = 'fieldmrkt_id IN (' . implode(',', $marketIds) . ')';

// Получаем массив товаров из репозитория
$items = MarketRepository::getInstance()->getByCondition($condition);

// Обрабатываем каждый товар
foreach ($items as $row) {
    // Формируем URL товара
    $url = cot_market_url($row);
    // Если URL не абсолютный, добавляем базовый URL сайта
    if (!cot_url_check($url)) {
        $url = COT_ABSOLUTE_URL . $url;
    }

    // Создаём DTO объект для товара
    // В отличие от market.itemService.getItems.php, здесь добавлен
    // дополнительный параметр 'market' — подтип элемента
    $dto = new ItemDto(
        MarketDictionary::SOURCE_MARKET,
        $row['fieldmrkt_id'],
        'market',                   // подтип (subtype) элемента
        Cot::$L['Market'],          // Общее название модуля (используется как тип)
        $row['fieldmrkt_title'],    // Заголовок товара
        $row['fieldmrkt_desc'],     // Описание товара
        $url,                       // Ссылка на товар
        (int) $row['fieldmrkt_ownerid'] // ID владельца
    );

    // Если запрошены полные данные, добавляем их в DTO
    if ($withFullItemData) {
        $dto->data = $row;
    }

    // Устанавливаем код категории товара
    $dto->categoryCode = $row['fieldmrkt_cat'];
    // По умолчанию заголовок категории неизвестен
    $dto->categoryTitle = 'Unknown';
    // Если категория существует в структуре, заполняем URL и заголовок
    if (isset(Cot::$structure['market'][$row['fieldmrkt_cat']])) {
        $dto->categoryUrl = cot_url('market', ['c' => $row['fieldmrkt_cat']]);
        $dto->categoryTitle = Cot::$structure['market'][$row['fieldmrkt_cat']]['title'];
    }

    // Добавляем DTO в общий результат, ключ — ID товара ($dto->id)
    $result[$dto->id] = $dto;
}

/* === Hook === */
// Даём возможность другим плагинам изменить результат
foreach (cot_getextplugins('market.item.getItems.done') as $pl) {
    include $pl;
}
/* ===== */