<!-- BEGIN: MAIN -->
<div class="border-bottom border-secondary py-3 px-3">
    <div class="row align-items-center">
        <div class="col-2 col-sm-1 d-flex justify-content-center">
            <a type="button" data-bs-toggle="offcanvas" data-bs-target="#treecatsMarketLeftOffcanvas" aria-controls="treecatsMarketLeftOffcanvas" data-bs-toggle="tooltip" title="{PHP.L.market_categories}">
                <i class="fa-solid fa-square-caret-right fa-2xl" style="color: #ff8000;"></i>
			</a>
		</div>
        <div class="col-9 col-sm-9">
		  <nav aria-label="breadcrumb">
			<div class="ps-container-breadcrumb">
			  <ol class="breadcrumb d-flex mb-0"> {LIST_BREADCRUMBS_FULL}</ol>
			</div>
		  </nav>
		</div>
	</div>
</div>
<div class="min-vh-50 px-2 pb-4">
	<div class="px-0 m-0 row justify-content-center">
		<div class="col-12 py-4">
			<!-- IF {PHP|cot_plugin_active('marketprofilter')} AND {MARKETFILTER_MESSAGE} -->
			<div class="alert {MARKETFILTER_MESSAGE_CLASS}"> {MARKETFILTER_MESSAGE} </div>
			<!-- ENDIF --> 
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"} 
			<div class="col-12">
				<div class="row align-items-center mb-2">
					<div class="col-md-8 col-lg-9 col-12 col-auto">
						<!-- IF {PHP.c} == '' -->
						<h1 class="h4 mt-0">{PHP.cfg.market.marketlist_default_title}</h1>
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
					<!-- IF {PHP|cot_auth('market', 'any', 'W')} -->
					<div class="col-md-4 col-lg-3 col-12 d-flex justify-content-center justify-content-md-end mt-3 mt-md-0">
						<a class="btn btn-outline-success" href="{PHP|cot_url('market', 'm=add')}">{PHP.L.market_addtitle}</a>
					</div>
					<!-- ENDIF -->
				</div>
				<!-- IF {LIST_CAT_DESCRIPTION} -->
				<h2 class="h5 mb-4">{LIST_CAT_DESCRIPTION}</h2>
				<!-- ENDIF -->
			</div>
			<div class="row px-0">
				<div class="col-12 col-lg-4 col-xl-3">
					<div class="card mb-4"> 
						{PHP|cot_build_structure_market_tree('', '', 0, 'list')}
					</div>
					<!-- IF {PHP|cot_plugin_active('marketprofilter')} --> 
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
					<!-- IF {PHP|cot_plugin_active('marketreviews')} --> 
					{PHP|cot_marketreviews_last(4)}
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
					<div class="card card-body mb-3">
						<form action="{MARKET_SEARCH_ACTION_URL}" method="get" class="row g-2">
							<input type="hidden" name="e" value="market">
							<input type="hidden" name="l" value="{PHP.lang}" />
							
							<div class="col-md-6">
								{MARKET_SEARCH_SQ}
							</div>
							
							<div class="col-md-4">
								{MARKET_SEARCH_CAT_SELECT2}
							</div>
							
							<div class="col-md-2">
		<div class="row">
			<div class="col-6">
				<button type="submit" title="{PHP.L.Search}" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
			</div>
			<div class="col-6">
				<a class="btn btn-outline-danger" title="{PHP.L.marketprofilter_reset}" href="{PHP|cot_url('market')}"><i class="fa-solid fa-filter-circle-xmark"></i></a>
			</div>
		</div>
							</div>
							<!-- IF {MARKET_SEARCH_RESULT_MSG} --> 
							<div class="alert alert-info" role="alert">
								{MARKET_SEARCH_RESULT_MSG}
							</div>
							<!-- ENDIF -->
						</form>
					</div>
					<div class="row g-4 mb-3">
						<!-- BEGIN: LIST_ROW -->
						<div class="col-12 col-md-6 col-xl-4">
							<div class="attacherPicIntList-card" style="background-color: var(--bs-sidebar-bg)">
								<a class="attacherPicIntList-thumbnail" data-fancybox="gallery" href="{LIST_ROW_URL}" data-caption="{MARKET_ROW_TITLE}">
									<!-- IF {PHP|cot_plugin_active('attacher')} -->
									<!-- IF {LIST_ROW_ID|att_count('market', $this, '', 'images')} > 0 --> {LIST_ROW_ID|att_display('market', $this, '', 'attacher.display.marketlist', 'images', 1)}
									<!-- ELSE -->
									<img src="{PHP.R.page_default_image}" alt="{LIST_ROW_TITLE}" class="img-fluid object-fit-cover h-100 w-100">
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
					<!-- IF {PHP.totallines} == 0 -->
					<div class="my-3">
					<div class="alert alert-light" role="alert"> {PHP.L.market_catEmpty} </div>
					</div>
					<!-- ENDIF -->
				</div>
			</div>
			<!-- IF {PHP.usr.isadmin} -->
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
			<!-- ENDIF -->
			<!-- IF {PHP.c} -->
			<blockquote>
				<p>{PHP.cfg.market.marketlist_default_title}</p>
				<p>{PHP.cfg.market.marketlist_default_desc}</p>
			</blockquote>
			<!-- ENDIF -->
		</div>
	</div>
