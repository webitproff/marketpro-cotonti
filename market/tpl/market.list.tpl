<!-- BEGIN: MAIN -->
<div class="border-bottom border-secondary py-3 px-3">
    <div class="d-flex align-items-center g-0">
        <div class="d-flex justify-content-center pe-2">
            <a type="button"
			data-bs-toggle="offcanvas"
			data-bs-target="#treecatsMarketLeftOffcanvas"
			aria-controls="treecatsMarketLeftOffcanvas"
			data-bs-toggle="tooltip"
			title="{PHP.L.market_categories}">
                <i class="fa-solid fa-square-caret-right fa-2xl" style="color:#ff8000;"></i>
			</a>
		</div>
        <div class="flex-grow-1">
            <nav aria-label="breadcrumb">
                <div class="ps-container-breadcrumb">
                    <ol class="breadcrumb d-flex mb-0">
                        {LIST_BREADCRUMBS_FULL}
					</ol>
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
					<!-- IF {PHP|function_exists('cot_build_structure_market_tree')} AND {PHP|cot_auth('market', 'any', 'R')} -->
					<div class="card mb-4"> 
						{PHP|cot_build_structure_market_tree('', '', 0, 'list')}
					</div>
					<!-- ENDIF -->
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
							<div class="row mt-2">
								<div class="col-12">
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="search_in" id="search_in_title" value="title" <!-- IF {PHP.search_in} == '' OR {PHP.search_in} == 'title' -->checked="checked"<!-- ENDIF -->>
										<label class="form-check-label" for="search_in_title">{PHP.L.market_search_in_title}</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="search_in" id="search_in_full" value="full" <!-- IF {PHP.search_in} == 'full' -->checked="checked"<!-- ENDIF -->>
										<label class="form-check-label" for="search_in_full">{PHP.L.market_search_in_title_and_descr}</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="search_in" id="search_in_pcod" value="pcod" <!-- IF {PHP.search_in} == 'pcod' -->checked="checked"<!-- ENDIF -->>
										<label class="form-check-label" for="search_in_pcod">{PHP.L.market_search_in_pcod}</label>
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
					<div class="row g-4 mb-3" id="market-items-container">
						<!-- BEGIN: LIST_ROW -->
						<div class="col-12 col-md-6 col-xl-4">
							<div class="attacherPicIntList-title card-title" style="background-color: var(--bs-sidebar-bg)">
								<a class="attacherPicIntList-thumbnail" data-fancybox="gallery" href="{LIST_ROW_URL}" data-caption="{MARKET_ROW_TITLE}">
									<!-- IF {PHP|cot_plugin_active('attacher')} -->
									<!-- IF {LIST_ROW_ID|att_count('market', $this, '', 'images')} > 0 --> {LIST_ROW_ID|att_display('market', $this, '', 'attacher.display.marketlist', 'images', 1)}
									<!-- ELSE -->
									<img src="{PHP.R.page_default_image}" alt="{LIST_ROW_TITLE}" class="img-fluid object-fit-cover h-100 w-100">
									<!-- ENDIF -->
									<!-- ENDIF -->
								</a>
								<div class="attacherPicIntList-card-body">
									
									<!-- IF {PHP.usr.isadmin} OR {PHP.usr.id} == {LIST_ROW_OWNER_ID} -->
									<!-- IF {LIST_ROW_STATE} == '2' -->
									<p class="mb-1">
										<strong>{PHP.L.Status}:</strong>
										<span class="badge bg-warning text-black">{LIST_ROW_LOCAL_STATUS}</span>
									</p>
									<!-- ENDIF -->
									<!-- IF {LIST_ROW_STATE} == '1' -->
									<p class="mb-1">
										<strong>{PHP.L.Status}:</strong>
										<span class="badge bg-danger text-black">{LIST_ROW_LOCAL_STATUS}</span>
									</p>
									<!-- ENDIF -->
									<!-- ENDIF -->
									
									<div class="attacherPicIntList-title card-title">
										<a href="{LIST_ROW_URL}" class="text-decoration-none" title="{LIST_ROW_TITLE}">{LIST_ROW_TITLE|cot_string_truncate($this, '64')}</a>
									</div>
									<div class="attacherPicIntList-desc">
										<!-- IF {PHP|cot_plugin_active('marketreviews')} -->
										<div><span class="review-stars" title="{PHP.L.marketreviews_pageRatingValue}">{LIST_ROW_REVIEWS_AVG_STARS_HTML}</span>
											<!-- IF {LIST_ROW_REVIEWS_TOTAL_COUNT} > 0 -->
											<span class="text-body" title="{PHP.L.marketreviews_pageCountReviewsTotalValue}">
											<span class="d-none d-sm-inline me-2">•</span><i class="fa-solid fa-comment-dots fa-lg"></i> {LIST_ROW_REVIEWS_TOTAL_COUNT}</span>
											<!-- ENDIF -->
										</div>
										<!-- ENDIF -->
										<!-- IF {LIST_ROW_DESCRIPTION} -->
										<div class="card-text text-muted small flex-grow-1">
											{LIST_ROW_DESCRIPTION}
										</div>
										<!-- ELSE -->
										<div class="card-text text-muted small flex-grow-1">
											{LIST_ROW_TEXT_CUT|strip_tags($this)}
										</div>
										<!-- ENDIF -->										
										{LIST_ROW_CAT_TITLE}
										<!-- IF {PHP|cot_plugin_active('marketcurrencyswitcher')} -->
										<!-- IF {LIST_ROW_COSTDFLT} > 0 -->
										<p class="fw-bold">
											<span class="price-label">{PHP.L.market_price}</span>
											<span class="ms-2 text-success market-price" data-base-price="{LIST_ROW_COST_RAW}">
												{LIST_ROW_COSTDFLT} {PHP.cfg.payments.valuta}
											</span>
										</p>
										<!-- ENDIF -->
										<!-- ELSE -->
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
										<!-- ENDIF -->
									</div>
									{LIST_ROW_OWNER}
									
									<!-- IF {PHP.usr.maingrp} == 5 --> 
									{PHP|get_cart_identifier()}
									<!-- ENDIF -->
									<!-- IF {PHP|cot_plugin_active('payordersmarket')} AND {PHP.usr.id} -->
									<!-- IF !{LIST_ROW_ORDER_IN_CART} -->
									<a href="javascript:void(0)" class="btn btn-sm btn-success add-to-cart" data-id="{LIST_ROW_ID}">
										{PHP.L.payordersmarket_add_to_cart}
									</a>
									<span class="cart-added-msg text-success ms-2" style="display:none;">{PHP.L.payordersmarket_added_to_cart}</span>
									<!-- ELSE -->
									<span class="btn btn-sm btn-outline-info">{PHP.L.payordersmarket_in_cart} ✅</span>
									<!-- ENDIF -->
									<!-- ENDIF -->
									<!-- IF {PHP|cot_plugin_active('payordersmarket')} AND {PHP|cot_auth('plug', 'payordersmarket', 'R')} AND {PHP.usr.id} == 0 -->
									<a class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#authModal">{PHP.L.payordersmarket_add_to_cart}</a>
									<!-- ENDIF -->
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
			<div class="alert alert-info">Ниже список категорий. market.list.tpl, LIST_CAT_ROW  оставил может кому понадобится</div>
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
			<!-- ELSE -->
			<!-- IF {PHP|cot_plugin_active('marketreviews')} --> 
			{PHP|cot_marketreviews_last_tpl(3, 'listmarket')}
			<!-- ENDIF -->
			<!-- ENDIF -->
		</div>
		
	</div>
</div>

<!-- IF {SEARCH_HIGHLIGHT_ACTIVE} -->
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
			var words = {SEARCH_HIGHLIGHT_WORDS};
			var scope = '{SEARCH_HIGHLIGHT_SCOPE}';
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

<!-- IF {PHP.usr.maingrp} == 5 AND {PHP.mskin} --> 
{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/mskin.tpl"}
<!-- ENDIF -->
<!-- END: MAIN -->


