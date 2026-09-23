<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.admin.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.admin.tpl
	* Recommended path to the file: _ _ _ _ themes/admin/{admintheme}/modules/market/market.admin.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.admin.php
	* Назначение:
	*   Административная панель модуля Market PRO. Открывается в разделе
	*   управления товарами (admin.php?m=market). Отображает список товаров
	*   с поиском, сортировкой, фильтром по статусу и категории, пагинацией.
	*   Позволяет выполнять массовые операции (утвердить, удалить выбранные)
	*   и индивидуальные действия над каждым товаром (утвердить, снять с
	*   публикации, удалить, открыть на сайте, редактировать). Показывает
	*   общее количество товаров, количество на текущей странице и число
	*   товаров, ожидающих модерации. Содержит ссылки на настройки модуля,
	*   управление структурой категорий и extrafields.
	*
	*   ПРИМЕР https://abuyfile.com/ru/admin/market
	*
	* Основные параметры URL:
	*   m=market                — админ-раздел модуля Market;
	*   a=<действие>            — validate, unvalidate, delete, update_checked;
	*   id=<ID товара>          — для индивидуальных действий;
	*   filter=<статус>         — all, valqueue, validated, drafts, expired;
	*   sorttype=<поле>         — поле сортировки (без префикса fieldmrkt_);
	*   sortway=<asc|desc>      — направление сортировки;
	*   sq=<запрос>             — поисковый запрос;
	*   search_in=<область>     — title, full, pcod;
	*   c=<код категории>       — фильтр по категории;
	*   d=<страница>            — номер страницы пагинации.
	*
	* Основные теги шаблона:
	*   Навигация и панель управления:
	*     ADMIN_MARKET_URL_CONFIG          — URL настроек модуля;
	*     ADMIN_MARKET_URL_STRUCTURE       — URL управления структурой категорий;
	*     ADMIN_MARKET_URL_EXTRAFIELDS     — URL управления extrafields модуля;
	*     ADMIN_MARKET_URL_ADD             — URL добавления нового товара (в новой вкладке).
	*
	*   Форма фильтра (GET):
	*     ADMIN_MARKET_SEARCH_ACTION_URL   — URL действия формы поиска;
	*     ADMIN_MARKET_SEARCH_SQ           — поле ввода поискового запроса;
	*     ADMIN_MARKET_SEARCH_CAT_SELECT2  — Select2 с категориями;
	*     ADMIN_MARKET_FILTER              — selectbox выбора фильтра по статусу;
	*     ADMIN_MARKET_ORDER               — selectbox поля сортировки;
	*     ADMIN_MARKET_WAY                 — selectbox направления сортировки;
	*     ADMIN_MARKET_SEARCH_RESULT_MSG   — сообщение о результатах поиска.
	*
	*   Массовые операции (POST):
	*     ADMIN_MARKET_FORM_URL            — URL action формы массовых операций;
	*     ADMIN_MARKET_TOTALDBITEMS        — общее число записей в таблице cot_market;
	*     ADMIN_MARKET_ON_PAGE             — число товаров на текущей странице.
	*
	*   Одна строка товара (BEGIN: MARKET_ROW):
	*     ADMIN_MARKET_ID                  — ID товара;
	*     ADMIN_MARKET_LOCAL_STATUS        — локализованный статус (published/pending/draft);
	*     ADMIN_MARKET_TITLE               — название товара;
	*     ADMIN_MARKET_DESCRIPTION         — краткое описание;
	*     ADMIN_MARKET_TEXT                — полный текст (в раскрывающемся блоке);
	*     ADMIN_MARKET_UPDATED             — дата обновления;
	*     ADMIN_MARKET_ID_URL              — URL товара на сайте (просмотр);
	*     ADMIN_MARKET_URL_FOR_VALIDATED   — ссылка «Утвердить» (с подтверждением);
	*     ADMIN_MARKET_URL_FOR_UNVALIDATE  — ссылка «Снять с публикации» (с подтверждением);
	*     ADMIN_MARKET_URL_FOR_DELETED     — ссылка «Удалить» (с подтверждением);
	*     ADMIN_MARKET_URL_FOR_EDIT        — ссылка «Редактировать»;
	*     ADMIN_MARKET_ODDEVEN             — odd/even для zebra-стилизации;
	*     ADMIN_MARKET_CAT_COUNT           — число товаров в подкатегориях;
	*     ADMIN_MARKET_OWNER[_*]           — теги владельца (cot_build_user / cot_generate_usertags);
	*     ADMIN_PAGE_MULTICATS             — доп. категории товара (плагин multicatmarket).
	*
	*   Пагинация и сводка:
	*     PAGINATION / PREVIOUS_PAGE / NEXT_PAGE — пагинация;
	*     TOTAL_ENTRIES                    — всего записей по фильтру;
	*     ENTRIES_ON_CURRENT_PAGE          — записей на текущей странице;
	*     CURRENT_PAGE / TOTAL_PAGES       — текущая и всего страниц.
	*
	*   Подсветка поиска:
	*     ADMIN_SEARCH_HIGHLIGHT_ACTIVE    — флаг: показывать ли CSS/JS подсветки;
	*     ADMIN_SEARCH_HIGHLIGHT_WORDS     — JSON-массив слов;
	*     ADMIN_SEARCH_HIGHLIGHT_SCOPE     — CSS-селектор области подсветки.
	*
	*   Прочее:
	*     TPL_PATH                         — путь к файлу шаблона (для отладки);
	*     PHP.filter                       — текущий фильтр (для условного показа кнопки «Утвердить»);
	*     PHP.ii / PHP.row.fieldmrkt_state — служебные переменные строки.
	*
	*   JS:
	*     Кнопка #toggleAllCheckboxes — «Выбрать все / Снять все» чекбоксы;
	*     Кнопки .confirm             — подтверждение перед массовой операцией
	*                                   (validate → market_confirm_validate,
	*                                    delete   → market_confirm_delete);
	*     Раскрытие .moreinfo по клику на .mor_info_on_off (jQuery slideToggle);
	*     Подсветка найденных слов внутри ADMIN_SEARCH_HIGHLIGHT_SCOPE.
	*
	* Используемые плагины (опционально):
	*   multicatmarket — доп. категории товара (ADMIN_PAGE_MULTICATS);
	*   attacher       — счётчик/превью изображений товара в списке;
	*   urleditor      — влияет на скрытое поле m=market в форме поиска.
	*
	* Хуки (в market.admin.php):
	*   market.admin.first              — в начале страницы;
	*   market.admin.validate           — перед утверждением товара;
	*   market.admin.validate.done      — после утверждения товара;
	*   market.admin.unvalidate         — перед снятием с публикации;
	*   market.admin.delete             — перед удалением товара;
	*   market.admin.delete.done        — после удаления товара;
	*   market.admin.checked_validate   — для каждого товара при массовом утверждении;
	*   market.admin.checked_delete     — для каждого товара при массовом удалении;
	*   market.admin.loop               — внутри цикла вывода строк товаров;
	*   market.admin.tags               — перед финальным парсингом шаблона.
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
	<div class="mb-3">
		<div class="row g-2">
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_MARKET_URL_CONFIG}" class="btn btn-outline-secondary w-100">
				<i class="fa-solid fa-gear me-1"></i>{PHP.L.adm_market_configuration} </a>
			</div>
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_MARKET_URL_STRUCTURE}" class="btn btn-outline-secondary w-100">
				<i class="fa-solid fa-list-ul me-1"></i>{PHP.L.adm_market_categories} </a>
			</div>
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_MARKET_URL_EXTRAFIELDS}" class="btn btn-outline-secondary w-100">
				<i class="fa-solid fa-table-columns me-1"></i>{PHP.L.adm_market_extrafields} </a>
			</div>
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_MARKET_URL_ADD}" class="btn btn-outline-primary w-100" target="_blank">
				<i class="fa-solid fa-plus me-1"></i>{PHP.L.market_goto_add_new_item_title} </a>
			</div>
		</div>
	</div> 
	
	{FILE "{PHP.cfg.themes_dir}/admin/{PHP.cfg.admintheme}/warnings.tpl"}
	
	<div class="card filter-section p-3 mb-4" style="border: 5px var(--bs-dark-border-subtle) solid">
		<!-- IF {ADMIN_MARKET_TOTALDBITEMS} == '0' -->
		<div class="alert alert-warning mb-3" role="alert">
			{PHP.L.market_no_products}
		</div>
		<!-- ENDIF --> 
		<form name="form_valqueue" method="get" action="{ADMIN_MARKET_SEARCH_ACTION_URL}" class="mb-3">
			<!-- IF !{PHP|cot_plugin_active('urleditor')} OR {PHP.cfg.plugin.urleditor.preset} != 'handy' -->
			<input type="hidden" name="m" value="market" />
			<!-- ENDIF -->
			<div class="row g-2 align-items-end mb-3">
				<div class="col-12 col-lg-4 d-flex flex-column h-100">
					<label class="form-label">{PHP.L.adm_market_search}</label>
					<div class="flex-grow-1">{ADMIN_MARKET_SEARCH_SQ}</div>
				</div>
				<div class="col-12 col-lg-4 d-flex flex-column h-100">
					<label class="form-label">{PHP.L.adm_market_category}</label>
					<div class="flex-grow-1 filterSelect">{ADMIN_MARKET_SEARCH_CAT_SELECT2}</div>
				</div>
				<div class="col-12 col-lg-4 d-flex flex-column h-100">
					<label class="form-label">{PHP.L.adm_market_select_status_publication}</label>
					<div class="flex-grow-1">{ADMIN_MARKET_FILTER}</div>
				</div>
				
				<div class="col-12">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="search_in" id="admin_search_in_title" value="title" <!-- IF {PHP.search_in} == '' OR {PHP.search_in} == 'title' -->checked="checked"<!-- ENDIF -->>
						<label class="form-check-label" for="admin_search_in_title">{PHP.L.market_search_in_title}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="search_in" id="admin_search_in_full" value="full" <!-- IF {PHP.search_in} == 'full' -->checked="checked"<!-- ENDIF -->>
						<label class="form-check-label" for="admin_search_in_full">{PHP.L.market_search_in_title_and_descr}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="search_in" id="admin_search_in_pcod" value="pcod" <!-- IF {PHP.search_in} == 'pcod' -->checked="checked"<!-- ENDIF -->>
						<label class="form-check-label" for="admin_search_in_pcod">{PHP.L.market_search_in_pcod}</label>
					</div>
				</div>
				
				<div class="col-12 col-lg-3 d-flex flex-column h-100">
					<label class="form-label">{PHP.L.adm_market_sort}</label>
					<div class="flex-grow-1">{ADMIN_MARKET_ORDER}</div>
				</div>
				<div class="col-12 col-lg-3 d-flex flex-column h-100">
					<label class="form-label">{PHP.L.adm_market_select_filter_sorting_direction}</label>
					<div class="flex-grow-1">{ADMIN_MARKET_WAY}</div>
				</div>
			</div>
			<div class="row g-2 align-items-end">
				<div class="col-12 col-lg-6 d-flex flex-column h-100">
					<button type="submit" class="btn btn-outline-primary w-100 mt-auto">
						<i class="fa-solid fa-filter me-1"></i>{PHP.L.adm_market_filter}
					</button>
				</div>
				<div class="col-12 col-lg-6 d-flex flex-column h-100">
					<a class="btn btn-outline-danger w-100 mt-auto" href="{PHP|cot_url('admin','m=market')}">
						<i class="fa-solid fa-broom me-1"></i>{PHP.L.adm_market_prune}
					</a>
				</div>
			</div>
		</form>
		
		<!-- ========== СООБЩЕНИЕ О РЕЗУЛЬТАТАХ ПОИСКА ========== -->
		<!-- IF {ADMIN_MARKET_SEARCH_RESULT_MSG} -->
		<div class="alert alert-info" role="alert">
			{ADMIN_MARKET_SEARCH_RESULT_MSG}
		</div>
		<!-- ENDIF -->
		
	</div>
	<div class="mb-3">
		<form id="form_valqueue" name="form_valqueue" method="post" action="{ADMIN_MARKET_FORM_URL}">
			<div class="list-group list-group-flush" id="admin-market-items-container">
				<div class="list-group-item list-group-item-dark">
					<div class="row align-items-center fw-bold">
						<div class="col-1"></div>
						<div class="col-1">{PHP.L.adm_market_id}</div>
						<div class="col-md-2">{PHP.L.adm_market_status}</div>
						<div class="col-md-5">{PHP.L.adm_market_title}</div>
						<div class="col-md-3">{PHP.L.adm_market_action}</div>
					</div>
				</div>
				<!-- BEGIN: MARKET_ROW -->
				
				<div class="list-group-item list-group-item-action">
					<div class="row align-items-center">
						<div class="col-1 text-center">
							<input type="checkbox" name="s[{ADMIN_MARKET_ID}]" class="form-check-input checkbox" />
						</div>
						<div class="col-1">{ADMIN_MARKET_ID}</div>
						<div class="col-md-2">{ADMIN_MARKET_LOCAL_STATUS}
							<!-- IF {PHP|cot_plugin_active('multicatmarket')} -->
							<div class="text-muted small">{ADMIN_PAGE_MULTICATS}</div>
							<!-- ENDIF -->	
							<!-- IF {PHP|cot_plugin_active('attacher')} -->
							<!-- IF {ADMIN_MARKET_ID|att_count('market', $this, '', 'images')} > 0 -->{ADMIN_MARKET_ID|att_count('market', $this, '', 'images')}
							<div class="att-image">{ADMIN_MARKET_ID|att_display('market',$this,'','attacher.display.admin.list','images',1)}</div>
							<!-- ELSE -->
							<img src="themes/index36/img/small-logo.webp" class="img-thumbnail" width="96" height="96">
							<!-- ENDIF -->
							<!-- ENDIF -->						
						</div>
						<div class="col-md-5">
							<div id="mor_{PHP.ii}" class="mor_info_on_off overflow-x-auto" style="max-width: 675px;">
								<span class="fw-bold card-title" style="cursor: pointer;">{ADMIN_MARKET_TITLE}</span>
								<!-- IF {ADMIN_MARKET_DESCRIPTION} -->
								<div class="text-muted small">{ADMIN_MARKET_DESCRIPTION}</div>
								<!-- ENDIF -->
								<div class="moreinfo collapse">
									<hr class="my-2" />
									<strong>{ADMIN_MARKET_UPDATED}</strong> 
									<!-- IF {ADMIN_MARKET_TEXT} -->
									<div class="mt-2">
										<strong>{PHP.L.adm_market_text}:</strong>
										<div>{ADMIN_MARKET_TEXT}</div>
									</div>
									<!-- ENDIF -->
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="d-flex flex-wrap gap-1">
								<!-- IF {PHP.row.fieldmrkt_state} == 1 -->
								<a href="{ADMIN_MARKET_URL_FOR_VALIDATED}" class="btn btn-sm btn-outline-success confirmLink">
								<i class="fa-solid fa-check me-1"></i>{PHP.L.adm_market_validate} </a>
								<!-- ENDIF -->
								<a href="{ADMIN_MARKET_URL_FOR_DELETED}" class="btn btn-sm btn-outline-danger confirmLink">
								<i class="fa-solid fa-trash me-1"></i>{PHP.L.adm_market_delete} </a>
								<a href="{ADMIN_MARKET_ID_URL}" target="_blank" class="btn btn-sm btn-primary">
								<i class="fa-solid fa-eye me-1"></i>{PHP.L.adm_market_open} </a>
								<a href="{ADMIN_MARKET_URL_FOR_EDIT}" target="_blank" class="btn btn-sm btn-outline-secondary">
								<i class="fa-solid fa-pen me-1"></i>{PHP.L.adm_market_edit} </a>
							</div>
						</div>
					</div>
				</div>
				<!-- END: MARKET_ROW -->
				
				
				<!-- IF !{TOTAL_ENTRIES} -->
				<div class="list-group-item text-center">{PHP.L.adm_market_none}</div>
				<!-- ELSE -->
				<div class="list-group-item mb-4">
					<button type="button" id="toggleAllCheckboxes" class="btn btn-outline-primary mt-2">
						{PHP.L.market_adm_select_all}
					</button>
				</div>
				<div class="list-group-item mb-4">
					<div class="d-flex gap-2">
						<!-- IF {PHP.filter} != 'validated' -->
						<button name="paction" type="submit" value="validate" class="btn btn-success confirm">
						<i class="fa-solid fa-check me-1"></i>{PHP.L.adm_market_validate} </button>
						<!-- ENDIF -->
						<button name="paction" type="submit" value="delete" class="btn btn-danger confirm">
						<i class="fa-solid fa-trash me-1"></i>{PHP.L.adm_market_delete} </button>
					</div>
				</div>
				<!-- ENDIF -->
				
			</div>
		</form>
	</div>
	<!-- IF {PAGINATION} -->
	<nav aria-label="Market Pagination" class="mb-3">
		<div class="text-center mb-2">{PHP.L.adm_market_total}: {TOTAL_ENTRIES}, {PHP.L.adm_market_onpage}: {ENTRIES_ON_CURRENT_PAGE}</div>
		<ul class="pagination justify-content-center">{PREVIOUS_PAGE} {PAGINATION} {NEXT_PAGE}</ul>
	</nav>
	<!-- ENDIF -->
