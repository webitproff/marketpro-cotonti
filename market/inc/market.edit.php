<?php global $cfg;

/**
 * Edit store item.
 * filename market.edit.php
 * @package Market
 * @copyright (c) webitproff
 * @license BSD
 */

use cot\exceptions\NotFoundHttpException;
use cot\modules\market\inc\MarketDictionary;
use cot\modules\market\inc\MarketRepository;
use cot\modules\market\inc\MarketControlService;


defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

$id = cot_import('id', 'G', 'INT');
$c = cot_import('c', 'G', 'TXT');
$item['fieldmrkt_parser'] = cot_import('rparser', 'P', 'ALP');
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', 'any');

/* === Hook === */
foreach (cot_getextplugins('market.edit.first') as $pl) {
	include $pl;
}
/* ===== */

cot_block(Cot::$usr['auth_read']);

if (!$id || $id < 0) {
    throw new NotFoundHttpException();
}
$row_item = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?', $id)->fetch();
if ($row_item === null) {
    throw new NotFoundHttpException();
}

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('market', $row_item['fieldmrkt_cat']);

// Устанавливаем парсер по умолчанию, если он пустой
if (empty($item['fieldmrkt_parser'])) {
    $item['fieldmrkt_parser'] = $cfg['market']['marketparser'] ?: 'html';
}
$parser_list = cot_get_parsers();
Cot::$sys['marketparser'] = $row_item['fieldmrkt_parser'];

if ($a == 'update') {
	/* === Hook === */
	foreach (cot_getextplugins('market.edit.update.first') as $pl) {
		include $pl;
	}
	/* ===== */

	cot_block(Cot::$usr['isadmin'] || Cot::$usr['auth_write'] && Cot::$usr['id'] == $row_item['fieldmrkt_ownerid']);

	$ritem = cot_market_import('POST', $row_item, Cot::$usr);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$ritemdelete = cot_import('ritemmarketdelete', 'P', 'BOL');
	} else {
		$ritemdelete = cot_import('delete', 'G', 'BOL');
		cot_check_xg();
	}

	if ($ritemdelete) {
		$resultOrMessage = MarketControlService::getInstance()->delete($id, $row_item);
		if ($resultOrMessage !== false) {
			cot_message($resultOrMessage);
			cot_redirect(cot_url('market', ['c' => $row_item['fieldmrkt_cat']], '', true));
		}
	}


	/* === Hook === */
	foreach (cot_getextplugins('market.edit.update.import') as $pl) {
		include $pl;
	}
	/* ===== */

	cot_market_validate($ritem);

	/* === Hook === */
	foreach (cot_getextplugins('market.edit.update.error') as $pl) {
		include $pl;
	}
	/* ===== */

	if (!cot_error_found()) {
		cot_market_update($id, $ritem);

		switch ($ritem['fieldmrkt_state']) {
			case MarketDictionary::STATE_PUBLISHED:
                $r_url = cot_market_url($ritem, [], '', true);
				break;

			case MarketDictionary::STATE_PENDING:
				$r_url = cot_url('message', 'msg=300', '', true);
				break;

			case MarketDictionary::STATE_DRAFT:
				cot_message(Cot::$L['market_savedasdraft']);
				$r_url = cot_url('market', 'm=edit&id=' . $id, '', true);
				break;
		}
		cot_redirect($r_url);
	} else {
		cot_redirect(cot_url('market', "m=edit&id=$id", '', true));
	}
}

$item = $row_item;

$item['fieldmrkt_status'] = cot_market_status($item['fieldmrkt_state']);

cot_block(Cot::$usr['isadmin'] || Cot::$usr['auth_write'] && Cot::$usr['id'] == $item['fieldmrkt_ownerid']);

Cot::$out['subtitle'] = Cot::$L['market_edittitle'];
if (!isset(Cot::$out['head'])) {
    Cot::$out['head'] = '';
}
Cot::$out['head'] .= Cot::$R['code_noindex'];
Cot::$sys['sublocation'] = Cot::$structure['market'][$item['fieldmrkt_cat']]['title'];

$mskin = cot_tplfile(array('market', 'edit', Cot::$structure['market'][$item['fieldmrkt_cat']]['tpl']));

/* === Hook === */
foreach (cot_getextplugins('market.edit.main') as $pl) {
	include $pl;
}
/* ===== */


$t = new XTemplate($mskin);

$breadcrumbs = cot_structure_buildpath('market', $item['fieldmrkt_cat']);
$breadcrumbs[] = [cot_market_url($item), $item['fieldmrkt_title']];
$breadcrumbs[] = Cot::$L['market_edittitle'];

