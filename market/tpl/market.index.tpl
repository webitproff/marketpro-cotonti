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
	<!-- IF {PHP.usr.auth_write} -->
	<div class="col-md-6 d-flex justify-content-center justify-content-md-end">
		<!-- IF {PHP.c} -->
		<a class="btn btn-outline-success" href="{PHP|cot_url('market', 'm=add', '&c={PHP.c}')}">{PHP.L.market_add_product}</a>
		<!-- ELSE -->
		<a class="btn btn-outline-success" href="{PHP|cot_url('market', 'm=add')}">{PHP.L.market_add_product}</a>
		<!-- ENDIF -->
	</div>
	<!-- ENDIF -->
	<!-- IF {PHP.usr.id} == 0 -->
	<div class="col-md-6 d-flex justify-content-center justify-content-md-end">
		<a class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#authModal" >{PHP.L.market_add_product}</a>
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
						<a href="{MARKET_ROW_URL}" class="text-decoration-none" title="{MARKET_ROW_TITLE}">{MARKET_ROW_TITLE|cot_string_truncate($this, '64')}</a>
					</div>
					<div class="attacherPicIntList-desc">{MARKET_ROW_CAT_TITLE}
						<!-- IF {MARKET_ROW_COSTDFLT} > 0 -->
						<span class="ms-2 text-success fw-bold">{MARKET_ROW_COSTDFLT} <!-- IF {PHP.cfg.payments.valuta} -->{PHP.cfg.payments.valuta}<!-- ELSE -->{PHP.cfg.market.market_currency}<!-- ENDIF --></span>
						<!-- ENDIF -->
					</div>

				</div>
			</div>
		</div>
		<!-- END: MARKET_ROW -->
	</div>
</div>
<!-- END: MARKET -->
