<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Market module main
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 *
 * @var string $m
 */

defined('COT_CODE') or die('Wrong URL.');

// Environment setup
define('COT_MARKET', TRUE);
$env['location'] = 'market';

// Additional API requirements
require_once cot_incfile('extrafields');

// Self requirements
require_once cot_incfile('market', 'module');

// Mode choice
if (!in_array($m, ['add', 'edit', 'counter'])) {
	if (isset($_GET['id']) || isset($_GET['al']))
	{
		$m = 'main';
	} else {
		$m = 'list';
	}
}

require_once cot_incfile('market', 'module', $m);

// Подключаем шапку сайта (header.tpl)
require_once $cfg['system_dir'].'/header.php';

// Выводим сгенерированное тело модуля (определяется в ранее подключённом файле)
echo $moduleBody;

// Подключаем футер сайта (footer.tpl)
require_once $cfg['system_dir'].'/footer.php';