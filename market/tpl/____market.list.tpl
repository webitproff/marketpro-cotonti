<!-- BEGIN: MAIN -->
<div class="border-bottom border-secondary py-3 px-3">
	<nav aria-label="breadcrumb">
		<div class="ps-container-breadcrumb">
			<ol class="breadcrumb d-flex mb-0">{LIST_BREADCRUMBS_FULL}</ol>
		</div>
	</nav>
</div>
<div class="min-vh-50 px-2 pb-4">
	<div class="px-0 m-0 row justify-content-center">
		<div class="col-12 container-3xl">
			<!-- IF {PHP|cot_plugin_active('marketfilter')} -->
			<div class="alert {MARKETFILTER_MESSAGE_CLASS}"> {MARKETFILTER_MESSAGE} </div>
			<!-- ENDIF --> 
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"} 
			<div class="col-12">
				<div class="row align-items-center mb-2">
					<div class="col-md-8 col-lg-9 col-12 col-auto">
						<!-- IF {PHP.c} == '' -->
						<h1 class="h2">{PHP.cfg.market.marketlist_default_title}</h1>
						<h2 class="h4">
							<span class="">{PHP.cfg.market.marketlist_default_desc}</span>
						</h2>
						<!-- ENDIF -->
						<!-- IF {PHP.c} -->
						<div class="row align-items-center">
							<div class="col-auto">
								<div class="position-relative">
									<!-- IF {LIST_CAT_ICON} -->
									<img width="27" height="27" alt="{LIST_CAT_TITLE}" src="{LIST_CAT_ICON_SRC}">
									<!-- ELSE -->
									<img width="27" height="27" alt="{LIST_CAT_TITLE}" src="{PHP.R.market_icon_cat_default}">
									<!-- ENDIF -->
									<!-- IF {PHP.cat.count} > 0 -->
									<span class="position-absolute top-0 start-100 translate-middle badge text-bg-primary">{PHP.cat.count}</span>
									<!-- ENDIF -->
								</div>
							</div>
							<div class="col">
								<h1 class="h4 mb-0">{LIST_CAT_TITLE}</h1>
							</div>
						</div>
						<!-- ENDIF -->
					</div>
					<!-- IF {PHP|cot_auth('market', '{PHP.c}', 'W')} -->
					<div class="col-md-4 col-lg-3 col-12 d-flex justify-content-center justify-content-md-end mt-3 mt-md-0">
						<a class="btn btn-outline-success" href="{PHP|cot_url('market', 'm=add', '&c={PHP.c}')}">{PHP.L.market_addtitle}</a>
					</div>
					<!-- ENDIF -->
				</div>
				<!-- IF {LIST_CAT_DESCRIPTION} -->
				<h2 class="h6 text-muted mb-2">{LIST_CAT_DESCRIPTION}</h2>
				<!-- ENDIF -->
			</div>
			<div class="row px-0">
				<div class="col-12 col-lg-4 col-xl-3">
					<div class="card mb-4"> 
						{PHP|cot_build_structure_market_tree('', '')} 
					</div>
					<!-- IF {PHP|cot_plugin_active('marketfilter')} --> 
					{MARKET_FILTER_FORM}
					<!-- ENDIF -->
					<!-- IF {PHP.usr.maingrp} == 5 -->
					<div class="card mt-4 mb-4">
						<div class="card-header">
							<h2 class="h5 mb-0">{PHP.L.2wd_toolsAdmin}</h2>
						</div>
						<div class="card-body">
							<ul class="list-group list-group-flush">
								<!-- IF {PHP.usr.isadmin} -->
								<li class="list-group-item">
									<a href="{PHP|cot_url('admin')}">{PHP.L.Adminpanel}</a>
								</li>
								<!-- IF {PHP.structure.market.unvalidated.path} -->
								<li class="list-group-item">
									<a href="{PHP|cot_url('market', 'c=unvalidated')}" title="{PHP.structure.market.unvalidated.title}">{PHP.structure.market.unvalidated.title}</a>
								</li>
								<!-- ENDIF -->
								<!-- ENDIF -->
								<li class="list-group-item">{LIST_SUBMIT_NEW_ITEM}</li>
							</ul>
						</div>
					</div>
					<!-- ENDIF -->
					<!-- IF {PHP|cot_plugin_active('marketreviews')} --> {PHP|cot_marketreviews_last(4)}
					<!-- ENDIF -->
					<!-- IF {PHP|cot_plugin_active('tags')} -->
					<div class="card mt-4 mb-4">
						<div class="card-header">
							<h2 class="h5 mb-0">{PHP.L.Tags}</h2>
						</div>
						<div class="card-body">{MARKET_TAG_CLOUD}</div>
					</div>
					<!-- ENDIF -->
				</div>
				<div class="col-12 col-lg-8 col-xl-9">
					<!-- IF {LIST_CAT_PAGINATION} -->
					<nav aria-label="Category Pagination" class="mb-3">
						<div class="text-center mb-2">{PHP.L.Page} {LIST_CAT_CURRENT_PAGE} {PHP.L.Of} {LIST_CAT_TOTAL_PAGES}</div>
						<ul class="pagination justify-content-center">{LIST_CAT_PREVIOUS_PAGE} {LIST_CAT_PAGINATION} {LIST_CAT_NEXT_PAGE}</ul>
					</nav>
					<!-- ENDIF -->
					<div class="row g-4">
						<!-- BEGIN: LIST_ROW -->
						<div class="col-12 col-md-6 col-xl-4">
							<div class="attacherPicIntList-card" style="background-color: var(--bs-sidebar-bg)">
								<a class="attacherPicIntList-thumbnail" data-fancybox="gallery" href="{LIST_ROW_URL}" data-caption="{MARKET_ROW_TITLE}">
									<!-- IF {PHP|cot_plugin_active('attacher')} -->
									<!-- IF {LIST_ROW_ID|att_count('market', $this, '', 'images')} > 0 --> {LIST_ROW_ID|att_display('market', $this, '', 'attacher.display.marketlist', 'images', 1)}
									<!-- ELSE -->
									<!-- IF {LIST_ROW_LINK_MAIN_IMAGE} -->
									<img src="{LIST_ROW_LINK_MAIN_IMAGE}" alt="{LIST_ROW_TITLE}" class="img-fluid object-fit-cover h-100 w-100">
									<!-- ELSE -->
									<img src="{PHP.R.market_default_image}" alt="{LIST_ROW_TITLE}" class="img-fluid object-fit-cover h-100 w-100">
									<!-- ENDIF -->
									<!-- ENDIF -->
									<!-- ENDIF -->
								</a>
								<div class="attacherPicIntList-card-body">
									<div class="attacherPicIntList-title">
										<a href="{LIST_ROW_URL}" class="text-decoration-none" title="{LIST_ROW_TITLE}">{LIST_ROW_TITLE|cot_string_truncate($this, '64')}</a>
									</div>
									<div class="attacherPicIntList-desc">
										{LIST_ROW_CAT_TITLE}
										<!-- IF {LIST_ROW_COSTDFLT} > 0 -->
										<span class="ms-2 text-success fw-bold">
											{LIST_ROW_COSTDFLT} 				  
											<!-- IF {PHP.cfg.payments.valuta} -->
											{PHP.cfg.payments.valuta}
											<!-- ELSE -->
											{PHP.cfg.market.market_currency}
											<!-- ENDIF -->
										</span>
										<!-- ENDIF -->
									</div>
								</div>
							</div>
						</div>
						<!-- END: LIST_ROW -->
					</div>
					<!-- IF {PAGINATION} -->
					<nav aria-label="Market Pagination" class="mt-3">
						<div class="text-center mb-2">{PHP.L.Page} {CURRENT_PAGE} {PHP.L.Of} {TOTAL_PAGES}</div>
						<ul class="pagination justify-content-center">{PREVIOUS_PAGE} {PAGINATION} {NEXT_PAGE}</ul>
					</nav>
					<!-- ENDIF -->
					<!-- IF {PHP.cat.count} == 0 -->
					<div class="alert alert-light" role="alert"> {PHP.L.2wd_market_catEmpty} </div>
					<!-- ENDIF -->
				</div>
			</div>
			<!-- BEGIN: LIST_CAT_ROW -->
			<div class="mb-3">
				<h3 class="h5">
					<a href="{LIST_CAT_ROW_URL}" title="{LIST_CAT_ROW_TITLE}">{LIST_CAT_ROW_TITLE}</a> ({LIST_CAT_ROW_COUNT})
				</h3>
				<!-- IF {LIST_CAT_ROW_DESCRIPTION} -->
				<p class="small mb-0">{LIST_CAT_ROW_DESCRIPTION}</p>
				<!-- ENDIF -->
			</div>
			<!-- END: LIST_CAT_ROW -->
		</div>
	</div>
</div>
<!-- IF {PHP.usr.maingrp} == 5 AND {PHP.mskin} --> {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/mskin.tpl"}
<!-- ENDIF -->
<!-- END: MAIN -->
