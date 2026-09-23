<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.edit.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.edit.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.edit.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.edit.php
	* Назначение:
	*   Шаблон формы редактирования существующего товара модуля Market.
	*   Открывается по URL m=edit&id=<ID>. Отображает форму с текущими
	*   данными товара: категория, название, H1, краткое описание, алиас,
	*   meta-заголовок/описание, парсер, полный текст, цена (базовая +
	*   международная с JS-пересчётом), артикул (PCOD), extrafields товара,
	*   изображения и файлы (Attacher), доп. поля xtradbrowmarket, параметры
	*   фильтра (marketprofilter), рекламные блоки (featured*), публикация
	*   в Telegram (tgm4market), удаление товара. Админ-блок позволяет менять
	*   владельца и счётчик просмотров.
	*
	*   Форма отправляется POST на {MARKETEDIT_FORM_SEND} (m=edit&a=update&id=…).
	*   Обработка и валидация — в market.edit.php.
	*
	*   
	*
	* Основные параметры URL:
	*   m=edit                  — метод модуля (форма редактирования);
	*   id=<числовой ID>        — обязательный ID редактируемого товара;
	*   c=<код категории>       — опционально, контекст категории;
	*   a=update                — действие обновления (POST);
	*   preview=1               — предпросмотр (сохранить как черновик и открыть preview);
	*   delete=1                — удалить товар (с подтверждением).
	*
	* Основные теги шаблона:
	*   Общие:
	*     MARKETEDIT_BREADCRUMBS       — хлебные крошки формы;
	*     MARKETEDIT_FORM_SEND         — URL action формы (m=edit&a=update&id=…);
	*     MARKETEDIT_FORM_ID           — ID редактируемого товара;
	*     MARKETEDIT_FORM_STATE        — числовой статус (0/1/2);
	*     MARKETEDIT_FORM_STATUS       — символьный статус (published/draft/pending);
	*     MARKETEDIT_FORM_LOCAL_STATUS — локализованный статус;
	*     MARKETEDIT_HAS_EXTRAFIELDS   — флаг: есть ли extrafields у cot_market;
	*     TPL_PATH                     — путь к файлу шаблона (только админ).
	*
	*   Владелец:
	*     MARKETEDIT_FORM_OWNER_FULL   — HTML-ссылка на профиль владельца;
	*     MARKETEDIT_FORM_OWNER_URL    — URL профиля владельца;
	*     MARKETEDIT_FORM_OWNER_NAME   — ник владельца (экранированный);
	*     MARKETEDIT_FORM_OWNER_ID     — поле ввода ID владельца (только админ);
	*     MARKETEDIT_FORM_HITS         — поле ввода счётчика просмотров (только админ).
	*
	*   Категория (варианты селектов):
	*     MARKETEDIT_FORM_CAT          — обычный selectbox структуры;
	*     MARKETEDIT_FORM_CAT_SHORT    — selectbox с ограничением по родителю $c;
	*     MARKETEDIT_FORM_CAT_S2       — Select2-версия;
	*     MARKETEDIT_FORM_CAT_SHORT_S2 — Select2 с ограничением по родителю.
	*
	*   Основные поля:
	*     MARKETEDIT_FORM_TITLE        — название товара;
	*     MARKETEDIT_FORM_META_H1      — SEO H1;
	*     MARKETEDIT_FORM_DESCRIPTION  — краткое описание;
	*     MARKETEDIT_FORM_ALIAS        — алиас (ЧПУ);
	*     MARKETEDIT_FORM_META_TITLE   — meta-заголовок;
	*     MARKETEDIT_FORM_META_DESC    — meta-описание;
	*     MARKETEDIT_FORM_PARSER       — выбор парсера текста;
	*     MARKETEDIT_FORM_TEXT         — полный текст (WYSIWYG/редактор);
	*     MARKETEDIT_FORM_COSTDFLT     — цена в базовой валюте;
	*     MARKETEDIT_FORM_COST_USD     — цена в международной валюте (JS-пересчёт);
	*     MARKETEDIT_FORM_PCOD         — артикул / код товара.
	*
	*   Даты:
	*     MARKETEDIT_FORM_DATE         — selectbox даты публикации + timetext;
	*     MARKETEDIT_FORM_DATENOW      — чекбокс «обновить дату»;
	*     MARKETEDIT_FORM_UPDATED      — дата последнего обновления (для информации).
	*
	*   Удаление:
	*     MARKETEDIT_FORM_DELETE       — радиокнопки «Да/Нет» (флаг ritemmarketdelete).
	*
	*   Extrafields (BEGIN: EXTRAFLD):
	*     MARKETEDIT_FORM_EXTRAFLD         — HTML-элемент поля;
	*     MARKETEDIT_FORM_EXTRAFLD_TITLE   — локализованный заголовок;
	*     MARKETEDIT_FORM_EXTRAFLD_CODENAME— машинное имя поля;
	*     MARKETEDIT_FORM_<ИМЯ>[_TITLE]    — прямой вывод конкретного поля.
	*
	*   Доп. поля xtradbrowmarket (BEGIN: XTRA_EXTRAFLD):
	*     MARKETEDIT_FORM_XTRA_EXTRAFLD[_TITLE] — поля плагина xtradbrowmarket.
	*
	*   Multicat (плагин multicatmarket):
	*     MARKET_FORM_MULTICAT[_HINT]  — чекбоксы выбора доп. категорий товара.
	*
	*   Фильтр (marketprofilter, BEGIN: MARKET_FORM_FILTER_PARAM):
	*     FILTER_PARAMS_HEADER             — заголовок блока фильтра;
	*     FILTER_PARAM_TITLE / _INPUT      — название и поле параметра;
	*     FILTER_PARAM_HASHELP / _HELP     — флаг и текст подсказки (модалка).
	*
	*   Рекламные блоки (внутри аккордеона Recommended):
	*     FEATURED_PRODUCTS_EDIT           — плагин featuredproducts;
	*     RECOMMENDED_FR_TOPIC_MARKET_EDIT_TOPIC — плагин featuredtopicsmarket;
	*     FEATUREDPRO_ARTICLES_EDIT        — плагин featuredpagesmarket.
	*
	*   Ссылки и внешние поля (внутри аккордеона YOUTUBE):
	*     MARKETEDIT_FORM_YOUTUBE_ID[_TITLE] — YouTube-видео (xtradbrowmarket);
	*     MARKETEDIT_FORM_FORUM_LINK[_TITLE] — тема форума (xtradbrowmarket).
	*
	*   Глобальные (шаблон темы, из Cotonti):
	*     PHP.usr_can_publish          — true, если админ + marketautovalidate=1
	*                                    (показывает кнопку «Опубликовать»);
	*     PHP.L.*                      — языковые строки формы;
	*     PHP.cfg.payments.valuta      — валюта базовой цены;
	*     PHP.cfg.market.market_currency — валюта модуля (fallback);
	*     PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd
	*                                  — курс для JS-пересчёта цены.
	*
	* Кнопки отправки формы (name → value):
	*   ritemmarketstate=0   — опубликовать (только если {PHP.usr_can_publish});
	*   preview=1            — предпросмотр (сохраняет как черновик, редирект на preview);
	*   ritemmarketstate=2   — сохранить как черновик;
	*   ritemmarketstate=1   — отправить на модерацию.
	*
	* JS-конвертер цены:
	*   Пересчитывает значение {MARKETEDIT_FORM_COST_USD} в базовую валюту
	*   по курсу cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd
	*   и пишет результат в <strong id="result_rate_value_to_fieldmrkt_costdflt">.
	*   В шаблоне используется IIFE с делегированием input/change на document
	*   (capture) и первичным расчётом на window.load — тот же паттерн, что
	*   и в market.add.tpl.
	*
	* JS-счётчик символов:
	*   Блоки .js-chars-limit-block содержат поле и .js-chars-counter
	*   с атрибутом data-limit. Скрипт обрезает значение до лимита и
	*   обновляет «Осталось: N симв.» при каждом input.
	*
	* Используемые плагины (опционально):
	*   aliasmarketpro       — генерация ЧПУ-алиасов;
	*   attacher             — управление изображениями и файлами;
	*   xtradbrowmarket      — доп. поля товара;
	*   marketprofilter      — параметры фильтра;
	*   multicatmarket       — товар в нескольких категориях;
	*   tgm4market           — публикация товара в Telegram-канал;
	*   featuredproducts     — рекомендуемые товары;
	*   featuredtopicsmarket — рекомендуемые темы форума;
	*   featuredpagesmarket  — рекомендуемые статьи.
	*
	* Хуки (в market.edit.php):
	*   market.edit.first          — в самом начале, до загрузки товара;
	*   market.edit.update.first   — перед обработкой POST-обновления;
	*   market.edit.update.import  — после импорта POST-данных;
	*   market.edit.update.error   — после валидации (если есть ошибки);
	*   market.edit.main           — после подготовки основных переменных и шаблона;
	*   market.edit.tags           — перед финальным парсингом шаблона.
	*
	* Source and updates   https://github.com/webitproff/marketpro-cotonti
	* ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
	* Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
	* API Extrafields:     https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
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
			<ol class="breadcrumb d-flex mb-0">{MARKETEDIT_BREADCRUMBS}</ol>
		</div>
	</nav>