$itemedit_array = [
	'MARKETEDIT_PAGETITLE' => Cot::$L['market_edittitle'],
	'MARKETEDIT_SUBTITLE' => Cot::$L['market_editsubtitle'],
    'MARKETEDIT_BREADCRUMBS' => cot_breadcrumbs($breadcrumbs, Cot::$cfg['homebreadcrumb']),
	'MARKETEDIT_FORM_SEND' => cot_url('market', ['m' => 'edit', 'a' => 'update', 'id' => $item['fieldmrkt_id']]),
	'MARKETEDIT_FORM_ID' => $item['fieldmrkt_id'],
	'MARKETEDIT_FORM_STATE' => $item['fieldmrkt_state'],
	'MARKETEDIT_FORM_STATUS' => $item['fieldmrkt_status'],
	'MARKETEDIT_FORM_LOCAL_STATUS' => Cot::$L['market_status_' . $item['fieldmrkt_status']],
	'MARKETEDIT_FORM_CAT' => cot_selectbox_structure('market', $item['fieldmrkt_cat'], 'ritemmarketcat'),
	'MARKETEDIT_FORM_CAT_SHORT' => cot_selectbox_structure('market', $item['fieldmrkt_cat'], 'ritemmarketcat', $c),

	'MARKETEDIT_FORM_CAT_S2' => cot_market_selectbox_structure_select2('market', $item['fieldmrkt_cat'], 'ritemmarketcat'),
	'MARKETEDIT_FORM_CAT_SHORT_S2' => cot_market_selectbox_structure_select2('market', $item['fieldmrkt_cat'], 'ritemmarketcat', $c),
	
	'MARKETEDIT_FORM_METATITLE' => cot_inputbox('text', 'ritemmarketmetatitle', $item['fieldmrkt_metatitle'], array('maxlength' => '255')),
	'MARKETEDIT_FORM_METADESC' => cot_textarea('ritemmarketmetadesc', $item['fieldmrkt_metadesc'], 2, 64, array('maxlength' => '255')),
	'MARKETEDIT_FORM_ALIAS' => cot_inputbox('text', 'ritemmarketalias', $item['fieldmrkt_alias'], array('maxlength' => '255')),
	'MARKETEDIT_FORM_TITLE' => cot_inputbox('text', 'ritemmarkettitle', $item['fieldmrkt_title'], array('maxlength' => '255')),
    'MARKETEDIT_FORM_DESCRIPTION' => cot_textarea('ritemmarketdesc', $item['fieldmrkt_desc'], 2, 64, array('maxlength' => '255')),
	
	'MARKETEDIT_FORM_DATE' => cot_selectbox_date($item['fieldmrkt_date'], 'long', 'ritemmarketdate').' '.Cot::$usr['timetext'],
	'MARKETEDIT_FORM_DATENOW' => cot_checkbox(0, 'ritemmarketdatenow'),

	'MARKETEDIT_FORM_UPDATED' => cot_date('datetime_full', $item['fieldmrkt_updated']).' '.Cot::$usr['timetext'],
	'MARKETEDIT_FORM_TEXT' => cot_textarea('ritemmarkettext', $item['fieldmrkt_text'], 24, 120, '', 'input_textarea_editor'),
	'MARKETEDIT_FORM_DELETE' => cot_radiobox(0, 'ritemmarketdelete', [1, 0], [Cot::$L['Yes'], Cot::$L['No']]),
	'MARKETEDIT_FORM_PARSER' => cot_selectbox($item['fieldmrkt_parser'], 'ritemmarketparser', cot_get_parsers(), cot_get_parsers(), false),
	'MARKETEDIT_FORM_COSTDFLT' => cot_inputbox('text', 'ritemmarketcostdflt', $item['fieldmrkt_costdflt'], 'size="10"'),
];

if (Cot::$usr['isadmin']) {
	$itemedit_array += [
		'MARKETEDIT_FORM_OWNER_ID' => cot_inputbox('text', 'ritemmarketownerid', $item['fieldmrkt_ownerid'], ['maxlength' => '24']),
		'MARKETEDIT_FORM_HITS' => cot_inputbox('text', 'ritemmarketcount', $item['fieldmrkt_count'], ['maxlength' => '8']),
	];
}

$t->assign($itemedit_array);
// если видим 
// Warning: Undefined array key "fieldmrkt_file" in /...../modules/market/inc/market.edit.php on line 190
// идем в экстраполя Управление сайтом Прочее Экстраполя cot_market - Модуль Market и удаляем fieldmrkt_file
// Extra fields
if (!empty(Cot::$extrafields[Cot::$db->market])) {
    foreach (Cot::$extrafields[Cot::$db->market] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $extrafieldElement = cot_build_extrafields(
            'ritemmarket' . $exfld['field_name'],
            $exfld,
            $item['fieldmrkt_' . $exfld['field_name']] // line 190
        );
        $extrafieldTitle = cot_extrafield_title($exfld, 'market_');

        $t->assign([
            'MARKETEDIT_FORM_' . $uname => $extrafieldElement,
            'MARKETEDIT_FORM_' . $uname . '_TITLE' => $extrafieldTitle,
            'MARKETEDIT_FORM_EXTRAFLD' => $extrafieldElement,
            'MARKETEDIT_FORM_EXTRAFLD_TITLE' => $extrafieldTitle
        ]);
        $t->parse('MAIN.EXTRAFLD');
    }
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('market.edit.tags') as $pl) {
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