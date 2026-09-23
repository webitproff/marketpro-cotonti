<!-- 
	* Market PRO Module for CMF Cotonti, PHP v.8.5+, MySQL v.8.4
	*
	* Filename: _ _ _ _ _ _ _ _ _ _ _ _ _ _ market.enum.tpl
	* Base path to the file: _ _ _ _ _ _ _ modules/market/tpl/market.enum.tpl
	* Recommended path to the file: _ _ _ _ themes/your-theme-name/modules/market/market.enum.tpl
	* Main business logic:_ _ _ _ _ _ _ _ _ modules/market/inc/market.functions.php
	*                     _ _ _ _ SEE: cot_market_enum()
	* Назначение:
	*   Шаблон виджета-перечисления товаров Market. Используется для вывода
	*   произвольной выборки товаров в любом месте сайта (сайдбар, главная,
	*   страница модуля, шаблоны плагинов и т.д.). Вызывается через функцию
	*   cot_market_enum() из PHP или напрямую из шаблона через {PHP|...}.
	*
	*   ПРИМЕР (PHP, в контроллере модуля):
	*     $market_widget_html = cot_market_enum(
	*         $categories,      // категории (строка/массив кодов или '' для всех)
	*         $count,           // количество товаров (0 = все или постранично)
	*         $template,        // файл шаблона ('' → market.enum.tpl)
	*         $order,           // SQL-сортировка (например 'fieldmrkt_date DESC')
	*         $condition,       // доп. SQL-условие (без WHERE)
	*         $active_only,     // только опубликованные (true/false)
	*         $use_subcat,      // включать подкатегории (true/false)
	*         $exclude_current, // исключить текущий товар (true/false)
	*         $blacklist,       // чёрный список категорий
	*         $pagination,      // имя GET-параметра пагинации ('' = выкл.)
	*         $cache_ttl        // время кэша в секундах (0/null = без кэша)
	*     );
	*     $t->assign('MARKET_WIDGET', $market_widget_html);
	*
	*   ПРИМЕР (шаблон темы, короткий вызов):
	*     {PHP|cot_market_enum('', 5, '', 'fieldmrkt_date DESC')}
	*
	* Основные параметры функции cot_market_enum():
	*   $categories     — '' | 'code1,code2' | ['code1','code2']; пусто = все категории;
	*   $count          — 0 = без LIMIT (все или постранично), >0 = LIMIT $d, $count;
	*   $template       — '' = market.enum.tpl, либо имя кастомного шаблона
	*                     (ищется в теме как market.enum.<template>.tpl);
	*   $order          — SQL ORDER BY без ключевого слова (напр. 'fieldmrkt_date DESC');
	*   $condition      — доп. SQL-условие, добавляется как есть (без WHERE);
	*   $active_only    — true: только fieldmrkt_state = 0 и актуальные по датам
	*                     (fieldmrkt_begin <= now AND (expire = 0 OR expire > now));
	*   $use_subcat     — true: раскрывать подкатегории выбранных кодов;
	*   $exclude_current— true: исключить текущий товар (по $id) при вызове
	*                     на странице товара (не внутри списка — COT_LIST);
	*   $blacklist      — 'code1,code2' | [ 'code1','code2' ];
	*   $pagination     — имя GET-параметра пагинации (например 'd'):
	*                     если задано, включает пагинацию и LIMIT;
	*   $cache_ttl      — целое число секунд; >0 включает кэш на диск,
	*                     0/null — без кэша.
	*
	* Основные теги шаблона:
	*   Одна карточка товара (BEGIN: MARKET_ROW):
	*     MARKET_ROW_*              — стандартные теги товара
	*                                 (cot_generate_markettags());
	*     MARKET_ROW_URL            — URL карточки (алиас с фолбэком на ID);
	*     MARKET_ROW_TITLE          — название товара (экранированное);
	*     MARKET_ROW_DESCRIPTION    — краткое описание (если задано);
	*     MARKET_ROW_TEXT_CUT       — обрезанный текст товара;
	*     MARKET_ROW_NUM            — порядковый номер в виджете (с 1);
	*     MARKET_ROW_ODDEVEN        — odd/even (для zebra-стилизации);
	*     MARKET_ROW_RAW            — «сырой» массив данных товара (может
	*                                 использоваться плагинами через хук);
	*     MARKET_ROW_OWNER_*        — теги владельца
	*                                 (cot_generate_usertags());
	*     MARKET_ROW_COMMENTS_LINK  — ссылка на комментарии (плагин comments);
	*     MARKET_ROW_COMMENTS_COUNT — число комментариев (плагин comments).
	*
	*   Пагинация (BEGIN неявный, проверка IF {PAGINATION}):
	*     PAGINATION                — HTML номеров страниц;
	*     PREVIOUS_PAGE             — ссылка «Предыдущая»;
	*     NEXT_PAGE                 — ссылка «Следующая»;
	*     CURRENT_PAGE              — текущая страница;
	*     TOTAL_PAGES               — всего страниц.
	*
	*   Пагинация включается ТОЛЬКО если в cot_market_enum() передан
	*   непустой параметр $pagination. Если $pagination = '' — блок
	*   пагинации в шаблоне просто не отрисуется.
	*
	* Правила использования:
	*   1. Корневой блок должен называться MAIN (стандарт XTemplate;
	*      функция парсит только MAIN).
	*   2. Обязательно оборачивать карточку в BEGIN/END: MARKET_ROW —
	*      именно это имя блока парсит функция в цикле по $sql_rowset.
	*   3. Название файла кастомного шаблона: market.enum.<template>.tpl
	*      (передаётся третьим параметром $template без префикса
	*      market.enum. и без .tpl). Например, для $template = 'sidebar'
	*      будет искаться market.enum.sidebar.tpl, а при отсутствии —
	*      откат на этот market.enum.tpl.
	*   4. Внутри одного шаблона могут использоваться только те теги,
	*      которые формирует cot_generate_markettags() + перечисленные
	*      выше. Список доступных полей товара — в БД таблицы market.
	*   5. Если виджет рендерится для гостя и включён кэш — используется
	*      disk-кэш по md5-хэшу (см. $md5hash в market.functions.php).
	*   6. Для исключения «текущего» товара (когда виджет стоит на странице
	*      товара) передайте $exclude_current = true. Внутри списка
	*      (константа COT_LIST) исключение автоматически отключается.
	*   7. Ссылка «Читать далее» в этом файле не выводится — это упрощённый
	*      виджет; если нужно — добавляйте в свой кастомный шаблон
	*      market.enum.<template>.tpl.
	*
	* Поддерживаемые вызовы:
	*   Из PHP (без ограничений):
	*     cot_market_enum();                                       // все товары, 0 лимит
	*     cot_market_enum('cat1,cat2', 5, '', 'fieldmrkt_date DESC');
	*     cot_market_enum(['cat1','cat2'], 10, 'sidebar', 'fieldmrkt_count DESC',
	*                     "fieldmrkt_costdflt > 0", true, true, false, 'excluded', 'd', 300);
	*   Из шаблона темы (CoTemplate):
	*     {PHP|cot_market_enum('', 5, '', 'fieldmrkt_date DESC')}
	*     {PHP|cot_market_enum('plugs', 3, 'sidebar', '', '', true)}
	*
	* Кэш:
	*   Если $cache_ttl > 0 и включён общий кэш Cotonti:
	*     - результат сохраняется на диск (ключ — md5 от mskin+lang+SQL);
	*     - при повторном вызове возвращается готовый HTML без SQL-запроса.
	*   Кэш не разделяется по пользователям — учитывайте это при
	*   персонализированных выборках.
	*
	* Зависимости:
	*   - Cot::$db, Cot::$db->market, Cot::$db->users — БД;
	*   - Cot::$structure['market']   — дерево категорий;
	*   - Cot::$cfg, Cot::$sys, Cot::$L, Cot::$Ls — настройки и язык;
	*   - cot_structure_children()    — сбор подкатегорий;
	*   - cot_import_pagenav()        — пагинация;
	*   - cot_tplfile()               — поиск файла шаблона;
	*   - cot_generate_markettags()   — теги товара;
	*   - cot_generate_usertags()     — теги владельца;
	*   - cot_pagenav()               — данные пагинации;
	*   - XTemplate                   — шаблонизатор Cotonti;
	*   - ExtensionsService           — определение модуля/плагина (для URL).
	*
	* Используемые плагины (опционально):
	*   comments — вывод ссылки и счётчика комментариев
	*              (MARKET_ROW_COMMENTS_LINK, MARKET_ROW_COMMENTS_COUNT).
	*
	* Хуки (в cot_market_enum(), market.functions.php):
	*   market.enum.query — перед сборкой SQL-запроса
	*                       (можно добавить свои условия/джойны);
	*   market.enum.loop  — внутри цикла по товарам
	*                       (можно доопределить теги каждой карточки);
	*   market.enum.tags  — перед финальным парсингом MAIN.
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