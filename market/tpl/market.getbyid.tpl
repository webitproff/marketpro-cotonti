<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.getbyid.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.getbyid.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.getbyid.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.functions.php
	*                     _ _ _ _ SEE: cot_market_get_by_id_owner_tpl()
	*                     _ _ _ _ SEE: cot_market_build_check_tags()
	*                     _ _ _ _ SEE: cot_market_get_by_id_as_recommend()
	* Назначение:
	*   Универсальный (базовый) шаблон для вывода одного или нескольких товаров
	*   Market по их ID в любом месте сайта — из index.tpl, page.tpl, шаблонов
	*   сторонних модулей и т.д. Служит «фолбэком»: если для конкретного ID
	*   нет персонального шаблона market.getbyid.<ID>.tpl, будет использован
	*   именно этот файл.
	*
	*   Теги вида {MARKET_CHECK_<ID>_ID|URL|TITLE} формируются функцией
	*   cot_market_build_check_tags() и подставляются автоматически. Если
	*   товар с указанным ID не найден или не опубликован (state != 0),
	*   соответствующий блок IF … ENDIF просто не рендерится — шаблон
	*   не ломается.
	*
	*   ПРИМЕР (обёртка в шаблоне темы):
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29,71')}
	*
	* Логика поиска шаблона (cot_tplfile):
	*   1. market.<template>.tpl          — этот файл (например market.getbyid.tpl);
	*   2. market.<template>.<id>.tpl     — если ID ровно один и есть
	*                                       персональный шаблон под него
	*                                       (например market.getbyid.29.tpl).
	*
	*   Приоритет: сначала ищется файл с точным именем, потом — с ID в имени.
	*   Итог: вызов
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29)}
	*   подхватит market.getbyid.29.tpl (если он есть), иначе откатится
	*   на этот market.getbyid.tpl.
	*
	* Основные параметры вызова (в шаблоне темы):
	*   $templateOrTpl = 'getbyid'          — часть имени шаблона;
	*   $ids           = 29 | '29' | '29,71' | '29 71' — один ID или список;
	*   $prefix        = 'MARKET_CHECK_'    — префикс тегов (опционально).
	*
	*   Из шаблона CoTemplate передаются только одиночные числа/строки
	*   и строки через запятую/пробел. Массивы и разделитель «|» — только
	*   при вызове из PHP.
	*
	* Основные теги шаблона (для каждого запрошенного ID):
	*   MARKET_CHECK_<ID>_ID      — числовой ID товара (пусто, если не найден);
	*   MARKET_CHECK_<ID>_URL     — абсолютный URL карточки товара
	*                                (алиас с фолбэком на ID);
	*   MARKET_CHECK_<ID>_TITLE   — название товара (htmlspecialchars).
	*
	*   При кастомном префиксе (например 'RECO_') имена тегов меняются
	*   на {RECO_<ID>_ID}, {RECO_<ID>_URL}, {RECO_<ID>_TITLE} — структура
	*   полностью идентична.
	*
	* Правила использования:
	*   1. Каждый товар оборачивается в блок
	*        скобка!-- IF {MARKET_CHECK_<ID>_ID} --скобка … скобка!-- ENDIF --скобка
	*      Иначе при отсутствии товара в каталоге появятся пустые карточки.
	*   2. Можно вывести любое количество ID в одном файле — по одному
	*      блоку IF на каждый ID.
	*   3. Корневой блок обязан называться MAIN — это стандарт XTemplate
	*      и требование функции cot_market_get_by_id_owner_tpl():
	*      парсится только MAIN, вложенные блоки можно называть как угодно.
	*   4. Стили карточек — на усмотрение темы. В этом базовом файле
	*      используется простая разметка с .gradient-border (см. тему).
	*   5. Для конкретного ID можно создать персональный шаблон
	*      market.getbyid.<ID>.tpl — он подхватится автоматически. Пример
	*      есть в market.getbyid.29.tpl и market.getbyid.marketpro.tpl.
	*   6. При пустом результате (ни один из ID не найден) вернётся
	*      пустая строка — сайт не покажет никаких «заглушек».
	*
	* Поддерживаемые вызовы:
	*   Из шаблона темы (CoTemplate):
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29)}
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29,71,105')}
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29 71 105')}
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29, 'RECO_')}
	*   Из PHP (без ограничений шаблонизатора):
	*     $html = cot_market_get_by_id_owner_tpl('getbyid', [29, 71, 105]);
	*     $html = cot_market_get_by_id_owner_tpl('getbyid', '29|71|105');
	*     cot_market_get_by_id_owner_tpl($t); // старый режим, XTemplate
	*
	* Рекомендуемая обёртка:
	*   скобка!-- IF {PHP|cot_module_active('market')} --скобка
	*   <div class="px-0 px-md-3 py-4">
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29,71')}
	*   </div>
	*   скобка!-- ENDIF --скобка
	*
	* Зависимости:
	*   - Cot::$db           — соединение с БД;
	*   - Cot::$usr / Cot::$cfg — язык и настройки (для i18n и URL);
	*   - cot_tplfile()      — поиск файла шаблона в теме;
	*   - cot_market_url()   — построение URL карточки;
	*   - cot_url_check()    — проверка URL;
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
<div class="row my-3 gy-3">
	
    <!-- IF {MARKET_CHECK_29_ID} -->
    <div class="col-md-6">
		<div class="gradient-border gradient-border-about-figure p-4">
			<a href="{MARKET_CHECK_29_URL}" title="{MARKET_CHECK_29_TITLE}">
				{MARKET_CHECK_29_TITLE}
			</a>
		</div>
	</div>
    <!-- ENDIF -->
	
    <!-- IF {MARKET_CHECK_71_ID} -->
    <div class="col-lg-6">
		<div class="gradient-border gradient-border-about-figure p-4">
			<a href="{MARKET_CHECK_71_URL}" title="{MARKET_CHECK_71_TITLE}">
				{MARKET_CHECK_71_TITLE}
			</a>
		</div>
	</div>
    <!-- ENDIF -->
	
</div>
<!-- END: MAIN -->