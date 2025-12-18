<?php
// Attention, this is very important!
// It's a must-read! and be sure to do it! 
// https://github.com/webitproff/cot-treecatspage or https://abuyfile.com/ru/forums/cotonti/custom/functions-custom/topic53
/* 
	...
4. To ensure the menu with subcategories works correctly and opens child categories when clicking on a parent category, check if you have a functions.custom.php file in the system folder.
4.1. If the file does not exist, upload functions.custom.php to the system folder and proceed to step 5.
4.2. If the file exists, open it and check if it contains the cot_load_structure_custom() function.
4.3. You can keep it as is if it’s present or update it.
5. Ensure that functions.custom.php is enabled.
5.2. Open the configuration file /datas/config.php and around line 89, verify that the following setting is present:
$cfg['customfuncs'] = true;
If it’s set to $cfg['customfuncs'] = false;, change it to true. Save and close the file. Code-related work is complete.
...
 */

defined('COT_CODE') or die('Wrong URL');

function cot_load_structure_custom()
{
    global $db, $db_structure, $cfg, $cot_extrafields, $structure;
    $structure = [];
    $subcats = [];

    if (defined('COT_UPGRADE')) {
        $sql = $db->query("SELECT * FROM $db_structure ORDER BY structure_path ASC");
        $row['structure_area'] = 'page';
    } else {
        $sql = $db->query("SELECT * FROM $db_structure ORDER BY structure_area ASC, structure_path ASC");
    }

    /* == Hook: Part 1 ==*/
    $extp = cot_getextplugins('structure');
    /* ================= */

    $path = []; // code path tree
    $tpath = []; // title path tree
    $tpls = []; // tpl codes tree

    foreach ($sql->fetchAll() as $row) {
        $last_dot = mb_strrpos($row['structure_path'], '.');

        $row['structure_tpl'] = $row['structure_tpl'] ?: $row['structure_code'];

        if ($last_dot !== false) {
            $path1 = mb_substr($row['structure_path'], 0, $last_dot);
            $path[$row['structure_path']] = ($path[$path1] ?? '') . '.' . $row['structure_code'];

            $separator = (strip_tags($cfg['separator']) === $cfg['separator']) ? ' ' . $cfg['separator'] . ' ' : ' \ ';
            $tpath[$row['structure_path']] = ($tpath[$path1] ?? '') . $separator . $row['structure_title'];

            $parent_dot = mb_strrpos($path[$path1] ?? '', '.');
            $parent = ($parent_dot !== false) ? mb_substr($path[$path1], $parent_dot + 1) : $path[$path1];

            $subcats[$row['structure_area']][$parent][] = $row['structure_code'];
        } else {
            $path[$row['structure_path']] = $row['structure_code'];
            $tpath[$row['structure_path']] = $row['structure_title'];
            $parent = $row['structure_code']; // self
        }

        if ($row['structure_tpl'] === 'same_as_parent') {
            $row['structure_tpl'] = $tpls[$parent] ?? $row['structure_code'];
        }

        $tpls[$row['structure_code']] = $row['structure_tpl'];

        $structure[$row['structure_area']][$row['structure_code']] = [
            'path' => $path[$row['structure_path']],
            'tpath' => $tpath[$row['structure_path']],
            'rpath' => $row['structure_path'],
            'id' => $row['structure_id'],
            'tpl' => $row['structure_tpl'],
            'title' => $row['structure_title'],
            'desc' => $row['structure_desc'],
            'icon' => $row['structure_icon'],
            'locked' => $row['structure_locked'],
            'count' => $row['structure_count']
        ];

        if (!empty($cot_extrafields[$db_structure])) {
            foreach ($cot_extrafields[$db_structure] as $exfld) {
                $fieldName = 'structure_' . $exfld['field_name'];
                $structure[$row['structure_area']][$row['structure_code']][$exfld['field_name']] = $row[$fieldName] ?? null;
            }
        }

        /* == Hook: Part 2 ==*/
        foreach ($extp as $pl) {
            include $pl;
        }
        /* ================= */
    }

    foreach ($structure as $area => $area_structure) {
        foreach ($area_structure as $i => $x) {
            $structure[$area][$i]['subcats'] = $subcats[$area][$i] ?? [];
        }
    }
}
