<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.vendor.categories.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.vendor.categories.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.vendor.categories.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.vendor.php
	*                     _ _ _ _ modules/market/inc/market.functions.php SEE: cot_market_build_vendor_categories_html()
	* Назначение:
	*   Шаблон витрины конкретного продавца модуля Market. Открывается
	*   по URL m=vendor&u=<username> (или uid=<id>, или ЧПУ-вариантам).
	*   Отображает:
	*     - хлебные крошки (Главная → Market → Список продавцов → Ник);
	*     - шапку витрины: аватар, ник, ссылку на профиль, дату последней
	*       активности, блок «о продавце» (extrafields xtradbrowusers),
	*       статистику (товаров, категорий, дата регистрации);
	*     - левый сайдбар: список категорий, в которых у продавца есть
	*       опубликованные товары, с количеством и подсветкой активной;
	*     - форму поиска по товарам продавца (поле + выбор области поиска);
	*     - сетку карточек товаров продавца (BEGIN: LIST_ROW) с превью
	*       (attacher), названием, кратким описанием и ценой;
	*     - пустое состояние (BEGIN: LIST_EMPTY) при отсутствии товаров;
	*     - пагинацию товаров.
	*
	*   ПРИМЕР https://abuyfile.com/ru/market/vendor/webitproff
	*
	* Основные параметры URL:
	*   m=vendor                — режим витрины продавца;
	*   u=<username>            — ник продавца (предпочтительно);
	*   uid=<user_id>           — ID продавца (альтернатива);
	*   c=<код категории>       — фильтр по категории внутри витрины;
	*   s=<поле сортировки>     — поле сортировки товаров (без fieldmrkt_);
	*   w=<asc|desc>            — направление сортировки;
	*   sq=<запрос>             — поисковый запрос по товарам продавца;
	*   search_in=<область>     — область поиска: title, full, pcod;
	*   d=<страница>            — номер страницы пагинации.
	*
	* Поддерживаемые варианты URL:
	*   /market/vendor/<username>             — предпочтительный;
	*   /market/vendor/<user_id>              — числовой ID;
	*   /market/vendor-<user_id>-<username>   — комбинированный;
	*   ?e=market&m=vendor&u=<username>       — query-string.
	*
	* Основные теги шаблона:
	*   Навигация и шапка витрины:
	*     VENDOR_BREADCRUMBS         — хлебные крошки витрины (HTML);
	*     VENDOR_USER_ID             — ID продавца;
	*     VENDOR_USERNAME            — ник продавца (экранированный);
	*     VENDOR_USER_AVATAR         — аватар продавца (cot_generate_usertags);
	*     VENDOR_USER_*              — дополнительные теги продавца (extrafields);
	*     VENDOR_USER_XTRA_*         — доп. поля продавца (xtradbrowusers, напр. XTRA_X020_ABOUT_VENDOR_TEXT);
	*     VENDOR_PROFILE_URL         — ссылка на профиль пользователя;
	*     VENDOR_SHOWCASE_URL        — канонический URL текущей витрины;
	*     VENDOR_VENDORS_LIST_URL    — ссылка на список всех витрин (m=vendors);
	*     VENDOR_PRODUCTS_PUBLISHED  — количество опубликованных товаров продавца;
	*     VENDOR_PRODUCTS_TOTAL      — общее количество товаров (с черновиками и модерацией);
	*     VENDOR_CATEGORIES_COUNT    — количество категорий, в которых торгует продавец;
	*     VENDOR_REGDATE[_STAMP]     — дата регистрации (текст / timestamp);
	*     VENDOR_LAST_SEEN[_STAMP]   — дата последней активности (текст / timestamp);
	*     VENDOR_CATEGORIES_TREE     — HTML-дерево категорий продавца
	*                                  (cot_market_build_vendor_categories_html()).
	*
	*   Форма поиска:
	*     VENDOR_SEARCH_ACTION_URL   — URL action формы поиска;
	*     VENDOR_SEARCH_SQ           — поле ввода поискового запроса (по товарам продавца);
	*     VENDOR_SEARCH_IN_SELECT    — selectbox области поиска (title / full / pcod).
	*
	*   Список товаров (BEGIN: LIST_ROW):
	*     LIST_ROW_*                 — стандартные теги товара (cot_generate_markettags());
	*     LIST_ROW_OWNER / OWNER_*   — теги владельца (cot_build_user / cot_generate_usertags);
	*     LIST_ROW_ODDEVEN           — odd/even для zebra-стилизации;
	*     LIST_ROW_NUM / ABS_NUM     — номер строки в списке / абсолютный (с учётом пагинации);
	*     LIST_ROW_DESCRIPTION_OR_TEXT_CUT — краткое описание (или обрезанный текст);
	*     LIST_ROW_COSTDFLT          — цена в базовой валюте.
	*
	*   Пустое состояние: BEGIN: LIST_EMPTY.
	*
	*   Пагинация:
	*     PAGINATION / PREVIOUS_PAGE / NEXT_PAGE / CURRENT_PAGE / TOTAL_PAGES.
	*
	*   Прочее:
	*     TPL_PATH                   — путь к файлу шаблона (только админ).
	*
	* Доступ к товарам:
	*   Владелец витрины и админ видят все товары продавца (в т.ч. черновики
	*   и на модерации). Гости и прочие пользователи — только опубликованные
	*   (STATE_PUBLISHED). Если у продавца нет товаров и это не владелец/админ — 404.
	*
	* Используемые плагины (опционально):
	*   attacher        — вывод изображений товаров (att_count / att_display);
	*   xtradbrowusers  — доп. поля продавца (например, XTRA_X020_ABOUT_VENDOR_TEXT);
	*   i18n4marketpro  — мультиязычный поиск и названия категорий;
	*   multicatmarket  — учитывается в cot_market_build_vendor_categories_html().
	*
	* Хуки (в market.vendor.php):
	*   market.vendor.first         — в начале страницы;
	*   market.vendor.query         — перед формированием SQL-запроса;
	*   market.vendor.main          — после подготовки данных продавца;
	*   market.vendor.before_loop   — перед циклом вывода товаров;
	*   market.vendor.loop          — внутри цикла товаров;
	*   market.vendor.tags          — перед финальным парсингом шаблона.
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
<div class="container-fluid px-3 px-lg-5 py-5">
    {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">{VENDOR_BREADCRUMBS}</ol>
	</nav>
	
    <!-- ==================== ШАПКА ВИТРИНЫ ==================== -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex flex-wrap align-items-center">
            <div class="me-4 mb-2">{VENDOR_USER_AVATAR}</div>
            <div class="flex-grow-1">
                <h1 class="h3 mb-1">
                    {VENDOR_USERNAME}
                    <a href="{VENDOR_PROFILE_URL}" class="text-decoration-none text-muted small">
                        ({PHP.L.market_vendor_profile_link})
					</a>
				</h1>
                <!-- IF {VENDOR_LAST_SEEN} -->
                <p class="mb-2 text-muted">{PHP.L.Lastlogged}: {VENDOR_LAST_SEEN}</p>
                <!-- ENDIF -->
					<!-- IF {PHP|cot_plugin_active('xtradbrowusers')} -->
					<!-- IF {VENDOR_USER_XTRA_X020_ABOUT_VENDOR_TEXT} -->
					<div class="mb-3">
						<div>
							<div class="contact-label">{VENDOR_USER_XTRA_X020_ABOUT_VENDOR_TEXT_TITLE}</div>
							<div class="contact-value">{VENDOR_USER_XTRA_X020_ABOUT_VENDOR_TEXT}</div>
						</div>
					</div>
					<!-- ENDIF -->
					<!-- ENDIF -->
                <div class="d-flex flex-wrap gap-3 small">
                    <span>
                        <i class="bi bi-box-seam"></i>
                        {PHP.L.market_vendor_products}:
                        <strong>{VENDOR_PRODUCTS_PUBLISHED}</strong>
					</span>
                    <span>
                        <i class="bi bi-folder"></i>
                        {PHP.L.market_vendor_categories}:
                        <strong>{VENDOR_CATEGORIES_COUNT}</strong>
					</span>
                    <span>
                        <i class="bi bi-calendar"></i>
                        {PHP.L.market_vendor_registered}: {VENDOR_REGDATE}
					</span>
				</div>
			</div>
		</div>
	</div>
	
    <div class="row">
		
        <!-- ==================== ЛЕВАЯ КОЛОНКА: КАТЕГОРИИ ==================== -->
		
		<aside class="col-md-3">
			<h5>{PHP.L.market_vendor_categories_of}</h5>
			<div class="market-vendor-categories">
				<!-- IF {VENDOR_CATEGORIES_TREE} -->
				{VENDOR_CATEGORIES_TREE}
				<!-- ELSE -->
				<p class="text-muted small">{PHP.L.market_vendor_no_categories}</p>
				<!-- ENDIF -->
			</div>
		</aside>
		
		
        <!-- ==================== ПРАВАЯ КОЛОНКА: ТОВАРЫ ==================== -->
        <div class="col-md-9">
			
            <!-- Форма поиска -->
            <form method="get" action="{VENDOR_SEARCH_ACTION_URL}" class="row g-2 mb-4">
                <div class="col-md-5">{VENDOR_SEARCH_SQ}</div>
                <div class="col-md-3">{VENDOR_SEARCH_IN_SELECT}</div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{PHP.L.Search}</button>
				</div>
			</form>
			
            <!-- Сетка товаров -->
            <div class="row g-4">
				
                <!-- BEGIN: LIST_ROW -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
				<div class="col-12">
					<div class="ratio ratio-4x3 ratio-lg-1x1 image-container">
						<!-- IF {PHP|cot_plugin_active('attacher')} -->
						<!-- IF {LIST_ROW_ID|att_count('market', $this, '', 'images')} > 0 --> 
						{LIST_ROW_ID|att_display('market',$this,'','attacher.display.marketlist','images',1)}
						<!-- ELSE -->
						<img src="{PHP.R.page_default_image}" class="card-img object-fit-cover" alt="{PAGE_TITLE}">
						<!-- ENDIF -->
						<!-- ELSE -->
						<img src="{PHP.R.page_default_image}" class="card-img object-fit-cover" alt="{PAGE_TITLE}">
						<!-- ENDIF --> 
					</div>
				</div>
                        <div class="card-body">
                            <div class="card-title">
                                <a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a>
							</div>
                            <p class="small text-muted">{LIST_ROW_DESCRIPTION_OR_TEXT_CUT}</p>
                            <div class="fw-bold">{LIST_ROW_COSTDFLT}</div>
						</div>
					</div>
				</div>
                <!-- END: LIST_ROW -->
				
                <!-- BEGIN: LIST_EMPTY -->
                <div class="col-12">
                    <div class="alert alert-info">{PHP.L.market_vendor_empty}</div>
				</div>
                <!-- END: LIST_EMPTY -->
				
			</div>
			
			<!-- IF {PAGINATION} -->
			<nav class="mt-5">
				<div class="pagination-scroll">
					<ul class="pagination justify-content-center flex-nowrap mb-0">
						{PREVIOUS_PAGE}
						{PAGINATION}
						{NEXT_PAGE}
					</ul>
				</div>
			</nav>
			
			<div class="text-center">
				{PHP.L.Page} {CURRENT_PAGE} {PHP.L.Of} {TOTAL_PAGES}
			</div>
			<!-- ENDIF -->	
		</div>
	</div>
	
</div>
<!-- IF {PHP.usr.isadmin} AND {TPL_PATH} --> 
<div class="container-fluid px-3 px-lg-5 py-5">
	<div class="alert alert-info" role="alert">
		{TPL_PATH}
	</div>
</div>
<!-- ENDIF -->
<!-- END: MAIN -->