</div>

<!-- IF {PHP.usr.maingrp} == 5 -->
<script>
	var ckeditorConfig = {
		link: {
			addTargetToExternalLinks: false,
			decorators: {
				dofollow: {
					mode: 'manual',                   // ручная галочка
					label: 'dofollow',                // подпись в диалоге
					attributes: {
						rel: 'dofollow'               // устанавливаемый атрибут
					}
				}
			}
		}
	};
</script>
<!-- ENDIF -->
<!-- IF !{PHP.usr_can_publish} -->
<div class="mb-3 mt-3">
    <div class="alert alert-info" role="alert">{PHP.L.market_formhint}</div>
</div>
<!-- ENDIF -->
<div class="container-xxl px-3 px-lg-5 py-5">
	<div class="alert alert-info mb-3">
		<div>{PHP.L.Status} <span class="me-2 text-danger fw-semibold">{MARKETEDIT_FORM_LOCAL_STATUS}</span></div>
	</div>
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	<div class="row justify-content-center">
		<form action="{MARKETEDIT_FORM_SEND}" enctype="multipart/form-data" method="post" name="marketform" class="needs-validation" novalidate>
			
			<div class="card mb-4">
				<div class="card-header">
					<h2 class="h5 mb-0">{PHP.L.market_form_item_edit_title} #{MARKETEDIT_FORM_ID}</h2>
					<p class="fw-semibold">{PHP.L.market_form_item_edit_subtitle}</p>
				</div>
				<div class="card-body">	
					<div class="alert alert-info">
						<p class="fw-semibold">{PHP.L.market_form_owner} <a href="{MARKETEDIT_FORM_OWNER_URL}" target="_blank">{MARKETEDIT_FORM_OWNER_NAME}</a></p>
						<small class="form-text text-muted">{PHP.L.market_form_owner_hint}</small>
					</div>
					<!-- BEGIN: ADMIN -->
					<div class="row mb-5">
						<div class="col-12 col-lg-6">
							<label for="marketOwner" class="form-label fw-semibold">{PHP.L.Owner}</label>
							<div class="input-group has-validation">{MARKETEDIT_FORM_OWNER_ID}</div>
						</div>
						<div class="col-12 col-lg-6">
							<label for="marketHits" class="form-label fw-semibold">{PHP.L.Hits}</label>
							<div class="input-group has-validation">{MARKETEDIT_FORM_HITS}</div>
						</div>
					</div>
					<div class="col-12 mb-3">						
						<!-- IF {PHP|cot_plugin_active('aliasmarketpro')} -->
						<div class="alert alert-info py-0 mb-0">
							{PHP.L.Plugin} 
							<a href="{PHP|cot_url('admin', 'm=extensions&a=details', '&pl=aliasmarketpro')}" target="_blank" class="text-decoration-none fw-semibold">{PHP.L.aliasmarketpro_title}
							</a>
						</div>
						<!-- ENDIF -->
						<label for="marketAlias" class="form-label fw-semibold">{PHP.L.market_form_item_alias}</label>
						<div class="input-group has-validation">{MARKETEDIT_FORM_ALIAS}</div>
						<small class="form-text text-muted">{PHP.L.market_form_item_alias_hint}</small>
					</div>
					<!-- END: ADMIN -->
				</div>
			</div>
			
			<div class="card mb-4">
				<div class="card-header">
					<h4></h4>
				</div>
				<div class="card-body">	
					
					<div class="col-12 mb-3">
						<label for="marketCat" class="form-label fw-semibold"><span class="me-2 text-danger">&#128681;</span>{PHP.L.market_form_category}</label>
						<div class="input-group has-validation">{MARKETEDIT_FORM_CAT_S2}</div>
						<small class="form-text text-muted">{PHP.L.market_form_category_hint}</small>
					</div>
					<hr>
					<!-- IF {PHP|cot_plugin_active('multicatmarket')} -->
					<!-- IF {PHP|cot_auth('plug', 'multicatmarket', 'W')} -->
					<div class="col-12">
						<label for="marketCat" class="form-label fw-semibold">{PHP.L.multicatmarket_cats_edit}</label>
						<div class="input-group has-validation py-3" style="background-color: var(--bs-body-bg);">
							{MARKET_FORM_MULTICAT}
						</div>
						<small class="form-text text-muted mt-1">{MARKET_FORM_MULTICAT_HINT}</small>
					</div>
					<!-- ENDIF -->
					<!-- ENDIF -->
				</div>
			</div>
			
			<div class="card mb-4">
				<div class="card-header">
					<h4></h4>
				</div>
				<div class="card-body">	
					<div class="col-12 js-chars-limit-block">
						<label for="marketTitle" class="form-label fw-semibold"><span class="me-2 text-danger">&#128681;</span>{PHP.L.market_form_item_title}</label>
						<div class="input-group has-validation">
							{MARKETEDIT_FORM_TITLE}
						</div>
						<small class="form-text text-muted">{PHP.L.market_form_item_title_hint}</small>
						<div class="form-text text-end">
							<span class="text-muted small js-chars-counter" data-limit="110"></span>
						</div>
					</div>
					<div class="col-12 mb-3">
						<label for="marketTitleH1" class="form-label fw-semibold">{PHP.L.market_form_meta_h1}</label>
						<div class="input-group has-validation">
							{MARKETEDIT_FORM_META_H1}
						</div>
						<small class="form-text text-muted">{PHP.L.market_form_meta_h1_hint}</small>
					</div>
					<hr>
					<div class="col-12 js-chars-limit-block">
						<label for="marketDesc" class="form-label fw-semibold">{PHP.L.market_form_item_desc}</label>
						<div class="input-group has-validation">
							{MARKETEDIT_FORM_DESCRIPTION}
						</div>
						<small class="form-text text-muted">{PHP.L.market_form_item_desc_hint}</small>
						<div class="form-text text-end">
							<span class="text-muted small js-chars-counter" data-limit="170"></span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="card mb-4">
				<div class="card-header">
					<h4></h4>
				</div>
				<div class="card-body">	
					
					
					
				</div>
			</div>
			
			<div class="card mb-4">
				<div class="card-header">
					<h4></h4>
				</div>
				<div class="card-body">
					<div class="col-12 js-chars-limit-block">
						<label for="marketMetaTitle" class="form-label fw-semibold">{PHP.L.market_form_meta_title}</label>
						<div class="input-group has-validation">
							{MARKETEDIT_FORM_META_TITLE}
						</div>
						<small class="form-text text-muted">{PHP.L.market_form_meta_title_hint}</small>
						<div class="form-text text-end">
							<span class="text-muted small js-chars-counter" data-limit="55"></span>
						</div>
					</div>
					
					<div class="col-12 js-chars-limit-block">
						<label for="marketMetaDesc" class="form-label fw-semibold">{PHP.L.market_form_meta_desc}</label>
						<div class="input-group has-validation">
							{MARKETEDIT_FORM_META_DESC}
						</div>
						<small class="form-text text-muted">{PHP.L.market_form_meta_desc_hint}</small>
						<div class="form-text text-end">
							<span class="text-muted small js-chars-counter" data-limit="160"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="card mb-4">
				<div class="card-header">
					<h4></h4>
				</div>
				<div class="card-body">	
					
					
				</div>
			</div>
			
			<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
			<div class="card mb-4">
				<!-- IF {PHP.usr.maingrp} == 5 -->
				<div class="card-header">
					<p>{PHP.L.Plugin} 
						<a href="{PHP|cot_url('admin', 'm=extensions&a=details', '&pl=xtradbrowmarket')}" target="_blank" class="text-decoration-none fw-semibold">{PHP.L.xtradbrowmarket_title}
						</a>
					</p>
				</div>
				<!-- ENDIF -->
				<div class="card-body">
					<h6>{PHP.L.xtradbrowmarket_edittpl_dynamic_title}</h6>
					<!-- BEGIN: XTRA_EXTRAFLD -->
					<div class="form-group mb-3">
						<label>{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}</label>
						{MARKETEDIT_FORM_XTRA_EXTRAFLD}
					</div>
					<!-- END: XTRA_EXTRAFLD -->
				</div>
			</div>
			<!-- ENDIF -->
			
			<div class="card mb-4">
				<div class="card-header">
					<h5><span class="me-2 text-danger">&#128681;</span>{PHP.L.market_form_text_full}</h5>
					<small class="form-text text-muted">{PHP.L.market_form_text_full_hint}</small>
					<hr>
					<div class="col-12 mb-3">
						<label for="marketParser" class="form-label fw-semibold">{PHP.L.market_form_parser}</label>
						<div class="input-group has-validation">{MARKETEDIT_FORM_PARSER}</div>
						<small class="form-text text-muted">{PHP.L.market_form_parser_hint}</small>
					</div>
				</div>
					{MARKETEDIT_FORM_TEXT}
			</div>
			
			<!-- IF {PHP.usr.maingrp} == 5 -->
			<!-- IF {MARKETEDIT_HAS_EXTRAFIELDS} -->
			<div class="alert alert-info">
				<div><span class="me-2 fs-3">&#129668;</span>{PHP.L.market_form_extrafield} {PHP.L.market_form_extrafield_link}</div>
				<div><small class="form-text text-muted">{PHP.L.market_form_extrafield_hint} </small></div>
			</div>
			<!-- ELSE -->
			<div class="alert alert-warning">
				<div><span class="me-2 fs-3">&#129668;</span>{PHP.L.market_form_extrafield_not_found} {PHP.L.market_form_extrafield_link}</div>
				<div><small class="form-text text-muted">{PHP.L.market_form_extrafield_hint} </small></div>
			</div>
			<!-- ENDIF --> 
			<!-- ENDIF -->
			<!-- BEGIN: EXTRAFLD -->
			<div class="col-12 mb-3">
				<label class="form-label fw-semibold">
					{MARKETEDIT_FORM_EXTRAFLD_TITLE}
				</label>
				<!-- IF {MARKETEDIT_FORM_EXTRAFLD_CODENAME} -->
				<small class="text-muted">(<code>{MARKETEDIT_FORM_EXTRAFLD_CODENAME}</code>)</small>
				<!-- ENDIF -->
				<div class="input-group">{MARKETEDIT_FORM_EXTRAFLD}</div>
			</div>
			<!-- END: EXTRAFLD -->
			<hr>
			<div class="accordion border border-info rounded mb-4" id="prData">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button type="button"
						class="accordion-button bg-info text-white"
						data-bs-toggle="collapse"
						data-bs-target="#Data"
						aria-expanded="true">
							Data
						</button>
					</h2>
					<div id="Data" class="accordion-collapse collapse" data-bs-parent="#prData" style="">
						<div class="accordion-body">			
							<div class="col-12">
								<label for="marketDate" class="form-label fw-semibold">{PHP.L.Date}</label>
								<div>{MARKETEDIT_FORM_DATE}</div>
								<small class="form-text text-muted mt-1">{MARKETEDIT_FORM_DATENOW} {PHP.L.market_date_now}</small>
							</div>
							
							<div class="col-12">
								<label for="marketAlias" class="form-label fw-semibold">{PHP.L.Code}</label>
								<div class="input-group has-validation">{MARKETEDIT_FORM_PCOD}</div>
							</div>
							
							
							
						</div>
					</div>
				</div>
			</div>
			<div class="accordion border border-info rounded mb-4" id="price">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button type="button"
						class="accordion-button bg-info text-white"
						data-bs-toggle="collapse"
						data-bs-target="#market_price"
						aria-expanded="true">
							{PHP.L.market_price}
						</button>
					</h2>
					<div id="market_price" class="accordion-collapse collapse" data-bs-parent="#price" style="">
						<div class="accordion-body">
							<div class="mb-3 row">
								<label class="col-lg-3 col-form-label fw-semibold">{PHP.L.market_form_item_fieldmrkt_costdflt}</label>
								<div class="col-lg-9">
									<div class="input-group">
										{MARKETEDIT_FORM_COSTDFLT}
										<span class="input-group-text">
											<!-- IF {PHP.cfg.payments.valuta} -->
											{PHP.cfg.payments.valuta}
											<!-- ELSE -->
											{PHP.cfg.market.market_currency}
											<!-- ENDIF -->
										</span>
									</div>
								</div>
							</div>
							<hr>
							<div class="mb-3 row">
								<div class="col-lg-3">
									<label class="form-label fw-semibold">
										{PHP.L.market_form_item_cost_usd}
									</label>
								</div>
								<div class="col-lg-9">
									<small class="text-muted">
										{PHP.L.market_form_item_price_base_after_rate}: <strong id="result_rate_value_to_fieldmrkt_costdflt">0.00</strong> {PHP.cfg.market.market_currency}
									</small>	
									<div class="input-group">
										{MARKETEDIT_FORM_COST_USD}
										<span class="input-group-text">{PHP.cfg.market.market_for_rate_base_currency_cost_usd}</span>
									</div>
									<small class="text-muted">{PHP.L.market_form_item_cost_usd_hint}</small>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="accordion border border-info rounded mb-4" id="att_fileboxq">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button type="button"
						class="accordion-button bg-info text-white"
						data-bs-toggle="collapse"
						data-bs-target="#att_add_pict"
						aria-expanded="true">
							{PHP.L.att_add_pict_files}
						</button>
					</h2>
					<div id="att_add_pict" class="accordion-collapse collapse" data-bs-parent="#att_fileboxq" style="">
						<div class="accordion-body">			
							<!-- IF {PHP|cot_plugin_active('attacher')} -->
							<!-- IF {PHP|cot_auth('plug', 'attacher', 'W')} -->
							<div class="mb-3 row col-12">
								<label class="form-label fw-semibold">{PHP.L.att_add_pict_files}</label>
								<div>
									{MARKETEDIT_FORM_ID|att_filebox('market', $this)}
								</div>
							</div>
							<!-- ENDIF -->
							<!-- ENDIF -->
						</div>
					</div>
				</div>
			</div>
			
			<!-- IF {PHP|cot_plugin_active('marketprofilter')} -->
			<div class="col-12">
				<label class="form-label fw-semibold">{FILTER_PARAMS_HEADER}</label>		
				<!-- BEGIN: MARKET_FORM_FILTER_PARAMS -->
				<!-- BEGIN: MARKET_FORM_FILTER_PARAM -->
				<div class="card mb-4">
					<div class="card-body">	
						
						<label class="form-label fw-bold">
							<!-- IF {FILTER_PARAM_HASHELP} -->
							<a href="#" data-bs-toggle="modal" data-bs-target="#helpModal_edit_{FILTER_PARAM_NAME}" class="me-2" title="{PHP.L.Help}">
								<i class="fa-solid fa-circle-question text-info"></i>
							</a>
							<!-- ENDIF -->
							{FILTER_PARAM_TITLE}
						</label>
						{FILTER_PARAM_INPUT}
					</div>
				</div>
				<!-- IF {FILTER_PARAM_HASHELP} -->
				<div class="modal fade" id="helpModal_edit_{FILTER_PARAM_NAME}" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">{FILTER_PARAM_TITLE}</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">{FILTER_PARAM_HELP}</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{PHP.L.Close}</button>
							</div>
						</div>
					</div>
				</div>
				<!-- ENDIF -->
				<!-- END: MARKET_FORM_FILTER_PARAM -->
				
				<!-- END: MARKET_FORM_FILTER_PARAMS -->
			</div>
			<!-- ENDIF -->
			
			
			<div class="accordion border border-info rounded mb-4" id="Recommended">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button type="button"
						class="accordion-button bg-info text-white"
						data-bs-toggle="collapse"
						data-bs-target="#recomAll"
						aria-expanded="true">
							FEATURED
						</button>
					</h2>
					<div id="recomAll" class="accordion-collapse collapse" data-bs-parent="#Recommended" style="">
						<div class="accordion-body">
							<!-- IF {PHP|cot_plugin_active('featuredproducts')} -->
							<!-- IF {PHP|cot_auth('plug', 'featuredproducts', 'W')} -->
							<div class="col-12">
								{FEATURED_PRODUCTS_EDIT}
							</div>
							<hr>
							<!-- ENDIF -->
							<!-- ENDIF -->
							
							<!-- IF {PHP|cot_plugin_active('featuredtopicsmarket')} -->
							<!-- IF {PHP|cot_auth('plug', 'featuredtopicsmarket', 'W')} -->
							{RECOMMENDED_FR_TOPIC_MARKET_EDIT_TOPIC}
							<hr>
							<!-- ENDIF -->
							<!-- ENDIF -->
							
							<!-- IF {PHP|cot_plugin_active('featuredpagesmarket')} -->
							<!-- IF {PHP|cot_auth('plug', 'featuredpagesmarket', 'W')} -->
							{FEATUREDPRO_ARTICLES_EDIT}
							<hr>
							<!-- ENDIF -->
							<!-- ENDIF -->				
						</div>
					</div>
				</div>
			</div>			
			
			<div class="accordion border border-info rounded mb-4" id="LinkToExtra">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button type="button"
						class="accordion-button bg-info text-white"
						data-bs-toggle="collapse"
						data-bs-target="#YOUTUBE_ID"
						aria-expanded="true">
							YOUTUBE
						</button>
					</h2>
					<div id="YOUTUBE_ID" class="accordion-collapse collapse" data-bs-parent="#LinkToExtra" style="">
						<div class="accordion-body">			
							<!-- IF {MARKETEDIT_FORM_YOUTUBE_ID} -->
							<div class="col-12">
								<label for="pageLinkMainImage" class="form-label mb-1 fw-semibold small">
									{MARKETEDIT_FORM_YOUTUBE_ID_TITLE}
								</label>
								
								<div class="input-group input-group">
									<span class="input-group-text">
										<i class="fa-brands fa-youtube text-danger fa-lg"></i>
									</span>
									
									{MARKETEDIT_FORM_YOUTUBE_ID}
								</div>
								
								<div class="form-text small">
									<small class="text-muted">
										{PHP.L.market_youtube_id_edit_tpl}
									</small>
									
								</div>
							</div>
							<!-- ENDIF -->
							<!-- IF {MARKETEDIT_FORM_FORUM_LINK} -->
							<div class="col-12">
								<label for="pageLinkMainImage" class="form-label mb-1 fw-semibold small">
									{MARKETEDIT_FORM_FORUM_LINK_TITLE}
								</label>
								
								<div class="input-group input-group">
									<span class="input-group-text">
										<i class="fa-solid fa-comments text-warning fa-lg"></i>
									</span>
									
									{MARKETEDIT_FORM_FORUM_LINK}
								</div>
								
								<div class="form-text small">
									<small class="text-muted">
										{PHP.L.market_forum_link_edit_tpl} 
									</small>
								</div>
							</div>
							<!-- ENDIF -->
						</div>
					</div>
				</div>
			</div>	
			<!-- IF {PHP|cot_plugin_active('tgm4market')} -->
			<!-- IF {PHP|cot_auth('plug', 'tgm4market', 'W')} -->
			<div class="card mb-4">
				<div class="card-body">
					<div class="col-12">
						<label for="marketDelete" class="form-label fw-semibold">{PHP.L.market_deleteitem}{PHP.L.Delete}</label>
						<div>{TGM4MARKET_EDIT_ACTION}</div>
					</div>
					
				</div>
			</div>
			<!-- ENDIF -->
			<!-- ENDIF -->
			
			<div class="card mb-4">
				<div class="card-body">
					<div class="col-12">
						<label for="marketDelete" class="form-label fw-semibold">{PHP.L.market_deleteitem}{PHP.L.Delete}</label>
						<div class="input-group has-validation">{MARKETEDIT_FORM_DELETE}</div>
					</div>
					<div class="col-12">
						<div class="d-grid gap-2 d-md-flex justify-content-md-end">
							<!-- IF {PHP.usr_can_publish} -->
							<button type="submit" name="ritemmarketstate" value="0" class="btn btn-success">{PHP.L.Publish}</button>
							<!-- ENDIF -->
							<button type="submit" name="preview" value="1" class="btn btn-info">{PHP.L.market_preview}</button>
							<button type="submit" name="ritemmarketstate" value="2" class="btn btn-secondary">{PHP.L.Saveasdraft}</button>
							<button type="submit" name="ritemmarketstate" value="1" class="btn btn-warning">{PHP.L.Submitforapproval}</button>
						</div>
					</div>
				</div>
			</div>
		</form>
		
	</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Для каждого блока с js-chars-limit-block
        document.querySelectorAll('.js-chars-limit-block').forEach(function(block) {
            // Найти первое поле ввода или textarea внутри этого блока
            const field = block.querySelector('input, textarea');
            // Найти счётчик внутри этого же блока
            const counter = block.querySelector('.js-chars-counter');
            if (!field || !counter) return;
            
            const limit = parseInt(counter.getAttribute('data-limit'), 10);
            
            function update() {
                let val = field.value;
                if (val.length > limit) {
                    field.value = val.substring(0, limit);
				}
                const remaining = limit - field.value.length;
                counter.textContent = 'Осталось: ' + remaining + ' симв.';
			}
            
            field.addEventListener('input', update);
            update(); // начальное значение
		});
	});
