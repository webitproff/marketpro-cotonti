<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.add.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.add.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.add.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.add.php
	* Назначение:
	*   Шаблон формы добавления нового товара модуля Market. Отображает
	*   форму создания товара с полями: категория, название, H1, краткое
	*   описание, алиас (ЧПУ), meta-заголовок, meta-описание, парсер,
	*   полный текст, цена (базовая валюта + международная с пересчётом),
	*   артикул (PCOD), extrafields товара, изображения (Attacher),
	*   публикация в Telegram (tgm4market). Кнопки отправки: опубликовать
	*   (для админа с автовалидацией), предпросмотр, черновик, на модерацию.
	*
	*   Форма отправляется POST на {MARKETADD_FORM_SEND} (m=add&a=add).
	*   Обработка и валидация — в market.add.php.
	*
	*   
	*
	* Основные параметры URL:
	*   m=add                   — метод модуля (форма добавления);
	*   c=<код категории>       — предустановленная категория товара;
	*   clone=<ID>              — клонировать существующий товар (данные подставляются).
	*
	* Основные теги шаблона:
	*   Общие:
	*     MARKETADD_BREADCRUMBS        — хлебные крошки формы;
	*     MARKETADD_FORM_SEND          — URL action формы (m=add&a=add&c=…);
	*     MARKETADD_HAS_EXTRAFIELDS    — флаг: есть ли extrafields у cot_market;
	*     TPL_PATH                     — путь к файлу шаблона (только админ).
	*
	*   Категория (варианты селектов):
	*     MARKETADD_FORM_CAT           — обычный selectbox структуры;
	*     MARKETADD_FORM_CAT_SHORT     — selectbox с ограничением по родителю $c;
	*     MARKETADD_FORM_CAT_S2        — Select2-версия;
	*     MARKETADD_FORM_CAT_SHORT_S2  — Select2 с ограничением по родителю.
	*
	*   Основные поля:
	*     MARKETADD_FORM_TITLE         — название товара;
	*     MARKETADD_FORM_META_H1       — SEO H1;
	*     MARKETADD_FORM_DESCRIPTION   — краткое описание;
	*     MARKETADD_FORM_ALIAS         — алиас (ЧПУ);
	*     MARKETADD_FORM_META_TITLE    — meta-заголовок;
	*     MARKETADD_FORM_META_DESC     — meta-описание;
	*     MARKETADD_FORM_PARSER        — выбор парсера текста;
	*     MARKETADD_FORM_TEXT          — полный текст (WYSIWYG/редактор);
	*     MARKETADD_FORM_COSTDFLT      — цена в базовой валюте;
	*     MARKETADD_FORM_COST_USD      — цена в международной валюте (пересчёт на JS);
	*     MARKETADD_FORM_PCOD          — артикул / код товара.
	*
	*   Владелец и дата:
	*     MARKETADD_FORM_OWNER         — HTML-ссылка на текущего пользователя;
	*     MARKETADD_FORM_OWNER_ID      — ID текущего пользователя;
	*     MARKETADD_FORM_DATE          — selectbox даты публикации.
	*
	*   Extrafields (BEGIN: EXTRAFLD):
	*     MARKETADD_FORM_EXTRAFLD         — HTML-элемент поля;
	*     MARKETADD_FORM_EXTRAFLD_TITLE   — локализованный заголовок;
	*     MARKETADD_FORM_EXTRAFLD_CODENAME— машинное имя поля;
	*     MARKETADD_FORM_<ИМЯ>[_TITLE]    — прямой вывод конкретного поля.
	*
	*   Глобальные (шаблон темы, из Cotonti):
	*     PHP.usr_can_publish          — true, если админ + marketautovalidate=1
	*                                    (показывает кнопку «Опубликовать»);
	*     PHP.L.*                      — языковые строки формы;
	*     PHP.cfg.payments.valuta      — валюта базовой цены;
	*     PHP.cfg.market.market_currency — валюта модуля (fallback);
	*     PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd
	*                                  — курс для JS-пересчёта цены.
	*
	* Кнопки отправки формы (name → value):
	*   ritemmarketstate=0   — опубликовать (только если {PHP.usr_can_publish});
	*   preview=1            — предпросмотр (сохраняет как черновик, редирект на preview);
	*   ritemmarketstate=2   — сохранить как черновик;
	*   ritemmarketstate=1   — отправить на модерацию.
	*
	* JS-конвертер цены:
	*   Пересчитывает значение {MARKETADD_FORM_COST_USD} в базовую валюту
	*   по курсу cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd
	*   и пишет результат в <strong id="result_rate_value_to_fieldmrkt_costdflt">.
	*   Слушает события input/change на document (capture) с фильтром
	*   по name="ritemmarketcostusd", плюс первичный расчёт на window.load
	*   (нужен при возврате формы после ошибки валидации).
	*
	* Используемые плагины (опционально):
	*   aliasmarketpro  — генерация ЧПУ-алиасов (ссылка в шаблоне для админа);
	*   attacher        — загрузка изображений и файлов;
	*   tgm4market      — публикация товара в Telegram-канал ({TGM4MARKET_ADD_ACTION}).
	*
	* Хуки (в market.add.php):
	*   market.add.first          — в начале, до основных проверок;
	*   market.add.add.first      — перед импортом данных из POST;
	*   market.add.add.import     — после импорта, до валидации;
	*   market.add.add.error      — после валидации (если есть ошибки);
	*   market.add.main           — после подготовки основных переменных и шаблона;
	*   market.add.tags           — перед финальным парсингом шаблона.
	*
	* Source and updates   https://github.com/webitproff/marketpro-cotonti
	* ReadMeMore:          https://abuyfile.com/ru/market/cotonti/plugs/marketpro
	* Support:             https://abuyfile.com/ru/forums/cotonti/custom/marketpro
	* API Extrafields:     https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
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
			<ol class="breadcrumb d-flex mb-0">{MARKETADD_BREADCRUMBS}</ol>
		</div>
	</nav>
