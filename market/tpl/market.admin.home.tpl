<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.admin.home.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.admin.home.tpl
	* Recommended path to the file: _ _ _ _ themes/admin/{admintheme}/modules/market/market.admin.home.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/market.admin.home.php
	* Назначение:
	*   Шаблон статистического виджета модуля Market для главной страницы
	*   админ-панели Cotonti (mainpanel). Выводит компактный дашборд:
	*   счётчики товаров по состояниям (published / pending / drafts),
	*   общее количество и суммарные просмотры, активность публикаций
	*   (сегодня / 7 / 30 дней), прогресс-бар доли опубликованных,
	*   алерт очереди модерации, топ-5 категорий, топ-5 продавцов,
	*   топ-10 просматриваемых товаров, ленту последних добавленных,
	*   быстрые ссылки на разделы модуля (настройки, структура, поля, добавление).
	*
	*   Виджет подключается через хук admin.home.mainpanel (Order=5)
	*   и отображается только администраторам модуля Market.
	*
	*   ПРИМЕР https://abuyfile.com/ru/admin (главная админки, виджет Market)
	*
	* Основные параметры URL:
	*   Виджет не принимает внешних параметров — вызывается автоматически
	*   при открытии admin.php без ?m=… . Все ссылки внутри — на разделы
	*   модуля и карточки товаров.
	*
	* Основные теги шаблона:
	*   Шапка и быстрые ссылки:
	*     ADMIN_HOME_URL               — ссылка на админ-раздел модуля Market;
	*     ADMIN_HOME_CONFIG_URL        — настройки модуля;
	*     ADMIN_HOME_STRUCTURE_URL     — структура категорий Market;
	*     ADMIN_HOME_EXTRAFIELDS_URL   — extrafields товаров;
	*     ADMIN_HOME_ADD_URL           — добавить новый товар (публичная форма, target="_blank");
	*     ADMIN_HOME_QUEUE_URL         — очередь модерации (m=market&filter=valqueue).
	*
	*   Счётчики состояний и общие:
	*     ADMIN_HOME_TOTAL             — всего товаров (во всех состояниях);
	*     ADMIN_HOME_PUBLISHED         — опубликовано;
	*     ADMIN_HOME_PENDING           — на модерации;
	*     ADMIN_HOME_DRAFTS            — черновиков;
	*     ADMIN_HOME_ORPHANED          — товаров без владельца (ownerid = 0);
	*     ADMIN_HOME_VIEWS             — суммарные просмотры (формат «1 234»);
	*     ADMIN_HOME_MARKETQUEUED      — дубль очереди (совместимость с кастомными шаблонами).
	*
	*   Активность публикаций:
	*     ADMIN_HOME_TODAY             — добавлено сегодня;
	*     ADMIN_HOME_WEEK              — за 7 дней;
	*     ADMIN_HOME_MONTH             — за 30 дней.
	*
	*   Проценты и флаги:
	*     ADMIN_HOME_PUBLISHED_PCT     — доля опубликованных от общего (0–100, 1 знак);
	*     ADMIN_HOME_PENDING_PCT       — доля на модерации;
	*     ADMIN_HOME_DRAFTS_PCT        — доля черновиков;
	*     ADMIN_HOME_HAS_PENDING       — true, если есть товары в очереди (алерт);
	*     ADMIN_HOME_IS_EMPTY          — true, если каталог пуст (пустое состояние);
	*     ADMIN_HOME_HAS_ITEMS         — true, если есть хотя бы один товар.
	*
	*   Блок последних товаров (BEGIN: RECENT_ITEMS / RECENT_ROW):
	*     RECENT_ITEM_ID               — ID товара;
	*     RECENT_ITEM_TITLE            — название (экранированное);
	*     RECENT_ITEM_URL              — URL карточки (алиас с фолбэком на ID);
	*     RECENT_ITEM_CAT              — название категории;
	*     RECENT_ITEM_DATE             — дата добавления (datetime_short);
	*     RECENT_ITEM_STATE_CLASS      — CSS-класс бейджа (success / warning / secondary);
	*     RECENT_ITEM_STATE_LANG       — локализованное название состояния;
	*     RECENT_ITEM_OWNER            — ник владельца.
	*   Пустое состояние блока: RECENT_EMPTY.
	*
	*   Блок топ-категорий (BEGIN: TOP_CATS / TOP_CAT_ROW):
	*     TOP_CAT_CODE                 — код категории;
	*     TOP_CAT_TITLE                — название категории;
	*     TOP_CAT_COUNT                — число товаров в категории;
	*     TOP_CAT_URL                  — ссылка на админ-список с фильтром по категории.
	*   Пустое состояние блока: TOP_CATS_EMPTY.
	*
	*   Блок топ-продавцов (BEGIN: TOP_SELLERS / TOP_SELLER_ROW):
	*     TOP_SELLER_ID                — ID продавца (user_id);
	*     TOP_SELLER_NAME              — ник (или #ID, если пользователь удалён);
	*     TOP_SELLER_COUNT             — число товаров продавца;
	*     TOP_SELLER_URL               — ссылка на профиль пользователя.
	*   Пустое состояние блока: TOP_SELLERS_EMPTY.
	*
	*   Блок топ-просмотров (BEGIN: TOP_VIEWED / TOP_VIEWED_ROW):
	*     TOP_VIEWED_ID                — ID товара;
	*     TOP_VIEWED_TITLE             — название (экранированное);
	*     TOP_VIEWED_URL               — URL карточки (алиас с фолбэком на ID);
	*     TOP_VIEWED_CAT               — название категории;
	*     TOP_VIEWED_COUNT             — просмотры (формат «1 234»).
	*   Пустое состояние блока: TOP_VIEWED_EMPTY.
	*
	*   Прочее:
	*     TPL_PATH                     — путь к файлу шаблона (для отладки).
	*     PHP.L.*                      — языковые строки (в т.ч. market_status_*,
	*                                    market_validation, market_no_products,
	*                                    market_vendors_title, market_vendors_empty,
	*                                    market_stats_*).
	*
	* Стили шаблона (inline в блоке <style>):
	*   .market-admin-home .recent-row,
	*   .market-admin-home .top-cat-row,
	*   .market-admin-home .top-seller-row,
	*   .market-admin-home .top-viewed-row — hover-подсветка строк
	*   (background var(--bs-tertiary-bg), скругление, боковые паддинги);
	*   последняя строка в каждом списке — без нижней границы.
	*
	* Используемые плагины:
	*   — не используются напрямую (виджет автономен).
	*
	* Хуки (в market.admin.home.php):
	*   market.admin.home.tags — вызывается перед финальным парсингом шаблона;
	*                            позволяет плагинам добавить свои теги или
	*                            изменить виджет.
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
<div class="card border-0 shadow-sm mb-3 market-admin-home">
    <!-- Шапка -->
    <div class="card-header bg-primary bg-gradient text-white d-flex align-items-center justify-content-between py-2">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-store me-2"></i>
            <strong>{PHP.L.market}</strong>
		</div>
        <a href="{ADMIN_HOME_URL}" class="btn btn-sm btn-light py-0 px-2" title="{PHP.L.Administration}">
            <i class="fa-solid fa-arrow-right"></i>
		</a>
	</div>
	
    <div class="card-body py-3">
		
        <!-- Алерт: очередь на утверждение -->
        <!-- IF {ADMIN_HOME_HAS_PENDING} -->
        <a href="{ADMIN_HOME_QUEUE_URL}"
		class="alert alert-warning d-flex align-items-center text-decoration-none mb-3 py-2 px-2 small">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>
            <span class="flex-grow-1">{PHP.L.market_validation}</span>
            <span class="badge bg-warning text-dark">{ADMIN_HOME_PENDING}</span>
		</a>
        <!-- ENDIF -->
		
        <!-- Пустое состояние -->
        <!-- IF {ADMIN_HOME_IS_EMPTY} -->
        <div class="text-center text-muted small py-3">
            <i class="fa-solid fa-box-open fa-2x mb-2 d-block opacity-50"></i>
            {PHP.L.market_no_products}
		</div>
        <!-- ENDIF -->
		
        <!-- IF {ADMIN_HOME_HAS_ITEMS} -->
		<div class="alert alert-warning mb-3" role="alert">
			<div class="text-muted small fst-italic">{PHP.L.Total} {ADMIN_HOME_TOTAL}</div>
		</div>
		<!-- ENDIF -->
		
        <!-- Плитки состояний -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-success bg-opacity-10">
                    <div class="text-success fw-bold fs-5 lh-1">{ADMIN_HOME_PUBLISHED}</div>
                    <div class="small text-muted mt-1">{PHP.L.market_status_published}</div>
				</div>
			</div>
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-warning bg-opacity-10">
                    <div class="text-warning fw-bold fs-5 lh-1">{ADMIN_HOME_PENDING}</div>
                    <div class="small text-muted mt-1">{PHP.L.market_status_pending}</div>
				</div>
			</div>
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-secondary bg-opacity-10">
                    <div class="text-secondary fw-bold fs-5 lh-1">{ADMIN_HOME_DRAFTS}</div>
                    <div class="small text-muted mt-1">{PHP.L.market_status_draft}</div>
				</div>
			</div>
            <div class="col-6">
                <div class="border rounded p-2 text-center h-100 bg-primary bg-opacity-10">
                    <div class="text-primary fw-bold fs-5 lh-1">{ADMIN_HOME_TOTAL}</div>
                    <div class="small text-muted mt-1">{PHP.L.Total}</div>
				</div>
			</div>
		</div>
		
        <!-- Прогресс публикаций -->
        <div class="mb-3">
            <div class="d-flex justify-content-between small text-muted mb-1">
                <span><i class="fa-solid fa-circle-check text-success me-1"></i>{PHP.L.market_status_published}</span>
                <span class="fw-semibold">{ADMIN_HOME_PUBLISHED_PCT}%</span>
			</div>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-success" style="width:{ADMIN_HOME_PUBLISHED_PCT}%"></div>
			</div>
		</div>
		
        <!-- Активность -->
		<div class="d-flex align-items-center my-4">
			<hr class="flex-grow-1">
            <div class="small fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-chart-line me-1"></i>{PHP.L.market_stats_activity}
			</div>
			<hr class="flex-grow-1">
		</div>
        <div class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
                <span><i class="fa-solid fa-calendar-day me-1 text-muted"></i>{PHP.L.market_stats_today}</span>
                <strong>{ADMIN_HOME_TODAY}</strong>
			</div>
            <div class="d-flex justify-content-between small mb-1">
                <span><i class="fa-solid fa-calendar-week me-1 text-muted"></i>{PHP.L.market_stats_week}</span>
                <strong>{ADMIN_HOME_WEEK}</strong>
			</div>
            <div class="d-flex justify-content-between small mb-1">
                <span><i class="fa-solid fa-calendar me-1 text-muted"></i>{PHP.L.market_stats_month}</span>
                <strong>{ADMIN_HOME_MONTH}</strong>
			</div>
            <div class="d-flex justify-content-between small">
                <span><i class="fa-solid fa-eye me-1 text-muted"></i>{PHP.L.Hits}</span>
                <strong>{ADMIN_HOME_VIEWS}</strong>
			</div>
		</div>
		
		
        <!-- Последние товары -->
		<div class="d-flex align-items-center my-4">
			<hr class="flex-grow-1">
            <div class="small fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-clock-rotate-left me-1"></i>{PHP.L.market_stats_latest}
			</div>
			<hr class="flex-grow-1">
		</div>
        <!-- IF {ADMIN_HOME_ORPHANED} == 0 -->
        <div class="mb-3">
            <!-- BEGIN: RECENT_ITEMS -->
            <!-- BEGIN: RECENT_ROW -->
            <a href="{RECENT_ITEM_URL}"
			class="d-flex align-items-start text-decoration-none text-body py-1 border-bottom border-light-subtle small recent-row">
                <span class="badge bg-{RECENT_ITEM_STATE_CLASS} bg-opacity-25 text-{RECENT_ITEM_STATE_CLASS} me-2 flex-shrink-0">
                    #{RECENT_ITEM_ID}
				</span>
                <span class="flex-grow-1 text-truncate">
                    <span class="d-block text-truncate fw-medium">{RECENT_ITEM_TITLE}</span>
                    <span class="d-block text-muted small">
                        <i class="fa-regular fa-folder me-1"></i>{RECENT_ITEM_CAT}
                        · {RECENT_ITEM_DATE}
					</span>
				</span>
			</a>
            <!-- END: RECENT_ROW -->
            <!-- BEGIN: RECENT_EMPTY -->
            <div class="text-muted small fst-italic">{PHP.L.None}</div>
            <!-- END: RECENT_EMPTY -->
            <!-- END: RECENT_ITEMS -->
		</div>
        <!-- ENDIF -->
		
		
        <!-- Топ просматриваемых товаров -->
		<div class="d-flex align-items-center my-4">
			<hr class="flex-grow-1">
            <div class="small fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-fire text-danger me-1"></i>{PHP.L.market_stats_top_viewed}
			</div>
			<hr class="flex-grow-1">
		</div>
        <!-- BEGIN: TOP_VIEWED -->
        <div class="mb-3">
            <!-- BEGIN: TOP_VIEWED_ROW -->
            <a href="{TOP_VIEWED_URL}"
			class="d-flex justify-content-between align-items-center small text-decoration-none text-body py-1 top-viewed-row">
                <span class="text-truncate me-2">
                    <span class="badge bg-danger bg-opacity-25 text-danger me-1">#{TOP_VIEWED_ID}</span>
                    {TOP_VIEWED_TITLE}
                    <span class="text-muted">· {TOP_VIEWED_CAT}</span>
				</span>
                <span class="badge bg-danger bg-opacity-25 text-danger-emphasis flex-shrink-0">
                    <i class="fa-solid fa-eye me-1"></i>{TOP_VIEWED_COUNT}
				</span>
			</a>
            <!-- END: TOP_VIEWED_ROW -->
            <!-- BEGIN: TOP_VIEWED_EMPTY -->
            <div class="text-muted small fst-italic">{PHP.L.None}</div>
            <!-- END: TOP_VIEWED_EMPTY -->
		</div>
        <!-- END: TOP_VIEWED --> 
		
		
        <!-- Топ категорий -->
		<div class="d-flex align-items-center my-4">
			<hr class="flex-grow-1">
            <div class="small fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-layer-group me-1"></i>{PHP.L.market_stats_top_cats}
			</div>
			<hr class="flex-grow-1">
		</div>
        <!-- BEGIN: TOP_CATS -->
        <div class="mb-3">
            <!-- BEGIN: TOP_CAT_ROW -->
            <a href="{TOP_CAT_URL}"
			class="d-flex justify-content-between align-items-center small text-decoration-none text-body py-1 top-cat-row">
                <span class="text-truncate me-2">
                    <i class="fa-solid fa-folder text-muted me-1"></i>{TOP_CAT_TITLE}
				</span>
                <span class="badge bg-secondary bg-opacity-25 text-secondary">{TOP_CAT_COUNT}</span>
			</a>
            <!-- END: TOP_CAT_ROW -->
            <!-- BEGIN: TOP_CATS_EMPTY -->
            <div class="text-muted small fst-italic">{PHP.L.None}</div>
            <!-- END: TOP_CATS_EMPTY -->
		</div>
        <!-- END: TOP_CATS -->
		
        <!-- Топ продавцов -->
		<div class="d-flex align-items-center my-4">
			<hr class="flex-grow-1">
            <div class="small fw-semibold text-muted text-uppercase mx-2">
                <i class="fa-solid fa-user-tie me-1"></i>{PHP.L.market_vendors_title}
			</div>
			<hr class="flex-grow-1">
		</div>
        <!-- BEGIN: TOP_SELLERS -->
        <div class="mb-3">
            <!-- BEGIN: TOP_SELLER_ROW -->
            <a href="{TOP_SELLER_URL}"
			class="d-flex justify-content-between align-items-center small text-decoration-none text-body py-1 top-seller-row">
                <span class="text-truncate me-2">
                    <i class="fa-solid fa-user text-muted me-1"></i>{TOP_SELLER_NAME}
				</span>
                <span class="badge bg-info bg-opacity-25 text-info-emphasis">{TOP_SELLER_COUNT}</span>
			</a>
            <!-- END: TOP_SELLER_ROW -->
            <!-- BEGIN: TOP_SELLERS_EMPTY -->
            <div class="text-muted small fst-italic">{PHP.L.market_vendors_empty}</div>
            <!-- END: TOP_SELLERS_EMPTY -->
		</div>
        <!-- END: TOP_SELLERS -->
		
		<div class="d-flex align-items-center my-4">
			<hr class="flex-grow-1">
            <div class="small fw-semibold text-muted text-uppercase mx-2">
                <a href="{ADMIN_HOME_URL}" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-screwdriver-wrench me-2"></i>{PHP.L.Administration} </a>
			</div>
			<hr class="flex-grow-1">
		</div>
		
		<div class="row gy-3 mb-5">
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_HOME_CONFIG_URL}" class="btn btn-outline-secondary w-100">
				<i class="fa-solid fa-gear me-1"></i>{PHP.L.adm_market_configuration} </a>
			</div>
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_HOME_STRUCTURE_URL}" class="btn btn-outline-secondary w-100">
				<i class="fa-solid fa-list-ul me-1"></i>{PHP.L.adm_market_categories} </a>
			</div>
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_HOME_EXTRAFIELDS_URL}" class="btn btn-outline-secondary w-100">
				<i class="fa-solid fa-table-columns me-1"></i>{PHP.L.adm_market_extrafields} </a>
			</div>
			<div class="col-12 col-lg-3">
				<a href="{ADMIN_HOME_ADD_URL}" class="btn btn-outline-primary w-100" target="_blank">
				<i class="fa-solid fa-plus me-1"></i>{PHP.L.market_goto_add_new_item_title} </a>
			</div>
		</div>
		
	</div>
</div>
</div>
<style>
    .market-admin-home .recent-row:hover,
    .market-admin-home .top-cat-row:hover,
    .market-admin-home .top-seller-row:hover,
    .market-admin-home .top-viewed-row:hover {
	background: var(--bs-tertiary-bg);
	border-radius: .25rem;
	padding-left: .25rem;
	padding-right: .25rem;
    }
    .market-admin-home .recent-row:last-child,
    .market-admin-home .top-cat-row:last-child,
    .market-admin-home .top-seller-row:last-child,
    .market-admin-home .top-viewed-row:last-child {
	border-bottom: 0 !important;
    }
</style>

<!-- IF {TPL_PATH} --> 
<div class="container-xxl px-3 px-lg-5 py-5">
	<div class="alert alert-info" role="alert">
		{TPL_PATH}
	</div>
</div>
<hr>
<!-- ENDIF -->

<!-- END: MAIN -->