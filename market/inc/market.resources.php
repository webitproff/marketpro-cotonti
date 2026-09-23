<?php
// declare(strict_types = 1);
/**
 * Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
 *
 * Ресурсы (строки-шаблоны) для списка товаров, сортировки, иконок и пр.
 * Эти ресурсы используются в XTemplate-шаблонах модуля Market.
 *
 * Filename: market.resources.php
 *
 * Path:    modules/market/inc/market.resources.php
 *
 * Source and updates   https://github.com/webitproff/marketpro-cotonti
 * ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
 * Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
 *
 * Date: Sep 10, 2026
 *
 * @package market
 * @version 5.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/marketpro-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Ссылка "Добавить новый товар"
 */
$R['market_submitnewitem'] = '<a href="{$sub_url}" rel="nofollow">' . Cot::$L['market_goto_add_new_item_title'] . '</a>';

/**
 * Ссылки сортировки для колонок списка
 */
$R['list_link_title']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_title'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> ' . $L['Title'];

$R['list_link_key']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_key'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> ' . $L['Key'];

$R['list_link_date']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_date'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> ' . $L['Date'];

$R['list_link_author']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_author'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> ' . $L['Author'];

$R['list_link_owner']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_owner'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> ' . $L['Owner'];

$R['list_link_count']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_count'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> ' . $L['Hits'];

$R['list_link_filecount']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_filecount'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a> ' . $L['Hits'];

$R['list_link_field_name']  = '<a href="{$list_link_url_down}" rel="nofollow">{$cot_img_down}</a>';
$R['list_link_field_name'] .= '<a href="{$list_link_url_up}" rel="nofollow">{$cot_img_up}</a>&nbsp;{$extratitle}';

/**
 * Ссылки для администратора в строке списка
 */
$R['list_row_admin'] = '<a href="{$unvalidate_url}">' . $L['Putinvalidationqueue'] . '</a> <a href="{$edit_url}">' . $L['Edit'] . '</a>';

/**
 * Ссылка "Читать далее"
 */
$R['list_more'] = ' <span class="readmore"><a href="{$page_url}" title="' . $L['ReadMore'] . '">' . $L['ReadMore'] . '</a></span>';

/**
 * Иконки Market
 */
$R['market_code_redir'] = '<script type="text/javascript">location.href="{$redir}"</script>Redirecting...';

$R['market_icon_file'] = '<img class="icon" src="{$icon}" alt="' . $L['File'] . '" />';
$R['market_icon_file_default'] = Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons'] . '/24/market.png';
$R['market_icon_file_path'] = 'images/filetypes/' . Cot::$cfg['defaulticons'] . '/{$type}.png';
$R['market_icon_cat_default'] = 'apple-touch-icon.png';