</div>
<div class="container-xxl px-3 px-lg-5 py-5">
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	<!-- IF !{PHP.usr_can_publish} -->
	<div class="mb-3 mt-3">
		<div class="alert alert-info" role="alert">{PHP.L.market_formhint}</div>
	</div>
	<!-- ENDIF -->
	<div class="row justify-content-center">
		<div class="col-12 mb-3">
			<div class="card mt-4 mb-4">
				<div class="card-header">
					<h2 class="h5 mb-0">{PHP.L.market_form_add_item_title}</h2>
					<p class="fw-semibold">{PHP.L.market_form_add_item_subtitle}</p>
				</div>
				<div class="card-body">
					
					<form action="{MARKETADD_FORM_SEND}" enctype="multipart/form-data" method="post" name="marketform" class="needs-validation" novalidate>
						<div class="alert alert-info">
							<p class="fw-semibold">{PHP.L.market_form_owner} {MARKETADD_FORM_OWNER}</p>
							<small class="form-text text-muted">{PHP.L.market_form_owner_hint}</small>
						</div>
						
						<div class="col-12 mb-3">
							<label for="marketCat" class="form-label fw-semibold"><span class="me-2 text-danger">&#128681;</span>{PHP.L.market_form_category}</label>
							<div class="input-group has-validation">{MARKETADD_FORM_CAT_S2}</div>
							<small class="form-text text-muted">{PHP.L.market_form_category_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
							<label for="marketTitle" class="form-label fw-semibold"><span class="me-2 text-danger">&#128681;</span>{PHP.L.market_form_item_title}</label>
							<div class="input-group has-validation">{MARKETADD_FORM_TITLE}</div>
							<small class="form-text text-muted">{PHP.L.market_form_item_title_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
							<label for="marketTitleH1" class="form-label fw-semibold">{PHP.L.market_form_meta_h1}</label>
							<div class="input-group has-validation">
								{MARKETADD_FORM_META_H1}
							</div>
							<small class="form-text text-muted">{PHP.L.market_form_meta_h1_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
							<label for="marketDesc" class="form-label fw-semibold">{PHP.L.market_form_item_desc}</label>
							<div class="input-group has-validation">{MARKETADD_FORM_DESCRIPTION}</div>
							<small class="form-text text-muted">{PHP.L.market_form_item_desc_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
						<!-- IF {PHP.usr.maingrp} == 5 -->
						<!-- IF {PHP|cot_plugin_active('aliasmarketpro')} -->
						<div class="alert alert-info py-0 mb-0">
							{PHP.L.Plugin} 
							<a href="{PHP|cot_url('admin', 'm=extensions&a=details', '&pl=aliasmarketpro')}" target="_blank" class="text-decoration-none fw-semibold">{PHP.L.aliasmarketpro_title}
							</a>
						</div>
						<!-- ENDIF -->
						<!-- ENDIF -->
							<label for="marketAlias" class="form-label fw-semibold">{PHP.L.market_form_item_alias}</label>
							<div class="input-group has-validation">{MARKETADD_FORM_ALIAS}</div>
							<small class="form-text text-muted">{PHP.L.market_form_item_alias_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
							<label for="marketMetaTitle" class="form-label fw-semibold">{PHP.L.market_form_meta_title}</label>
							<div class="input-group has-validation">{MARKETADD_FORM_META_TITLE}</div>
							<small class="form-text text-muted">{PHP.L.market_form_meta_title_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
							<label for="marketMetaDesc" class="form-label fw-semibold">{PHP.L.market_form_meta_desc}</label>
							<div class="input-group has-validation">{MARKETADD_FORM_META_DESC}</div>
							<small class="form-text text-muted">{PHP.L.market_form_meta_desc_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
							<label for="marketParser" class="form-label fw-semibold">{PHP.L.market_form_parser}</label>
							<div class="input-group has-validation">{MARKETADD_FORM_PARSER}</div>
							<small class="form-text text-muted">{PHP.L.market_form_parser_hint}</small>
						</div>
						<hr>
						<div class="col-12 mb-3">
							<label for="marketText" class="form-label fw-semibold"><span class="me-2 text-danger">&#128681;</span>{PHP.L.market_form_text_full}</label>
							<div><small class="form-text text-muted">{PHP.L.market_form_text_full_hint}</small></div>
							<div class="input-group has-validation">{MARKETADD_FORM_TEXT}</div>
						</div>
						<div class="col-12 mb-3">
							<div class="mb-3 row">
								<label class="col-lg-3 col-form-label fw-semibold">{PHP.L.market_form_item_fieldmrkt_costdflt}</label>
								<div class="col-lg-9">
									<div class="input-group">
										{MARKETADD_FORM_COSTDFLT}
										<span class="input-group-text">
											<!-- IF {PHP.cfg.payments.valuta} -->
											{PHP.cfg.payments.valuta}
											<!-- ELSE -->
											{PHP.cfg.market.market_currency}
											<!-- ENDIF -->
										</span>
									</div>
								</div>
							</div>
							<hr>
							<div class="mb-3 row">
								<div class="col-lg-3">
									<label class="form-label fw-semibold">
										{PHP.L.market_form_item_cost_usd}
									</label>
								</div>
								<div class="col-lg-9">
									<small class="text-muted">
										{PHP.L.market_form_item_price_base_after_rate}: <strong id="result_rate_value_to_fieldmrkt_costdflt">0.00</strong> {PHP.cfg.market.market_currency}
									</small>	
									<div class="input-group">
										{MARKETADD_FORM_COST_USD}
										<span class="input-group-text">{PHP.cfg.market.market_for_rate_base_currency_cost_usd}</span>
									</div>
									<small class="text-muted">{PHP.L.market_form_item_cost_usd_hint}</small>
								</div>
							</div>
						</div>

						<hr>
						<div class="col-12 mb-3">
							<label class="col-sm-3 col-form-label fw-semibold">{PHP.L.market_form_item_pcod}</label>
							<div class="col-sm-9">
								<div class="input-group">
									{MARKETADD_FORM_PCOD}
									<span class="input-group-text">
										&#35;
									</span>
								</div>
							</div>
							<small class="form-text text-muted">{PHP.L.market_form_item_pcod_hint}</small>
						</div>
						<hr>
						<!-- IF {PHP.usr.maingrp} == 5 -->
						<!-- IF {MARKETADD_HAS_EXTRAFIELDS} -->
						<div class="alert alert-info">
							<div><span class="me-2 fs-3">&#129668;</span>{PHP.L.market_form_extrafield} {PHP.L.market_form_extrafield_link}</div>
							<div><small class="form-text text-muted">{PHP.L.market_form_extrafield_hint} </small></div>
						</div>
						<!-- ELSE -->
						<div class="alert alert-warning">
							<div><span class="me-2 fs-3">&#129668;</span>{PHP.L.market_form_extrafield_not_found} {PHP.L.market_form_extrafield_link}</div>
							<div><small class="form-text text-muted">{PHP.L.market_form_extrafield_hint} </small></div>
						</div>
						<!-- ENDIF --> 
						<!-- ENDIF -->
						<!-- BEGIN: EXTRAFLD -->
						<div class="col-12 mb-3">
							<label class="form-label fw-semibold">
								{MARKETADD_FORM_EXTRAFLD_TITLE}
							</label>
							<!-- IF {MARKETADD_FORM_EXTRAFLD_CODENAME} -->
							<small class="text-muted">({MARKETADD_FORM_EXTRAFLD_CODENAME})</small>
							<!-- ENDIF -->
							<div class="input-group">{MARKETADD_FORM_EXTRAFLD}</div>
						</div>
						<!-- END: EXTRAFLD -->
						<hr>
						<!-- IF {PHP|cot_plugin_active('attacher')} -->
						<!-- IF {PHP|cot_auth('plug', 'attacher', 'W')} -->
						<div class="col-12 mb-3">
							<label class="form-label fw-semibold">{PHP.L.att_add_pict_files}</label>
							<div class="input-group">{PHP|att_filebox('market', 0)}</div>
						</div>
						<!-- ENDIF -->
						<!-- ENDIF -->
						<!-- IF {PHP|cot_plugin_active('tgm4market')} -->
						<!-- IF {PHP|cot_auth('plug', 'tgm4market', 'W')} -->
						<hr>
						<div class="col-12 mb-3">
							{TGM4MARKET_ADD_ACTION}
						</div>
						<!-- ENDIF -->
						<!-- ENDIF -->
						<hr>
						<div class="col-12 mb-3">
							<div class="d-grid gap-2 d-md-flex justify-content-md-end">
								<!-- IF {PHP.usr_can_publish} -->
								<button type="submit" name="ritemmarketstate" value="0" class="btn btn-success">{PHP.L.Publish}</button>
								<!-- ENDIF -->
								<button type="submit" name="preview" value="1" class="btn btn-info">{PHP.L.market_preview}</button>
								<button type="submit" name="ritemmarketstate" value="2" class="btn btn-secondary">{PHP.L.Saveasdraft}</button>
								<button type="submit" name="ritemmarketstate" value="1" class="btn btn-warning">{PHP.L.Submitforapproval}</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    // ============================================================
    // Пересчёт цены: МЕЖДУНАРОДНАЯ валюта → БАЗОВАЯ валюта.
    // Работает на странице добавления товара (market&m=add).
    // Логика идентична той, что в market.edit.tpl.
    // ============================================================

    // Самовызывающаяся функция (IIFE) — изолирует переменные
    // (RATE, upd) от глобальной области, чтобы не пересекаться
    // с другими скриптами на странице.
    (function () {

        // --------------------------------------------------------
        // Курс пересчёта.
        // Значение: сколько единиц БАЗОВОЙ валюты содержится
        // в 1 единице МЕЖДУНАРОДНОЙ валюты.
        // Источник: cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd —
        // параметр модуля Market PRO, подставляется PHP при рендере шаблона.
        // Если значение пустое или не число — parseFloat вернёт NaN,
        // тогда "|| 1" подставит fallback = 1.
        // --------------------------------------------------------
        var RATE = parseFloat('{PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd}') || 1;

        // --------------------------------------------------------
        // upd(el) — пересчёт и запись результата в <strong>.
        // Принимает элемент <input> с введённой суммой в
        // международной валюте.
        // --------------------------------------------------------
        function upd(el) {

            // Элемент вывода пересчитанной суммы в БАЗОВОЙ валюте.
            // id="result_rate_value_to_fieldmrkt_costdflt" задан
            // в разметке формы выше (внутри блока цены).
            var span = document.getElementById('result_rate_value_to_fieldmrkt_costdflt');

            // Если span не найден или переданный элемент пуст —
            // выходим без ошибок, чтобы не уронить остальные обработчики.
            if (!span || !el) return;

            // Значение поля. Если строка пустая или не число —
            // parseFloat вернёт NaN, "|| 0" превратит его в 0.
            var v = parseFloat(el.value) || 0;

            // Пересчёт: международная сумма × курс = базовая сумма.
            // toFixed(2) — округление до 2 знаков после запятой.
            // Результат записывается как текст в <strong>.
            span.textContent = (v * RATE).toFixed(2);
        }

        // --------------------------------------------------------
        // Обработчик события 'input' — срабатывает при каждом
        // изменении значения в поле (набор с клавиатуры, вставка).
        // Делегирование на document: слушатель висит на всём документе
        // и перехватывает события от любых полей.
        // Третий параметр true — фаза захвата (capture): событие
        // доходит до нашего обработчика до других слушателей
        // и не может быть остановлено ими.
        // Фильтр по e.target.name: обрабатываем только поле
        // с name="ritemmarketcostusd" (рендерится макросом
        // {MARKETADD_FORM_COST_USD}). Остальные события игнорируем.
        // --------------------------------------------------------
        document.addEventListener('input', function (e) {
            if (e.target && e.target.name === 'ritemmarketcostusd') upd(e.target);
        }, true);

        // --------------------------------------------------------
        // Обработчик события 'change' — срабатывает при потере
        // фокуса, изменении значения стрелками вверх/вниз,
        // автозаполнении браузером и т.п.
        // Логика та же, что и в 'input': делегирование на document,
        // фаза захвата, фильтр по name поля.
        // --------------------------------------------------------
        document.addEventListener('change', function (e) {
            if (e.target && e.target.name === 'ritemmarketcostusd') upd(e.target);
        }, true);

        // --------------------------------------------------------
        // Первичный расчёт после полной загрузки страницы.
        // Нужен для случая, когда поле уже заполнено до срабатывания
        // наших обработчиков (например, при возврате на форму после
        // ошибки валидации — поле сохраняет введённое значение,
        // а события input/change при этом не срабатывают).
        // Находим поле по name, если оно есть — запускаем пересчёт.
        // --------------------------------------------------------
        window.addEventListener('load', function () {
            var el = document.querySelector('input[name="ritemmarketcostusd"]');
            if (el) upd(el);
        });

    })();
</script>


<!-- IF {PHP.usr.maingrp} == 5 -->
<div class="container-xxl px-3 px-lg-5 py-5">
	<div class="alert alert-secondary">
		<p class="fw-semibold">{TPL_PATH}</p>
	</div>
</div>
<!-- ENDIF -->
<!-- END: MAIN -->


<!-- пока оставляю как альтернатива для конвертора валюты -->
<script>
// ============================================================
// Пересчёт цены: международная валюта → базовая валюта.
// Работает на странице добавления товара (market&m=add).
// Логика идентична той, что в market.edit.tpl.
// ============================================================

// ------------------------------------------------------------
// Блок 1. Реакция на событие 'input' (ввод с клавиатуры).
// Делегирование на document — ловит событие даже если поле
// появилось в DOM позже (или до) парсинга этого скрипта.
// Третий параметр true — фаза захвата (capture), чтобы
// событие долетело до document до остановки другими обработчиками.
// ------------------------------------------------------------
document.addEventListener('input', function (e) {

    // Фильтр: обрабатываем только поле с name="ritemmarketcostusd".
    // Все остальные события input игнорируем.
    // Поле рендерится макросом {MARKETADD_FORM_COST_USD}.
    if (!e.target || e.target.name !== 'ritemmarketcostusd') return;

    // Находим <strong>, куда пишем пересчитанное значение.
    // id="result_rate_value_to_fieldmrkt_costdflt" задан в разметке выше.
    var span = document.getElementById('result_rate_value_to_fieldmrkt_costdflt');

    // Если элемента нет на странице — выходим без ошибок.
    if (!span) return;

    // Курс: сколько единиц БАЗОВОЙ валюты в 1 единице МЕЖДУНАРОДНОЙ.
    // Значение берётся из настроек модуля:
    // cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd.
    // PHP подставляет значение на этапе рендера шаблона.
    // "|| 1" — страховка: если значение пустое или не число,
    // parseFloat вернёт NaN, а "|| 1" заменит его на 1.
    var rate = parseFloat('{PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd}') || 1;

    // Читаем значение из поля ввода.
    // Пустая строка или не число → 0.
    var v = parseFloat(e.target.value) || 0;

    // Умножаем международную сумму на курс → базовая сумма.
    // toFixed(2) — округление до 2 знаков после запятой.
    // Записываем результат в <strong>.
    span.textContent = (v * rate).toFixed(2);

}, true);

// ------------------------------------------------------------
// Блок 2. Реакция на событие 'change' (потеря фокуса, стрелки,
// автозаполнение, вставка из буфера). Аналогичен блоку 1.
// ------------------------------------------------------------
document.addEventListener('change', function (e) {

    // Тот же фильтр по name поля.
    if (!e.target || e.target.name !== 'ritemmarketcostusd') return;

    // Тот же элемент вывода.
    var span = document.getElementById('result_rate_value_to_fieldmrkt_costdflt');
    if (!span) return;

    // Тот же курс из конфига модуля.
    var rate = parseFloat('{PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd}') || 1;

    // Тот же пересчёт.
    var v = parseFloat(e.target.value) || 0;
    span.textContent = (v * rate).toFixed(2);

}, true);

// ------------------------------------------------------------
// Блок 3. Первичный расчёт при полной загрузке страницы.
// Нужен, если поле уже заполнено (например, при возврате
// на форму после ошибки валидации). Событие 'input'/'change'
// в этом случае не сработает — значение уже в поле.
// ------------------------------------------------------------
window.addEventListener('load', function () {

    // Поле ввода цены в международной валюте.
    var el = document.querySelector('input[name="ritemmarketcostusd"]');

    // Элемент вывода пересчитанной суммы.
    var span = document.getElementById('result_rate_value_to_fieldmrkt_costdflt');

    // Если хотя бы одного элемента нет — выходим.
    if (!el || !span) return;

    // Курс из конфига.
    var rate = parseFloat('{PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd}') || 1;

    // Читаем текущее значение поля и пересчитываем.
    var v = parseFloat(el.value) || 0;
    span.textContent = (v * rate).toFixed(2);

});
</script>
<script>
(function () {
    // Курс: сколько единиц БАЗОВОЙ валюты в 1 единице МЕЖДУНАРОДНОЙ.
    // Источник: cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd.
    // Если значение пустое/невалидное — parseFloat вернёт NaN, fallback "|| 1".
    var RATE = parseFloat('{PHP.cfg.market.market_rate_value_fieldmrkt_costdflt_to_cost_usd}') || 1;

    // Пересчёт: берёт число из input, умножает на RATE,
    // округляет до 2 знаков и записывает в <strong id="result_rate_value_to_fieldmrkt_costdflt">.
    function upd(el) {
        // Элемент вывода пересчитанной суммы в базовой валюте
        var span = document.getElementById('result_rate_value_to_fieldmrkt_costdflt');
        // Если span или поле не найдены — выходим
        if (!span || !el) return;
        // Значение поля; пусто/не число → 0
        var v = parseFloat(el.value) || 0;
        // Международная сумма × курс → базовая сумма, 2 знака после запятой
        span.textContent = (v * RATE).toFixed(2);
    }

    // Делегирование: ловим input на уровне document (фаза захвата),
    // фильтруем по name="ritemmarketcostusd" — поле международной валюты
    document.addEventListener('input', function (e) {
        if (e.target && e.target.name === 'ritemmarketcostusd') upd(e.target);
    }, true);

    // То же для change (потеря фокуса, стрелки, автозаполнение)
    document.addEventListener('change', function (e) {
        if (e.target && e.target.name === 'ritemmarketcostusd') upd(e.target);
    }, true);

    // Первичный расчёт после полной загрузки страницы —
    // чтобы показать актуальную сумму для уже заполненного поля
    window.addEventListener('load', function () {
        var el = document.querySelector('input[name="ritemmarketcostusd"]');
        if (el) upd(el);
    });
})();
</script>