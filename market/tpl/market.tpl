<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.main.php
	* Назначение:
	*   Шаблон страницы отдельного товара модуля Market (карточка товара).
	*   Открывается по прямому URL: с числовым ID (id=) или алиасом (al=).
	*   Отображает:
	*     - хлебные крошки, заголовок, краткое и полное описание товара;
	*     - изображения товара (плагин Attacher), вложения на скачивание;
	*     - цену (с учётом marketcurrencyswitcher / market_currency);
	*     - статус, счётчик просмотров, артикул (PCOD);
	*     - кнопки действий владельца/админа (edit, clone, delete, unvalidate, add, i18n);
	*     - кнопку «В корзину» (payordersmarket) и инфо о заказах;
	*     - многостраничный текст с вкладками ([newpage] и [title]...[/title]);
	*     - блок продавца (аватар, контакты, доп. поля xtradbrowusers);
	*     - доп. поля товара (xtradbrowmarket), отзывы (marketreviews);
	*     - связанные товары/рекомендации по ID (cot_market_get_by_id_owner_tpl);
	*     - сайдбар с рекламными блоками плагинов (featured*, getlastposts и др.).
	*
	*   ПРИМЕР https://abuyfile.com/ru/market/cotonti/plugs/marketpro
	*
	* Основные параметры URL:
	*   c=<код категории>       — категория товара (обязателен для ЧПУ);
	*   id=<числовой ID>        — идентификатор товара (если нет алиаса);
	*   al=<алиас>              — алиас товара (ЧПУ);
	*   pg=<номер>              — номер вкладки многостраничного текста (с 0).
	*
	* Основные теги шаблона:
	*   Общие:
	*     MARKET_BREADCRUMBS_ITEM             — хлебные крошки товара (главная + market + категория + товар);
	*     MARKET_ID                           — ID товара;
	*     MARKET_TITLE                        — название товара;
	*     MARKET_DESCRIPTION                  — краткое описание;
	*     MARKET_TEXT                         — полный текст (или текущей вкладки);
	*     MARKET_BUY_DESCRIPTION              — описание с подстановкой названия (для i18n);
	*     MARKET_CAT                          — код категории товара;
	*     MARKET_PCOD                         — артикул;
	*     MARKET_HITS                         — счётчик просмотров;
	*     MARKET_LOCAL_STATUS / MARKET_STATE  — статус товара (публичный/локальный);
	*     MARKET_CREATED / MARKET_UPDATED     — даты публикации и обновления;
	*     MARKET_COSTDFLT / MARKET_COST_RAW   — цена (форматированная / «сырая» для JS-конвертера);
	*     MARKET_IS_PREVIEW + PREVIEW_*       — режим предпросмотра (при редактировании).
	*
	*   Владелец:
	*     MARKET_OWNER                        — HTML-ссылка на профиль владельца;
	*     MARKET_OWNER_*                      — доп. теги пользователя (cot_generate_usertags);
	*     MARKET_OWNER_AVATAR_SRC             — аватар (плагин userimages);
	*     MARKET_OWNER_ONLINE                 — онлайн-статус (плагин whosonline);
	*     MARKET_OWNER_LASTLOG                — дата последнего входа;
	*     MARKET_OWNER_XTRA_*                 — доп. поля владельца (xtradbrowusers);
	*     MARKET_OWNER_VENDOR_URL / _LINK     — ссылка на витрину продавца и её HTML-обёртка.
	*
	*   Админ-панель (BEGIN: MARKET_ADMIN, видна владельцу и админу):
	*     MARKET_ADMIN_EDIT_URL               — редактировать;
	*     MARKET_ADMIN_CLONE_URL              — клонировать;
	*     MARKET_ADMIN_DELETE_URL             — удалить;
	*     MARKET_ADMIN_UNVALIDATE_URL         — снять с публикации;
	*     MARKET_I18N4MARKETPRO_*             — правки/перевод/удаление переводов (i18n4marketpro).
	*
	*   Многостраничный текст (BEGIN: MARKET_MULTI):
	*     MARKET_MULTI_TABTITLES              — список заголовков вкладок (ссылки);
	*     MARKET_MULTI_TABNAV                 — пагинация вкладок;
	*     MARKET_MULTI_CURTAB / _MAXTAB       — текущая вкладка и всего вкладок;
	*     MARKET_TEXT                         — текст текущей вкладки.
	*
	*   Доп. поля товара (BEGIN внутри, xtradbrowmarket):
	*     MARKET_XTRA_*[_TITLE|_VALUE]        — все поля из cot_extrafields для market;
	*     MARKET_XTRA_011_GITHUB_RC           — ссылка GitHub-релиза;
	*     MARKET_XTRA_010_FORUM_LINK          — тема на форуме;
	*     MARKET_XTRA_012_YOUTUBE_ID          — YouTube-видео (Fancybox).
	*
	*   Multicat (BEGIN: MARKET_MULTICATS_LIST / MARKET_MULTICATS_ROW, плагин multicatmarket):
	*     MARKET_MULTICATS_ROW_URL / _TITLE   — ссылки на другие категории товара.
	*
	*   Заказы / корзина (payordersmarket):
	*     MARKET_ORDER_IN_CART                — флаг «в корзине»;
	*     MARKET_ORDER_ID / _URL / _LOCALSTATUS — текущий заказ;
	*     MARKET_ORDER_DOWNLOAD / _DOWNLOAD_COUNT — скачивание и счётчик;
	*     MARKET_ORDER_FILE_NAME / _LOCALSTATUS — имя файла и его статус;
	*     MARKET_ORDER_ORDER_LAST_ID / _URL   — последний заказ пользователя.
	*
	*   Прочее:
	*     MARKET_CHECK_29_ID / _URL / _TITLE  — вывод товара по ID через cot_market_get_by_id_*;
	*     MARKET_REVIEWS*                     — блок отзывов и агрегаты (marketreviews);
	*     MARKET_FILTER_PARAMS                — параметры товара (marketprofilter);
	*     SEOMARKETPRO_*                      — SEO-разметка (seomarketpro);
	*     TGM4MARKET_DISCUSSION               — обсуждение из Telegram (tgm4market);
	*     FEATUREDPRO_ARTICLES_PAGES / FEATURED_PRODUCTS_PAGES — реклама (featured*);
	*     RECOMMENDED_FR_TOPIC_MARKET_TOPICS  — рекомендуемые темы форума (featuredtopicsmarket);
	*     TPL_PATH                            — путь к файлу шаблона (только админ).
	*
	* Используемые плагины (опционально):
	*   attacher              — изображения и файлы товара (att_count / att_display / att_downloads);
	*   marketreviews         — отзывы и рейтинг;
	*   marketcurrencyswitcher— пересчёт цены в валюте пользователя;
	*   marketprofilter       — вывод параметров фильтра;
	*   xtradbrowmarket       — доп. поля товара;
	*   xtradbrowusers        — доп. поля владельца;
	*   userimages            — аватар владельца;
	*   whosonline            — онлайн-статус владельца;
	*   multicatmarket        — один товар в нескольких категориях;
	*   payordersmarket       — корзина, заказы, файлы;
	*   i18n4marketpro        — переводы товара и админ-ссылки переводов;
	*   seomarketpro          — SEO-блок (время чтения, владелец);
	*   tgm4market            — обсуждение товара из Telegram-канала;
	*   featuredpagesmarket   — рекламный блок «статьи»;
	*   featuredproducts      — рекламный блок «товары»;
	*   featuredtopicsmarket  — рекламный блок «темы форума»;
	*   getlastposts          — последние сообщения форума в сайдбаре.
	*
	* Хуки (в market.main.php):
	*   market.first             — в начале, до загрузки товара;
	*   market.main              — после установки основных переменных и шаблона;
	*   market.tags              — перед финальным парсингом шаблона.
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



