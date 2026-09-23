<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.tree.list.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.tree.list.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.tree.list.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.functions.php
	*                     _ _ _ _ SEE: cot_build_structure_market_tree()
	* Назначение:
	*   Шаблон иерархического дерева категорий (структуры) товаров модуля
	*   Market. Вторичный шаблон — вызывается внутри других шаблонов,
	*   например в market.list.tpl, для вывода бокового дерева категорий.
	*
	*   Пример вызова в другом шаблоне:
	*     <div class="card mb-4">
	*       {PHP|cot_build_structure_market_tree('', '', 0, 'list')}
	*     </div>
	*
	*   ПРИМЕР https://abuyfile.com/ru/market
	*
	* Как работает:
	*   cot_build_structure_market_tree($parent, $selected, $level, $template)
	*   рекурсивно обходит категории модуля market, для каждой ветки
	*   подключает файл market.tree.<template>.tpl (здесь template='list'),
	*   передаёт набор тегов ROW_* и парсит блок CATS.
	*   Готовый HTML одного уровня возвращается в вызывающий код и
	*   вставляется в контейнер {ROW_SUBCAT} — так строится дерево.
	*
	* Аргументы функции (не URL):
	*   $parent   — код родительской категории ('' — корень дерева);
	*   $selected — код(ы) выбранной категории для подсветки (строка или массив);
	*   $level    — текущий уровень вложенности (0 — верхний);
	*   $template — часть имени файла шаблона (здесь 'list').
	*
	* Основные теги шаблона:
	*   Уровень / общие (уровень 0):
	*     LEVEL                     — текущий уровень вложенности (int);
	*     TOTAL_COUNT               — общее количество опубликованных товаров
	*                                  во всём каталоге (state = 0).
	*   Одна категория (BEGIN: CATS):
	*     ROW_ID                    — код категории;
	*     ROW_TITLE                 — название категории (экранированное);
	*     ROW_DESC                  — описание категории (если задано);
	*     ROW_ICON                  — путь к иконке категории;
	*     ROW_HREF                  — URL перехода по категории (с фильтром c=<код>);
	*     ROW_URL                   — альтернативный URL (i18n4marketpro, если активен);
	*     ROW_SELECTED              — 1, если категория совпадает с выбранной;
	*     ROW_SUBCAT                — готовый HTML вложенного поддерева (рекурсия);
	*     ROW_LEVEL                 — уровень вложенности категории;
	*     ROW_ODDEVEN               — odd/even (для zebra-стилизации);
	*     ROW_JJ                    — порядковый номер в списке;
	*     ROW_COUNT                 — количество опубликованных товаров
	*                                  в этой категории (без потомков);
	*     ROW_PARENT_COUNT          — количество товаров в категории
	*                                  и во всех её потомках (state = 0);
	*     ROW_<EXFIELD>[_TITLE|_VALUE] — extrafields структуры категорий.
	*
	*   i18n (плагин i18n4marketpro, если активен и язык не основной):
	*     ROW_TITLE / ROW_DESC      — переопределяются переведёнными значениями;
	*     ROW_URL                   — URL с параметром l=<locale>.
	*
	*   Особые случаи:
	*     Если у категории нет дочерних — ROW_SUBCAT пустой, кнопка «свернуть»
	*     не показывается (условие  IF {ROW_SUBCAT} ).
	*     Если после фильтрации по чёрному списку
	*     (cfg.market.marketblacktreecatspage) не осталось дочерних — функция
	*     возвращает false, и в родительский шаблон ничего не подставляется.
	*
	* Black-list категорий:
	*   Параметр cfg.market.marketblacktreecatspage — список кодов через
	*   запятую, которые исключаются из дерева на странице «list».
	*
	* Структура блоков шаблона:
	*   MAIN                     — корневой блок;
	*   MAIN.CATS                — блок одной категории (BEGIN/END: CATS),
	*                              парсится в цикле по $children.
	*
	* Используемые плагины (опционально):
	*   i18n4marketpro  — перевод названий и описаний категорий.
	*
	* Хуки (в market.functions.php → cot_build_structure_market_tree()):
	*   market.tree.first   — в начале функции;
	*   market.tree.main    — после создания XTemplate, до цикла по категориям;
	*   market.tree.loop    — внутри цикла по каждой категории.
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
<!-- IF {LEVEL} == 0 -->
<div class="p-2" title="{PHP.L.All}">
	<a class="nav-link" href="{PHP|cot_url('market')}">
		<i class="fa-solid fa-store me-2"></i>
		<span>{PHP.L.market_title_general}</span>
		<span class="ms-auto">({TOTAL_COUNT})</span>
	</a>
</div>
<hr class="mt-0">
<!-- ENDIF -->

<div id="market-tree-list-desktop" class="market-tree" data-tree="desktop">
	<div class="list-group list-group-flush">
		<!-- BEGIN: CATS -->
		<div class="list-group-item py-2" data-level="{ROW_LEVEL}" data-id="{ROW_ID}">
			<div class="d-flex align-items-center min-vh-0">
				<div class="flex-grow-1">

					<a href="{ROW_HREF}" class="text-decoration-none fw-medium">
						{ROW_TITLE}
					</a>

				<!-- IF {PHP.usr.maingrp} == 5 --> 
					<!-- IF {ROW_SUBCAT} -->
						{ROW_PARENT_COUNT}
					<!-- ELSE -->
						<span class="badge bg-secondary ms-2 small">{ROW_COUNT}</span>
					<!-- ENDIF -->
				<!-- ENDIF -->

				</div>

				<!-- IF {ROW_SUBCAT} -->
				<a class="my-0 toggle-subcats"
						type="button"
						data-bs-toggle="collapse"
						data-bs-target="#sub-{ROW_LEVEL}-{ROW_JJ}-{ROW_ID}">
					<i class="fa-solid fa-chevron-left"></i>
				</a>
				<!-- ENDIF -->
			</div>

			<!-- IF {ROW_SUBCAT} -->
			
				<div id="sub-{ROW_LEVEL}-{ROW_JJ}-{ROW_ID}" class="collapse">
					<div class="mt-2">{ROW_SUBCAT}</div>
				</div>
			
			<!-- ENDIF -->

		</div>
		<!-- END: CATS -->
	</div>
</div>
<!-- END: MAIN -->
