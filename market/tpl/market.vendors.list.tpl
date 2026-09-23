<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.vendors.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.vendors.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.vendors.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.vendors.php
	* Назначение:
	*   Шаблон каталога витрин продавцов (vendors). Отображает список всех
	*   продавцов модуля Market в виде карточек: аватар, никнейм, краткое
	*   описание (extrafields плагина xtradbrowusers), количество товаров,
	*   список категорий, в которых торгует продавец, дата регистрации.
	*   Ссылка с карточки ведёт на витрину продавца (market.vendor.php),
	*   а не на страницу профиля пользователя.
	*
	*   ПРИМЕР https://abuyfile.com/ru/market/vendors
	*
	* Основные параметры URL:
	*   m=vendors               — режим каталога продавцов;
	*   s=<поле>                — поле сортировки (last_item, items_count, username, regdate, last_seen);
	*   w=<asc|desc>            — направление сортировки;
	*   sq=<запрос>             — поиск по нику продавца (u.user_name);
	*   c=<код категории>       — фильтр по продавцам, торгующим в категории;
	*   d=<страница>            — номер страницы пагинации.
	*
	* Основные теги шаблона:
	*   VENDORS_TOTAL                — общее количество найденных продавцов;
	*   VENDORS_SORT_CURRENT         — текущее поле сортировки;
	*   VENDORS_WAY_CURRENT          — текущее направление сортировки;
	*   VENDORS_SORT_*_ACTIVE(_UP)   — CSS-класс active для активного пункта дропдауна сортировки;
	*   VENDORS_SORT_*_ASC|DESC      — URL сортировки по полю и направлению;
	*   VENDORS_SEARCH_ACTION_URL    — URL формы поиска продавцов;
	*   VENDORS_SEARCH_CAT_SELECT2   — выпадающий список категорий (Select2);
	*   VENDORS_SEARCH_SQ            — поле ввода поискового запроса (по нику);
	*   VENDORS_LIST_URL             — URL текущего списка продавцов (канонический);
	*   VENDOR_ROW_*                 — теги карточки продавца (см. market.vendors.php);
	*   VENDOR_ROW_USER_*            — дополнительные поля пользователя (extrafields);
	*   VENDOR_EMPTY                 — блок «продавцов не найдено»;
	*   PAGINATION / PREVIOUS_PAGE / NEXT_PAGE / CURRENT_PAGE / TOTAL_PAGES — пагинация;
	*   TPL_PATH                     — путь к файлу шаблона (только для админа).
	*
	* Используемые плагины (опционально):
	*   xtradbrowusers  — дополнительные поля продавца (например, XTRA_X020_ABOUT_VENDOR_TEXT);
	*   i18n4marketpro  — перевод названий категорий на текущий язык.
	*
	* Хуки (в market.vendors.php):
	*   market.vendors.first   — в начале страницы;
	*   market.vendors.query   — перед формированием SQL-запроса;
	*   market.vendors.loop    — внутри цикла вывода продавцов;
	*   market.vendors.tags    — перед финальным парсингом шаблона.
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
				<li class="breadcrumb-item"><a href="{PHP|cot_url('index')}">{PHP.L.Main}</a></li>
				<li class="breadcrumb-item"><a href="{PHP|cot_url('market')}">{PHP.L.market_title_general}</a></li>
				<li class="breadcrumb-item active">{PHP.L.market_vendors_title}</li>
			</ol>
		</div>
	</nav>
</div>

