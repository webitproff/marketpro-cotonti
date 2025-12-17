<!-- BEGIN: MAIN -->
<div class="border-bottom border-secondary py-3 px-3">
    <div class="row align-items-center">
        <div class="col d-flex justify-content-center">
            <a type="button" data-bs-toggle="offcanvas" data-bs-target="#treecatsMarketLeftOffcanvas" aria-controls="treecatsMarketLeftOffcanvas" data-bs-toggle="tooltip" title="{PHP.L.market_categories}">
                <i class="fa-solid fa-square-caret-right fa-2xl" style="color: #ff8000;"></i>
			</a>
		</div>
        <div class="col">
		  <nav aria-label="breadcrumb">
			<div class="ps-container-breadcrumb">
			  <ol class="breadcrumb d-flex mb-0">{MARKET_BREADCRUMBS_ITEM}</ol>
			</div>
		  </nav>
		</div>
	</div>
</div>
<div class="min-vh-50 px-2 px-md-3 py-4">
	<div class="col-12 container-3xl px-1 px-md-3"> 
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"} 
			<div class="row align-items-center mb-4">
				<div class="col-md-8 col-lg-9 col-12 col-auto">
					<h1 class="h4 mb-3">{MARKET_TITLE}</h1>
				</div>
				<!-- IF {PHP|cot_auth('market', '{PHP.c}', 'W')} -->
				<div class="col-md-4 col-lg-3 col-12 d-flex justify-content-center justify-content-md-end mt-3 mt-md-0">
					<a class="btn btn-outline-success" href="{PHP|cot_url('market', 'm=add', '&c={PHP.c}')}">{PHP.L.market_addtitle}</a>
				</div>
				<!-- ENDIF -->
			</div>
			<!-- IF {PHP.usr.isadmin} OR {PHP.usr.id} == {MARKET_OWNER_ID} -->
			<p class="mb-1">
				<strong>{PHP.L.Status}:</strong>
				<span class="badge bg-warning text-black">{MARKET_LOCAL_STATUS}</span>
			</p>
			<!-- ENDIF -->
			<!-- IF {MARKET_DESCRIPTION} -->
			<h2 class="fs-6 mb-4">{MARKET_DESCRIPTION}</h2>
			<!-- ENDIF -->
			<div class="row pt-5">
				<div class="col-12 col-md-8 mx-auto pb-5">
				<div class="mb-4">
					<!-- IF {PHP|cot_plugin_active('attacher')} -->
					<!-- IF {MARKET_ID|att_count('market', $this, '', 'images')} > 0 -->
					<div class="mb-5"> {MARKET_ID|att_display('market', $this, '', 'attacher.gallery.fancybox.market')} </div>
					<!-- ELSE -->
					<div class="position-relative overflow-hidden rounded-5 shadow-bottom" style="aspect-ratio: 2 / 1; background-image: url('{PHP.R.page_default_image}'); background-size: cover; background-position: center;"></div>
					<!-- ENDIF -->
					<!-- ENDIF -->
				</div>

					<div class="card mb-4">
						<div class="card-body">  
							<div class="row g-3 mb-3">
								<div class="col-4 col-md-6">
									<!-- IF {MARKET_COMMENTS_COUNT} > 0 -->
									<div class="mb-3">{PHP.L.2wd_Comments}: {MARKET_COMMENTS_COUNT}</div>
									<!-- ENDIF -->
								</div>
								<div class="col-12 col-md-6 text-md-end">
									<strong>{PHP.L.Filedunder}:</strong> {MARKET_CAT_PATH_SHORT}
								</div>
							</div>
							<div class="mb-3">
								{MARKET_TEXT}
							</div>
                                    <!-- IF {PHP|cot_plugin_active('marketproreviews')} -->
                                    <div><span class="small">{PHP.L.pagereviews_pageRatingValue}:</span> <span class="review-stars">{PAGE_REVIEWS_AVG_STARS_HTML}</span></div>
                                    <div><span class="small">{PHP.L.pagereviews_pageCountStarsTotalValue}:</span> {PAGE_REVIEWS_STARS_SUMM}</div>
                                    <div><span class="small">{PHP.L.pagereviews_pageCountReviewsTotalValue}:</span> {PAGE_REVIEWS_TOTAL_COUNT}</div>
                                    <div><span class="small">{PHP.L.pagereviews_pageAverageRatingValue}:</span> {PAGE_REVIEWS_AVG_STARS}</div>
                                    <!-- ENDIF -->
									<!-- IF {PHP|cot_plugin_active('seomarketpro')} -->
									<div><span class="text-info">{PAGE_READ_TIME}</span> {PAGE_AUTHOR}</div>
									<!-- ENDIF -->
							<!-- IF {PHP|cot_plugin_active('tags')} -->
							<strong>{PHP.L.Tags}:</strong>
							<!-- BEGIN: MARKET_TAGS_ROW -->
							<!-- IF {PHP.tag_i} > 0 -->,
							<!-- ENDIF -->
							<a href="{MARKET_TAGS_ROW_URL}" title="{MARKET_TAGS_ROW_TAG}" rel="nofollow">{MARKET_TAGS_ROW_TAG}</a>
							<!-- END: MARKET_TAGS_ROW -->
							<!-- BEGIN: MARKET_NO_TAGS --> {MARKET_NO_TAGS}
							<!-- END: MARKET_NO_TAGS -->
							<!-- ENDIF -->
						</div>
					</div>
					<!-- IF {PHP|cot_plugin_active('attacher')} -->
					<!-- IF {MARKET_ID|att_count('market', $this, '', 'files')} > 0 -->
					<div class="mb-4" data-att-downloads="download">
						<h5>{PHP.L.att_attachments} {PHP.L.att_downloads}</h5> 
						{MARKET_ID|att_downloads('market', $this)}
					</div>
					<!-- ENDIF -->
					<!-- ENDIF -->
                    <!-- IF {PHP|cot_plugin_active('marketproreviews')} -->
                    {PAGE_REVIEWS}
                    <hr />
                    <!-- ENDIF -->
					<!-- IF {PHP|cot_plugin_active('comments')} --> 
					{MARKET_COMMENTS}
					<!-- ENDIF -->
				</div>
				<div class="col-12 col-md-4">
                    <!-- IF {MARKET_COSTDFLT} > 0 -->
                    <p class="mfw-bold">{PHP.L.market_price} 
						<span class="ms-2 text-success">
							{MARKET_COSTDFLT} 				  
							<!-- IF {PHP.cfg.payments.valuta} -->
							{PHP.cfg.payments.valuta}
							<!-- ELSE -->
							{PHP.cfg.market.market_currency}
							<!-- ENDIF -->
						</span>
					</p>
                    <!-- ENDIF -->
					<!-- IF {PHP|cot_plugin_active('marketprice')} -->
					{MARKETPRICE_PRICES}
					<hr>
					<!-- ENDIF -->
					<!-- IF {PHP|cot_plugin_active('marketprofilter')} -->
					<h3>{PHP.L.marketfilter_paramsItem}</h3>
					<dl class="row">
						<!-- BEGIN: MARKET_FILTER_PARAMS -->
						<dt class="col-sm-4">{PARAM_TITLE}</dt>
						<dd class="col-sm-8">{PARAM_VALUE}</dd>
						<!-- END: MARKET_FILTER_PARAMS -->
					</dl>
					<!-- ENDIF -->
					<!-- BEGIN: MARKET_MULTI -->
					<div class="card mb-4">
						<div class="card-header">
							<h2 class="h5 mb-0">{PHP.L.Summary}</h2>
						</div>
						<div class="card-body"> 
							{MARKET_MULTI_TABTITLES} 
							<p class="mb-0">{MARKET_MULTI_TABNAV}</p>
						</div>
					</div>
					<!-- END: MARKET_MULTI -->
					<!-- IF {PHP.usr.maingrp} == 5 -->
					<!-- BEGIN: MARKET_ADMIN -->
					<div class="card mb-4">
						<div class="card-header">
							<h2 class="h5 mb-0">{PHP.L.Adminpanel}</h2>
						</div>
						<div class="card-body p-0">
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
								<li class="list-group-item">
									<a href="{MARKET_CAT|cot_url('market', 'm=add&c=$this')}">{PHP.L.market_addtitle}</a>
								</li>
								<li class="list-group-item">{MARKET_ADMIN_UNVALIDATE}</li>
								<li class="list-group-item">{MARKET_ADMIN_EDIT}</li>
								<!-- IF {I18N_LANG_ROW_CLASS} == "selected" -->
								<li class="list-group-item">
									<a href="{MARKET_ADMIN_EDIT_URL}" class="btn btn-warning">{PHP.L.i18n_editing}</a>
								</li>
								<!-- ENDIF -->
								<li class="list-group-item">{MARKET_ADMIN_CLONE}</li>
								<li class="list-group-item">{MARKET_ADMIN_DELETE}</li>
								<!-- IF {MARKET_I18N_TRANSLATE} -->
								<li class="list-group-item">{MARKET_I18N_TRANSLATE}</li>
								<!-- ENDIF -->
								<!-- IF {MARKET_I18N_DELETE} -->
								<li class="list-group-item">{MARKET_I18N_DELETE}</li>
								<!-- ENDIF -->
							</ul>
						</div>
					</div>
					<!-- END: MARKET_ADMIN -->
					<!-- ENDIF -->
					<div class="card mb-4">
						<h2 class="h5 card-header">{PHP.L.2wd_contentAuthor}</h2>
						<div class="card-body">
							<div class="row justify-content-between">
								<div class="col-md-auto text-center text-md-start">
									<!-- IF {PHP|cot_plugin_active('userimages')} -->
									<!-- IF {MARKET_OWNER_AVATAR_SRC} -->
									<img src="{MARKET_OWNER_AVATAR_SRC}" alt="{MARKET_OWNER_NICKNAME}" class="rounded-circle" width="50" height="50">
									<!-- ELSE -->
									<img src="{PHP.R.userimg_default_avatar}" alt="{MARKET_OWNER_NICKNAME}" class="rounded-circle" width="50" height="50">
									<!-- ENDIF -->
									<!-- ENDIF -->
									<!-- IF {PHP|cot_plugin_active('whosonline')} -->
									<!-- IF {MARKET_OWNER_ONLINE} -->
									<p class="my-2">
										<span class="badge text-bg-success">{PHP.L.Online}</span>
									</p>
									<!-- ELSE -->
									<p class="my-2">
										<span class="badge text-bg-secondary">{PHP.L.Offline}</span>
									</p>
									<!-- ENDIF -->
									<!-- ENDIF -->
								</div>
								<div class="col-md-auto text-center text-md-end">
									<h4 class="h5 mb-0">
										 {MARKET_OWNER}
									</h4>
									<p class="small">{PHP.L.Lastlogged}: {MARKET_OWNER_LASTLOG}</p>
								</div>
							</div>
							<ul class="list-group list-group-flush">
								<!-- IF {PHP|cot_module_active('pm')} AND {PHP.usr.id} > 0 AND {PHP.usr.id} != {MARKET_OWNER_ID} -->
								<li class="list-group-item px-0">
									<a href="{PHP.pag.user_id|cot_url('pm','m=send&to=$this', '', 1)}"><i class="fa-regular fa-envelope fa-xl me-3"></i> {PHP.L.users_sendpm}</a>
								</li>
								<!-- ENDIF -->
								<!-- IF {PHP.usr.id|cot_auth('market', '', 'W')} -->
								<!-- IF {PHP.usr.auth_write} -->
								<li class="list-group-item px-0">
									<a href="{MARKET_CAT|cot_url('market', 'm=add&c=$this')}">{PHP.L.market_addtitle}</a>
								</li>
								<!-- ENDIF -->
								<!-- IF {PHP.usr.id} == {MARKET_OWNER_ID} -->
								<li class="list-group-item px-0">
									<a href="{MARKET_ID|cot_url('market', 'm=edit&id=$this')}">{PHP.L.Edit}</a>
								</li>
								<!-- ENDIF -->
								<!-- IF {I18N_LANG_ROW_CLASS} == "selected" -->
								<li class="list-group-item">
									<a href="{MARKET_ADMIN_EDIT_URL}" class="btn btn-warning">{PHP.L.i18n_editing}</a>
								</li>
								<!-- ENDIF -->
								<!-- IF {MARKET_I18N_TRANSLATE} -->
								<li class="list-group-item px-0">
									<a href="{PHP.url_i18n}">{PHP.L.i18n_translate}</a>
								</li>
								<!-- ENDIF -->
								<!-- ENDIF -->
								<!-- IF {MARKET_CREATED} -->
								<li class="list-group-item px-0">
									<strong>{PHP.L.2wd_market_published}</strong> {MARKET_CREATED}
								</li>
								<!-- ENDIF -->
								<!-- IF {MARKET_UPDATED} -->
								<li class="list-group-item px-0">
									<strong>{PHP.L.2wd_market_latest_update}</strong> {MARKET_UPDATED}
								</li>
								<!-- ENDIF -->
								<!-- IF {PHP.pag_i18n_locales} > 1 -->
								<!-- BEGIN: I18N_LANG -->
								<li class="list-group-item px-0">
									<strong>{PHP.L.Language}:</strong>
									<ul class="list-inline mt-1">
										<!-- BEGIN: I18N_LANG_ROW -->
										<!-- IF {PHP.i18n_locale} != {I18N_LANG_ROW_CODE} -->
										<li class="list-inline-item">
											<a href="{I18N_LANG_ROW_URL}">
												<!-- IF {I18N_LANG_ROW_CODE|is_file('images/flags/$this.png')} -->
												<img src="images/flags/{I18N_LANG_ROW_CODE}.png" alt="{I18N_LANG_ROW_CODE}" class="me-1" style="width: 16px;">
												<!-- ENDIF --> {I18N_LANG_ROW_TITLE}
											</a>
										</li>
										<!-- ENDIF -->
										<!-- END: I18N_LANG_ROW -->
									</ul>
								</li>
								<!-- END: I18N_LANG -->
								<!-- ENDIF -->
							</ul>
									<!-- IF {PHP|cot_plugin_active('userfields')} -->						
									<div class="row mb-3">
										<div class="list-group list-group-striped list-group-flush mb-4">
											<!-- IF {USERFIELDS_PROMO_TEXT} -->
											<li class="list-group-item list-group-item-action ">
												<div class="row g-3">
													<div class="col-12">
														<h5 class="mb-0 fs-6 text-secondary fw-semibold">
															{USERFIELDS_PROMO_TEXT_TITLE}
														</h5>
													</div>
													<div class="col-12">
														<div>
															<p><i class="fa-solid fa-list-check fa-lg me-2"></i><small class="text-wrap text-hyphen">{USERFIELDS_PROMO_TEXT}</small></p>
														</div>
													</div>
												</div>
											</li>
											<!-- ENDIF -->
											<!-- IF {USERFIELDS_GITHUB} -->
											<li class="list-group-item list-group-item-action ">
												<div class="row g-3">
													<div class="col-12">
														<h5 class="mb-0 fs-6 text-secondary">
															{USERFIELDS_GITHUB_TITLE}
														</h5>
													</div>
													<div class="col-12">
														<div>
															<a rel="noopener noreferrer" href="https://github.com/{USERFIELDS_GITHUB}" target="_blank" class="fw-semibold">
																<i class="fa-brands fa-square-github fa-xl me-2"></i>{PHP.L.userfields_github_details}
															</a>
														</div>
													</div>
												</div>
											</li>
											<!-- ENDIF -->
											<!-- IF {USERFIELDS_TELEGRAM} -->
											<li class="list-group-item list-group-item-action ">
												<div class="row g-3">
													<div class="col-12">
														<h5 class="mb-0 fs-6 text-secondary">
															{USERFIELDS_TELEGRAM_TITLE}
														</h5>
													</div>
													<div class="col-12">
														<div>
															<a rel="noopener noreferrer" href="https://t.me/{USERFIELDS_TELEGRAM}" target="_blank" class="fw-semibold">
																<i class="fa-brands fa-telegram fa-xl me-2"></i>{PHP.L.userfields_telegram_details}
															</a>
														</div>
													</div>
												</div>
											</li>
											<!-- ENDIF -->
										</div>
									</div>
									<!-- ENDIF -->
						</div>
					</div>
            <!-- IF {PHP|cot_plugin_active('seomarketpro')} -->
				<!-- BEGIN: RELATED_PAGES -->
				<div class="mb-4 mt-5">
				  <h3 class="h4 mt-3">{PHP.L.seomarketpro_related}</h3>
				  <div class="list-group list-group-striped list-group-flush">
					<!-- BEGIN: RELATED_ROW -->
					<div class="list-group-item list-group-item-action">
							<a class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="{RELATED_ROW_URL}">
								<div class="position-relative overflow-hidden rounded-5 shadow-bottom" style="aspect-ratio: 2 / 1;">
									<!-- IF {RELATED_ROW_LINK_MAIN_IMAGE} -->
									<img src="{RELATED_ROW_LINK_MAIN_IMAGE}" alt="{RELATED_ROW_TITLE}" class="img-fluid object-fit-cover">
									<!-- ENDIF -->
								</div>
								<h3 class="h5 fw-semibold my-2">{RELATED_ROW_TITLE}</h3>
								<p class="mb-1">{RELATED_ROW_DESC}</p>
							</a>
					</div>
					<!-- END: RELATED_ROW -->
				  </div>
				</div>
				<!-- END: RELATED_PAGES -->
			<!-- ENDIF -->		

					<!-- IF {PHP|cot_plugin_active('similar')} --> 
					{MARKET_SIMILAR}
					<!-- ENDIF -->
				</div>
			</div>
			<blockquote>
				<p>{PHP.cfg.market.marketlist_default_title}</p>
				<p>{PHP.cfg.market.marketlist_default_desc}</p>
			</blockquote>
			
		</div>
</div>
<!-- IF {PHP.usr.maingrp} == 5 AND {PHP.mskin} --> 
{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/inc/mskin.tpl"}
<!-- ENDIF -->
<!-- END: MAIN -->