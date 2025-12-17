<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */



defined('COT_CODE') or die('Wrong URL');


// marketTreeScript
$marketTreeScriptFile = Cot::$cfg['modules_dir'] . '/market/js/marketTreeScript.js';
if (file_exists($marketTreeScriptFile)) {
    Resources::linkFileFooter($marketTreeScriptFile, 'js');
}
