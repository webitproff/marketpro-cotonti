<?php
/**
 * Store item repository
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

declare(strict_types=1);

namespace cot\modules\market\inc;

use Cot;
use cot\repositories\BaseRepository;

defined('COT_CODE') or die('Wrong URL');

class MarketRepository extends BaseRepository
{
    private static $cacheById = [];

    public static function getTableName(): string
    {
        if (empty(Cot::$db->market)) {
            Cot::$db->registerTable('market');
        }
        return Cot::$db->market;
    }

    /**
     * Fetches store item entry from DB
     * @param int $id Store item ID
     * @param bool $useCache Use one time session cache
     * @return ?array
     */
    public function getById(int $id, bool $useCache = true): ?array
    {
        if ($id < 1) {
            return null;
        }

        if ($useCache && isset(self::$cacheById[$id])) {
            return self::$cacheById[$id] !== false ? self::$cacheById[$id] : null;
        }

        $condition = 'fieldmrkt_id = :itemId';
        $params = ['itemId' => $id];

        $results = $this->getByCondition($condition, $params);
        $result = !empty($results) ? $results[0] : null;

        self::$cacheById[$id] = !empty($result) ? $result : false;

        return $result;
    }

    protected function afterFetch(array $item): array
    {
        $item['fieldmrkt_id'] = (int) $item['fieldmrkt_id'];
        $item['fieldmrkt_state'] = (int) $item['fieldmrkt_state'];
        $item['fieldmrkt_ownerid'] = (int) $item['fieldmrkt_ownerid'];
        $item['fieldmrkt_date'] = (int) $item['fieldmrkt_date'];
        $item['fieldmrkt_begin'] = (int) $item['fieldmrkt_begin'];
        $item['fieldmrkt_expire'] = (int) $item['fieldmrkt_expire'];
        $item['fieldmrkt_updated'] = (int) $item['fieldmrkt_updated'];

        $item['fieldmrkt_count'] = (int) $item['fieldmrkt_count'];


        return $item;
    }
}