</script>


<script>
    // ============================================================
    // Пересчёт цены: МЕЖДУНАРОДНАЯ валюта → БАЗОВАЯ валюта.
    // Работает на странице добавления товара (market&m=add).
    // Логика идентична той, что в market.edit.tpl.
    // ============================================================

    // Самовызывающаяся функция (IIFE) — изолирует переменные
    // (RATE, upd) от глобальной области, чтобы не пересекаться
    // с другими скриптами на странице.
    (function () {

        // --------------------------------------------------------
        // Курс пересчёта.
        // Значение: сколько единиц БАЗОВОЙ валюты содержится
        // в 1 единице МЕЖДУНАРОДНОЙ валюты.
        // Источник: cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd —
        // параметр модуля Market PRO, подставляется PHP при рендере шаблона.
        // Если значение пустое или не число — parseFloat вернёт NaN,
        // тогда "|| 1" подставит fallback = 1.
        // --------------------------------------------------------
        var RATE = parseFloat('{PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd}') || 1;

        // --------------------------------------------------------
        // upd(el) — пересчёт и запись результата в <strong>.
        // Принимает элемент <input> с введённой суммой в
        // международной валюте.
        // --------------------------------------------------------
        function upd(el) {

            // Элемент вывода пересчитанной суммы в БАЗОВОЙ валюте.
            // id="result_rate_value_to_fieldmrkt_costdflt" задан
            // в разметке формы выше (внутри блока цены).
            var span = document.getElementById('result_rate_value_to_fieldmrkt_costdflt');

            // Если span не найден или переданный элемент пуст —
            // выходим без ошибок, чтобы не уронить остальные обработчики.
            if (!span || !el) return;

            // Значение поля. Если строка пустая или не число —
            // parseFloat вернёт NaN, "|| 0" превратит его в 0.
            var v = parseFloat(el.value) || 0;

            // Пересчёт: международная сумма × курс = базовая сумма.
            // toFixed(2) — округление до 2 знаков после запятой.
            // Результат записывается как текст в <strong>.
            span.textContent = (v * RATE).toFixed(2);
        }

        // --------------------------------------------------------
        // Обработчик события 'input' — срабатывает при каждом
        // изменении значения в поле (набор с клавиатуры, вставка).
        // Делегирование на document: слушатель висит на всём документе
        // и перехватывает события от любых полей.
        // Третий параметр true — фаза захвата (capture): событие
        // доходит до нашего обработчика до других слушателей
        // и не может быть остановлено ими.
        // Фильтр по e.target.name: обрабатываем только поле
        // с name="ritemmarketcostusd" (рендерится макросом
        // {MARKETADD_FORM_COST_USD}). Остальные события игнорируем.
        // --------------------------------------------------------
        document.addEventListener('input', function (e) {
            if (e.target && e.target.name === 'ritemmarketcostusd') upd(e.target);
        }, true);

        // --------------------------------------------------------
        // Обработчик события 'change' — срабатывает при потере
        // фокуса, изменении значения стрелками вверх/вниз,
        // автозаполнении браузером и т.п.
        // Логика та же, что и в 'input': делегирование на document,
        // фаза захвата, фильтр по name поля.
        // --------------------------------------------------------
        document.addEventListener('change', function (e) {
            if (e.target && e.target.name === 'ritemmarketcostusd') upd(e.target);
        }, true);

        // --------------------------------------------------------
        // Первичный расчёт после полной загрузки страницы.
        // Нужен для случая, когда поле уже заполнено до срабатывания
        // наших обработчиков (например, при возврате на форму после
        // ошибки валидации — поле сохраняет введённое значение,
        // а события input/change при этом не срабатывают).
        // Находим поле по name, если оно есть — запускаем пересчёт.
        // --------------------------------------------------------
        window.addEventListener('load', function () {
            var el = document.querySelector('input[name="ritemmarketcostusd"]');
            if (el) upd(el);
        });

    })();
