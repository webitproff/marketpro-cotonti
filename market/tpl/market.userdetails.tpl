<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.userdetails.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.userdetails.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.userdetails.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.userdetails.php
	* Назначение:
	*   Шаблон вкладки «Товары» в профиле пользователя (users.php?m=details&tab=market).
	*   Подключается через хук users.details.tags (см. market.userdetails.php).
	*   Отображает:
	*     - список категорий, в которых у пользователя есть товары (табы);
	*     - сетку карточек товаров пользователя;
	*     - кнопку «Загрузить ещё» с AJAX-подгрузкой (без перезагрузки);
	*     - классическую пагинацию;
	*     - кнопку «Добавить товар» (если есть право записи).
	*
	*   AJAX-подгрузка работает через параметр ajax=1 и возвращает JSON:
	*   {rows: "<html>", pagination: "<html>"}.
	*
	* Основные параметры URL:
	*   m=details               — метод модуля users (профиль пользователя);
	*   id=<user_id>            — ID пользователя;
	*   u=<username>            — ник пользователя (для ЧПУ);
	*   tab=market              — активная вкладка «Товары»;
	*   cat=<код категории>     — фильтр по категории товаров;
	*   dmarket=<смещение>      — постраничная навигация (шаг = товаров на страницу);
	*   ajax=1                  — флаг AJAX-запроса (возвращает JSON).
	*
	* Основные теги шаблона:
	*   MARKET_ADD_URL              — URL формы добавления товара;
	*   MARKET_ADD_SHOWBUTTON       — флаг: показывать ли кнопку «Добавить товар»;
	*   MARKET_TAB_URL              — URL вкладки «Товары» текущего пользователя;
	*   MARKET                      — итоговый HTML вкладки (генерируется в PHP);
	*   CAT_ROW                     — блок одной категории (таб):
	*     MARKET_CAT_ROW_TITLE         — название категории (с учётом i18n4marketpro);
	*     MARKET_CAT_ROW_ICON          — путь к иконке категории;
	*     MARKET_CAT_ROW_URL           — URL фильтра по категории;
	*     MARKET_CAT_ROW_COUNT_MARKET  — количество товаров в категории;
	*     MARKET_CAT_ROW_SELECT        — 1, если категория активна (выбрана);
	*   MARKET_ROWS                 — блок одной карточки товара:
	*     MARKET_ROW_*                 — стандартные теги товара (см. cot_generate_markettags());
	*     MARKET_ROW_ADMIN_EDIT        — ссылка «Редактировать» (для владельца/админа);
	*     MARKET_ROW_ADMIN_DELETE      — ссылка «Удалить» с подтверждением (для владельца/админа);
	*     MARKET_ROW_ADMIN_UNVALIDATE  — ссылка «Отправить на модерацию» (для админа);
	*   LOAD_MORE_URL               — URL AJAX-запроса следующей страницы (dmarket=смещение);
	*   LOAD_MORE_PERPAGE           — количество товаров на страницу;
	*   LOAD_MORE_TOTALPAGES        — всего страниц;
	*   LOAD_MORE_CURRENTPAGE       — текущая страница;
	*   LOAD_MORE_LANG              — JSON с локализованными строками для JS:
	*                                 load_more, loading, error;
	*   PAGINATION / PREVIOUS_PAGE / NEXT_PAGE / CURRENT_PAGE / TOTAL_PAGES — пагинация.
	*
	* Родительский шаблон получает (см. market.userdetails.php):
	*   USERS_DETAILS_MARKET_COUNT      — общее количество товаров пользователя;
	*   USERS_DETAILS_MARKET_TAB_URL    — URL вкладки «Товары»;
	*   MARKET_VENDOR_SHOWCASE_URL      — URL витрины продавца (market.vendor.php).
	*
	* Используемые плагины (опционально):
	*   attacher        — вывод изображений товара (att_count / att_display);
	*   i18n4marketpro  — перевод названий категорий на текущий язык.
	*
	* Хуки (в market.userdetails.php):
	*   market.userdetails.query       — модификация условий SQL-запроса (основной и AJAX);
	*   market.userdetails.loop        — внутри цикла вывода товаров (основной и AJAX);
	*   market.userdetails.tags        — перед финальным парсингом шаблона.
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
<div class="card mb-4">
	<div class="card-body">
		<h4 class="d-flex align-items-center mb-4">
			{PHP.L.market_user_products}
			<!-- IF {MARKET_ADD_SHOWBUTTON} -->
			<a href="{MARKET_ADD_URL}" class="btn btn-success ms-auto">
				{PHP.L.market_add_product}
			</a>
			<!-- ENDIF -->
		</h4>
		
		<ul class="nav nav-tabs mb-4">
			<li class="nav-item">
				<a class="nav-link" href="{PHP.urr.user_id|cot_url('users', 'm=details&id=$this&tab=market')}">
					{PHP.L.All}
				</a>
			</li>
			<!-- BEGIN: CAT_ROW -->
			<li class="nav-item <!-- IF {MARKET_CAT_ROW_SELECT} -->active<!-- ENDIF -->">
				<a class="nav-link <!-- IF {MARKET_CAT_ROW_SELECT} -->active<!-- ENDIF -->" href="{MARKET_CAT_ROW_URL}">
					<!-- IF {MARKET_CAT_ROW_ICON} -->
					<img src="{MARKET_CAT_ROW_ICON}" alt="{MARKET_CAT_ROW_TITLE}" class="me-1">
					<!-- ENDIF -->
					{MARKET_CAT_ROW_TITLE}
					<span class="badge bg-dark ms-1">{MARKET_CAT_ROW_COUNT_MARKET}</span>
				</a>
			</li>
			<!-- END: CAT_ROW -->
		</ul>
		
	</div>
