<?php
/**
 * Store item service
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

declare(strict_types=1);

namespace cot\modules\market\inc;

use Cot;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use Throwable;

defined('COT_CODE') or die('Wrong URL.');

class MarketControlService
{
    use GetInstanceTrait;

    /**
     * Removes a store item from the CMS.
     * @param int $id Store item ID
     * @param array $itemData Store item data
     * @return bool|string "deleted" message on success, FALSE on error
     */
    public function delete(int $id, array $itemData = []): bool|string
    {
        if ($id <= 0) {
            return false;
        }

        // Если $itemData пустой, пробуем получить данные из репозитория
        if (empty($itemData)) {
            $itemData = MarketRepository::getInstance()->getById($id);
            if (empty($itemData)) {
                return false; // Товар не найден, возвращаем false
            }
        }

        try {
            Cot::$db->beginTransaction();

            // Удаляем связанные файлы для дополнительных полей
            foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
                if (isset($itemData['fieldmrkt_' . $exfld['field_name']])) {
                    cot_extrafield_unlinkfiles($itemData['fieldmrkt_' . $exfld['field_name']], $exfld);
                }
            }

            $trashcanId = 0; // Для плагина корзины, если используется

            // Удаляем товар из базы
            Cot::$db->delete(Cot::$db->market, 'fieldmrkt_id = ?', $id);

            // Обновляем счетчики структуры
            cot_market_updateStructureCounters($itemData['fieldmrkt_cat']);

            $itemDeletedMessage = ['deleted' => Cot::$L['market_deleted']];

            // Хук для дополнительных действий после удаления
            foreach (cot_getextplugins('market.delete.done') as $pl) {
                include $pl;
            }

            // Уведомляем ItemService об удалении
            ItemService::getInstance()->onDelete(MarketDictionary::SOURCE_MARKET, $id, $trashcanId);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            return false; // В случае ошибки возвращаем false
        }

        // Очищаем кэш, если он включен
        if (Cot::$cache) {
            if (Cot::$cfg['cache_market']) {
                Cot::$cache->static->clearByUri(cot_market_url($itemData));
                Cot::$cache->static->clearByUri(cot_url('market', ['c' => $itemData['fieldmrkt_cat']]));
            }
            if (Cot::$cfg['cache_index']) {
                Cot::$cache->static->clear('index');
            }
        }

        return is_array($itemDeletedMessage) ? implode('; ', $itemDeletedMessage) : $itemDeletedMessage;
    }
}