</div>
<style scoped>
	.filterSelect select {
	width: 100%;
	}
</style>
<script>
	(function() {
		var toggleBtn = document.getElementById('toggleAllCheckboxes');
		if (!toggleBtn) return;
		
		var checkboxes = document.querySelectorAll('input[type=checkbox][name^="s["]');
		
		function updateButtonText() {
			var allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(function(cb) { return cb.checked; });
			toggleBtn.textContent = allChecked ? '{PHP.L.market_adm_deselect_all}' : '{PHP.L.market_adm_select_all}';
		}
		
		updateButtonText();
		
		toggleBtn.addEventListener('click', function() {
			var allChecked = Array.from(checkboxes).every(function(cb) { return cb.checked; });
			checkboxes.forEach(function(cb) { cb.checked = !allChecked; });
			updateButtonText();
		});
		
		checkboxes.forEach(function(cb) {
			cb.addEventListener('change', updateButtonText);
		});
	})();
</script>
<script>
	document.addEventListener('DOMContentLoaded', () => {
		$('.moreinfo').hide();
		$('.mor_info_on_off').click(function () {
			let $this = $(this);
			$this.find('.moreinfo').slideToggle(100);
		});
		
		let submitButtons = document.querySelectorAll('.confirm');
		let form = document.getElementById('form_valqueue');
		submitButtons.forEach(function (elem) {
			elem.addEventListener('click', function (e) {
				let checkedCnt = form.querySelectorAll('input[type=checkbox]:checked').length;
				if (checkedCnt < 1) {
					e.preventDefault();
					return false;
				}
				
				let message = 'Are you sure?';
				switch (this.value) {
					case 'delete':
					message = '{PHP.L.market_confirm_delete}';
					break;
					case 'validate':
					message = '{PHP.L.market_confirm_validate}';
					break;
				}
				
				if (!confirm(message)) {
					e.preventDefault();
				}
			});
		});
	});
