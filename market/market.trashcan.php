<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=trashcan.api
[END_COT_EXT]
==================== */

/**
 * Trash can support for market
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('market', 'module');

// Register restoration table
$trash_types['market'] = Cot::$db->market;

/**
 * Sync market action
 * @param array $data Market item data as array (trashcan item data)
 * @return bool
 */
function cot_trash_market_sync($data)
{
    cot_market_updateStructureCounters($data['fieldmrkt_cat']);
    if (\Cot::$cache) {
        if (\Cot::$cfg['cache_market']) {
            \Cot::$cache->static->clearByUri(cot_market_url($data));
            \Cot::$cache->static->clearByUri(cot_url('market', ['c' => $data['fieldmrkt_cat']]));
        }
        if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
        }
    }
	return true;
}