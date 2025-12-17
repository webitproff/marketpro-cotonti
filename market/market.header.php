<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
[END_COT_EXT]
==================== */

/**
 * Header notices for new store items
 *
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (
    Cot::$usr['id'] > 0
    && (cot_auth('market', 'any', 'A') || cot_auth('market', 'any', 'W'))
) {
    require_once cot_incfile('market', 'module');
}

if (Cot::$usr['id'] > 0 && cot_auth('market', 'any', 'A')) {
    Cot::$sys['marketqueued'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
        ' WHERE fieldmrkt_state = 1')->fetchColumn();

    if (Cot::$sys['marketqueued'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('admin', 'm=market'),
            cot_declension(Cot::$sys['marketqueued'], $Ls['unvalidated_market'])
        ];
    }

    Cot::$sys['marketindrafts'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market
        ." WHERE fieldmrkt_state = 2")->fetchColumn();

    if (Cot::$sys['marketindrafts'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('admin', 'm=market&filter=drafts'),
            cot_declension(Cot::$sys['marketindrafts'], $Ls['market_in_drafts'])
        ];
    }

} elseif (Cot::$usr['id'] > 0 && cot_auth('market', 'any', 'W')) {
    Cot::$sys['marketqueued'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
        ' WHERE fieldmrkt_state=1 AND fieldmrkt_ownerid = ' . Cot::$usr['id'])->fetchColumn();

    if (Cot::$sys['marketqueued'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('market', 'c=unvalidated'),
            cot_declension(Cot::$sys['marketqueued'], $Ls['unvalidated_market'])
        ];
    }

    Cot::$sys['marketindrafts'] = (int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->market .
        " WHERE fieldmrkt_state=2 AND fieldmrkt_ownerid = " . Cot::$usr['id'])->fetchColumn();

    if (Cot::$sys['marketindrafts'] > 0) {
        Cot::$out['notices_array'][] = [
            cot_url('market', 'c=saved_drafts'),
            cot_declension(Cot::$sys['marketindrafts'], $Ls['market_in_drafts'])
        ];
    }
}