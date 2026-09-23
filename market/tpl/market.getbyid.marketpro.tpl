<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.getbyid.marketpro.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.getbyid.marketpro.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.getbyid.marketpro.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.functions.php
	*                     _ _ _ _ SEE: cot_market_get_by_id_owner_tpl()
	*                     _ _ _ _ SEE: cot_market_build_check_tags()
	* Назначение:
	*   Именованный шаблон произвольной конфигурации — вызывается вручную,
	*   когда нужен НЕ стандартный 'getbyid', а свой «сценарий отображения».
	*   Классический случай: вывести конкретный ID (например 31 — карточка
	*   самого модуля Market PRO) в своём стиле и в своём месте сайта.
	*
	*   В отличие от market.getbyid.<ID>.tpl (который привязан к ID через
	*   авто-подстановку cot_tplfile) этот шаблон вызывается явно по имени:
	*
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid.marketpro', 31)}
	*
	*   Имя «getbyid.marketpro» — это полностью контролируемое имя шаблона;
	*   его можно заменить на любое своё, например 'getbyid.recommend',
	*   'getbyid.promo', 'alias-some' и т.д. Тогда файл называть
	*   market.<ваше-имя>.tpl, например market.alias-some.tpl.
	*
	*   Приоритет поиска шаблона при вызове
	*     cot_market_get_by_id_owner_tpl('getbyid.marketpro', 31):
	*       1. market.getbyid.marketpro.tpl — этот файл (точное имя);
	*       2. market.getbyid.marketpro.31.tpl — если первого нет
	*                                            и ID ровно один.
	*
	* Основные параметры вызова:
	*   $templateOrTpl = 'getbyid.marketpro' — точное имя шаблона;
	*   $ids           = 31                  — ID товара (или список);
	*   $prefix        = 'MARKET_CHECK_'     — префикс тегов (по умолчанию).
	*
	*   Кастомный префикс (например 'PROMO_') — теги становятся
	*   {PROMO_31_ID}, {PROMO_31_URL}, {PROMO_31_TITLE}.
	*
	*   ВАЖНО: имя файла — это и есть «публичный API» шаблона. Договоритесь
	*   с самим собой: если шаблон называется market.getbyid.marketpro.tpl,
	*   то вызывать нужно именно 'getbyid.marketpro' (без расширения .tpl).
	*
	* Основные теги шаблона:
	*   MARKET_CHECK_31_ID      — числовой ID товара (пусто, если не найден);
	*   MARKET_CHECK_31_URL     — абсолютный URL карточки (алиас с фолбэком на ID);
	*   MARKET_CHECK_31_TITLE   — название товара (htmlspecialchars).
	*
	* Правила использования:
	*   1. Обязательно оборачивать в блок
	*        скобка!-- IF {MARKET_CHECK_31_ID} --скобка … скобка!-- ENDIF --скобка
	*      Если товара нет или он не опубликован (state != 0) —
	*      блок не отрисуется, пустой карточки не будет.
	*   2. Корневой блок — MAIN (стандарт XTemplate; только MAIN
	*      парсится функцией cot_market_get_by_id_owner_tpl()).
	*   3. Файл не участвует в авто-подстановке по ID (в отличие от
	*      market.getbyid.<ID>.tpl). Всегда вызывается явно по имени.
	*   4. Можно использовать несколько ID в одном файле — по одному
	*      блоку IF на каждый: {MARKET_CHECK_31_*}, {MARKET_CHECK_55_*}
	*      и т.д. Функция сформирует все нужные теги сразу.
	*   5. Для другого набора ID и/или другой разметки — создайте ещё
	*      один именованный файл, например market.getbyid.promo.tpl,
	*      и вызывайте его так:
	*        {PHP|cot_market_get_by_id_owner_tpl('getbyid.promo', '71,105')}
	*   6. Стили и тексты — на усмотрение темы; этот файл — образец
	*      («You may have been looking for this for a long time»).
	*   7. Если файл удалить — ничего не сломается: вызов вернёт пустую
	*      строку (шаблон не найден), общий getbyid не задействуется.
	*
	* Поддерживаемые вызовы:
	*   Из шаблона темы (CoTemplate):
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid.marketpro', 31)}
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid.marketpro', '31,55')}
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid.marketpro', 31, 'PROMO_')}
	*   Из PHP:
	*     $html = cot_market_get_by_id_owner_tpl('getbyid.marketpro', 31);
	*     $html = cot_market_get_by_id_owner_tpl('getbyid.marketpro', [31, 55]);
	*     $html = cot_market_get_by_id_owner_tpl('getbyid.marketpro', '31|55');
	*
	* Рекомендуемая обёртка:
	*   скобка!-- IF {PHP|cot_module_active('market')} --скобка
	*   <div class="col-12 px-3 mb-5">
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid.marketpro', 31)}
	*   </div>
	*   скобка!-- ENDIF --скобка
	*
	* Зависимости:
	*   - Cot::$db           — соединение с БД;
	*   - Cot::$usr / Cot::$cfg — язык и настройки (i18n, URL);
	*   - cot_tplfile()      — поиск файла шаблона в теме;
	*   - cot_market_url()   — построение URL карточки;
	*   - COT_ABSOLUTE_URL   — абсолютный базовый URL сайта;
	*   - XTemplate          — шаблонизатор Cotonti.
	*
	* Используемые плагины (опционально):
	*   i18n4marketpro — перевод заголовка товара для текущей локали.
	*
	* Хуки:
	*   — собственных хуков в этом шаблоне нет.
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
<div class="row my-3">
	
    <!-- IF {MARKET_CHECK_31_ID} -->

		<div class="gradient-border gradient-border-about-figure p-4">
		<p class="text-success fw-semibold">You may have been looking for this for a long time.:</p>
			<a href="{MARKET_CHECK_31_URL}" title="{MARKET_CHECK_31_TITLE}">
				{MARKET_CHECK_31_TITLE}
			</a>
		</div>

    <!-- ENDIF -->
	
</div>
<!-- END: MAIN -->