</div>
<!-- IF {PHP.usr.maingrp} == 5 AND {PHP.mskin} --> {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/mskin.tpl"}
<!-- ENDIF -->
<!-- END: MAIN -->
				<div class="col-12 col-lg-8 col-xl-9">
					<!-- IF {LIST_CAT_PAGINATION} -->
					<nav aria-label="Category Pagination" class="mb-3">
						<div class="text-center mb-2">{PHP.L.Page} {LIST_CAT_CURRENT_PAGE} {PHP.L.Of} {LIST_CAT_TOTAL_PAGES}</div>
						<ul class="pagination justify-content-center">{LIST_CAT_PREVIOUS_PAGE} {LIST_CAT_PAGINATION} {LIST_CAT_NEXT_PAGE}</ul>
					</nav>
					<!-- ENDIF -->

					<!-- BEGIN: LIST_ROW -->
					<div class="card mb-3">
						<div class="row g-0 align-items-stretch">
							<div class="col-md-5 col-lg-4 d-flex" style="background-color: var(--bs-card-cap-bg)">
								<a href="{LIST_ROW_URL}" class="attacherPicIntList-thumbnail-left-position-pict text-decoration-none flex-grow-1" title="{LIST_ROW_TITLE}">
									<!-- IF {PHP|cot_plugin_active('attacher')} -->
									<!-- IF {LIST_ROW_ID|att_count('market', $this, '', 'images')} > 0 --> 
									{LIST_ROW_ID|att_display('market', $this, '', 'attacher.display.marketlist', 'images', 1)}
									<!-- ELSE -->
									<!-- IF {LIST_ROW_LINK_MAIN_IMAGE} -->
									<img src="{LIST_ROW_LINK_MAIN_IMAGE}" alt="{LIST_ROW_TITLE}" class="img-fluid object-fit-cover h-100 w-100">
									<!-- ELSE -->
									<img src="{PHP.R.market_default_image}" alt="{LIST_ROW_TITLE}" class="img-fluid object-fit-cover h-100 w-100">
									<!-- ENDIF -->
									<!-- ENDIF -->
									<!-- ENDIF -->
								</a>
							</div>
							<div class="col-md-7 col-lg-8">
								<div class="card position-relative h-100 border-0">
									<div class="card-header border-0 rounded-0">
										<h3 class="card-title mb-0 fs-6">
											<a href="{LIST_ROW_URL}" class="text-decoration-none" title="{LIST_ROW_TITLE}">{LIST_ROW_TITLE}</a>
										</h3>
										<!-- IF {PHP.usr.isadmin} OR {PHP.usr.id} == {LIST_ROW_OWNER_ID} -->
										<p class="mb-1">
											<strong>{PHP.L.Status}:</strong>
											<span class="badge bg-warning text-black">{LIST_ROW_LOCAL_STATUS}</span>
										</p>
										<!-- ENDIF -->
										<!-- IF {LIST_ROW_COSTDFLT} > 0 -->
										<span class="ms-2 text-success fw-bold">{LIST_ROW_COSTDFLT} {PHP.cfg.market.currency}</span>
										<!-- ENDIF -->
									</div>
									<div class="card-body border-top d-flex flex-column justify-content-center"> 
										{LIST_ROW_FILTER_PARAMS_HTML}
										<!-- IF {LIST_ROW_DESCRIPTION} -->
										<p class="text-muted">{LIST_ROW_DESCRIPTION}</p>
										<!-- ELSE -->
										<p class="text-secondary">{LIST_ROW_TEXT_CUT|strip_tags($this)|cot_string_truncate($this, '170')}</p>
										<!-- ENDIF --> {LIST_ROW_PRICE_ROWS_HTML} 
										<p class="card-text">
											<small class="text-body-secondary">{LIST_ROW_CREATED}</small>
										</p>
										<!-- IF {LIST_ROW_COMMENTS_COUNT} > 0 -->
										<div class="position-absolute top-0 end-0 mt-2 me-2" data-bs-toggle="tooltip" data-bs-title="{PHP.L.2wd_Comments}">
											<span class="badge bg-primary">{LIST_ROW_COMMENTS_COUNT}</span>
										</div>
										<!-- ENDIF -->
									</div>
									<div class="card-footer text-end border-top rounded-0">
										<div class="row">
											<!-- IF {LIST_ROW_ADMIN} -->
											<div class="col-12 col-md-auto my-1">
												<p class="my-0 list-row-admin-link">{LIST_ROW_ADMIN}, {LIST_ROW_ADMIN_DELETE}, </p>
											</div>
											<!-- ENDIF -->
											<div class="col-12 col-md-4 d-flex justify-content-end align-items-center">
												<span class="me-2">({LIST_ROW_HITS})</span>
												<a href="{LIST_ROW_URL}" class="btn btn-outline-primary btn-sm">{PHP.L.ReadMore}</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END: LIST_ROW -->