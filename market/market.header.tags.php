<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
[END_COT_EXT]
==================== */

/**
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Обработчик хука `header.tags` для модуля Market.
 *
 * Назначение:
 *   Переопределяет переменную HEADER_TITLE в шаблоне header.tpl для страниц списка товаров Market.
 *   Заголовок берётся из языковой константы модуля (например, `$L['market_metatitle_elektrosamokaty']`)
 *   или из настройки конфигурации категории (`Cot::$cfg['market']['cat_<код_категории>']['marketmetatitle']`).
 *   Это позволяет задавать индивидуальные SEO-заголовки для категорий товаров.
 *
 * Что делает:
 *   1. Проверяет, что мы находимся на странице списка товаров (константа COT_LIST определена)
 *      и задан код категории `$c`.
 *   2. Пытается найти заголовок в языковом файле по ключу `market_metatitle_<код_категории>`.
 *   3. Если языковая константа не найдена, ищет значение в конфиге категории.
 *   4. Если заголовок найден, присваивает его переменной шаблона HEADER_TITLE с экранированием HTML.
 *
 * Почему нужен:
 *   Без этого файла заголовки страниц категорий не учитывали бы мультиязычные настройки
 *   и не могли бы быть заданы через конфигурацию. Обеспечивает гибкое управление SEO.
 *
 * Filename: market.header.tags.php
 *
 * Path:    modules/market/market.header.tags.php
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

// Проверяем, что мы находимся на странице списка товаров (COT_LIST) и задан код категории $c
if (defined('COT_LIST') && !empty($c)) {
    // Формируем ключ языковой константы для текущей категории: например, 'market_metatitle_elektrosamokaty'
    $langKey = 'market_metatitle_' . $c;
    // Проверяем, существует ли такая константа в языковом массиве $L
    if (isset($L[$langKey])) {
        // Если да, берём заголовок из языка
        $customTitle = $L[$langKey];
    }
    // Если языковой константы нет, пробуем взять заголовок из конфигурации категории
    elseif (!empty(Cot::$cfg['market']['cat_' . $c]['marketmetatitle'])) {
        // Присваиваем значение из конфига
        $customTitle = Cot::$cfg['market']['cat_' . $c]['marketmetatitle'];
    }

    // Если заголовок в итоге определён (не пуст)
    if (!empty($customTitle)) {
        // Передаём его в шаблон, экранируя специальные символы HTML
        $t->assign('HEADER_TITLE', htmlspecialchars($customTitle));
    }
}