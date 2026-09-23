<?php
// declare(strict_types = 1);
/**
 * MarketDictionary - словарь констант модуля Market.
 *
 * Содержит основные константы, используемые в модуле Market PRO:
 * - идентификатор источника (source) для ItemService;
 * - статусы товара (опубликован, на модерации, черновик).
 *
 * Константы оформлены в виде публичных констант класса, чтобы обеспечить
 * централизованное хранение и избежать "магических чисел" в коде.
 *
 * Filename: MarketDictionary.php
 *
 * Path:    modules/market/inc/MarketDictionary.php
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

// Включаем строгую типизацию для надёжности
declare(strict_types=1);

// Пространство имён модуля Market
namespace cot\modules\market\inc;

// Защита от прямого вызова файла
defined('COT_CODE') or die('Wrong URL');

/**
 * Класс MarketDictionary
 *
 * Предоставляет набор констант для работы с товарами.
 * Константы используются в других классах модуля (например, MarketControlService,
 * MarketRepository, market.add.php, market.edit.php) для указания источника и статусов.
 */
class MarketDictionary
{
    /**
     * Идентификатор источника (source) товара.
     *
     * Используется при вызове ItemService::onDelete() и других методов,
     * чтобы идентифицировать, что элемент относится к модулю Market.
     */
    public const SOURCE_MARKET = 'market';

    /**
     * Статус: опубликован.
     *
     * Товар прошёл модерацию (или опубликован сразу, если автоутверждение включено)
     * и доступен для просмотра всем пользователям.
     */
    public const STATE_PUBLISHED = 0;

    /**
     * Статус: ожидает утверждения администратором (модератором).
     *
     * Товар создан, но ещё не опубликован. Он не виден обычным пользователям
     * до тех пор, пока администратор не утвердит его.
     */
    public const STATE_PENDING = 1;

    /**
     * Статус: черновик.
     *
     * Товар сохранён как черновик. Он не виден никому, кроме автора (и администраторов),
     * и может быть отредактирован или удалён.
     */
    public const STATE_DRAFT = 2;
}