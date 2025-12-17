<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=itemService.getItems
[END_COT_EXT]
==================== */

declare(strict_types = 1);

use cot\dto\ItemDto;
use cot\extensions\ExtensionsDictionary;
use cot\modules\market\inc\MarketDictionary;
use cot\modules\market\inc\MarketRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Market module
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 *
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== MarketDictionary::SOURCE_MARKET || empty($sourceIds)) {
    return;
}

// for include file
global $L, $R, $Ls;

require_once cot_incfile('market', ExtensionsDictionary::TYPE_MODULE);

$marketIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $marketIds[] = $id;
    }
}
$marketIds = array_unique($marketIds);

$condition = 'fieldmrkt_id IN (' . implode(',', $marketIds) . ')';
$items = MarketRepository::getInstance()->getByCondition($condition);

foreach ($items as $row) {
    $url = cot_market_url($row);
    if (!cot_url_check($url)) {
        $url = COT_ABSOLUTE_URL . $url;
    }

    $dto = new ItemDto(
        MarketDictionary::SOURCE_MARKET,
        $row['fieldmrkt_id'],
        Cot::$L['Market'],
        $row['fieldmrkt_title'],
        $row['fieldmrkt_desc'],
        $url,
        (int) $row['fieldmrkt_ownerid']
    );

    if ($withFullItemData) {
        $dto->data = $row;
    }
    $dto->categoryCode = $row['fieldmrkt_cat'];
    $dto->categoryTitle = 'Unknown';
    if (isset(Cot::$structure['market'][$row['fieldmrkt_cat']])) {
        $dto->categoryUrl = cot_url('market', ['c' => $row['fieldmrkt_cat']]);
        $dto->categoryTitle = Cot::$structure['market'][$row['fieldmrkt_cat']]['title'];
    }

    $result[$dto->sourceId] = $dto;
}

/* === Hook === */
foreach (cot_getextplugins('market.itemService.getItems') as $pl) {
    include $pl;
}
/* ===== */