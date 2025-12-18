<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$useUrlEditorTree = false;

if (cot_plugin_active('urleditor')) {
    $preset = $cfg['plugin']['urleditor']['preset'] ?? 'none';
    if ($preset === 'handy' || $preset === 'myconfig') {
        $useUrlEditorTree = true;
    }
}

if ($useUrlEditorTree) {
    // URL Editor ON
    $file = Cot::$cfg['modules_dir'] . '/market/js/marketTreeScriptURLEditor.js';
} else {
    // URL Editor OFF (default)
    $file = Cot::$cfg['modules_dir'] . '/market/js/marketTreeScript.js';
}

if (file_exists($file)) {
    Resources::linkFileFooter($file, 'js');
}
