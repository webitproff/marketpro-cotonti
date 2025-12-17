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
		
		<hr>
		
		<div class="row g-4">
			<!-- BEGIN: MARKET_ROWS -->
			<div class="col-12 col-md-6 col-xl-4">
				<div class="attacherPicIntList-card" style="background-color: var(--bs-sidebar-bg)">
					<a class="attacherPicIntList-thumbnail" data-fancybox="gallery" href="{MARKET_ROW_URL}" data-caption="{MARKET_ROW_TITLE}">
						<!-- IF {PHP|cot_plugin_active('attacher')} -->
						<!-- IF {MARKET_ROW_ID|att_count('market', $this, '', 'images')} > 0 -->
						<div class="att-image">{MARKET_ROW_ID|att_display('market',$this,'','attacher.display.indexmarketlist','images',1)}</div>
						<!-- ELSE -->
						<img src="{PHP.R.page_default_image}" alt="{MARKET_ROW_TITLE}">
						<!-- ENDIF -->
						<!-- ELSE -->
						<img src="{PHP.R.page_default_image}" alt="{MARKET_ROW_TITLE}">
						<!-- ENDIF -->
					</a>
					<div class="attacherPicIntList-card-body">
						<div class="attacherPicIntList-title">
							<a href="{MARKET_ROW_URL}" class="text-decoration-none" title="{MARKET_ROW_TITLE}">{MARKET_ROW_TITLE|cot_string_truncate($this, '64')}</a>
						</div>
						<div class="attacherPicIntList-desc">{MARKET_ROW_CAT_TITLE}
							<!-- IF {MARKET_ROW_COSTDFLT} > 0 -->
							<span class="ms-2 text-success fw-bold">{MARKET_ROW_COSTDFLT} {PHP.cfg.market.currency}</span>
							<!-- ENDIF -->
						</div>
					</div>
				</div>
			</div>
			<!-- END: MARKET_ROWS -->
		</div>
		
		<!-- IF {PAGENAV_COUNT} > 0 -->
		<nav aria-label="pagination" class="mt-4">
			<ul class="pagination justify-content-center">
				{PAGENAV_PAGES}
			</ul>
		</nav>
		<!-- ELSE -->
		<div class="alert alert-warning mt-4">
			{PHP.L.market_no_products}
		</div>
		<!-- ENDIF -->
	</div>
</div>
<!-- END: MAIN -->
