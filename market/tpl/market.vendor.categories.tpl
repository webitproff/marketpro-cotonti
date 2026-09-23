<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.vendor.categories.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.vendor.categories.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.vendor.categories.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.vendor.php (передаёт тег {VENDOR_CATEGORIES_TREE})
	*                     _ _ _ _ modules/market/inc/market.functions.php
	*                     _ _ _ _ SEE: cot_market_build_vendor_categories_html()
	*                     _ _ _ _ SEE: cot_market_render_vendor_categories_tree()
	* Назначение:
	*   Шаблон иерархического дерева категорий КОНКРЕТНОГО ПРОДАВЦА.
	*   Вторичный шаблон — вызывается изнутри market.vendor.tpl через тег
	*   {VENDOR_CATEGORIES_TREE}, который формируется функцией
	*   cot_market_build_vendor_categories_html() и рекурсивно рендерится
	*   функцией cot_market_render_vendor_categories_tree().
	*
	*   В дерево попадают ТОЛЬКО те категории, в которых у продавца есть
	*   опубликованные товары (state = 0), плюс их родители — чтобы
	*   сохранить целостность ветки. Количество товаров у каждой категории
	*   берётся из данных продавца, а не из глобальной структуры.
	*
	*   ПРИМЕР https://abuyfile.com/ru/market/vendor/webitproff
	*
	* Как подключается:
	*   В шаблоне market.vendor.tpl:
	*     <aside class="col-md-3">
	*       <h5>{PHP.L.market_vendor_categories_of}</h5>
	*       <div class="market-vendor-categories">
	*         скобка!-- IF {VENDOR_CATEGORIES_TREE} --скобка
	*           {VENDOR_CATEGORIES_TREE}
	*         скобка!-- ELSE --скобка
	*           <p class="text-muted small">{PHP.L.market_vendor_no_categories}</p>
	*         скобка!-- ENDIF --скобка
	*       </div>
	*     </aside>
	*
	*   ОБЯЗАТЕЛЬНО: тег {VENDOR_CATEGORIES_TREE} должен выводиться ВНУТРИ
	*   контейнера с классом .market-vendor-categories и с проверкой
	*   IF / ELSE / ENDIF — JS-скрипт шаблона опирается на этот контейнер
	*   для авто-раскрытия активной ветки.
	*
	* Как работает (в market.functions.php):
	*   1. cot_market_build_vendor_categories_html($vendorCats, $user, $sel)
	*      собирает индекс [код => количество] из плоского списка категорий
	*      продавца ($vendorCats). Дубли объединяются через MAX (например,
	*      если плагин Multicat добавляет ту же категорию, что и модуль).
	*   2. Для каждой категории продавца добавляются все родители — чтобы
	*      дерево было целым, без «висящих» веток.
	*   3. Строится индекс «родитель → дети», дети сортируются по названию.
	*   4. Рекурсивный рендер через cot_market_render_vendor_categories_tree():
	*      на каждом уровне создаётся новый XTemplate, заполняются теги
	*      ROW_*, парсится блок MAIN.CATS, готовый HTML уровня вкладывается
	*      в {ROW_SUBCAT} родительской категории.
	*   5. Верхний уровень возвращается в market.vendor.php и присваивается
	*      тегу {VENDOR_CATEGORIES_TREE}.
	*
	* Основные теги шаблона:
	*   Одна категория (BEGIN: CATS):
	*     ROW_ID                — уникальный HTML-id блока сворачивания
	*                             (формат: lvl<level>-<num>-<code>, код
	*                             очищен от небезопасных символов);
	*     ROW_TITLE             — название категории (экранированное;
	*                             с учётом перевода i18n4marketpro);
	*     ROW_HREF              — URL перехода по категории внутри витрины
	*                             продавца: m=vendor&u=<ник>&c=<код>;
	*     ROW_COUNT             — количество опубликованных товаров
	*                             продавца в этой категории;
	*     ROW_LEVEL             — уровень вложенности (0 — верхний);
	*     ROW_PADDING           — отступ слева в px (8 + level × 14),
	*                             для визуальной иерархии;
	*     ROW_LINK_CLASS        — CSS-классы ссылки. Базовые:
	*                             'text-decoration-none fw-medium'.
	*                             Если категория активна (совпадает
	*                             с {selectedCode}), добавляется
	*                             ' text-primary fw-bold' — это позволяет
	*                             JS автоматически раскрывать ветку.
	*     ROW_HAS_SUBCATS       — 1, если у категории есть дочерние
	*                             в текущем дереве; иначе 0. Управляет
	*                             выводом кнопки сворачивания и блока
	*                             {ROW_SUBCAT}.
	*     ROW_SUBCAT            — готовый HTML вложенного поддерева
	*                             (рекурсивный рендер следующего уровня).
	*
	*   Активная категория:
	*     Подсветка выполняется классом .text-primary.fw-bold в
	*     ROW_LINK_CLASS. JS-скрипт шаблона находит эту ссылку и раскрывает
	*     все родительские .collapse, чтобы активная ветка была видна
	*     сразу после загрузки страницы.
	*
	*   i18n (плагин i18n4marketpro, если язык не основной):
	*     ROW_TITLE переопределяется переводом названия категории
	*     через cot_i18n4marketpro_get_cat($code, $locale).
	*
	*   Особые случаи:
	*     Если у продавца нет ни одной категории с товарами — функция
	*     cot_market_build_vendor_categories_html() возвращает пустую
	*     строку, и в market.vendor.tpl срабатывает блок ELSE с сообщением
	*     {PHP.L.market_vendor_no_categories}.
	*
	* Структура блоков шаблона:
	*   MAIN          — корневой блок;
	*   MAIN.CATS     — блок одной категории (BEGIN/END: CATS), парсится
	*                   в цикле по $children в рекурсивной функции.
	*
	* JS шаблона (inline в блоке <script>):
	*   IIFE с защитой от повторной инициализации (window.__mcVendorCatsInit).
	*   На DOMContentLoaded (или сразу, если DOM уже загружен):
	*     - находит активную ссылку по селектору
	*       '.market-vendor-categories a.text-primary, .market-vendor-categories a.fw-bold';
	*     - поднимается вверх по DOM, добавляя класс .show и атрибут
	*       aria-expanded="true" всем родительским .collapse;
	*   Это гарантирует, что активная ветка дерева видна без ручного
	*   раскрытия.
	*
	* Используемые плагины (опционально):
	*   i18n4marketpro  — перевод названий категорий на текущий язык.
	*
	* Хуки (в market.functions.php — в parent-функциях, не в этом шаблоне):
	*   market.tree.first   — в начале cot_build_structure_market_tree()
	*                         (к этому шаблону отношения не имеет, упомянут
	*                          для контекста соседних tree-шаблонов);
	*   Данный шаблон рендерится отдельной функцией
	*   cot_market_render_vendor_categories_tree(), собственных хуков
	*   не вызывает.
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
<div class="list-group list-group-flush">
    <!-- BEGIN: CATS -->
    <div class="list-group-item py-2" data-level="{ROW_LEVEL}">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1" style="padding-left:{ROW_PADDING}px;">
                <a href="{ROW_HREF}" class="{ROW_LINK_CLASS}">{ROW_TITLE}</a>
                <span class="badge bg-secondary ms-2 small">{ROW_COUNT}</span>
            </div>
            <!-- IF {ROW_HAS_SUBCATS} -->
            <a class="my-0 toggle-subcats" role="button"
               data-bs-toggle="collapse"
               data-bs-target="#{ROW_ID}">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <!-- ENDIF -->
        </div>
        <!-- IF {ROW_HAS_SUBCATS} -->
        <div id="{ROW_ID}" class="collapse mt-2">
            {ROW_SUBCAT}
        </div>
        <!-- ENDIF -->
    </div>
    <!-- END: CATS -->
</div>

<script>
(function () {
    if (window.__mcVendorCatsInit) {
        return;
    }
    window.__mcVendorCatsInit = true;

    function revealActive() {
        var activeLink = document.querySelector(
            '.market-vendor-categories a.text-primary, .market-vendor-categories a.fw-bold'
        );
        if (!activeLink) {
            return;
        }

        var el = activeLink.parentElement;
        while (el && el !== document.body) {
            if (el.classList && el.classList.contains('collapse')) {
                el.classList.add('show');
                el.setAttribute('aria-expanded', 'true');
            }
            el = el.parentElement;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', revealActive);
    } else {
        revealActive();
    }
})();
</script>
<!-- END: MAIN -->