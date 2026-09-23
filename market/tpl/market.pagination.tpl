<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.pagination.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.pagination.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.pagination.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.userdetails.php
	*                     _ _ _ _ _ _ _ _ _ (используется в AJAX-ветке вкладки «Товары»)
	* Назначение:
	*   Шаблон пагинации для AJAX-подгрузки списка товаров во вкладке «Товары»
	*   на странице профиля пользователя. Возвращается в виде HTML-фрагмента
	*   в JSON-ответе (поле "pagination") и подменяет содержимое блока
	*   #pagination-block без перезагрузки страницы.
	*
	*   ПРИМЕР использования:
	*     https://abuyfile.com/ru/users/webitproff/tab/market/plugs
	*     https://abuyfile.com/ru/users/2?m=details&tab=market
	*
	*   В market.userdetails.php этот шаблон подключается так:
	*     $pagination_tpl = new XTemplate(cot_tplfile(['market', 'pagination'], 'module'));
	*     $pagination_tpl->assign(cot_generatePaginationTags($pagenav));
	*     $pagination_tpl->parse('MAIN');
	*     $pagination_html = $pagination_tpl->text('MAIN');
	*
	* Как работает:
	*   1. Основной шаблон market.userdetails.tpl выводит контейнер
	*      #pagination-block с классической пагинацией и весь список товаров.
	*   2. При клике на «Загрузить ещё» JS отправляет AJAX-запрос
	*      (параметры: ajax=1, dmarket=<offset>, tab=market, cat=<cat>).
	*   3. Серверная ветка ajax=1 в market.userdetails.php строит новую
	*      пагинацию через cot_pagenav() с тем же $d, $totalitems, $perpageincat
	*      и формирует этот HTML.
	*   4. На клиенте $('#pagination-block').html(data.pagination) — блок
	*      пагинации перерисовывается, кнопка «Загрузить ещё» обновляется
	*      через updateButtonText().
	*
	* Основные теги шаблона (все — стандартные теги Cotonti, передаются
	* через cot_generatePaginationTags($pagenav)):
	*   PAGINATION      — HTML со списком номеров страниц (li/li);
	*                     если страниц меньше 2 — пустая строка, и весь
	*                     блок IF … ENDIF не отрисуется.
	*   PREVIOUS_PAGE   — HTML ссылки «Предыдущая страница» (пусто на первой).
	*   NEXT_PAGE       — HTML ссылки «Следующая страница» (пусто на последней).
	*   CURRENT_PAGE    — номер текущей страницы (начиная с 1);
	*   TOTAL_PAGES     — общее количество страниц.
	*
	*   Языковые вставки: {PHP.L.Page} — «Страница», {PHP.L.Of} — «из».
	*
	* Правила использования:
	*   1. Обязательная проверка скобка!-- IF {PAGINATION} --скобка вокруг вывода —
	*      иначе при одной странице появится пустая «пагинация».
	*   2. Корневой блок должен называться MAIN (стандарт XTemplate).
	*   3. Шаблон рендерится только в AJAX-ветке market.userdetails.php.
	*      Для не-AJAX вывода используется обычная пагинация из
	*      market.userdetails.tpl (блок #pagination-block).
	*   4. Совпадающее имя GET-параметра страницы: 'dmarket' — обязательно
	*      указывать в cot_import_pagenav() и cot_pagenav(), иначе ссылки
	*      в пагинации не будут работать.
	*   5. Классы обёртки (.pagination-scroll, .pagination.flex-nowrap)
	*      и стили .pagination-scroll должны быть определены в теме,
	*      если нужен горизонтальный скролл при большом числе страниц.
	*   6. Шаблон не содержит кнопки «Загрузить ещё» — она живёт в
	*      market.userdetails.tpl и управляется отдельным JS.
	*
	* Ключевые зависимости:
	*   - cot_pagenav()              — генерация данных пагинации;
	*   - cot_generatePaginationTags() — преобразование массива в теги;
	*   - XTemplate                  — шаблонизатор Cotonti;
	*   - Cot::$cfg, Cot::$L         — настройки и язык.
	*
	* Хуки:
	*   — собственных хуков в этом шаблоне нет.
	*   Родительские хуки в market.userdetails.php:
	*     market.userdetails.query — модификация SQL-условий;
	*     market.userdetails.loop  — внутри цикла вывода товаров;
	*     market.userdetails.tags  — перед финальным парсингом.
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
<!-- IF {PAGINATION} -->
<nav class="mt-5">
    <div class="pagination-scroll">
        <ul class="pagination justify-content-center flex-nowrap mb-0">
            {PREVIOUS_PAGE}
            {PAGINATION}
            {NEXT_PAGE}
        </ul>
    </div>
</nav>
<div class="text-center">
    {PHP.L.Page} {CURRENT_PAGE} {PHP.L.Of} {TOTAL_PAGES}
</div>
<!-- ENDIF -->
<!-- END: MAIN -->