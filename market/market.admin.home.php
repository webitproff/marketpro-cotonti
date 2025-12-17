<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.sidepanel
[END_COT_EXT]
==================== */

/**
 * Market manager & Queue of store items
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

$tt = new XTemplate(cot_tplfile('market.admin.home', 'module', true));

require_once cot_incfile('market', 'module');

$itemsqueued = $db->query("SELECT COUNT(*) FROM $db_market WHERE fieldmrkt_state='1'");
$itemsqueued = $itemsqueued->fetchColumn();
$tt->assign([
	'ADMIN_HOME_URL' => cot_url('admin', 'm=market'),
	'ADMIN_HOME_MARKETQUEUED' => $itemsqueued
]);

$tt->parse('MAIN');

$line = $tt->text('MAIN');