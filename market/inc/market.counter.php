<?php
/**
 * Store item views counter. For cached items.
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 *
 * @var string $a
 */

defined('COT_CODE') or die('Wrong URL');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');
cot_block(Cot::$usr['auth_read']);

$id = cot_import('id', 'G', 'INT');
cot_die(empty($id) || empty($a), true);

switch ($a) {
    case 'views':
        Cot::$db->query(
            'UPDATE ' . Cot::$db->market . ' SET fieldmrkt_count = fieldmrkt_count + 1 WHERE fieldmrkt_id = ?',
            $id
        );
}

exit();