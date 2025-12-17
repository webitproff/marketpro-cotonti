<!-- BEGIN: MAIN -->	
	<!-- IF {LEVEL} == 0 -->
	<div class="p-2" title="{PHP.L.All}">
			
			<a class="nav-link" href="{PHP|cot_url('market')}"><i class="fa-solid fa-store fa-xl me-2"></i><span>{PHP.L.market_Market} {PHP.L.All}</span>
				<span class="ms-auto">({TOTAL_COUNT})</span>
			</a>
			
		</div>
<hr class="mt-0" >	
	<!-- ENDIF -->

<div id="market-tree-offcanvas">
  <div class="list-group list-group-flush">
    <!-- BEGIN: CATS -->
    <div class="list-group-item py-2 border-0" data-level="{ROW_LEVEL}" data-id="{ROW_ID}">
      <div class="d-flex align-items-start">
        <!-- Стрелка -->


        <div class="flex-grow-1">
          <!-- Ссылка на категорию -->
          <!-- IF {ROW_HREF} -->
          <a href="{ROW_HREF}" class="text-decoration-none fw-medium">{ROW_TITLE}</a>
          <!-- ENDIF -->
          <!-- IF {ROW_LEVEL} --><span class="badge bg-secondary ms-2 small">{ROW_COUNT}</span><!-- ENDIF -->

        </div>
        <!-- IF {ROW_SUBCAT} -->
        <button class="btn btn-sm btn-info me-2 toggle-subcats" type="button"
                data-bs-toggle="collapse" data-bs-target="#sub-{ROW_LEVEL}-{ROW_JJ}-{ROW_ID}">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <!-- ENDIF -->
      </div>

      
      <!-- IF {ROW_SUBCAT} -->
      <div id="sub-{ROW_LEVEL}-{ROW_JJ}-{ROW_ID}" class="collapse">
        <div class="ms-1">
          {ROW_SUBCAT}<!-- Подкатегории -->
        </div>
      </div>
      <!-- ENDIF -->
    </div>
    <!-- END: CATS -->
  </div>
</div>
<!-- END: MAIN -->
