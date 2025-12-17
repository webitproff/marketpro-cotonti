<?php
/* ====================
[BEGIN_COT_EXT]
Code=market
Name=Market PRO v.5+ by webitproff
Description=Store Items and Categories
Version=5.0.1
Date=2025-12-03
Author=webitproff
Copyright=(c) 
Notes=BSD License
Auth_guests=R
Lock_guests=A
Auth_members=RW1
Lock_members=
Admin_icon=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
marketmarkup=01:radio::1:
marketparser=02:callback:cot_get_parsers():none:
marketcount_admin=03:radio::0:
marketautovalidate=04:radio::1:
marketmaxlistsperpage=06:select:5,6,7,8,9,10,15,20:10:
markettitle_page=07:string::{TITLE} - {CATEGORY}:
marketlist_default_title=08:string::Заголовок магазина по-умолчанию, когда не выбрана категория или товар:
marketlist_default_desc=09:string::Описание магазина по-умолчанию, когда не выбрана категория или товар:
marketblacktreecatspage=10:string:::Category codes (black list codes page structure as system, unvalidated e.t.c)
market_currency=06:select:USD,EUR,RUB,UAH,USDT,BTC:BTC:
[END_COT_EXT_CONFIG]

[BEGIN_COT_EXT_CONFIG_STRUCTURE]
marketorder=01:callback:cot_market_config_order():title:
marketway=02:select:asc,desc:asc:
maxrowsperpage=03:string::30:
markettruncatetext=04:string::0:
marketallowemptytext=05:radio::0:
marketmetatitle=07:string:::
marketmetadesc=08:string:::
marketmaxlistsperpage=09:select:5,6,7,8,9,10,15,20:10:
[END_COT_EXT_CONFIG_STRUCTURE]
==================== */