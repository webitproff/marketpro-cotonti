<!-- BEGIN: MAIN -->

<div class="mb-3">
	<div class="row g-2">
		<div class="col-12 col-lg-3">
			<a href="{ADMIN_MARKET_URL_CONFIG}" class="btn btn-outline-secondary w-100">
			<i class="fa-solid fa-gear me-1"></i>{PHP.L.Configuration} </a>
		</div>
		<div class="col-12 col-lg-3">
			<a href="{ADMIN_MARKET_URL_STRUCTURE}" class="btn btn-outline-secondary w-100">
			<i class="fa-solid fa-list-ul me-1"></i>{PHP.L.Categories} </a>
		</div>
		<div class="col-12 col-lg-3">
			<a href="{ADMIN_MARKET_URL_EXTRAFIELDS}" class="btn btn-outline-secondary w-100">
			<i class="fa-solid fa-table-columns me-1"></i>{PHP.L.Extrafields} </a>
		</div>
		<div class="col-12 col-lg-3">
			<a href="{ADMIN_MARKET_URL_ADD}" class="btn btn-outline-primary w-100">
			<i class="fa-solid fa-plus me-1"></i>{PHP.L.market_addtitle} </a>
		</div>
	</div>
</div> 
{FILE "{PHP.cfg.themes_dir}/admin/{PHP.cfg.admintheme}/warnings.tpl"}
<hr>
<!-- IF {CATTITLE} -->
<div class="alert alert-info my-2" role="alert">
	{PHP.L.Category}: {CATTITLE}
</div>
<!-- ENDIF -->
<div class="card filter-section p-3 mb-4" style="border: 5px var(--bs-dark-border-subtle) solid">
	<!-- IF {ADMIN_MARKET_TOTALDBITEMS} -->
	<form name="form_valqueue" method="get" action="{SEARCH_ACTION_URL}" class="mb-3">
		<!-- IF !{PHP|cot_plugin_active('urleditor')} OR {PHP.cfg.plugin.urleditor.preset} != 'handy' -->
		<input type="hidden" name="m" value="market" />
		<!-- ENDIF -->
		<div class="row g-2 align-items-end">
			<div class="col-12 col-lg-3 d-flex flex-column h-100">
				<label class="form-label">{PHP.L.Search}</label>
				<div class="flex-grow-1">{SEARCH_SQ}</div>
			</div>
			<div class="col-12 col-lg-3 d-flex flex-column h-100">
				<label class="form-label">{PHP.L.Category}</label>
				<div class="flex-grow-1 filterSelect">{SEARCH_CAT_SELECT2}</div>
			</div>
			<div class="col-12 col-lg-3 d-flex flex-column h-100">
				<label class="form-label">{PHP.L.cotcp_select_filter_options}</label>
				<div class="flex-grow-1">{ADMIN_MARKET_FILTER}</div>
			</div>
			<!-- IF {TOTAL_ENTRIES} > 1 -->
			<div class="col-12 col-lg-3 d-flex flex-column h-100">
				<label class="form-label">{PHP.L.adm_sort}</label>
				<div class="flex-grow-1">{ADMIN_MARKET_ORDER}</div>
			</div>
			<div class="col-12 col-lg-3 d-flex flex-column h-100">
				<label class="form-label">{PHP.L.cotcp_select_filter_sorting_direction}</label>
				<div class="flex-grow-1">{ADMIN_MARKET_WAY}</div>
			</div>
			<!-- ENDIF -->
			<div class="col-12 col-lg-3 d-flex flex-column h-100">
				<button type="submit" class="btn btn-outline-primary w-100 mt-auto">
				<i class="fa-solid fa-filter me-1"></i>{PHP.L.Filter} </button>
			</div>
			<div class="col-12 col-lg-3 d-flex flex-column h-100">
				<a class="btn btn-outline-danger w-100 mt-auto" href="{PHP|cot_url('admin','m=market')}"><i class="fa-solid fa-broom me-1"></i>{PHP.L.Prune} </a>
			</div>
		</div>
	</form>
	<!-- ENDIF -->
</div>
<div class="mb-3">
	<form id="form_valqueue" name="form_valqueue" method="post" action="{ADMIN_MARKET_FORM_URL}">
		<div class="list-group list-group-flush">
			<div class="list-group-item list-group-item-dark">
				<div class="row align-items-center fw-bold">
					<div class="col-1"></div>
					<div class="col-1">{PHP.L.Id}</div>
					<div class="col-md-2">{PHP.L.Status}</div>
					<div class="col-md-5">{PHP.L.Title}</div>
					<div class="col-md-3">{PHP.L.Action}</div>
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
					</div>
					<div class="col-md-5">
						<div id="mor_{PHP.ii}" class="mor_info_on_off overflow-x-auto" style="max-width: 675px;">
							<span class="fw-bold" style="cursor: pointer;">{ADMIN_MARKET_TITLE}</span>
							<!-- IF {ADMIN_MARKET_DESCRIPTION} -->
							<div class="text-muted small">{ADMIN_MARKET_DESCRIPTION}</div>
							<!-- ENDIF -->
							<div class="moreinfo collapse">
								<hr class="my-2" />
								<strong>{ADMIN_MARKET_UPDATED}</strong> 
								<!-- IF {ADMIN_MARKET_TEXT} -->
								<div class="mt-2">
									<strong>{PHP.L.Text}:</strong>
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
							<i class="fa-solid fa-check me-1"></i>{PHP.L.Validate} </a>
							<!-- ENDIF -->
							<a href="{ADMIN_MARKET_URL_FOR_DELETED}" class="btn btn-sm btn-outline-danger confirmLink">
							<i class="fa-solid fa-trash me-1"></i>{PHP.L.Delete} </a>
							<a href="{ADMIN_MARKET_ID_URL}" target="_blank" class="btn btn-sm btn-primary">
							<i class="fa-solid fa-eye me-1"></i>{PHP.L.Open} </a>
							<a href="{ADMIN_MARKET_URL_FOR_EDIT}" target="_blank" class="btn btn-sm btn-outline-secondary">
							<i class="fa-solid fa-pen me-1"></i>{PHP.L.Edit} </a>
						</div>
					</div>
				</div>
			</div>
			<!-- END: MARKET_ROW -->
			<!-- IF !{TOTAL_ENTRIES} -->
			<div class="list-group-item text-center">{PHP.L.None}</div>
			<!-- ELSE -->
			<div class="list-group-item mt-4">
				<div class="d-flex gap-2">
					<!-- IF {PHP.filter} != 'validated' -->
					<button name="paction" type="submit" value="validate" class="btn btn-success confirm">
					<i class="fa-solid fa-check me-1"></i>{PHP.L.Validate} </button>
					<!-- ENDIF -->
					<button name="paction" type="submit" value="delete" class="btn btn-danger confirm">
					<i class="fa-solid fa-trash me-1"></i>{PHP.L.Delete} </button>
				</div>
			</div>
			<!-- ENDIF -->
		</div>
	</form>
</div>
<!-- IF {TOTAL_ENTRIES} -->
<nav aria-label="Market Pagination" class="mt-3">
	<div class="text-center mb-2">{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</div>
	<ul class="pagination justify-content-center">{PREVIOUS_PAGE} {PAGINATION} {NEXT_PAGE}</ul>
</nav>
<!-- ENDIF -->

<style scoped>
	.filterSelect select {
	width: 100%;
	}
</style>

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
<div class="alert alert-success" role="alert">
	public_html/ 
	modules/ 
	market/ 
	tpl/ 
	market.admin.tpl
</div>
<!-- END: MAIN -->