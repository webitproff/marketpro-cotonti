<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.list.unvalidated.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.list.unvalidated.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.list.unvalidated.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.list.php
	* Назначение:
	*   Шаблон списка товаров для СПЕЦИАЛЬНЫХ категорий 'unvalidated' и
	*   'saved_drafts'. Подключается автоматически из market.list.php, когда
	*   в URL передан c=unvalidated (товары на модерации) или
	*   c=saved_drafts (черновики пользователя). В обоих случаях
	*   $cat['tpl'] выставляется в 'unvalidated', поэтому используется
	*   именно этот файл:
	*
	*     $mskin = cot_tplfile(['market', 'list', $cat['tpl']]);
	*     // $cat['tpl'] === 'unvalidated' → market.list.unvalidated.tpl
	*
	*   Отображает упрощённый список товаров (карточка с заголовком,
	*   статусом, описанием/обрезанным текстом, кнопками админа).
	*   Внешний вид карточек отличается от основного market.list.tpl:
	*   меньше сеточной вёрстки, акцент на модерацию/черновики.
	*
	*   ПРИМЕР:
	*     https://abuyfile.com/ru/market?c=unvalidated
	*     https://abuyfile.com/ru/market?c=saved_drafts
	*
	* Доступ:
	*   Доступ к обеим категориям требует прав на запись в модуль Market:
	*     list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin'])
	*         = cot_auth('market', 'any');
	*     cot_block(Cot::$usr['auth_write']);
	*   Т.е. гости и обычные читатели увидят 403. Владелец видит свои
	*   черновики/товары на модерации; админ — все.
	*
	* Основные параметры URL:
	*   c=unvalidated           — список товаров в очереди на модерацию
	*                             (fieldmrkt_state = STATE_PENDING);
	*   c=saved_drafts          — список черновиков пользователя
	*                             (fieldmrkt_state = STATE_DRAFT);
	*   d=<номер>               — страница пагинации товаров;
	*   dc=<номер>              — страница пагинации подкатегорий
	*                             (в этом шаблоне подкатегории не выводятся,
	*                              но параметр сохраняется в URL).
	*
	* Основные теги шаблона:
	*   Хлебные крошки и обёртка:
	*     LIST_BREADCRUMBS      — хлебные крошки (главная + market + метка
	*                              раздела «Валидация» / «Черновики»);
	*
	*   Одна карточка товара (BEGIN: LIST_ROW):
	*     LIST_ROW_URL          — URL карточки товара (алиас или id);
	*     LIST_ROW_TITLE        — название товара (экранированное);
	*     LIST_ROW_LOCAL_STATUS — локализованный статус (например
	*                              «На модерации» / «Черновик»);
	*     LIST_ROW_DESCRIPTION  — краткое описание (если задано);
	*     LIST_ROW_TEXT_CUT     — обрезанный полный текст;
	*     LIST_ROW_TEXT_IS_CUT  — флаг: текст был обрезан (показать
	*                              ссылку «Читать далее»);
	*     LIST_ROW_COMMENTS_COUNT — число комментариев;
	*     LIST_ROW_HITS         — счётчик просмотров;
	*     LIST_ROW_ADMIN        — флаг: показать блок админ-кнопок
	*                              (доступен админу и владельцу);
	*     LIST_ROW_ADMIN_EDIT   — HTML-ссылка «Редактировать»;
	*     LIST_ROW_ADMIN_DELETE — HTML-ссылка «Удалить» (с подтверждением).
	*
	*     Стандартные теги товара с префиксом LIST_ROW_* формируются
	*     через cot_generate_markettags() и включают все прочие поля
	*     (см. market.list.php → основной цикл по $sqllist_rowset).
	*
	*   Пагинация (BEGIN неявный, проверка IF {LIST_PAGINATION}):
	*     LIST_PAGINATION       — HTML списка страниц;
	*     LIST_PREVIOUS_PAGE    — ссылка «Предыдущая»;
	*     LIST_NEXT_PAGE        — ссылка «Следующая»;
	*     LIST_CURRENT_PAGE     — текущая страница;
	*     LIST_TOTAL_PAGES      — всего страниц.
	*
	*     Приставка LIST_ используется, потому что теги пагинации в этом
	*     шаблоне генерируются cot_generatePaginationTags($pagenav) —
	*     для основного market.list.tpl без префикса, а для пагинации
	*     подкатегорий — с префиксом LIST_CAT_. В этом шаблоне (файл
	*     unvalidated) — тоже с префиксом LIST_ (см. момент формирования
	*     пагинации в market.list.php).
	*
	*   Правая колонка (только для группы maingrp == 5):
	*     LIST_SUBMIT_NEW_PAGE  — HTML-ссылка «Добавить товар»
	*                              (получает из market_submitnewitem);
	*     Если админ — выводится ссылка на панель администратора
	*     ({PHP.L.Adminpanel}, {PHP|cot_url('admin')}).
	*
	*   Подвал (для группы maingrp == 5):
	*     {FILE "…/inc/mskin.tpl"} — путь к шаблону темы, если задан {PHP.mskin}.
	*
	* Особенности:
	*   1. Файл ОБЯЗАН быть в теме (или в modules/market/tpl по умолчанию).
	*      Если файл отсутствует — market.list.php вызовет cot_error().
	*   2. Отображаются только товары, к которым у пользователя есть
	*      доступ по правам записи (см. cot_auth('market', 'any')).
	*   3. Категория подкатегорий в этом шаблоне НЕ выводится (в отличие
	*      от market.list.tpl): этот список используется для модерации
	*      и черновиков, а не для навигации по дереву.
	*   4. Список товаров сортируется по fieldmrkt_date DESC — самые
	*      новые сверху.
	*
	* Используемые плагины (опционально):
	*   attacher  — если включён, LIST_ROW_* содержит теги для изображений
	*               (в этом файле напрямую не используются).
	*   Прочие плагины, добавляющие теги в cot_generate_markettags(),
	*   работают и здесь.
	*
	* Хуки (в market.list.php, влияют на этот шаблон):
	*   market.list.first        — в начале страницы;
	*   market.list.query        — перед SQL-запросом;
	*   market.list.main         — после подготовки данных и шаблона;
	*   market.list.rowcat.first — перед списком подкатегорий
	*                              (в этом шаблоне не используются);
	*   market.list.rowcat.loop  — внутри цикла подкатегорий
	*                              (в этом шаблоне не используются);
	*   market.list.before_loop  — перед циклом вывода товаров;
	*   market.list.loop         — внутри цикла товаров;
	*   market.list.tags         — перед финальным парсингом шаблона.
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

