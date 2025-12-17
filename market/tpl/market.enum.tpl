<!-- BEGIN: MAIN -->

    <!-- BEGIN: MARKET_ROW -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title fs-6 mb-3">
                <a href="{MARKET_ROW_URL}" title="{MARKET_ROW_TITLE}">{MARKET_ROW_TITLE}</a>
            </h3>

            <!-- MARKET description (if exists) -->
            <!-- IF {MARKET_ROW_DESCRIPTION} -->
            <p class="card-text small text-muted">{MARKET_ROW_DESCRIPTION}</p>
            <!-- ENDIF -->

            <!-- MARKET text preview -->
            <div class="card-text">
                {LIST_ROW_TEXT_CUT}
            </div>
        </div>
    </div>
    <!-- END: MARKET_ROW -->

    <!-- Pagination (if exists) -->
    <!-- IF {PAGINATION} -->
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            {PREVIOUS_PAGE}
            {PAGINATION}
            {NEXT_PAGE}
        </ul>
    </nav>
    <!-- ENDIF -->

<!-- END: MAIN -->