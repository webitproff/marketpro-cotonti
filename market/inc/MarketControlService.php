<?php
// declare(strict_types = 1);
/**
 * MarketControlService - сервис управления товарами.
 *
 * Содержит методы для выполнения операций над товарами, в частности удаление.
 * Использует паттерн Singleton через trait GetInstanceTrait.
 * Взаимодействует с репозиторием MarketRepository, сервисом ItemService,
 * а также с дополнительными полями (extrafields) и кэшем.
 *
 * Filename: MarketControlService.php
 *
 * Path:    modules/market/inc/MarketControlService.php
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

declare(strict_types=1);  // Включаем строгую типизацию для надёжности

namespace cot\modules\market\inc;  // Пространство имён модуля Market

use Cot;  // Импортируем основной класс Cot для доступа к статическим свойствам
use cot\services\ItemService;  // Сервис для работы с элементами (уведомления об удалении)
use cot\traits\GetInstanceTrait;  // Трейт для реализации Singleton
use Throwable;  // Базовый интерфейс для всех исключений

defined('COT_CODE') or die('Wrong URL.');  // Защита от прямого вызова файла

/**
 * Класс MarketControlService
 *
 * Предоставляет методы для управления товарами (удаление и др.).
 * Реализует Singleton, поэтому экземпляр получаем через getInstance().
 */
class MarketControlService
{
    use GetInstanceTrait;  // Подключаем трейт Singleton

    /**
     * Удаляет товар из системы.
     *
     * Выполняет удаление товара, связанных файлов, обновляет счётчики структуры,
     * очищает кэш и вызывает хуки для интеграции с другими модулями.
     *
     * @param int   $id       ID товара
     * @param array $itemData Данные товара (если не переданы, будут загружены из БД)
     * @return bool|string    Сообщение об успехе или FALSE при ошибке
     */
    public function delete(int $id, array $itemData = []): bool|string
    {
        if ($id <= 0) {
            return false;  // Неверный ID, возвращаем false
        }

        // Если данные товара не переданы, получаем их из репозитория
        if (empty($itemData)) {
            $itemData = MarketRepository::getInstance()->getById($id);
            if (empty($itemData)) {
                return false; // Товар не найден, возвращаем false
            }
        }

        try {
            Cot::$db->beginTransaction();  // Начинаем транзакцию, чтобы обеспечить целостность данных

            // Хук перед удалением (например, для удаления переводов товара)
            foreach (cot_getextplugins('market.delete.first') as $pl) {
                include $pl;  // Подключаем плагины, привязанные к хуку
            }

            // Удаляем связанные файлы дополнительных полей
            foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
                // Проверяем, есть ли значение поля в данных товара
                if (isset($itemData['fieldmrkt_' . $exfld['field_name']])) {
                    // Удаляем файлы, прикреплённые к данному полю
                    cot_extrafield_unlinkfiles($itemData['fieldmrkt_' . $exfld['field_name']], $exfld);
                }
            }

            $trashcanId = 0;  // Идентификатор корзины (0, если корзина не используется)

            // Непосредственно удаляем товар из таблицы market
            Cot::$db->delete(Cot::$db->market, 'fieldmrkt_id = ?', $id);

            // Обновляем счётчики товаров в структуре категорий
            cot_market_updateStructureCounters($itemData['fieldmrkt_cat']);

            $itemDeletedMessage = ['deleted' => Cot::$L['market_deleted']];  // Сообщение об успешном удалении

            // Хук после удаления (для дополнительных действий)
            foreach (cot_getextplugins('market.delete.done') as $pl) {
                include $pl;
            }

            // Уведомляем общий сервис ItemService об удалении элемента
            ItemService::getInstance()->onDelete(MarketDictionary::SOURCE_MARKET, $id, $trashcanId);

            Cot::$db->commit();  // Фиксируем транзакцию
        } catch (Throwable $e) {
            Cot::$db->rollBack();  // Откатываем транзакцию при ошибке
            echo $e->getMessage();  // Выводим сообщение об ошибке (временное решение, в production лучше логировать)
            die;  // Прекращаем выполнение
        }

        // Очищаем кэш, если он включён
        if (Cot::$cache) {
            // Если включён кэш для модуля market
            if (Cot::$cfg['cache_market']) {
                // Очищаем кэш страницы товара
                Cot::$cache->static->clearByUri(cot_market_url($itemData));
                // Очищаем кэш страницы категории
                Cot::$cache->static->clearByUri(cot_url('market', ['c' => $itemData['fieldmrkt_cat']]));
            }
            // Если включён кэш главной страницы, очищаем его
            if (Cot::$cfg['cache_index']) {
                Cot::$cache->static->clear('index');
            }
        }

        // Возвращаем сообщение об успехе (если массив, склеиваем элементы в строку)
        return is_array($itemDeletedMessage) ? implode('; ', $itemDeletedMessage) : $itemDeletedMessage;
    }
}