<div class="border-bottom border-secondary py-3 px-3">
  <nav aria-label="breadcrumb">
    <div class="ps-container-breadcrumb">
      <ol class="breadcrumb d-flex mb-0">{LIST_BREADCRUMBS}</ol>
    </div>
  </nav>
</div>
<div class="min-vh-50 px-2 px-md-3 py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10 col-xxl-9"> 
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"} 
	<div class="row">
        <div class="col-12 col-md-8 mx-auto pt-4">
          <!-- BEGIN: LIST_ROW -->
          <div class="card mb-4">
            <div class="card-header bg-secondary-subtle text-dark">
              <h2 class="h5 card-title mb-0">
                <a href="{LIST_ROW_URL}" title="{LIST_ROW_TITLE}">{LIST_ROW_TITLE}</a>
              </h2>
            </div>
            <div class="card-body">
              <p class="mb-1">
                <strong>{PHP.L.Status}:</strong>
                <span class="badge bg-warning">{LIST_ROW_LOCAL_STATUS}</span>
              </p>
              <!-- IF {LIST_ROW_DESCRIPTION} -->
              <p class="card-text text-muted small">{LIST_ROW_DESCRIPTION}</p>
              <!-- ENDIF -->
              <div class="card-text"> {LIST_ROW_TEXT_CUT|strip_tags($this)}
                <!-- IF {LIST_ROW_TEXT_IS_CUT} -->
                <a href="{LIST_ROW_URL}" class="btn btn-outline-primary btn-sm mt-2">{PHP.L.ReadMore}</a>
                <!-- ENDIF -->
                <!-- IF {LIST_ROW_ADMIN} --> {LIST_ROW_ADMIN_EDIT} {LIST_ROW_ADMIN_DELETE} ({LIST_ROW_HITS})
                <!-- ENDIF -->
              </div>
              <!-- IF {LIST_ROW_COMMENTS_COUNT} > 0 -->
              <div class="position-absolute top-0 end-0 mt-2 me-2">
                <span class="badge bg-primary">{LIST_ROW_COMMENTS_COUNT}</span>
              </div>
              <!-- ENDIF -->
            </div>
          </div>
          <!-- END: LIST_ROW -->
          <!-- IF {LIST_PAGINATION} -->
          <nav aria-label="Page Pagination" class="mt-3">
            <div class="text-center mb-2">{PHP.L.Page} {LIST_CURRENT_PAGE} {PHP.L.Of} {LIST_TOTAL_PAGES}</div>
            <ul class="pagination justify-content-center"> {LIST_PREVIOUS_PAGE} {LIST_PAGINATION} {LIST_NEXT_PAGE} </ul>
          </nav>
          <!-- ENDIF -->
        </div>
        <div class="col-12 col-md-4 mx-auto">
          <!-- IF {PHP.usr.maingrp} == 5 -->
          <div class="card mt-4 mb-4">
            <div class="card-header">
              <h2 class="h5 mb-0">{PHP.L.2wd_publicCardAdmin}</h2>
            </div>
            <div class="card-body">
              <ul class="list-unstyled mb-0">
                <!-- IF {PHP.usr.isadmin} -->
                <li>
                  <a href="{PHP|cot_url('admin')}">{PHP.L.Adminpanel}</a>
                </li>
                <!-- ENDIF -->
                <li>{LIST_SUBMIT_NEW_PAGE}</li>
              </ul>
            </div>
          </div>
          <!-- ENDIF -->
        </div>
      </div>
    </div>
  </div>
</div>
<!-- IF {PHP.usr.maingrp} == 5 AND {PHP.mskin} --> {FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/mskin.tpl"}
<!-- ENDIF -->
<!-- END: MAIN -->