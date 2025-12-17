<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
==================== */

/**
 * Market module
 * filename market.extrafields.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('market', 'module');
$extra_whitelist[$db_market] = [
	'name' => $db_market,
	'caption' => $L['Module'].' Market',
	'type' => 'module',
	'code' => 'market',
	'tags' => [
		'market.list.tpl' => '{LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}',
		'market.tpl' => '{MARKET_XXXXX}, {MARKET_XXXXX_TITLE}',
		'market.add.tpl' => '{MARKETADD_FORM_XXXXX}, {MARKETADD_FORM_XXXXX_TITLE}',
		'market.edit.tpl' => '{MARKETEDIT_FORM_XXXXX}, {MARKETEDIT_FORM_XXXXX_TITLE}',
		'news.tpl' => '{MARKET_ROW_XXXXX}',
		'recentitems.market.tpl' => '{MARKET_ROW_XXXXX}',
	]
];