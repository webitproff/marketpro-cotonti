<?php
// declare(strict_types = 1);
/**
 * Add store item.
 * filename market.add.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

$id = cot_import('id', 'G', 'INT');
$c = cot_import('c', 'G', 'TXT');
//$a = cot_import('a', 'P', 'ALP'); // или 'POST' если форма отправляется методом POST
if (empty($c) && !isset(Cot::$structure['market'][$c])) {
	$c = '';
}

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');

/* === Hook === */
foreach (cot_getextplugins('market.add.first') as $pl) {
	include $pl;
}
/* ===== */
cot_block(Cot::$usr['auth_write']);

Cot::$sys['marketparser'] = Cot::$cfg['market']['marketparser'];
$parser_list = cot_get_parsers();

if ($a == 'add') {
	cot_shield_protect();

	/* === Hook === */
	foreach (cot_getextplugins('market.add.add.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$ritem = cot_market_import('POST', array(), Cot::$usr);

	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', $ritem['fieldmrkt_cat']);
	cot_block(Cot::$usr['auth_write']);

	/* === Hook === */
	foreach (cot_getextplugins('market.add.add.import') as $pl) {
		include $pl;
	}
	/* ===== */

	cot_market_validate($ritem);

	/* === Hook === */
	foreach (cot_getextplugins('market.add.add.error') as $pl) {
		include $pl;
	}
	/* ===== */
	if (!cot_error_found()) {
		$id = cot_market_add($ritem, Cot::$usr);

		switch ($ritem['fieldmrkt_state']) {
			case 0:
				$r_url = empty($ritem['fieldmrkt_alias']) 
					? cot_url('market', ['c' => $ritem['fieldmrkt_cat'], 'id' => $id], '', true)
					: cot_url('market', ['c' => $ritem['fieldmrkt_cat'], 'al' => $ritem['fieldmrkt_alias']], '', true);
               //$r_url = cot_market_url($ritem, [], '', true);
				break;
			case 1:
				$r_url = cot_url('message', 'msg=300', '', true);
				break;
			case 2:
				cot_message('market_savedasdraft');
				$r_url = cot_url('market', 'm=edit&id='.$id, '', true);
				break;
		}
		cot_redirect($r_url);

	} else {
        $urlParams = ['m' => 'add'];
	    if (!empty($c)) {
            $urlParams['c'] = $c;
        }
		cot_redirect(cot_url('market', $urlParams, '', true));
	}

}

$ritem = [
    'fieldmrkt_metatitle' => '',
    'fieldmrkt_metadesc' => '',
    'fieldmrkt_alias' => '',
    'fieldmrkt_title' => '',
    'fieldmrkt_desc' => '',
    'fieldmrkt_text' => '',
    'fieldmrkt_costdflt' => 0,
];

// Item cloning support
$clone = cot_import('clone', 'G', 'INT');
if ($clone > 0) {
	$ritem = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?', $clone)->fetch();
    if (!$ritem) {
        cot_die_message(404);
    }
}

if (empty($ritem['fieldmrkt_cat'])) {
    $ritem['fieldmrkt_cat'] = isset($c) ? $c : '';
}

$breadcrumbs = [];
$urlParams = ['m' => 'add'];

if (!empty($ritem['fieldmrkt_cat'])) {
    list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', $ritem['fieldmrkt_cat']);
    cot_block(Cot::$usr['auth_write']);

    if (!Cot::$usr['isadmin'] && Cot::$structure['market'][$ritem['fieldmrkt_cat']]['locked']) {
        cot_die_message(602, TRUE);
    }

    Cot::$sys['sublocation'] = Cot::$structure['market'][$ritem['fieldmrkt_cat']]['title'];
    $mskin = cot_tplfile(['market', 'add', Cot::$structure['market'][$ritem['fieldmrkt_cat']]['tpl']]);
    $breadcrumbs = cot_structure_buildpath('market', $ritem['fieldmrkt_cat']);
    $urlParams['c'] = $ritem['fieldmrkt_cat'];
} else {
    if (!Cot::$usr['isadmin']) {
        // User can add item to these categories
        $categories = [];
        if (!empty(Cot::$structure['market'])) {
            foreach (Cot::$structure['market'] as $i => $x) {
                $display = cot_auth('market', $i, 'W');
                if ($display && !empty($subcat) && isset(Cot::$structure['market'][$subcat])) {
                    $mtch = Cot::$structure['market'][$subcat]['path'] . ".";
                    $mtchlen = mb_strlen($mtch);
                    $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
                }
                if ($i != 'all' && $display) {
                    $categories[] = $i;
                }
            }
        }
        cot_block(count($categories) > 0);
    }

    Cot::$sys['sublocation'] = Cot::$L['market_addtitle'];
    $mskin = cot_tplfile(['market', 'add']);
}

Cot::$out['subtitle'] = Cot::$L['market_addsubtitle'];
if (!isset(Cot::$out['head'] )) {
    Cot::$out['head']  = '';
}
Cot::$out['head'] .= Cot::$R['code_noindex'];

$breadcrumbs[] = [cot_url('market', $urlParams), Cot::$L['market_addtitle']];

/* === Hook === */
foreach (cot_getextplugins('market.add.main') as $pl) {
	include $pl;
}
/* ===== */


$t = new XTemplate($mskin);

$itemadd_array = [
	'MARKETADD_PAGETITLE' => Cot::$L['market_addtitle'],
    'MARKETADD_BREADCRUMBS' => cot_breadcrumbs($breadcrumbs, Cot::$cfg['homebreadcrumb']),
	'MARKETADD_SUBTITLE'  => Cot::$L['market_addsubtitle'],
	'MARKETADD_ADMINEMAIL' => 'mailto:' . Cot::$cfg['adminemail'],
	'MARKETADD_FORM_SEND' => cot_url('market', 'm=add&a=add&c=' . $c),
	'MARKETADD_FORM_CAT' => cot_selectbox_structure('market', $ritem['fieldmrkt_cat'], 'ritemmarketcat'),
	'MARKETADD_FORM_CAT_SHORT' => cot_selectbox_structure('market', $ritem['fieldmrkt_cat'], 'ritemmarketcat', $c),

	'MARKETADD_FORM_CAT_S2' => cot_market_selectbox_structure_select2('market', $ritem['fieldmrkt_cat'], 'ritemmarketcat'),
	'MARKETADD_FORM_CAT_SHORT_S2' => cot_market_selectbox_structure_select2('market', $ritem['fieldmrkt_cat'], 'ritemmarketcat', $c),

	'MARKETADD_FORM_METATITLE' => cot_inputbox('text', 'ritemmarketmetatitle', $ritem['fieldmrkt_metatitle'], ['maxlength' => '255']),
	'MARKETADD_FORM_METADESC' => cot_textarea('ritemmarketmetadesc', $ritem['fieldmrkt_metadesc'], 2, 64, ['maxlength' => '255']),
	'MARKETADD_FORM_ALIAS' => cot_inputbox('text', 'ritemmarketalias', $ritem['fieldmrkt_alias'], ['maxlength' => '255']),
	'MARKETADD_FORM_TITLE' => cot_inputbox('text', 'ritemmarkettitle', $ritem['fieldmrkt_title'], ['maxlength' => '255']),
	'MARKETADD_FORM_DESCRIPTION' => cot_textarea('ritemmarketdesc', $ritem['fieldmrkt_desc'], 2, 64, ['maxlength' => '255']),
	
	'MARKETADD_FORM_OWNER' => cot_build_user(Cot::$usr['id'], Cot::$usr['name']),
	'MARKETADD_FORM_OWNER_ID' => Cot::$usr['id'],
	'MARKETADD_FORM_DATE' => cot_selectbox_date(Cot::$sys['now'], 'long', 'ritemmarketdate'),

	'MARKETADD_FORM_TEXT' => cot_textarea('ritemmarkettext', $ritem['fieldmrkt_text'], 24, 120, '', 'input_textarea_editor'),
	'MARKETADD_FORM_PARSER' => cot_selectbox(Cot::$cfg['market']['marketparser'], 'ritemmarketparser', $parser_list, $parser_list, false),
	'MARKETADD_FORM_COSTDFLT' => cot_inputbox('text', 'ritemmarketcostdflt', $ritem['fieldmrkt_costdflt'], 'size="10"'),
];


$t->assign($itemadd_array);

// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->market])) {
    foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $data = isset($ritem['fieldmrkt_' . $exfld['field_name']]) ? $ritem['fieldmrkt_' . $exfld['field_name']] : null;
        $exfld_val = cot_build_extrafields('ritemmarket' . $exfld['field_name'], $exfld, $data);
        $exfld_title = cot_extrafield_title($exfld, 'market_');

        $t->assign([
            'MARKETADD_FORM_' . $uname => $exfld_val,
            'MARKETADD_FORM_' . $uname . '_TITLE' => $exfld_title,
            'MARKETADD_FORM_EXTRAFLD' => $exfld_val,
            'MARKETADD_FORM_EXTRAFLD_TITLE' => $exfld_title,
        ]);
        $t->parse('MAIN.EXTRAFLD');
    }
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('market.add.tags') as $pl) {
	include $pl;
}
/* ===== */

$usr_can_publish = false;
if (Cot::$usr['isadmin']) {
	if (Cot::$cfg['market']['marketautovalidate']) {
        $usr_can_publish = true;
    }
	$t->parse('MAIN.ADMIN');
}

$t->parse('MAIN');
$moduleBody = $t->text('MAIN');