</script>

<!-- IF {ADMIN_SEARCH_HIGHLIGHT_ACTIVE} -->
<style>
    .search-highlight {
	font-weight: bold;
	letter-spacing: 1px;
	padding: 2px;
	color: #000 !important;
	background-color: #ffc107 !important;
	border-radius: 5px;
    }
</style>

<script>
	try {
		function highlightWords(node, regex, excludeElements) {
			if (node === null) return;
			excludeElements || (excludeElements = ['script', 'style', 'iframe', 'canvas', 'pre']);
			let child = node.firstChild;
			const callback = function(match) {
				let span = document.createElement('mark');
				span.className = 'search-highlight';
				span.textContent = match;
				return span;
			};
			while (child) {
				switch (child.nodeType) {
					case 1:
                    if (excludeElements.indexOf(child.tagName.toLowerCase()) > -1) break;
                    highlightWords(child, regex, excludeElements);
                    break;
					case 3:
                    let bk = 0;
                    child.data.replace(regex, function(all) {
                        let args = [].slice.call(arguments);
                        let offset = args[args.length - 2];
                        let newTextNode = child.splitText(offset + bk);
                        let tag;
                        bk -= child.data.length + all.length;
                        newTextNode.data = newTextNode.data.substring(all.length);
                        tag = callback.apply(window, [args[0]]);
                        child.parentNode.insertBefore(tag, newTextNode);
                        child = newTextNode;
					});
                    regex.lastIndex = 0;
                    break;
				}
				child = child.nextSibling;
			}
		}
		
		document.addEventListener('DOMContentLoaded', function() {
			var words = {ADMIN_SEARCH_HIGHLIGHT_WORDS};
			var scope = '{ADMIN_SEARCH_HIGHLIGHT_SCOPE}';
			if (words && Array.isArray(words) && words.length && scope) {
				var escapedWords = words.map(function(w) {
					if (typeof w !== 'string') return '';
					return w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
				}).filter(function(w) { return w.length > 0; });
				if (escapedWords.length === 0) return;
				var regex = new RegExp(escapedWords.join('|'), 'gi');
				var elements = document.querySelectorAll(scope);
				elements.forEach(function(el) {
					highlightWords(el, regex);
				});
			}
		});
		} catch (e) {
		console.error('Ошибка подсветки, поиск продолжает работать:', e);
	}
</script>
<!-- ENDIF -->

<!-- IF {TPL_PATH} --> 
<div class="container-xxl px-3 px-lg-5 py-5">
	<div class="alert alert-info" role="alert">
		{TPL_PATH}
	</div>
</div>
<!-- ENDIF -->

<!-- END: MAIN -->