</div>
<div class="row row-cols-1 row-cols-xl-3 row-cols-lg-2 row-cols-md-1 g-3 g-lg-4" id="market-items-container">
	<!-- BEGIN: MARKET_ROWS -->
	<div class="col">
		<div class="card h-100 border-0 shadow-sm overflow-hidden blog-card">
			<div class="row g-0 flex-lg-row">
				<div class="col-12">
					<div class="ratio ratio-4x3 ratio-lg-1x1 image-container">
						<!-- IF {PHP|cot_plugin_active('attacher')} -->
						<!-- IF {MARKET_ROW_ID|att_count('market', $this, '', 'images')} > 0 --> 
						{MARKET_ROW_ID|att_display('market',$this,'','attacher.display.marketlistfirst','images',1)}
						<!-- ELSE -->
						<img src="{PHP.R.page_default_image}" class="card-img object-fit-cover" alt="{PAGE_TITLE}">
						<!-- ENDIF -->
						<!-- ELSE -->
						<img src="{PHP.R.page_default_image}" class="card-img object-fit-cover" alt="{PAGE_TITLE}">
						<!-- ENDIF --> 
					</div>
				</div>
				<div class="col-12">
					<div class="card-body d-flex flex-column h-100 p-4">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<span class="badge bg-info-subtle text-info px-2 py-1">{MARKET_ROW_HITS}</span><span class="badge bg-info-subtle text-info px-2 py-1">{MARKET_ROW_CREATED}</span>
							<!-- IF {PHP.usr.isadmin} OR {PHP.usr.id} === {MARKET_ROW_OWNER_ID} -->
							<div class="dropdown">
								<button class="btn btn-outline-warning btn-lg rounded-circle d-flex align-items-center justify-content-center shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px;height:32px;">
									<i class="fa-solid fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-end border shadow-sm py-2" style="min-width:280px;">
									<!-- IF {MARKET_ROW_ADMIN_EDIT} -->
									<li>
										<a class="dropdown-item py-2 px-4" 
										href="{MARKET_ROW_ADMIN_EDIT_URL}">
											{PHP.L.Edit}
										</a>
									</li>
									<!-- ENDIF -->
									<!-- IF {MARKET_ROW_ADMIN_DELETE} -->
									<li>
										<a class="dropdown-item py-2 px-4" 
										href="{MARKET_ROW_ADMIN_DELETE_URL}">
											{PHP.L.Delete}
										</a>
									</li>
									<!-- ENDIF -->
									<!-- IF {MARKET_ROW_ADMIN_UNVALIDATE} -->
									<li>
										<a class="dropdown-item py-2 px-4" 
										href="{MARKET_ROW_ADMIN_UNVALIDATE_URL}">
											{PHP.L.Putinvalidationqueue}
										</a>
									</li>
									<!-- ENDIF -->
								</ul>
							</div>
							<!-- ENDIF -->
						</div>
						<h5 class="card-title mb-2">
							<a href="{MARKET_ROW_URL}" class="text-decoration-none">{MARKET_ROW_TITLE}</a>
						</h5>
						<!-- IF {MARKET_ROW_DESCRIPTION} -->
						<div class="card-text text-muted small flex-grow-1">
							{MARKET_ROW_DESCRIPTION}
						</div>
						<!-- ELSE -->
						<div class="card-text text-muted small flex-grow-1">
							{MARKET_ROW_TEXT_SHORT}
						</div>
						<!-- ENDIF -->
						<!-- IF {MARKET_ROW_COMMENTS_COUNT} > 0 -->
						<div class="position-absolute top-0 end-0 mt-2 me-2" data-bs-toggle="tooltip" data-bs-title="{PHP.L.2wd_Comments}">
							<span class="badge bg-primary">{MARKET_ROW_COMMENTS_COUNT}</span>
						</div>
						<!-- ENDIF -->								
						<div class="d-flex align-items-center small text-muted mt-3">
							
							<!-- IF {MARKET_ROW_COSTDFLT} > 0 -->
							<span class="ms-2 text-success fw-bold">{MARKET_ROW_COSTDFLT} {PHP.cfg.market.currency}</span>
							<!-- ENDIF -->
							<span class="mx-2">{MARKET_ROW_CAT_TITLE}</span>
						</div>
						<div class="mt-3 text-end">
							<a href="{MARKET_ROW_URL}" class="btn btn-sm btn-outline-primary text-uppercase">{PHP.L.ReadMore}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> 
	<!-- END: MARKET_ROWS -->
