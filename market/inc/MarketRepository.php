<?php
// declare(strict_types = 1);
/**
 * MarketRepository - репозиторий для работы с товарами.
 *
 * Обеспечивает получение данных о товарах из базы данных с использованием
 * базового репозитория BaseRepository. Включает простое кэширование
 * результатов в рамках одного запроса.
 *
 * Filename: MarketRepository.php
 *
 * Path:    modules/market/inc/MarketRepository.php
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

// Импортируем основной класс Cot для доступа к статическим свойствам
use Cot;

// Импортируем базовый репозиторий с общими методами
use cot\repositories\BaseRepository;

// Защита от прямого вызова файла
defined('COT_CODE') or die('Wrong URL');

/**
 * Класс MarketRepository
 *
 * Наследует BaseRepository и реализует специфичную логику для выборки товаров.
 * Включает кэш первого уровня (в рамках одного HTTP-запроса), чтобы избежать
 * повторных запросов к БД для одного и того же товара.
 */
class MarketRepository extends BaseRepository
{
    // Кэш товаров по ID (на время одного запроса)
    private static $cacheById = [];

    /**
     * Возвращает имя таблицы товаров.
     *
     * Если таблица ещё не зарегистрирована в Cot::$db, регистрирует её.
     *
     * @return string Имя таблицы
     */
    public static function getTableName(): string
    {
        // Если таблица market ещё не определена, регистрируем её
        if (empty(Cot::$db->market)) {
            Cot::$db->registerTable('market');
        }
        // Возвращаем имя таблицы
        return Cot::$db->market;
    }

    /**
     * Получает товар по его ID.
     *
     * Использует кэш первого уровня, чтобы не делать лишних запросов к БД.
     * Если товар не найден, возвращает null.
     *
     * @param int  $id       ID товара
     * @param bool $useCache Использовать ли кэш первого уровня (по умолчанию true)
     * @return ?array         Данные товара или null, если не найден
     */
    public function getById(int $id, bool $useCache = true): ?array
    {
        // Если ID меньше 1, возвращаем null (некорректный ID)
        if ($id < 1) {
            return null;
        }

        // Если кэш включён и товар уже был загружен ранее, возвращаем его из кэша
        if ($useCache && isset(self::$cacheById[$id])) {
            // Если в кэше не false, значит товар существует, возвращаем данные; иначе null
            return self::$cacheById[$id] !== false ? self::$cacheById[$id] : null;
        }

        // Формируем условие для выборки по ID
        $condition = 'fieldmrkt_id = :itemId';
        // Параметры для подстановки в запрос
        $params = ['itemId' => $id];

        // Получаем все записи, удовлетворяющие условию
        $results = $this->getByCondition($condition, $params);
        // Берём первую запись (должна быть одна) или null, если пусто
        $result = !empty($results) ? $results[0] : null;

        // Сохраняем в кэш: либо сам массив с данными, либо false, если товар не найден
        self::$cacheById[$id] = !empty($result) ? $result : false;

        // Возвращаем данные товара или null
        return $result;
    }

    /**
     * Метод вызывается после получения записи из БД.
     *
     * Приводит типы основных полей к целым числам для удобства работы.
     *
     * @param array $item Массив с данными товара
     * @return array       Обработанный массив
     */
    protected function afterFetch(array $item): array
    {
        // Приводим каждое числовое поле к int (если ключ отсутствует, используем 0)
        $item['fieldmrkt_id']      = (int)($item['fieldmrkt_id'] ?? 0);
        $item['fieldmrkt_state']   = (int)($item['fieldmrkt_state'] ?? 0);
        $item['fieldmrkt_ownerid'] = (int)($item['fieldmrkt_ownerid'] ?? 0);
        $item['fieldmrkt_date']    = (int)($item['fieldmrkt_date'] ?? 0);
        $item['fieldmrkt_begin']   = (int)($item['fieldmrkt_begin'] ?? 0);
        $item['fieldmrkt_expire']  = (int)($item['fieldmrkt_expire'] ?? 0);
        $item['fieldmrkt_updated'] = (int)($item['fieldmrkt_updated'] ?? 0);
        $item['fieldmrkt_count']   = (int)($item['fieldmrkt_count'] ?? 0);

        // Возвращаем обработанный массив
        return $item;
    }
}