</script>


<!-- IF {PHP.usr.isadmin} -->
<div class="container-xxl px-3 px-lg-5 py-5">
	<div class="alert alert-info" role="alert">
		{TPL_PATH}
	</div>
</div>
<!-- ENDIF -->

<!-- END: MAIN -->


<!-- пока оставляю как альтернатива для конвертора валюты -->
<script>
    // Ждём полной загрузки DOM, чтобы поля формы уже существовали в документе
    document.addEventListener('DOMContentLoaded', function() {
		
        // Находим input для ввода цены в международной валюте по атрибуту name
        // Этот input рендерится макросом {MARKETEDIT_FORM_COST_USD}
        const usdInput = document.querySelector('input[name="ritemmarketcostusd"]');
		
        // Находим элемент <strong>, в который будем выводить пересчитанную сумму
        // В шаблоне выше он имеет id="result_rate_value_to_fieldmrkt_costdflt"
        const resultSpan = document.getElementById('result_rate_value_to_fieldmrkt_costdflt');
		
        // Берём курс из конфига модуля: сколько базовой валюты в 1 единице международной
        // Значение подставляется PHP при рендере шаблона
        // Если конфиг пустой/невалидный — parseFloat вернёт NaN, поэтому подстраховываемся через "|| 1"
        const rate = parseFloat({PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd}) || 1;
		
        // Страховка: если поля ввода на странице нет — выходим, чтобы не было ошибок ниже
        if (!usdInput) return;
		
        // Функция пересчёта: берёт текущее значение из input, умножает на курс,
        // округляет до 2 знаков и записывает в <strong>
        function update_value_fieldmrkt_costdflt() {
            // Читаем значение поля; если пусто/не число — считаем как 0
            let value = parseFloat(usdInput.value) || 0;
            // Умножаем международную сумму на курс и пишем результат в элемент вывода
            resultSpan.textContent = (value * rate).toFixed(2);
		}
		
        // Пересчёт срабатывает на каждое изменение значения в поле (в т.ч. при вводе с клавиатуры)
        usdInput.addEventListener('input', update_value_fieldmrkt_costdflt);
		
        // Пересчёт срабатывает при потере фокуса/изменении значения (например, стрелками, автозаполнением)
        usdInput.addEventListener('change', update_value_fieldmrkt_costdflt);
		
        // Первичный расчёт сразу при загрузке страницы — чтобы показать актуальную сумму,
        // если поле уже заполнено (например, при редактировании существующего товара)
        update_value_fieldmrkt_costdflt();
		
	});
</script>