</div>

<!-- IF {LOAD_MORE_TOTALPAGES} > {LOAD_MORE_CURRENTPAGE} -->
<div class="text-center mt-5 load-more-container" id="load-more-container">
    <button class="btn btn-primary" id="load-more-products" 
	data-url="{LOAD_MORE_URL}" 
	data-perpage="{LOAD_MORE_PERPAGE}" 
	data-total="{LOAD_MORE_TOTALPAGES}" 
	data-page="{LOAD_MORE_CURRENTPAGE}">{PHP.L.market_load_more}</button>
</div>
<!-- ENDIF -->

<div id="pagination-block">
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


<script>
	var marketLang = {LOAD_MORE_LANG};
</script>

<script>
	$(document).ready(function() {
		var loadMoreBtn = $('#load-more-products');
		if (loadMoreBtn.length === 0) return;
		
		var container = $('#market-items-container');
		var loading = false;
		var currentPage = parseInt(loadMoreBtn.data('page'));
		var totalPages = parseInt(loadMoreBtn.data('total'));
		var perpage = parseInt(loadMoreBtn.data('perpage'));
		var baseUrl = loadMoreBtn.data('url');
		
		function updateButtonText() {
			if (currentPage < totalPages) {
				var nextPage = currentPage + 1;
				var text = marketLang.load_more.replace('%d', nextPage).replace('%d', totalPages);
				loadMoreBtn.text(text);
				} else {
				loadMoreBtn.hide();
			}
		}
		
		updateButtonText();
		
		loadMoreBtn.click(function(e) {
			e.preventDefault();
			if (loading || currentPage >= totalPages) return;
			
			loading = true;
			var originalText = loadMoreBtn.text();
			loadMoreBtn.prop('disabled', true).html(marketLang.loading);
			
			var nextOffset = currentPage * perpage;
			var url = baseUrl + '&dmarket=' + nextOffset;
			
			$.getJSON(url, function(data) {
				if (data.rows.trim() === '') {
					loadMoreBtn.hide();
					return;
				}
				container.append(data.rows);
				$('#pagination-block').html(data.pagination);
				currentPage++;
				loadMoreBtn.data('page', currentPage);
				
				loadMoreBtn.prop('disabled', false);
				updateButtonText();
				
				if (currentPage >= totalPages) {
					loadMoreBtn.hide();
				}
				loading = false;
				}).fail(function() {
				loading = false;
				loadMoreBtn.prop('disabled', false).text(originalText);
				alert(marketLang.error);
			});
		});
	});
</script>
<!-- END: MAIN -->
