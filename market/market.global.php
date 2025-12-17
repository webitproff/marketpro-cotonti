<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=global
 * [END_COT_EXT]
 */



defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('market', 'module');
if (cot_module_active('payments')) {
    require_once cot_incfile('payments', 'module');
}