<!-- BEGIN: MAIN -->
<div class="border-bottom border-secondary py-3 px-3">
	<nav aria-label="breadcrumb">
		<div class="ps-container-breadcrumb">
			<ol class="breadcrumb d-flex mb-0">
				{MARKET_BREADCRUMBS_ITEM}
			</ol>
		</div>
	</nav>
</div>

<div class="container-fluid px-3 px-lg-5 py-5">
		
		<!-- BEGIN: MARKET_ADMIN -->
		<!-- IF {PHP.usr.isadmin} OR {PHP.usr.id} == {MARKET_OWNER_ID} -->				
		<nav class="row row-cols-auto g-2 mb-3">
			<div class="col">
				<a href="{MARKET_ADMIN_EDIT_URL}" type="button" class="btn btn-primary p-3" title="{PHP.L.market_edit_product}"
				title="{PHP.L.market_edit_product}"
				data-bs-toggle="tooltip">
					<i class="fa-solid fa-edit  fa-xl"></i>
				</a>
			</div>
			<div class="col">
				<a href="{MARKET_ADMIN_CLONE_URL}" type="button" class="btn btn-primary p-3" title="{PHP.L.market_clone}"
				title="{PHP.L.market_clone}"
				data-bs-toggle="tooltip">
					<i class="fa-solid fa-clone fa-xl"></i>
				</a>
			</div>
			
			<div class="col">
				<a href="{MARKET_ADMIN_DELETE_URL}" type="button" class="btn btn-primary p-3" title="{PHP.L.Delete}"
				title="{PHP.L.Delete}"
				data-bs-toggle="tooltip">
					<i class="fa-solid fa-trash-can fa-xl"></i>
				</a>
			</div>
			<div class="col">
				<a href="{MARKET_ADMIN_UNVALIDATE_URL}" type="button" class="btn btn-primary p-3" title="{PHP.L.Putinvalidationqueue}"
				title="{PHP.L.Putinvalidationqueue}"
				data-bs-toggle="tooltip">
					<i class="fa-solid fa-thumbtack-slash fa-xl"></i>
				</a>
			</div>
			<div class="col">
				<a href="{PHP|cot_url('market', 'm=add', '&c={MARKET_CAT}')}" type="button" class="btn btn-primary p-3" title="{PHP.L.market_goto_add_new_item_title}"
				title="{PHP.L.market_goto_add_new_item_title}"
				data-bs-toggle="tooltip">
					<i class="fa fa-plus fa-lg fa-xl"></i>
				</a>
			</div>	
			<!-- IF {PHP|cot_plugin_active('i18n4marketpro')} -->
			<!-- IF {MARKET_I18N4MARKETPRO_ADMIN_EDIT_URL} -->
			<div class="col">
				<a href="{MARKET_I18N4MARKETPRO_ADMIN_EDIT_URL}" type="button" class="btn btn-warning p-3" title="{PHP.L.i18n4marketpro_editing}"
				title="{PHP.L.i18n4marketpro_editing}"
				data-bs-toggle="tooltip">
					<i class="fa-solid fa-user-pen fa-xl"></i>
				</a>
			</div>
			<!-- ENDIF -->
			<!-- IF {MARKET_I18N4MARKETPRO_TRANSLATE_URL} -->
			<div class="col">
				<a href="{MARKET_I18N4MARKETPRO_TRANSLATE_URL}" type="button" class="btn btn-success p-3" title="{PHP.L.i18n4marketpro_translate}"
				title="{PHP.L.i18n4marketpro_translate}"
				data-bs-toggle="tooltip">
					<i class="fa-solid fa-language fa-xl"></i>
				</a>
			</div>
			<!-- ENDIF -->
			<!-- IF {MARKET_I18N4MARKETPRO_ADMIN_DELETE_URL} -->
			<div class="col">
				<a href="{MARKET_I18N4MARKETPRO_ADMIN_DELETE_URL}" type="button" class="btn btn-danger p-3" title="{PHP.L.i18n4marketpro_delete}"
				title="{PHP.L.i18n4marketpro_delete}"
				data-bs-toggle="tooltip">
					<i class="fa-regular fa-trash-can fa-xl"></i>
				</a>
			</div>
			<!-- ENDIF -->
			<!-- ENDIF -->
		</nav>
		<!-- ENDIF -->
		
		<!-- END: MARKET_ADMIN -->
		
		<!-- IF {MARKET_IS_PREVIEW} -->
		<div class="alert alert-warning">
			<p>{PHP.L.market_preview_notice}</p>
			<div class="d-flex gap-2">
				<a href="{MARKET_PREVIEW_SAVE_URL}" class="btn btn-success">{PHP.L.market_publish}</a>
				<a href="{MARKET_PREVIEW_EDIT_URL}" class="btn btn-secondary">{PHP.L.market_edit}</a>
			</div>
		</div>
		<!-- ENDIF -->
		
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"} 
		
		<div class="row align-items-center mb-3">
			
			<div class="col-12 col-lg-9">
				<div class="gradient-border p-4">
					<h1 class="h3 mb-3">{MARKET_TITLE}</h1>
					<!-- IF {MARKET_DESCRIPTION} -->
					<p class="fs-5 fw-light">{MARKET_DESCRIPTION}</p>
					<!-- ENDIF -->
				</div>
			</div>
			
			<div class="col-12 col-lg-3 mt-3 mt-md-0">
				
				<!-- IF {PHP|cot_plugin_active('payordersmarket')} AND {PHP.usr.id} --> <!-- начало плагина 'payordersmarket' -->
				<div class="mb-3">
					<!-- IF !{MARKET_ORDER_IN_CART} -->
					<a href="javascript:void(0)" class="btn btn-success btn-lg w-100 add-to-cart" data-id="{MARKET_ID}">
						<span class="text-warning me-2"><i class="fa-solid fa-cart-plus"></i></span>
						<span class="fw-bold">{PHP.L.payordersmarket_add_to_cart}</span>
					</a>
					<span class="cart-added-msg text-success ms-2" style="display:none;">{PHP.L.payordersmarket_added_to_cart}</span>
					<!-- ELSE -->
					<span class="btn btn-danger btn-lg w-100">{PHP.L.payordersmarket_in_cart}</span>
					<!-- ENDIF -->
				</div>
				<!-- ENDIF -->
				
				<!-- IF {PHP|cot_plugin_active('payordersmarket')} AND {PHP|cot_auth('plug', 'payordersmarket', 'R')} AND {PHP.usr.id} == 0 -->
				<div class="mb-3">
					<a class="btn btn-lg btn-warning w-100" data-bs-toggle="modal" data-bs-target="#authModal">
						<span class="text-dark me-2"><i class="fa-solid fa-cart-plus"></i></span>
						<span class="fw-bold">{PHP.L.payordersmarket_add_to_cart}</span>
					</a>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {PHP.usr.isadmin} OR {PHP.usr.id} == {MARKET_OWNER_ID} -->	
				<!-- IF {MARKET_ORDER_FILE_LOCALSTATUS} -->
				<p class="text-truncate"><span class="badge bg-info small">{MARKET_ORDER_FILE_LOCALSTATUS}</span></p>
				<!-- ENDIF -->
				<!-- ENDIF -->
				
				<!-- IF {MARKET_ORDER_FILE_NAME} -->
				<p><span class="badge bg-info small text-truncate text-black">{MARKET_ORDER_FILE_NAME}</span></p>
				<p>{PHP.L.payordersmarket_product_file_count_of_downloads} <span class="badge bg-info text-black">{MARKET_ORDER_DOWNLOAD_COUNT}</span></p>
				<!-- ENDIF -->	
				
				<!-- IF {MARKET_STATE} == 0 -->
				<p>&nbsp;</p>
				<!-- IF {PHP|cot_plugin_active('payordersmarket')} AND {PHP|cot_auth('plug', 'payordersmarket', 'R')} -->
				
				<!-- IF {MARKET_ORDER_ID} -->
				
				<div class="alert alert-info">
					<h3 class="h5 mb-2"><a href="{MARKET_ORDER_URL}"><i class="fa-solid fa-paperclip fa-lg"></i> {PHP.L.payordersmarket_product_order_num} {MARKET_ORDER_ID}</a></h3>
					<p> <span class="badge bg-warning text-black">{MARKET_ORDER_LOCALSTATUS}</span></p>
					
					<!-- IF {MARKET_ORDER_DOWNLOAD} -->
					<p><a class="btn btn-success" href="{MARKET_ORDER_DOWNLOAD}">{PHP.L.payordersmarket_product_file_download}</a></p>
					
					<!-- ENDIF -->
				</div>
				<!-- ENDIF -->
				<!-- Если есть последний заказ для товара -->
				<!-- Проверяем наличие тега ORDER_ORDER_LAST_ID -->
				<!-- IF {MARKET_ORDER_ORDER_LAST_ID} -->
				<div class="alert alert-info">
					<h3 class="h5 mb-2">
						<a href="{MARKET_ORDER_ORDER_LAST_URL}">
							<i class="fa-solid fa-paperclip fa-lg"></i> 
							{PHP.L.payordersmarket_product_last_order_num} {MARKET_ORDER_ORDER_LAST_ID}
						</a>
					</h3>
				</div>
				<!-- ENDIF -->
				
				<!-- ENDIF -->
				<!-- ENDIF --> <!-- конец плагина 'payordersmarket' -->
				
				<!-- IF {PHP|cot_plugin_active('marketcurrencyswitcher')} -->				
				<!-- IF {MARKET_COSTDFLT} > 0 -->
				<p class="fw-bold">
					<span id="price-label">{PHP.L.market_price}</span>
					<span class="ms-2 text-success market-price" data-base-price="{MARKET_COST_RAW}">
						{MARKET_COSTDFLT} {PHP.cfg.payments.valuta}
					</span>
				</p>
				<!-- ENDIF -->
				<!-- ELSE -->
				<!-- IF {MARKET_COSTDFLT} > 0 -->
				<p class="mfw-bold">{PHP.L.market_price} 
					<span class="ms-2 text-success">
						{MARKET_COSTDFLT} 				  
						<!-- IF {PHP.cfg.payments.valuta} -->
						{PHP.cfg.payments.valuta}
						<!-- ELSE -->
						{PHP.cfg.market.market_currency}
						<!-- ENDIF -->
					</span>
				</p>
				<!-- ENDIF -->
				<!-- ENDIF -->
			</div>
		</div>
		
		<div class="row text-center mb-3 gy-3">
			
			<!-- IF {MARKET_LOCAL_STATUS} -->
			<div class="col-12 col-md-6 col-lg-3 d-flex align-items-center justify-content-center">
				<p class="mb-0">
					<strong>{PHP.L.Status}:</strong>
					<span class="badge bg-warning text-black">{MARKET_LOCAL_STATUS}</span>
				</p>
			</div>
			<!-- ENDIF -->
			
			<!-- IF {MARKET_HITS} -->
			<div class="col-12 col-md-6 col-lg-3 d-flex align-items-center justify-content-center">
				<span class="badge bg-primary rounded-pill px-3 py-2 fs-6 shadow"
				title="{PHP.L.Views}"
				data-bs-toggle="tooltip">
					<i class="fa-solid fa-thumbs-up me-2 "></i>
					{MARKET_HITS}
				</span>
			</div>
			<!-- ENDIF -->
			
			<!-- IF {MARKET_PCOD} -->
			<div class="col-12 col-md-6 col-lg-3 d-flex align-items-center justify-content-center">
				<p class="mb-0">{PHP.L.Code}
					<span class="badge bg-warning text-black">{MARKET_PCOD}</span>
				</p>
			</div>
			<!-- ENDIF -->
			
			<!-- IF {MARKET_XTRA_011_GITHUB_RC} -->
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Плагин добавляет экстраполя для модуля «Market PRO v.5» в собственную таблицу базы данных
					https://github.com/webitproff/xtradbrowmarket-cotonti
					-->
					<!-- ENDIF -->
			<div class="col-12 col-md-6 col-lg-3 d-flex align-items-center justify-content-center"
			data-bs-toggle="tooltip" data-bs-html="true"
			data-bs-title="{PHP.L.xtradbrowmarket_github_rc_tooltip}" title="{PHP.L.xtradbrowmarket_github_rc_tooltip}">
				<a target="_blank" rel="nofollow noreferrer noopener"
				href="{MARKET_XTRA_011_GITHUB_RC}"
				class="btn btn-common btn-github btn-lg w-100">
					<i class="fa-brands fa-github mx-2 fa-2xl"></i>
					<span>{MARKET_XTRA_011_GITHUB_RC_TITLE}</span>
				</a>
			</div>
			<!-- ENDIF -->
		</div>
		
		<div class="row pt-5">
			<div class="col-12 col-md-8 mx-auto pb-5">
				<div class="mb-4">
					<!-- IF {PHP|cot_plugin_active('attacher')} -->
						<!-- IF {MARKET_ID|att_count('market', $this, '', 'images')} > 0 -->
						
							<!-- IF {PHP.usr.isadmin} --> 
							<!-- 
							Плагин Attacher (прикрепляем файлы и картинки)
							https://abuyfile.com/ru/market/cotonti/plugs/attacher-files-new
							https://github.com/webitproff/cot-Attacher-Roffun
							-->
							<!-- ENDIF -->
							
							<!-- первая картинка выводится своим шаблоном -->
							<!-- остальные "мини"-картинки берем другим шаблоном -->
							
							<div class="mb-3">
								{MARKET_ID|att_display('market', $this, '', 'attacher.display.grid.first', 'images', '1')}
							</div>
							<!-- IF {MARKET_ID|att_count('market', $this, '', 'images')} > 1 -->
							
							<div class="mb-3">
								{MARKET_ID|att_display('market', $this, '', 'attacher.display.grid.other', 'images', '')}
							</div>
							<!-- ENDIF -->
							
						<!-- ELSE -->
							<div class="position-relative overflow-hidden rounded-5 shadow-bottom" style="aspect-ratio: 2 / 1; background-image: url('{PHP.R.page_default_image}'); background-size: cover; background-position: center;"></div>
						<!-- ENDIF -->
					<!-- ELSE -->
					<div class="position-relative overflow-hidden rounded-5 shadow-bottom" style="aspect-ratio: 2 / 1; background-image: url('{PHP.R.page_default_image}'); background-size: cover; background-position: center;"></div>
					<!-- ENDIF -->
				</div>
				
				<!-- IF {PHP|cot_plugin_active('marketprofilter')} AND {PARAM_VALUE} -->
				
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Плагин Фильтр товаров "Market PRO Filter"
					https://abuyfile.com/ru/market/cotonti/plugs/market-pro-filter
					https://github.com/webitproff/marketprofilter-cotonti
					-->
					<!-- ENDIF -->
					
				<div class="gradient-border gradient-border-about-figure p-4 text-dark">
					<h3>{PHP.L.marketfilter_paramsItem}</h3>
					<dl class="row">
						<!-- BEGIN: MARKET_FILTER_PARAMS -->
						<dt class="col-sm-4">{PARAM_TITLE}</dt>
						<dd class="col-sm-8">{PARAM_VALUE}</dd>
						<!-- END: MARKET_FILTER_PARAMS -->
					</dl>
				</div>
				<!-- ENDIF -->	
				
				<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} --> <!-- начало экстраполя товара через плагин 'xtradbrowmarket' -->
				
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Плагин добавляет экстраполя для модуля «Market PRO v.5» в собственную таблицу базы данных
					https://github.com/webitproff/xtradbrowmarket-cotonti
					-->
					<!-- ENDIF -->
					
				<div class="row g-3 my-5 text-center">
					
					<!-- IF {MARKET_XTRA_012_YOUTUBE_ID} -->
					<div class="col-12 col-md-6 col-xl-4 mb-3" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="{PHP.L.page_youtube_id_tooltip}" title="{PHP.L.page_youtube_id_tooltip}">
						<a 
						data-fancybox="video"
						data-type="iframe"
						data-src="https://www.youtube.com/watch?v={MARKET_XTRA_012_YOUTUBE_ID}"
						href="javascript:;"
						class="btn btn-common btn-video btn-lg w-100"
						>
							<i class="fa-brands fa-square-youtube fa-2xl mx-2"></i>
							<span>{MARKET_XTRA_012_YOUTUBE_ID_TITLE}</span>
						</a>
					</div>
					<!-- ENDIF -->
					
					<!-- IF {MARKET_XTRA_010_FORUM_LINK} -->
					<div class="col-12 col-md-6 col-xl-4 mb-3" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="{PHP.L.xtradbrowmarket_forum_link_tooltip}" title="{PHP.L.xtradbrowmarket_forum_link_tooltip}">
						<a 
						href="{MARKET_XTRA_010_FORUM_LINK}"
						class="btn btn-common btn-forum btn-lg w-100"
						>
							<i class="fa-solid fa-person-circle-question fa-2xl mx-2"></i>
							<span>{MARKET_XTRA_010_FORUM_LINK_TITLE}</span>
						</a>
					</div>
					
					<!-- ENDIF -->
					
				</div>
				<!-- ENDIF --> <!-- нконец экстраполя товара через плагин 'xtradbrowmarket' -->
				
				<!-- BEGIN: MARKET_MULTI -->
				<div class="card mb-4">
					<div class="card-header">
						<h2 class="h5 mb-0">{PHP.L.Summary}</h2>
					</div>
					<div class="card-body"> 
						{MARKET_MULTI_TABTITLES} 
						<nav class="my-4" aria-label="Article pagination">
							<ul class="pagination justify-content-center pagination-md">
								{MARKET_MULTI_TABNAV}
							</ul>
						</nav>
					</div>
				</div>
				<!-- END: MARKET_MULTI -->				
				<div class="card mb-4">
					<div class="card-body message-body">  
						<div class="mb-3" id="protected-block">
							<div class="market-text-body">
								{MARKET_TEXT}
								
								<!-- IF {MARKET_BUY_DESCRIPTION} -->
								{MARKET_BUY_DESCRIPTION}
								<!-- ENDIF -->
							
							<!-- IF {MARKET_CHECK_29_ID} == '29' AND {MARKET_ID|cot_market_not_in($this,29,71)} -->
								
									<!-- IF {PHP.usr.isadmin} --> 
									<!-- 
									Выводим товар где угодно с любым шаблоном по ID товара
									именно такой способ получения товара реализован в:
									в market.main.php в конце файла вызов функции cot_market_get_by_id_as_recommend($t);
									https://abuyfile.com/ru/usersblog/vyvodim-tovar-gde-ugodno-s-lyubym-shablonom-po-id-tovara
									-->
									<!-- ENDIF -->
								
								<div class="alert alert-info mt-5">
									{PHP.L.market_get_by_id_as_recommend}: <strong><a href="{MARKET_CHECK_29_URL}" title="{MARKET_CHECK_29_TITLE}">{MARKET_CHECK_29_TITLE}. </a></strong>
								</div>
							<!-- ENDIF -->
							
							</div>
							<div class="d-flex align-items-center my-4">
								<hr class="flex-grow-1">
								<button type="button" class="btn btn-primary btn-lg read-more-btn d-none" id="readMoreBtn"
								data-read-more="{PHP.L.market_read_more}"
								data-collapse="{PHP.L.market_collapse}">
									{PHP.L.market_read_more}
								</button>
								<hr class="flex-grow-1">
							</div>
						</div>
						
						<!-- IF {PHP|cot_plugin_active('marketreviews')} -->
						<div><span class="small">{PHP.L.marketreviews_pageRatingValue}:</span> <span class="review-stars">{MARKET_REVIEWS_AVG_STARS_HTML}</span></div>
						<div><span class="small">{PHP.L.marketreviews_pageCountStarsTotalValue}:</span> {MARKET_REVIEWS_STARS_SUMM}</div>
						<div><span class="small">{PHP.L.marketreviews_pageCountReviewsTotalValue}:</span> {MARKET_REVIEWS_TOTAL_COUNT}</div>
						<div><span class="small">{PHP.L.marketreviews_pageAverageRatingValue}:</span> {MARKET_REVIEWS_AVG_STARS}</div>
						<!-- ENDIF -->
						
						<!-- IF {PHP|cot_plugin_active('seomarketpro')} -->
						<div><span class="text-info">{SEOMARKETPRO_PAGE_READ_TIME}</span></div>
						<div>{PHP.L.Owner}:  <a class="link-info" href="{SEOMARKETPRO_MARKET_OWNER_URL}">{SEOMARKETPRO_PAGE_OWNER}</a></div>
						<!-- ENDIF -->
						
					</div>	
				</div>

				<!-- IF {PHP|cot_market_get_by_id_owner_tpl('getbyid.29', '29')} AND {MARKET_ID|cot_market_not_in($this,29)} -->
				
									<!-- IF {PHP.usr.isadmin} --> 
									<!-- 
									Выводим товар где угодно с любым шаблоном по ID товара
									https://abuyfile.com/ru/usersblog/vyvodim-tovar-gde-ugodno-s-lyubym-shablonom-po-id-tovara
									-->
									<!-- ENDIF -->
									
				<div class="col-12 px-3 mb-5">
					{PHP|cot_market_get_by_id_owner_tpl('getbyid.29', '29')}
				</div>
				<!-- ENDIF -->
				
				<!-- IF {PHP|cot_market_get_by_id_owner_tpl('getbyid.marketpro', '31')} AND {MARKET_ID|cot_market_not_in($this,31)} -->
				
									<!-- IF {PHP.usr.isadmin} --> 
									<!-- 
									Выводим товар где угодно с любым шаблоном по ID товара
									https://abuyfile.com/ru/usersblog/vyvodim-tovar-gde-ugodno-s-lyubym-shablonom-po-id-tovara
									-->
									<!-- ENDIF -->
									
				<div class="col-12 px-3 mb-5">
					{PHP|cot_market_get_by_id_owner_tpl('getbyid.marketpro', '31')}
				</div>
				<!-- ENDIF -->	

				<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} --> <!-- начало экстраполя товара через плагин 'xtradbrowmarket' -->

					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Плагин добавляет экстраполя для модуля «Market PRO v.5» в собственную таблицу базы данных
					https://github.com/webitproff/xtradbrowmarket-cotonti
					-->
					<!-- ENDIF -->

				<!-- IF {MARKET_XTRA_EVENT_NAME} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_EVENT_NAME_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_EVENT_NAME}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_EVENT_DESCRIPTION} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_EVENT_DESCRIPTION_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_EVENT_DESCRIPTION}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_EVENT_START_VALUE} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_EVENT_START_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_EVENT_START}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_EVENT_TICKETPRICE} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_EVENT_TICKETPRICE_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_EVENT_TICKETPRICE}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_EVENT_SESON} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_EVENT_SESON_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_EVENT_SESON}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_INT} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_INT_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_INT}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_DOUBLE} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_DOUBLE_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_DOUBLE}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_SELECT} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_SELECT_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_SELECT}</div>
					</div>
				</div>
				<!-- ENDIF -->
				<!-- IF {MARKET_XTRA_DEMO_CHECKBOX} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_CHECKBOX_TITLE}</div>
						<div class="contact-value"><span class="badge bg-success">{PHP.L.Yes}</span></div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_RADIO} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_RADIO_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_RADIO}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_DATETIME_VALUE} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_DATETIME_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_DATETIME}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_FILE} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_FILE_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_FILE}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_COUNTRY} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_COUNTRY_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_COUNTRY} {MARKET_XTRA_DEMO_COUNTRY_NAME}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_RANGE} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_RANGE_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_RANGE}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- IF {MARKET_XTRA_DEMO_CHECKLISTBOX} -->
				<div class="d-flex mb-3">
					<div class="contact-icon about me-3"><i class="fa-solid fa-circle-info fa-xl"></i></div>
					<div>
						<div class="contact-label">{MARKET_XTRA_DEMO_CHECKLISTBOX_TITLE}</div>
						<div class="contact-value">{MARKET_XTRA_DEMO_CHECKLISTBOX}</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<!-- ENDIF --> <!-- конец экстраполя товара через плагин 'xtradbrowmarket' -->
				
				<!-- IF {PHP|cot_plugin_active('attacher')} -->
				<!-- IF {MARKET_ID|att_count('market', $this, '', 'files')} > 0 -->
				<div class="mb-4" data-att-downloads="download">
					<h5>{PHP.L.att_attachments} {PHP.L.att_downloads}</h5> 
					{MARKET_ID|att_downloads('market', $this)}
				</div>
				<!-- ENDIF -->
				<!-- ENDIF -->
				
				<!-- IF {PHP|cot_plugin_active('marketreviews')} -->
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Плагин "Market Reviews" - Отзывы к товарам с ответами
					https://abuyfile.com/ru/market/cotonti/plugs/marketreviews 
					-->
					<!-- ENDIF -->
				{MARKET_REVIEWS} 
				<hr />
				<!-- ENDIF --> 
				
				
			</div>
			
			<div class="col-12 col-md-4">
			
			<!-- IF {PHP|cot_plugin_active('multicatmarket')} -->
				<!-- IF {PHP.usr.isadmin} --> 
				<!-- 
				Один товар одновременно в нескольких категориях при просмотре списков товаров
				https://github.com/webitproff/cotonti-multicatmarket
				https://abuyfile.com/ru/market/cotonti/plugs/multicatmarket
				-->
				<!-- ENDIF -->
			
			<!-- BEGIN: MARKET_MULTICATS_LIST -->
			<div class="card mb-4">
				<div class="card-header">
					<h3 class="h6 mb-0">{PHP.L.multicatmarket_market_cats_links}:</h3>
					<small>{PHP.L.multicatmarket_market_cats_links_hint}</small>
				</div>
				<div class="card-body">
					<ul class="list-group list-group-striped list-group-flush">
						<!-- BEGIN: MARKET_MULTICATS_ROW -->
						<li class="list-group-item"><a href="{MARKET_MULTICATS_ROW_URL}">{MARKET_MULTICATS_ROW_TITLE}</a></li>
						<!-- END: MARKET_MULTICATS_ROW -->
					</ul>
				</div>
			</div>
			<!-- END: MARKET_MULTICATS_LIST -->	
			
			<!-- ENDIF --> 
				<div class="card mb-4">
					<div class="h5 card-header">{PHP.L.market_seller}</div>
					<div class="card-body message-body">
						<div class="row justify-content-between">
							<div class="col-md-auto text-center text-md-start">
								
								<!-- IF {PHP|cot_plugin_active('userimages')} -->
								<!-- IF {MARKET_OWNER_AVATAR_SRC} -->
								<img src="{MARKET_OWNER_AVATAR_SRC}" alt="{MARKET_OWNER_NICKNAME}" class="rounded-circle" width="50" height="50">
								<!-- ELSE -->
								<img src="{PHP.R.userimg_default_avatar}" alt="{MARKET_OWNER_NICKNAME}" class="rounded-circle" width="50" height="50">
								<!-- ENDIF -->
								<!-- ENDIF -->
								
								<!-- IF {PHP|cot_plugin_active('whosonline')} -->
								<!-- IF {MARKET_OWNER_ONLINE} -->
								<p class="my-2">
									<span class="badge text-bg-success">{PHP.L.Online}</span>
								</p>
								<!-- ELSE -->
								<p class="my-2">
									<span class="badge text-bg-secondary">{PHP.L.Offline}</span>
								</p>
								<!-- ENDIF -->
								<!-- ENDIF -->
							</div>
							<div class="col-md-auto text-center text-md-end">
								<h4 class="h5 mb-0">
									{MARKET_OWNER}
								</h4>
								<p class="small">{PHP.L.Lastlogged}: {MARKET_OWNER_LASTLOG}</p>
							</div>
						</div>
						
						
						<!-- IF {PHP|cot_plugin_active('xtradbrowusers')} --> <!-- начало экстраполя пользователей -->
						
						<!-- IF {MARKET_OWNER_XTRA_X020_ABOUT_VENDOR_TEXT} -->
						<div class="d-flex mb-3">
							<div class="contact-icon about me-3">
								<i class="fa-solid fa-circle-info fa-xl"></i>
							</div>
							<div>
								<div class="contact-label">{MARKET_OWNER_XTRA_X020_ABOUT_VENDOR_TEXT_TITLE}</div>
								<div class="contact-value">{MARKET_OWNER_XTRA_X020_ABOUT_VENDOR_TEXT}</div>
							</div>
						</div>
						<!-- ENDIF -->
						
						<!-- IF {MARKET_OWNER_XTRA_X021_GITHUB_VALUE} --> 
						<div class="d-flex align-items-center mb-3">
							<div class="contact-icon github me-3">
								<i class="fa-brands fa-square-github fa-xl"></i>
							</div>
							<div>
								<div class="contact-label">{MARKET_OWNER_XTRA_X021_GITHUB_TITLE}</div>
								<a href="https://github.com/{MARKET_OWNER_XTRA_X021_GITHUB}" target="_blank" rel="noopener noreferrer" class="contact-link">
									{PHP.L.xtradbrowusers_custom_github_details}
								</a>
							</div>
						</div>
						<!-- ENDIF -->	
						
						<!-- IF {MARKET_OWNER_XTRA_X010_PHONE_VENDOR_ORDERS_VALUE} -->
						<div class="d-flex align-items-center mb-3">
							<div class="contact-icon phone me-3">
								<i class="fa-solid fa-phone fa-xl"></i>
							</div>
							<div>
								<div class="contact-label">{MARKET_OWNER_XTRA_X010_PHONE_VENDOR_ORDERS_TITLE}</div>
								<div class="d-flex align-items-center">
									<span id="phone-{MARKET_OWNER_ID}" class="contact-value fs-3" style="letter-spacing:2px"></span>
									<button type="button" class="btn btn-outline-primary btn-sm ms-2"
									id="show-phone-btn-{MARKET_OWNER_ID}"
									data-phone="{MARKET_OWNER_XTRA_X010_PHONE_VENDOR_ORDERS_VALUE}"
									onclick="document.getElementById('phone-{MARKET_OWNER_ID}').textContent = this.dataset.phone; this.style.display='none';">
										<i class="fa-solid fa-eye me-1"></i> {PHP.L.xtradbrowusers_details_tpl_show_hidden_content}
									</button>
								</div>
							</div>
						</div>
						<!-- ENDIF -->
						
						<!-- IF {MARKET_OWNER_XTRA_X011_TG_VENDOR_ORDERS_VALUE} -->
						<div class="d-flex align-items-center mb-3">
							<div class="contact-icon telegram me-3">
								<i class="fa-brands fa-telegram fa-xl"></i>
							</div>
							<div>
								<div class="contact-label">{MARKET_OWNER_XTRA_X011_TG_VENDOR_ORDERS_TITLE}</div>
								<a href="https://t.me/{MARKET_OWNER_XTRA_X011_TG_VENDOR_ORDERS_VALUE}" target="_blank" rel="noopener noreferrer" class="telegram-link">
									@{MARKET_OWNER_XTRA_X011_TG_VENDOR_ORDERS_VALUE}
								</a>
							</div>
						</div>
						<!-- ENDIF -->
						
						<!-- IF {MARKET_OWNER_XTRA_X012_TG_CHANEL_VENDOR_VALUE} -->
						<div class="d-flex align-items-center mb-3">
							<div class="contact-icon telegram me-3">
								<i class="fa-brands fa-telegram fa-xl"></i>
							</div>
							<div>
								<div class="contact-label">{MARKET_OWNER_XTRA_X012_TG_CHANEL_VENDOR_TITLE}</div>
								<a href="https://t.me/{MARKET_OWNER_XTRA_X012_TG_CHANEL_VENDOR_VALUE}" target="_blank" rel="noopener noreferrer" class="telegram-link">
									@{MARKET_OWNER_XTRA_X012_TG_CHANEL_VENDOR_VALUE}
								</a>
							</div>
						</div>
						<!-- ENDIF -->
						
						<!-- ENDIF -->	<!-- конец экстраполя пользователей cot_plugin_active('xtradbrowusers') -->
						
						<ul class="list-group list-group-flush">
							
							<!-- IF {PHP|cot_module_active('pm')} AND {PHP.usr.id} > 0 AND {PHP.usr.id} != {MARKET_OWNER_ID} -->
							<li class="list-group-item px-0">
								<a href="{PHP.item.user_id|cot_url('pm','m=send&to=$this', '', 1)}"><i class="fa-regular fa-envelope fa-xl me-3"></i> {PHP.L.users_sendpm}</a>
							</li>
							<!-- ENDIF -->
							
							<!-- IF {MARKET_CREATED} -->
							<li class="list-group-item px-0">
								<strong>{PHP.L.market_date_published}</strong> {MARKET_CREATED}
							</li>
							<!-- ENDIF -->
							
							<!-- IF {MARKET_UPDATED} -->
							<li class="list-group-item px-0">
								<strong>{PHP.L.market_latest_update}</strong> {MARKET_UPDATED}
							</li>
							<!-- ENDIF -->
							
						</ul>
						
					</div>
				</div>
				
				
				
				<!-- IF {PHP|cot_plugin_active('tgm4market')} AND {PHP.chat_id} -->
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Публикуем товар из Market PRO в свой телеграм канал и подтягиваем обсуждение
					https://github.com/webitproff/telegram-market-cotonti 
					-->
					<!-- ENDIF -->
				<div class="mb-3">
					<div class="card mb-4">
						<div class="card-header">
							<h4 class="h5 mb-0">{PHP.L.m2t_discussion_title}</h4>
						</div>
						<div class="card-body p-0">
							{TGM4MARKET_DISCUSSION}
						</div>
					</div>
				</div>
				<!-- ENDIF -->
				
				<div class="mb-4">
					<iframe 
					src="https://widget.wptelegram.pro/s/abuyfile?scrollbar=custom&hideRightColumn=1&theme=auto" 
					width="100%" 
					height="600" 
					frameborder="0">
					</iframe>
					<!-- https://comments.app/manage -->
				</div>
				
				<!-- IF {PHP|cot_plugin_active('featuredpagesmarket')} AND {FEATUREDPRO_ARTICLES_TRUE} -->
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Рекомендуемые Статьи в Market PRO 
					https://github.com/webitproff/featured-pages-market-cotonti 
					-->
					<!-- ENDIF -->
				{FEATUREDPRO_ARTICLES_PAGES}
				<!-- ENDIF -->
				
				
				<!-- IF {PHP|cot_plugin_active('featuredproducts')} AND {FEATURED_PRODUCTS_TRUE} -->
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Рекомендуемые Товары в Market PRO 
					https://github.com/webitproff/featuredproducts-cotonti 
					-->
					<!-- ENDIF -->
				{FEATURED_PRODUCTS_PAGES}
				<!-- ENDIF -->		
				

				
				<!-- IF {PHP|cot_plugin_active('getlastposts')} -->
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Выводит последние сообщения форума в любом месте сайта 
					https://github.com/webitproff/getlastposts-cotonti 
					-->
					<!-- ENDIF -->
				{PHP|cot_forums_getLastPosts(10, '', 'getlastposts.sidebar')}			
				<!-- ENDIF -->	
				
			</div>
			<!-- IF {PHP|cot_plugin_active('featuredtopicsmarket')} AND {RECOMMENDED_FR_TOPIC_MARKET_TOPICS_TRUE} -->
					<!-- IF {PHP.usr.isadmin} --> 
					<!-- 
					Рекомендуемые темы (топики) форумов в карточке товара или услуги 
					https://github.com/webitproff/featured-topics-market-cotonti
					-->
					<!-- ENDIF -->
			{RECOMMENDED_FR_TOPIC_MARKET_TOPICS}
			<!-- ENDIF -->
		</div>
		<blockquote>
			<p>{PHP.cfg.market.marketlist_default_title}</p>
			<p>{PHP.cfg.market.marketlist_default_desc}</p>
		</blockquote>
		
</div>

<script>
	// для описания с оглавлением и ссылками на разделы (TOC+закладки)
	document.addEventListener('DOMContentLoaded', function() {
		const path = window.location.pathname.replace(/^\//, ''); // текущий путь без начального слеша
		document.querySelectorAll('a[href^="#"]').forEach(link => {
			link.href = path + link.getAttribute('href');
		});
	});
</script>

<!-- IF {PHP.usr.isadmin} AND {TPL_PATH} --> 
<div class="container-fluid px-3 px-lg-5 py-5">
	<div class="alert alert-info" role="alert">
		{TPL_PATH}
	</div>
</div>
<!-- ENDIF -->
<!-- END: MAIN -->

