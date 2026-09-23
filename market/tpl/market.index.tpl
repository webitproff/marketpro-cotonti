<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.index.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.index.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.index.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.functions.php
	*                     _ _ _ _ SEE: cot_getmarketlist()
	* Назначение:
	*   Шаблон блока «товары Market» для вывода на главной странице сайта
	*   (или в любом другом месте через cot_getmarketlist()). Отображает
	*   компактную сетку карточек товаров с обложкой, названием, категорией,
	*   ценой, звёздами рейтинга (при активном плагине marketreviews) и
	*   кнопкой «В корзину» (при активном payordersmarket). Данные получает
	*   готовым HTML через cot_getmarketlist() — функция сама подставляет
	*   значения через cot_generate_markettags().
	*
	*   ПРИМЕР https://abuyfile.com/ru (главная сайта, блок Market)
	*
	* Как подключается:
	*   В шаблоне темы (обычно index.tpl или его блок) вызывается:
	*     {PHP|cot_getmarketlist('index', 5)}
	*   где 'index' — часть имени этого шаблона, 5 — количество товаров.
	*   Дополнительные аргументы функции:
	*     cot_getmarketlist($template, $count, $sqlsearch, $order)
	*       $sqlsearch — доп. условие WHERE для SQL;
	*       $order     — SQL-строка сортировки (по умолчанию из cfg.market.market_main_order).
	*
	* Основные параметры (аргументы функции, не URL):
	*   $template = 'index'                — имя шаблона без .tpl (например 'index');
	*   $count    = 5                      — количество товаров;
	*   $sqlsearch= ''                     — доп. SQL-условие WHERE;
	*   $order    = 'fieldmrkt_updated DESC' (или из cfg.market.market_main_order).
	*
	* Основные теги шаблона:
	*   Общие:
	*     PHP.cfg.market.marketlist_default_title — заголовок блока (из настроек);
	*     PHP.cfg.market.marketlist_default_desc  — описание блока;
	*     PHP.L.market_go_to_catalog              — текст кнопки «В каталог»;
	*     PHP.L.market_goto_add_new_item_title    — текст кнопки «Добавить товар»;
	*     PHP.cfg.payments.valuta                 — валюта (если задана);
	*     PHP.cfg.market.market_currency          — валюта модуля (fallback).
	*
	*   Одна карточка товара (BEGIN: MARKET_ROW):
	*     MARKET_ROW_ID              — ID товара;
	*     MARKET_ROW_URL             — URL карточки товара;
	*     MARKET_ROW_TITLE           — название товара;
	*     MARKET_ROW_CAT_TITLE       — название категории;
	*     MARKET_ROW_COSTDFLT        — цена в базовой валюте;
	*     MARKET_ROW_COST_RAW        — «сырая» цена (для JS-конвертера
	*                                  marketcurrencyswitcher);
	*     MARKET_ROW_ODDEVEN         — odd/even (для zebra-стилизации);
	*     MARKET_ROW_OWNER_*         — теги владельца (cot_generate_usertags);
	*     MARKET_ROW_REVIEWS_AVG_STARS_HTML — HTML звёзд рейтинга (marketreviews);
	*     MARKET_ROW_REVIEWS_TOTAL_COUNT    — число отзывов (marketreviews);
	*     MARKET_ROW_ORDER_IN_CART          — флаг «уже в корзине» (payordersmarket).
	*
	*   Кнопки в карточке (опционально):
	*     payordersmarket: «Добавить в корзину» / «В корзине» / модалка auth
	*                      для гостей (#authModal);
	*     marketcurrencyswitcher: цена с data-base-price для JS-конвертера.
	*
	*   Вспомогательный вывод по ID (внизу файла):
	*     cot_market_get_by_id_owner_tpl('getbyid', '29,71') — вывод товаров
	*     по ID с заданным шаблоном (см. документацию функции).
	*
	*   Отладка (только админ):
	*     Блок «This is usually a template: market.index.tpl» — подсказка
	*     администратору, видна только при PHP.usr.isadmin.
	*
	* Используемые плагины (опционально):
	*   attacher               — вывод изображения товара (att_count / att_display);
	*   marketreviews          — звёзды рейтинга и число отзывов;
	*   marketcurrencyswitcher — вывод цены с data-base-price для JS;
	*   payordersmarket        — кнопка «В корзину» / «В корзине» / модалка входа.
	*
	* Source and updates   https://github.com/webitproff/marketpro-cotonti
	* ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
	* Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
	*
	* Date: Sep 23, 2026
	*
	* @package market
	* @version 5.7.9
	* @author webitproff
	* @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff
	* @license BSD	
-->



<!-- BEGIN: MARKET -->
<h2 class="text-success h4 mt-0 mb-3">{PHP.cfg.market.marketlist_default_title}</h2>
<p class="h6 mt-0 mb-4">{PHP.cfg.market.marketlist_default_desc}</p>
<div class="row align-items-center mb-4">
	<div class="col-md-6 d-flex justify-content-center justify-content-md-start mb-3 mb-md-0">
		<a href="{PHP|cot_url('market')}" class="btn btn-outline-primary">
			<span class="me-2">
				<i class="fa-solid fa-store"></i>
			</span>{PHP.L.market_go_to_catalog}
		</a>
	</div>
	<!-- IF {PHP|cot_auth('market', 'any', 'W')} -->
	<div class="col-md-6 d-flex justify-content-center justify-content-md-end">
		<a class="btn btn-outline-success" href="{PHP|cot_url('market', 'm=add')}">{PHP.L.market_goto_add_new_item_title}</a>
	</div>
	<!-- ENDIF -->
	<!-- IF {PHP.usr.id} == 0 -->
	<div class="col-md-6 d-flex justify-content-center justify-content-md-end">
		<a class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#authModal" >{PHP.L.market_goto_add_new_item_title}</a>
	</div>
	<!-- ENDIF -->
</div>
<div id="listmarket">
	<div class="row">
		<!-- BEGIN: MARKET_ROW -->
		<div class="col-12 col-md-6 col-xl-4">
			<div class="attacherPicIntList-card" style="background-color: var(--bs-sidebar-bg)">
				<a class="attacherPicIntList-thumbnail" data-fancybox="gallery" href="{MARKET_ROW_URL}" data-caption="{MARKET_ROW_TITLE}">
					<!-- IF {PHP|cot_plugin_active('attacher')} -->
					<!-- IF {MARKET_ROW_ID|att_count('market', $this, '', 'images')} > 0 -->
					<div class="att-image">{MARKET_ROW_ID|att_display('market',$this,'','attacher.display.marketlist','images',1)}</div>
					<!-- ELSE -->
					<img src="{PHP.R.page_default_image}" alt="{MARKET_ROW_TITLE}">
					<!-- ENDIF -->
					<!-- ELSE -->
					<img src="{PHP.R.page_default_image}" alt="{MARKET_ROW_TITLE}">
					<!-- ENDIF -->
				</a>
				<div class="attacherPicIntList-card-body">
					<div class="attacherPicIntList-title">
						<a href="{MARKET_ROW_URL}" class="text-decoration-none" title="{MARKET_ROW_TITLE}">{MARKET_ROW_TITLE}</a>
					</div>
					<div class="attacherPicIntList-desc">
						<!-- IF {PHP|cot_plugin_active('marketreviews')} -->
						<div><span class="review-stars" title="{PHP.L.marketreviews_pageRatingValue}">{MARKET_ROW_REVIEWS_AVG_STARS_HTML}</span>
							<!-- IF {MARKET_ROW_REVIEWS_TOTAL_COUNT} > 0 -->
							<span class="text-body" title="{PHP.L.marketreviews_pageCountReviewsTotalValue}">
							<span class="d-none d-sm-inline me-2">•</span><i class="fa-solid fa-comment-dots fa-lg"></i> {MARKET_ROW_REVIEWS_TOTAL_COUNT}</span>
							<!-- ENDIF -->
						</div>
						<!-- ENDIF -->
						{MARKET_ROW_CAT_TITLE}
						<!-- IF {PHP|cot_plugin_active('marketcurrencyswitcher')} -->
						<!-- IF {MARKET_ROW_COSTDFLT} > 0 -->
						<p class="fw-bold">
							<span class="price-label">{PHP.L.market_price}</span>
							<span class="ms-2 text-success market-price" data-base-price="{MARKET_ROW_COST_RAW}">
								{MARKET_ROW_COSTDFLT} {PHP.cfg.payments.valuta}
							</span>
						</p>
						<!-- ENDIF -->
						<!-- ELSE -->
						<!-- IF {MARKET_ROW_COSTDFLT} > 0 -->
						<span class="ms-2 text-success fw-bold">
							{MARKET_ROW_COSTDFLT} 
							<!-- IF {PHP.cfg.payments.valuta} -->
							{PHP.cfg.payments.valuta}
							<!-- ELSE -->
							{PHP.cfg.market.market_currency}
							<!-- ENDIF -->
						</span>
						<!-- ENDIF -->
						<!-- ENDIF -->
					</div>
					
					<!-- IF {PHP|cot_plugin_active('payordersmarket')} AND {PHP.usr.id} -->
					<!-- IF !{MARKET_ROW_ORDER_IN_CART} -->
					<a href="javascript:void(0)" class="btn btn-success add-to-cart" data-id="{MARKET_ROW_ID}">
						{PHP.L.payordersmarket_add_to_cart}
					</a>
					<span class="cart-added-msg text-success ms-2" style="display:none;">{PHP.L.payordersmarket_added_to_cart}</span>
					<!-- ELSE -->
					<span class="btn btn-secondary">{PHP.L.payordersmarket_in_cart}</span>
					<!-- ENDIF -->
					<!-- ENDIF -->
					
					<!-- IF {PHP|cot_plugin_active('payordersmarket')} AND {PHP|cot_auth('plug', 'payordersmarket', 'R')} AND {PHP.usr.id} == 0 -->
					<a class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#authModal">{PHP.L.payordersmarket_add_to_cart}</a>
					<!-- ENDIF -->
				</div>
			</div>
		</div>
		<!-- END: MARKET_ROW -->
	</div>
</div>

<!-- IF {PHP.usr.isadmin} --> 
<div class="col-12">
<div class="alert alert-info" role="alert">
	<div>This is usually a template: <code>market.index.tpl</code></div>
	<div>See your index.tpl and <code>cot_getmarketlist()</code> in modules/market/inc/market.functions.php</div>
	<div class="small text-dark">This message is shown only to the Administrator.</div>
</div>
</div>
<!-- ENDIF -->

<!-- IF {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29,71')} -->
<div class="mb-5">
    {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29,71')}
</div>
<!-- ENDIF -->

<!-- END: MARKET -->
