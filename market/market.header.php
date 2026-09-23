<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
[END_COT_EXT]
==================== */

/**
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Обработчик хука `header.main` для модуля Market.
 *
 * Назначение:
 *   Добавляет уведомления (notices) в массив Cot::$out['notices_array'] для администраторов
 *   и пользователей с правом на запись в модуле Market. Уведомления содержат количество товаров,
 *   ожидающих утверждения (state=1) и находящихся в черновиках (state=2). Для администраторов
 *   учитываются все товары, для обычных пользователей — только их собственные.
 *
 * Что делает:
 *   1. Проверяет, что пользователь авторизован и имеет соответствующие права.
 *   2. Для администраторов (право 'A') получает общее количество товаров в очереди и черновиках,
 *      добавляет уведомления со ссылками на страницы админ-панели.
 *   3. Для пользователей с правом записи ('W') получает количество их собственных товаров
 *      в очереди и черновиках, добавляет уведомления со ссылками на соответствующие разделы.
 *
 * Почему нужен:
 *   Позволяет администраторам и продавцам быстро видеть, сколько товаров требуют
 *   модерации или находятся в черновиках, и переходить к их управлению.
 *
 * Filename: market.header.php
 *
 * Path:    modules/market/market.header.php
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

// Защита от прямого вызова файла
defined('COT_CODE') or die('Wrong URL');

// Проверяем, что пользователь авторизован и имеет право на запись или администрирование
if (
    Cot::$usr['id'] > 0
    && (cot_auth('market', 'any', 'A') || cot_auth('market', 'any', 'W'))
) {
    // Подключаем функции модуля Market (нужны для объявления $Ls и др.)
    require_once cot_incfile('market', 'module');
}

// Если пользователь авторизован и является администратором market
if (Cot::$usr['id'] > 0 && cot_auth('market', 'any', 'A')) {
    // Получаем количество товаров, ожидающих утверждения (state = 1), из таблицы market
    Cot::$sys['marketqueued'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
        ' WHERE fieldmrkt_state = 1')->fetchColumn();

    // Если есть товары в очереди, добавляем уведомление со ссылкой на админ-панель
    if (Cot::$sys['marketqueued'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('admin', 'm=market'),  // ссылка на страницу управления товарами
            cot_declension(Cot::$sys['marketqueued'], $Ls['unvalidated_market']) // текст с правильным склонением
        ];
    }

    // Получаем количество товаров в черновиках (state = 2) из таблицы market
    Cot::$sys['marketindrafts'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market
        ." WHERE fieldmrkt_state = 2")->fetchColumn();

    // Если есть черновики, добавляем уведомление со ссылкой на админ-панель с фильтром drafts
    if (Cot::$sys['marketindrafts'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('admin', 'm=market&filter=drafts'),
            cot_declension(Cot::$sys['marketindrafts'], $Ls['market_in_drafts'])
        ];
    }

// Если пользователь авторизован и имеет право на запись, но не администратор
} elseif (Cot::$usr['id'] > 0 && cot_auth('market', 'any', 'W')) {
    // Получаем количество товаров пользователя, ожидающих утверждения (state = 1)
    Cot::$sys['marketqueued'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
        ' WHERE fieldmrkt_state=1 AND fieldmrkt_ownerid = ' . Cot::$usr['id'])->fetchColumn();

    // Если есть такие товары, добавляем уведомление со ссылкой на раздел "unvalidated" модуля
    if (Cot::$sys['marketqueued'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('market', 'c=unvalidated'),
            cot_declension(Cot::$sys['marketqueued'], $Ls['unvalidated_market'])
        ];
    }

    // Получаем количество черновиков пользователя (state = 2)
    Cot::$sys['marketindrafts'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
        " WHERE fieldmrkt_state=2 AND fieldmrkt_ownerid = " . Cot::$usr['id'])->fetchColumn();

    // Если есть черновики, добавляем уведомление со ссылкой на раздел "saved_drafts" модуля
    if (Cot::$sys['marketindrafts'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('market', 'c=saved_drafts'),
            cot_declension(Cot::$sys['marketindrafts'], $Ls['market_in_drafts'])
        ];
    }
}