<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.getbyid.29.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.getbyid.29.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.getbyid.29.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.functions.php
	*                     _ _ _ _ SEE: cot_market_get_by_id_owner_tpl()
	*                     _ _ _ _ SEE: cot_market_build_check_tags()
	* Назначение:
	*   Персональный шаблон для вывода ОДНОГО конкретного товара с ID = 29
	*   в любом месте сайта. Подхватывается автоматически функцией
	*   cot_market_get_by_id_owner_tpl(), если ID = 29 и файл присутствует
	*   в теме:
	*
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29)}
	*
	*   Приоритет поиска шаблона:
	*     1. market.getbyid.29.tpl   — этот файл (персональный, для ID 29);
	*     2. market.getbyid.tpl      — общий, если персонального нет.
	*
	*   Если у вас несколько персональных шаблонов под разные ID — просто
	*   создайте отдельные файлы market.getbyid.<ID>.tpl (например
	*   market.getbyid.29.tpl, market.getbyid.71.tpl). Каждый подхватится
	*   автоматически при вызове функции с соответствующим ID.
	*
	*   Пример оформления: карточка с текстом «You may have been looking
	*   for this for a long time» и ссылкой на товар. Тексты, стили и
	*   разметка — на усмотрение темы.
	*
	* Основные параметры вызова:
	*   $templateOrTpl = 'getbyid'    — общая часть имени шаблона;
	*   $ids           = 29           — ID, под который сделан этот файл;
	*   $prefix        = 'MARKET_CHECK_' (по умолчанию).
	*
	*   Кастомный префикс (например 'RECO_') — теги становятся
	*   {RECO_29_ID}, {RECO_29_URL}, {RECO_29_TITLE}.
	*
	* Основные теги шаблона:
	*   MARKET_CHECK_29_ID      — числовой ID товара (пусто, если не найден);
	*   MARKET_CHECK_29_URL     — абсолютный URL карточки (алиас с фолбэком на ID);
	*   MARKET_CHECK_29_TITLE   — название товара (htmlspecialchars).
	*
	* Правила использования:
	*   1. Обязательно оборачивать вставку в блок
	*        скобка!-- IF {MARKET_CHECK_29_ID} --скобка … скобка!-- ENDIF --скобка
	*      Если товар с ID 29 не найден или не опубликован (state != 0),
	*      блок не отрисуется — не будет пустой «заглушки».
	*   2. Корневой блок — MAIN (стандарт XTemplate; только MAIN
	*      парсится функцией cot_market_get_by_id_owner_tpl()).
	*   3. Внутри MAIN можно использовать вложенные блоки с любыми
	*      именами, кроме MAIN.
	*   4. Стили и тексты — на усмотрение темы; этот файл — образец.
	*   5. Никаких дополнительных тегов вида {MARKET_CHECK_71_*} здесь
	*      использовать не нужно — файл заточен под один ID.
	*      Если нужно несколько ID с разной разметкой — используйте
	*      разные файлы market.getbyid.<ID>.tpl и вызывайте функцию
	*      с каждым ID отдельно.
	*   6. Если файл удалить — ничего не сломается: вызов с ID 29
	*      подхватит market.getbyid.tpl (общий).
	*
	* Поддерживаемые вызовы:
	*   Из шаблона темы (CoTemplate):
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29)}
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', '29,71')}
	*       → для ID 29 подхватит этот файл, для 71 — общий
	*         market.getbyid.tpl (если персонального под 71 нет).
	*   Из PHP:
	*     $html = cot_market_get_by_id_owner_tpl('getbyid', 29);
	*
	* Рекомендуемая обёртка:
	*   скобка!-- IF {PHP|cot_module_active('market')} --скобка
	*   <div class="px-0 px-md-3 py-4">
	*     {PHP|cot_market_get_by_id_owner_tpl('getbyid', 29)}
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
	
    <!-- IF {MARKET_CHECK_29_ID} -->

		<div class="gradient-border p-4"> 
		<p class="text-danger fw-semibold">You may have been looking for this for a long time</p>
			<a href="{MARKET_CHECK_29_URL}" title="{MARKET_CHECK_29_TITLE}">
				{MARKET_CHECK_29_TITLE}
			</a>
		</div>

    <!-- ENDIF -->
	
</div>
<!-- END: MAIN -->