<div class="container-fluid px-3 px-lg-5 py-5">	
    <h1 class="mb-4">{PHP.L.market_vendors_title}</h1>
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	
	<!-- Форма поиска и сортировки продавцов -->
	<div class="card card-body mb-4">
		<form method="get" action="{VENDORS_SEARCH_ACTION_URL}" class="row g-2">
			<input type="hidden" name="m" value="vendors">
			<input type="hidden" name="l" value="{PHP.lang}" />
			<input type="hidden" name="s" value="{VENDORS_SORT_CURRENT}">
			<input type="hidden" name="w" value="{VENDORS_WAY_CURRENT}">
			
			<div class="col-lg-4">
				{VENDORS_SEARCH_SQ}
			</div>
			
			<div class="col-lg-3">
				{VENDORS_SEARCH_CAT_SELECT2}
			</div>
			
			<div class="col-lg-3">
				<div class="dropdown">
					<button class="btn btn-outline-secondary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
						{PHP.L.market_vendors_sort_title}
					</button>
					<ul class="dropdown-menu w-100">
						<li><a class="dropdown-item {VENDORS_SORT_LAST_ITEM_ACTIVE}"    href="{VENDORS_SORT_LAST_ITEM_DESC}">{PHP.L.market_vendors_sort_last_item} ↓</a></li>
						<li><a class="dropdown-item {VENDORS_SORT_LAST_ITEM_ACTIVE_UP}" href="{VENDORS_SORT_LAST_ITEM_ASC}">{PHP.L.market_vendors_sort_last_item} ↑</a></li>
						<li><hr class="dropdown-divider"></li>
						<li><a class="dropdown-item {VENDORS_SORT_ITEMS_COUNT_ACTIVE}"    href="{VENDORS_SORT_ITEMS_COUNT_DESC}">{PHP.L.market_vendors_sort_items_count} ↓</a></li>
						<li><a class="dropdown-item {VENDORS_SORT_ITEMS_COUNT_ACTIVE_UP}" href="{VENDORS_SORT_ITEMS_COUNT_ASC}">{PHP.L.market_vendors_sort_items_count} ↑</a></li>
						<li><hr class="dropdown-divider"></li>
						<li><a class="dropdown-item {VENDORS_SORT_USERNAME_ACTIVE}"    href="{VENDORS_SORT_USERNAME_ASC}">{PHP.L.market_vendors_sort_username} A→Z</a></li>
						<li><a class="dropdown-item {VENDORS_SORT_USERNAME_ACTIVE_UP}" href="{VENDORS_SORT_USERNAME_DESC}">{PHP.L.market_vendors_sort_username} Z→A</a></li>
					</ul>
				</div>
			</div>
			
			<div class="col-lg-2">
				<div class="row g-1">
					<div class="col-6">
						<button type="submit" class="btn btn-primary w-100" title="{PHP.L.Search}">
							<i class="fa-solid fa-magnifying-glass"></i>
						</button>
					</div>
					<div class="col-6">
						<a class="btn btn-outline-danger w-100"
						title="{PHP.L.marketprofilter_reset}"
						href="{PHP|cot_url('market', 'm=vendors')}">
							<i class="fa-solid fa-filter-circle-xmark"></i>
						</a>
					</div>
				</div>
			</div>
		</form>
	</div>
	
    <p class="text-muted">{PHP.L.market_vendors_total}: {VENDORS_TOTAL}</p>
	
    <!-- Сетка карточек продавцов -->
    <div class="row g-4">
		
        <!-- BEGIN: VENDOR_ROW -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
					<div class="text-center">
						<div class="mb-3">
							{VENDOR_ROW_USER_AVATAR}
						</div>
						<h5 class="card-title">
							<a href="{VENDOR_ROW_SHOWCASE_URL}" class="text-decoration-none">
								{VENDOR_ROW_USERNAME}
							</a>
						</h5>
						<p class="card-text mb-3">
							<!-- IF {VENDOR_ROW_PRODUCTS_COUNT} -->
							<span class="badge text-bg-primary">
								{VENDOR_ROW_PRODUCTS_COUNT} {PHP.L.market_vendors_items}
							</span>
							<!-- ENDIF -->
							<!-- IF {VENDOR_ROW_CATEGORIES_COUNT} -->
							<span class="badge text-bg-secondary">
								{VENDOR_ROW_CATEGORIES_COUNT} {PHP.L.market_vendors_categories}
							</span>
							<!-- ENDIF -->
						</p>
					</div>
                    <!-- IF {VENDOR_ROW_DESCRIPTION} -->
                    <p class="card-text small text-muted">{VENDOR_ROW_DESCRIPTION}</p>
					это пока заготовка. вместо этого использовать лучше экстраполя плагина 'xtradbrowusers'
                    <!-- ENDIF -->
					
					<!-- IF {PHP|cot_plugin_active('xtradbrowusers')} -->
					<!-- IF {VENDOR_ROW_USER_XTRA_X020_ABOUT_VENDOR_TEXT} -->
					<div class="mb-3">
						<div>
							<div class="contact-label text-center">{VENDOR_ROW_USER_XTRA_X020_ABOUT_VENDOR_TEXT_TITLE}</div>
							<div class="contact-value">{VENDOR_ROW_USER_XTRA_X020_ABOUT_VENDOR_TEXT}</div>
						</div>
					</div>
					<!-- ENDIF -->
					<!-- ENDIF -->
					
                    <!-- IF {VENDOR_ROW_CATEGORIES} -->
					<div class="text-center text-uppercase"><small>{PHP.L.market_vendor_categories_of}</small></div>
                    <p class="small">{VENDOR_ROW_CATEGORIES}</p>
                    <!-- ENDIF -->
					<!-- IF {VENDOR_ROW_REGDATE} -->
                    <p class="small text-muted mb-1">
                        {PHP.L.market_vendors_registered}: {VENDOR_ROW_REGDATE}
					</p>
					<!-- ENDIF -->
				</div>
                <div class="card-footer bg-transparent d-flex justify-content-between">
                    <a href="{VENDOR_ROW_SHOWCASE_URL}" class="btn btn-sm btn-primary">
                        {PHP.L.market_vendors_goto_showcase}
					</a>
                    <a href="{VENDOR_ROW_PROFILE_URL}" class="btn btn-sm btn-outline-secondary">
                        {PHP.L.market_vendors_profile}
					</a>
				</div>
			</div>
		</div>
        <!-- END: VENDOR_ROW -->
		
        <!-- BEGIN: VENDOR_EMPTY -->
        <div class="col-12">
            <div class="alert alert-info">{PHP.L.market_vendors_empty}</div>
		</div>
        <!-- END: VENDOR_EMPTY -->
		
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
<!-- IF {PHP.usr.isadmin} AND {TPL_PATH} --> 
<div class="container-fluid px-3 px-lg-5 py-5">
	<div class="alert alert-info" role="alert">
		{TPL_PATH}
	</div>
</div>
<!-- ENDIF -->
<!-- END: MAIN -->