<?php
/**
 * Store item views counter. For cached items.
 *
 * Увеличивает счётчик просмотров товара на 1. Используется для страниц,
 * которые закэшированы, поэтому обычный PHP-код счётчика не выполняется.
 * Этот файл вызывается через AJAX-запрос с параметрами id и a=views.
 *
 * Filename: market.counter.php
 *
 * Path:    modules/market/inc/market.counter.php
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
 * @var string $a  Действие, передаётся из вызывающего кода (обычно 'views')
 */

defined('COT_CODE') or die('Wrong URL');  // Защита от прямого вызова

// Получаем права пользователя для модуля market (область 'any' — без конкретной категории)
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');

// Блокируем доступ, если у пользователя нет права на чтение (просмотр товаров)
cot_block(Cot::$usr['auth_read']);

// Импортируем ID товара из GET-запроса как целое число
$id = cot_import('id', 'G', 'INT');

// Если ID пуст или не указано действие $a — прерываем выполнение (завершаем скрипт)
// cot_die(condition, true) выводит сообщение об ошибке и останавливает выполнение
cot_die(empty($id) || empty($a), true);

// Обрабатываем действие
switch ($a) {
    case 'views':
        // Увеличиваем счётчик просмотров в БД для указанного товара
        Cot::$db->query(
            'UPDATE ' . Cot::$db->market . ' SET fieldmrkt_count = fieldmrkt_count + 1 WHERE fieldmrkt_id = ?',
            $id
        );
        // После обновления выходим из switch
        break;
    // Другие действия можно добавить здесь
}

// Завершаем выполнение, чтобы не выводить лишний HTML
exit();