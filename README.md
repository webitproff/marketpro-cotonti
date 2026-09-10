# Market PRO v.5+ by webitproff

[![Version](https://img.shields.io/badge/version-5.0.1-green.svg)](https://github.com/webitproff/marketpro-cotonti/releases)
[![Cotonti Compatibility](https://img.shields.io/badge/Cotonti-1.0-orange.svg)](https://github.com/Cotonti/Cotonti)
[![PHP](https://img.shields.io/badge/PHP-8.5-purple.svg)](https://www.php.net/releases/8_5_6.php)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-blue.svg)](https://www.mysql.com/)
[![Bootstrap v5.3.8](https://img.shields.io/badge/Bootstrap-v5.3.8-blueviolet.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/license-BSD-blue.svg)](https://github.com/webitproff/marketpro-cotonti/blob/main/LICENSE)


## [Demo](https://abuyfile.com/market)

___


# Market PRO — e-commerce and marketplace module for Cotonti

[![PHP](https://img.shields.io/badge/PHP-8.5%2B-777bb3.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.4%2B-4479a1.svg)](https://www.mysql.com/)
[![Cotonti](https://img.shields.io/badge/Cotonti-%2B-2e7d32.svg)](https://www.cotonti.com/)
[![License](https://img.shields.io/badge/License-BSD-blue.svg)](https://github.com/webitproff/marketpro-cotonti/blob/main/LICENSE)

**Market PRO** is a module for CMF Cotonti that implements e-commerce and multi-vendor marketplace functionality. Fully compatible with PHP 8.5+, MySQL 8.4+, uses strict typing and namespaces.

---

## Table of Contents

- [1. General description](#1-general-description)
- [2. Key features](#2-key-features)
- [3. Requirements](#3-requirements)
- [4. Installation](#4-installation)
- [5. File structure](#5-file-structure)
- [6. Architecture](#6-architecture)
  - [6.1. Classes and services](#61-classes-and-services)
  - [6.2. Hook handler files](#62-hook-handler-files)
  - [6.3. Module entry points](#63-module-entry-points)
- [7. Database](#7-database)
- [8. API functions](#8-api-functions)
  - [8.1. Working with items](#81-working-with-items)
  - [8.2. URL and permissions](#82-url-and-permissions)
  - [8.3. Template tag generation](#83-template-tag-generation)
  - [8.4. Working with structure](#84-working-with-structure)
  - [8.5. Tree and widgets](#85-tree-and-widgets)
  - [8.6. Utilities](#86-utilities)
- [9. Hooks list](#9-hooks-list)
- [10. Integration with ItemService](#10-integration-with-itemservice)
- [11. Extra fields (extrafields)](#11-extra-fields-extrafields)
- [12. Templates](#12-templates)
  - [12.1. Template files](#121-template-files)
  - [12.2. Per-category templates](#122-per-category-templates)
  - [12.3. Custom header and footer](#123-custom-header-and-footer)
- [13. Caching](#13-caching)
- [14. Multilingual support](#14-multilingual-support)
- [15. Extending functionality](#15-extending-functionality)
  - [15.1. Via plugins](#151-via-plugins)
  - [15.2. Via hooks](#152-via-hooks)
  - [15.3. Via extra fields](#153-via-extra-fields)
  - [15.4. Via custom templates](#154-via-custom-templates)
- [16. Plugin compatibility](#16-plugin-compatibility)
- [17. Links](#17-links)
- [18. License](#18-license)

---

## 1. General description

Market PRO is a Cotonti module that implements e-commerce (single seller) and multi-vendor marketplace (multiple sellers) functionality. The module is written in modern PHP using:

- **Strict typing** (`declare(strict_types=1)`).
- **Namespaces** (`cot\modules\market\inc\...`).
- **Services** (Singleton, Repository, DTO).
- **Hooks** (full compatibility with the Cotonti plugin system).
- **Class autoloading** via namespace paths.

The module integrates with the general Cotonti mechanisms: extra fields system, authentication, cache, language files, and the `ItemService` service for participating in universal selections (search, cart, favorites).

---

## 2. Key features

### For users
- Product catalog with unlimited category hierarchy.
- Product card with full description, gallery, comments.
- Search by title, description, SKU.
- Filtering and sorting by any fields.
- AJAX list loading ("Load more").
- Personal seller showcase in the user profile.
- Product publishing: draft, moderation, publish.
- Product cloning.

### For administrators
- Manage all products with status filters.
- Bulk operations: approve, delete.
- Publication moderation.
- Per-category permission configuration.
- Extra fields management.
- Sorting and pagination configuration per category.

### Technical
- SEO: meta title, meta description, H1, aliases, canonical URLs.
- Caching: static, disk, first-level repository cache.
- Multilingual: language files + `i18n4marketpro` support.
- Security: CSRF protection, prepared SQL queries, permission separation.
- Extensibility via hooks and extra fields.

---

## 3. Requirements

| Component | Minimum version |
|-----------|-----------------|
| PHP | 8.5 |
| MySQL | 8.4 |
| Cotonti | Siena and above |
| Required Cotonti modules | `extrafields`, `forms`, `users` |
| Optional plugins | `comments`, `attacher`, `tgm4market`, `i18n4marketpro`, `aliasmarketpro` |

Additional dependencies (composer packages) are not required.

---

## 4. Installation

1. Copy the `market` directory to `modules/` of your Cotonti site.
2. Go to **Administration → Extensions** and install the Market module.
3. During installation:
   - the `cot_market` table is created automatically (if it doesn't exist);
   - the `market` table is registered in `Cot::$db`;
   - the extrafields table is connected for `market`.
4. Configure the module in **Administration → Configuration → Market**.
5. Create the category structure in **Administration → Structure → Market**.
6. Optionally install additional plugins (comments, attacher, etc.).

---

## 5. File structure

```
modules/market/
├── market.php                              # main module file (entry point)
├── market.admin.php                        # admin hook handler (admin panel)
├── market.item.getItems.php                # item.getItems hook handler
├── market.itemService.getItems.php         # itemService.getItems hook handler
├── market.header.php                       # header.main hook handler (notices)
├── market.header.first.php                 # header.first hook handler (location)
├── market.header.tags.php                  # header.tags hook handler (title)
├── market.footer.first.php                 # footer.first hook handler (location)
├── market.userdetails.php                  # users.details.tags hook handler
├── inc/
│   ├── market.functions.php                # main module API
│   ├── market.add.php                      # add item
│   ├── market.edit.php                     # edit item
│   ├── market.list.php                     # item list
│   ├── market.main.php                     # item page
│   ├── market.preview.php                  # preview page
│   ├── market.counter.php                  # AJAX view counter
│   ├── MarketControlService.php            # item management service
│   ├── MarketDictionary.php                # constants dictionary
│   ├── MarketRepository.php                # item repository
│   └── market.setup.php                    # module settings
├── lang/
│   ├── market.ru.lang.php                  # Russian
│   ├── market.en.lang.php                  # English
│   └── market.uk.lang.php                  # Ukrainian
└── tpl/
    ├── market.add.tpl                      # add form template
    ├── market.edit.tpl                     # edit form template
    ├── market.list.tpl                     # item list template
    ├── market.main.tpl                     # item page template
    ├── market.enum.tpl                     # list widget template
    ├── market.tree.tpl                     # category tree template
    ├── market.userdetails.tpl              # seller showcase template
    └── market.pagination.tpl               # pagination template
```

---

## 6. Architecture

### 6.1. Classes and services

The module uses a modern approach to code organization. All classes are located in the `cot\modules\market\inc` namespace.

#### `MarketDictionary`

Module constants dictionary.

```php
namespace cot\modules\market\inc;

class MarketDictionary
{
    public const SOURCE_MARKET = 'market';

    public const STATE_PUBLISHED = 0;  // Published
    public const STATE_PENDING   = 1;  // Pending moderation
    public const STATE_DRAFT     = 2;  // Draft
}
```

#### `MarketRepository`

Repository for working with the `cot_market` table. Inherits `BaseRepository`. Contains a first-level cache (within a single HTTP request).

```php
namespace cot\modules\market\inc;

class MarketRepository extends BaseRepository
{
    private static $cacheById = [];

    public static function getTableName(): string;
    public function getById(int $id, bool $useCache = true): ?array;
    protected function afterFetch(array $item): array;
}
```

The `getById($id)` method returns an item data array or `null`.

#### `MarketControlService`

Item management service. Implements Singleton via the `GetInstanceTrait`. Responsible for operations that require transactions (e.g., deletion).

```php
namespace cot\modules\market\inc;

class MarketControlService
{
    use GetInstanceTrait;

    public function delete(int $id, array $itemData = []): bool|string;
}
```

The `delete()` method returns a success message or `false` on error. Internally:
- opens a transaction;
- deletes related extrafields files;
- calls the `market.delete.first` and `market.delete.done` hooks;
- deletes the record from the DB;
- updates structure counters;
- notifies `ItemService::onDelete()`;
- clears the static cache of item and category pages.

#### `ItemDto`

Core Cotonti DTO used for integration with `ItemService`. Contains fields: `source`, `sourceId`, `type`, `title`, `description`, `url`, `ownerId`, `categoryCode`, `categoryTitle`, `categoryUrl`, `data`.

### 6.2. Hook handler files

These files are connected automatically via the `[BEGIN_COT_EXT]` block:

| File | Hook | Purpose |
|------|------|---------|
| `market.admin.php` | `admin` | Admin panel for item management |
| `market.item.getItems.php` | `item.getItems` | Providing item data through the common Cotonti API |
| `market.itemService.getItems.php` | `itemService.getItems` | Providing item data through ItemService |
| `market.header.php` | `header.main` | Notices about items in the site header |
| `market.header.first.php` | `header.first` | Setting `Cot::$env['location']` for header templates |
| `market.header.tags.php` | `header.tags` | Overriding `HEADER_TITLE` for categories |
| `market.footer.first.php` | `footer.first` | Setting `Cot::$env['location']` for footer templates |
| `market.userdetails.php` | `users.details.tags` | "Items" tab in the user profile |

### 6.3. Module entry points

All inc files are called via `market.php`:

| Parameter `m` | File |
|---------------|------|
| `list` | `inc/market.list.php` |
| `main` | `inc/market.main.php` |
| `add` | `inc/market.add.php` |
| `edit` | `inc/market.edit.php` |
| `preview` | `inc/market.preview.php` |
| `counter` | `inc/market.counter.php` (AJAX) |

---

## 7. Database

### Table `cot_market`

| Field | Type | Description |
|-------|------|-------------|
| `fieldmrkt_id` | INT AUTO_INCREMENT | Primary key |
| `fieldmrkt_cat` | VARCHAR(255) | Category code |
| `fieldmrkt_alias` | VARCHAR(255) | Alias (SEO URL) |
| `fieldmrkt_title` | VARCHAR(255) | Item title |
| `fieldmrkt_desc` | VARCHAR(255) | Short description |
| `fieldmrkt_text` | TEXT | Full text |
| `fieldmrkt_parser` | VARCHAR(64) | Text parser |
| `fieldmrkt_pcod` | VARCHAR(64) | SKU / product code |
| `fieldmrkt_costdflt` | DECIMAL | Default price |
| `fieldmrkt_cost_usd` | DECIMAL | Price in USD |
| `fieldmrkt_date` | INT | Publication date (timestamp) |
| `fieldmrkt_begin` | INT | Activity start date |
| `fieldmrkt_expire` | INT | Activity end date |
| `fieldmrkt_updated` | INT | Update date |
| `fieldmrkt_ownerid` | INT | Owner ID |
| `fieldmrkt_count` | INT | View counter |
| `fieldmrkt_state` | TINYINT | Status (0/1/2) |
| `fieldmrkt_metah1` | VARCHAR(255) | SEO H1 |
| `fieldmrkt_metatitle` | VARCHAR(255) | Meta title |
| `fieldmrkt_metadesc` | VARCHAR(255) | Meta description |

Any additional fields can be added to the table via the extrafields system.

---

## 8. API functions

All functions are located in `inc/market.functions.php`.

### 8.1. Working with items

#### `cot_market_import($source, $ritem, $auth)`

Imports item data from the request (GET/POST) into an array.

- `$source` — request type (`'POST'`, `'GET'`, `'PATCH'`, `'D'` for direct).
- `$ritem` — initial data array (for editing).
- `$auth` — permissions array.

Returns an array with `fieldmrkt_*` fields.

#### `cot_market_validate($ritem)`

Validates item data. Checks:
- category presence;
- title length;
- alias correctness;
- non-empty description (considering category settings).

Returns `true`/`false`.

#### `cot_market_add(&$ritem, $auth)`

Adds an item to the DB. Returns the new item ID or `null`.

Automatically:
- checks alias uniqueness;
- downgrades status to `STATE_PENDING` if the author is not an admin;
- updates structure counters;
- calls the `market.add.add.query`, `market.add.add.done` hooks;
- clears the cache.

#### `cot_market_update($id, &$ritem, $auth)`

Updates an item. Returns `true`/`false`.

Similar to `cot_market_add()`, but for an existing record.

#### `cot_market_status($state)`

Returns the string status: `'published'`, `'draft'`, `'pending'`.

### 8.2. URL and permissions

#### `cot_market_url($data, $params = [], $tail = '', ...)`

Builds the item URL. Considers alias or ID, category, and additional parameters.

#### `cot_market_auth($cat = null)`

Returns the permissions array for a category: `auth_read`, `auth_write`, `isadmin`, `auth_download`.

### 8.3. Template tag generation

#### `cot_generate_markettags($item_data, $tag_prefix, $textLength, $admin_rights, ...)`

Returns an array of tags for the template. All tags receive the specified prefix (e.g., `LIST_ROW_` or `MARKET_`).

Main tags:
- `TITLE`, `DESCRIPTION`, `TEXT`, `TEXT_SHORT`, `TEXT_CUT`, `TEXT_IS_CUT`
- `URL`, `ID`, `ALIAS`, `PCOD`
- `STATE`, `STATUS`, `LOCAL_STATUS`
- `CAT`, `CAT_URL`, `CAT_TITLE`, `CAT_ICON`, `CAT_ICON_SRC`
- `COSTDFLT`, `COST_USD`, `COST_USD_FORMATTED`
- `CREATED`, `UPDATED`, `HITS`
- `META_H1`, `META_TITLE`
- `ADMIN`, `ADMIN_EDIT`, `ADMIN_DELETE`, `ADMIN_UNVALIDATE`
- `BREADCRUMBS`, `BREADCRUMBS_ITEM`

#### `cot_market_enum($categories, $count, $template, $order, $condition, $active_only, ...)`

Main widget generator for the item list. All parameters are described in section 8.5.

### 8.4. Working with structure

#### `cot_market_sync($category)`

Returns the number of items in a category.

#### `cot_market_updateStructureCounters($category)`

Recalculates and updates the structure counter for a category. Clears the structure cache.

#### `cot_market_updatecat($oldcat, $newcat)`

Updates the category code for all items. Used when renaming categories.

#### `cot_market_count_with_children($cat)`

Returns the number of active items (state=0) in a category **and all its subcategories**.

#### `cot_market_count_active_in_cat($cat)`

Returns the number of active items in a specific category.

### 8.5. Tree and widgets

#### `cot_build_structure_market_tree($parent, $selected, $level, $template)`

Builds a hierarchical category tree. Returns HTML.

#### `cot_market_enum(...)`

**Full signature:**

```php
function cot_market_enum(
    $categories = '',        // string or array of category codes, '' = all
    $count = 0,              // count, 0 = all
    $template = '',          // template part name or path
    $order = '',             // SQL sorting
    $condition = '',         // additional SQL condition
    $active_only = true,     // only published and active
    $use_subcat = true,      // include subcategories
    $exclude_current = false,// exclude current item
    $blacklist = '',         // category blacklist
    $pagination = '',        // pagination GET parameter name
    $cache_ttl = null        // cache TTL in seconds
)
```

**Example usage in a template:**

```
{PHP|cot_market_enum('', 5, '', 'fieldmrkt_date DESC')}
{PHP|cot_market_enum('electronics', 10, 'sidebar')}
{PHP|cot_market_enum('', 5, '', '', '', true, true, false, '', 'p', 3600)}
```

#### `cot_getmarketlist($template, $count, $sqlsearch, $order)`

Simpler widget for displaying an item list on the home page. Returns HTML.

### 8.6. Utilities

#### `cot_cut_more_market($html)`

Truncates text by the `<!--more-->`, `[more]`, or `<hr class="more">` tag.

#### `cot_readraw_market($file)`

Reads file contents with directory traversal protection.

#### `cot_market_config_order($adminpart)`

Returns an array of available fields for item sorting.

#### `cot_market_config_main_order()`

Callback for configuring home page sorting. Returns a list of options.

#### `market_cat_has_header_tpl($catCode)`

Checks the existence of the `header.market.<cat>.tpl` template.

#### `market_cat_has_header_tpl_pageid($catCode)`

Checks the existence of the `header.market.<cat>.pagehasid.tpl` template.

#### `market_cat_has_footer_tpl($catCode)`

Checks the existence of the `footer.market.<cat>.tpl` template.

#### `market_cat_has_footer_tpl_pageid($catCode)`

Checks the existence of the `footer.market.<cat>.pagehasid.tpl` template.

#### `cot_market_selectbox_structure_select2($extension, $check, $name, ...)`

Renders a `<select>` with Select2 support and indentation for nested categories.

#### `cot_market_selectcat_select2($check, $name, $subcat, $hideprivate)`

Specialized category select for the search form.

---

## 9. Hooks list

The module provides the following hooks for plugins:

### Main
- `market.first` — at the beginning of the item page.
- `market.main` — after loading item data.
- `market.tags` — before the final parsing of the item template.
- `market.add.first` — start of the add page.
- `market.add.add.first` — before importing POST data.
- `market.add.add.import` — after import, before validation.
- `market.add.add.error` — after validation.
- `market.add.main` — before creating the XTemplate object.
- `market.add.tags` — before final parsing.
- `market.edit.first` — start of the edit page.
- `market.edit.update.first` — before POST processing.
- `market.edit.update.import` — after import.
- `market.edit.update.error` — after validation.
- `market.edit.main` — after template preparation.
- `market.edit.tags` — before final parsing.
- `market.list.first` — at the beginning of the list.
- `market.list.query` — before building the SQL.
- `market.list.main` — after data preparation.
- `market.list.rowcat.first` — before rendering subcategories.
- `market.list.rowcat.loop` — inside the subcategory loop.
- `market.list.before_loop` — before the item loop.
- `market.list.loop` — inside the item loop.
- `market.list.tags` — before final parsing.

### Administrative
- `market.admin.first` — at the beginning of the admin panel.
- `market.admin.validate` — before approval.
- `market.admin.validate.done` — after approval.
- `market.admin.unvalidate` — before unpublication.
- `market.admin.delete` — before deletion.
- `market.admin.delete.done` — after deletion.
- `market.admin.checked_validate` — during bulk approval.
- `market.admin.checked_delete` — during bulk deletion.
- `market.admin.loop` — inside the admin list loop.
- `market.admin.tags` — before final parsing of the admin template.

### Other
- `market.delete.first` — before item deletion (in the service).
- `market.delete.done` — after deletion.
- `market.enum.query` — in the cot_market_enum widget, before building the query.
- `market.enum.loop` — inside the widget loop.
- `market.enum.tags` — before final parsing of the widget.
- `market.tree.first` — at the beginning of tree building.
- `market.tree.main` — before rendering the tree.
- `market.tree.loop` — inside the tree loop.
- `markettags.first` — before item tag generation.
- `markettags.main` — at the end of tag generation.
- `market.item.getItems` — in the item.getItems handler.
- `market.itemService.getItems` — in the itemService.getItems handler.
- `market.userdetails.query` — in the seller showcase, before the query.
- `market.userdetails.loop` — inside the showcase loop.
- `market.userdetails.tags` — before final parsing of the showcase.

---

## 10. Integration with ItemService

The module automatically registers itself in the Cotonti `ItemService`. This allows items to participate in universal mechanisms:

- search;
- favorites;
- cart (if the corresponding plugin is installed);
- notifications;
- and other services working with abstract items.

Two files provide this integration:

- **`market.item.getItems.php`** — handler for the `item.getItems` hook.
- **`market.itemService.getItems.php`** — handler for the `itemService.getItems` hook.

Both files:
1. Check that the request source is `market`.
2. Normalize the list of item IDs.
3. Load data via `MarketRepository`.
4. Build `ItemDto` objects.
5. Add them to the common result array.
6. Call the corresponding hooks for extensions.

---

## 11. Extra fields (extrafields)

The module fully supports the Cotonti extrafields system. Registration is automatic:

```php
Cot::$db->registerTable('market');
cot_extrafields_register_table('market');
```

### Working with extrafields

All extra fields are automatically displayed in the add and edit forms. The `EXTRAFLD` block is provided in the template for this:

```html
<!-- BEGIN: EXTRAFLD -->
<div class="form-group">
    <label>{MARKETADD_FORM_EXTRAFLD_TITLE}</label>
    <div>{MARKETADD_FORM_EXTRAFLD}</div>
    <small>{MARKETADD_FORM_EXTRAFLD_CODENAME}</small>
</div>
<!-- END: EXTRAFLD -->
```

The `MARKETADD_HAS_EXTRAFIELDS` flag allows conditionally displaying a message about the presence or absence of fields.

### Extra field types

All standard Cotonti types are supported:
- `input` — text field;
- `textarea` — multiline text;
- `select` — dropdown (with value localization via language keys);
- `checkbox` — checkbox;
- `radio` — radio buttons;
- `file` — file;
- `datetime` — date/time.

---

## 12. Templates

### 12.1. Template files

All module templates are located in `modules/market/tpl/`. They can be overridden in the theme:

```
themes/<your_theme>/modules/market/<tpl_name>.tpl
```

Standard templates:

| Template | Purpose |
|----------|---------|
| `market.list.tpl` | Item list in the catalog |
| `market.main.tpl` | Item page |
| `market.add.tpl` | Add form |
| `market.edit.tpl` | Edit form |
| `market.enum.tpl` | Item list widget |
| `market.tree.tpl` | Category tree |
| `market.userdetails.tpl` | Seller showcase in the profile |
| `market.pagination.tpl` | Pagination |

### 12.2. Per-category templates

Each category can have its own template via the `tpl` field in the structure. The module will then look for:

- `market.list.<tpl>.tpl`
- `market.main.<tpl>.tpl`
- `market.add.<tpl>.tpl`
- `market.edit.<tpl>.tpl`

### 12.3. Custom header and footer

For each category, you can create custom templates:

- `header.market.<cat>.tpl` — common header for category pages;
- `header.market.<cat>.pagehasid.tpl` — header for the item page in the category;
- `footer.market.<cat>.tpl` — common footer;
- `footer.market.<cat>.pagehasid.tpl` — footer for the item page.

The module automatically determines the needed `location` through the `market.header.first.php` and `market.footer.first.php` handlers.

---

## 13. Caching

The module supports three cache levels:

### 13.1. First-level cache (in the repository)

`MarketRepository::getById()` saves the result in the static array `$cacheById`. Repeated requests for the same item within one HTTP request do not hit the DB.

### 13.2. Disk caching

Via the `$cache_ttl` parameter in `cot_market_enum()`, the widget rendering result is saved to the Cotonti disk cache. The cache key depends on the template, language, and SQL query.

### 13.3. Static page caching

The module supports the Cotonti static cache:

- Automatically clears item and category pages on edit.
- Clears the home page when a new item is published.
- Uses the AJAX view counter so that cached pages still track statistics.

```php
if (Cot::$cache) {
    if (Cot::$cfg['cache_market']) {
        Cot::$cache->static->clearByUri(cot_market_url($itemData));
        Cot::$cache->static->clearByUri(cot_url('market', ['c' => $itemData['fieldmrkt_cat']]));
    }
    if (Cot::$cfg['cache_index']) {
        Cot::$cache->static->clear('index');
    }
}
```

---

## 14. Multilingual support

### 14.1. Language files

The module supports the standard Cotonti language file mechanism. For each language, a file `market.<lang>.lang.php` is created. Currently available:

- `market.ru.lang.php` — Russian;
- `market.en.lang.php` — English;
- `market.uk.lang.php` — Ukrainian.

### 14.2. Custom language files

Via the `cot_langfile_custom()` function, you can connect your own files:

```php
if (function_exists('cot_langfile_custom')) {
    cot_langfile_custom('market', 'module');
}
```

This allows you to store custom strings in a separate file `market.custom.<lang>.lang.php` and keep them safe during module updates.

### 14.3. Integration with i18n4marketpro

The `i18n4marketpro` plugin is used to translate category and item titles and descriptions. The module automatically picks up translations via the function:

```php
cot_i18n4marketpro_get_cat($code, $locale)
```

If a translation is not found, the default value is displayed.

---

## 15. Extending functionality

### 15.1. Via plugins

Creating a plugin that extends Market PRO:

1. Register your plugin in `plugins/<your_plugin>/`.
2. Specify the needed hooks in the `[BEGIN_COT_EXT]` block:

```
[BEGIN_COT_EXT]
Hooks=market.list.loop,market.add.main
[END_COT_EXT]
```

3. In the handlers, access the environment variables and modify them.

### 15.2. Via hooks

The module calls hooks at almost every stage of operation. For example, to add an extra block to the item card:

```php
/* === Hook === */
foreach (cot_getextplugins('market.main') as $pl) {
    include $pl;
}
/* ===== */
```

Your plugin can add its own variables to `$t` before parsing.

### 15.3. Via extra fields

Adding a new field to an item:

1. Go to **Administration → Other → Extrafields**.
2. Select the `cot_market` table.
3. Create a field (e.g., `fieldmrkt_brand`).
4. The field automatically appears in the add and edit forms.
5. In templates it will be available as `{MARKET_BRAND}` (with the corresponding block prefix).

### 15.4. Via custom templates

Create a file in your theme with a name matching the module template. For example:

```
themes/mytheme/modules/market/market.list.tpl
```

Cotonti will automatically pick it up instead of the standard one.

---

## 16. Plugin compatibility

### Required modules
- `extrafields` — working with extra fields.
- `forms` — forms and validation.
- `users` — users and profiles.

### Recommended plugins
- **comments** — item comments.
- **attacher** — image and file uploads.
- **tgm4market** — Telegram bot integration.
- **i18n4marketpro** — multilingual translations.
- **aliasmarketpro** — advanced SEO URL handling.
- **xtradbrowmarket-cotonti** — extended extrafields output.

### Payment systems
The module is compatible with the Cotonti payment system. The store currency is automatically substituted from the payment settings or configured manually.

---

## 17. Links

- **Source code and updates**: [https://github.com/webitproff/marketpro-cotonti](https://github.com/webitproff/marketpro-cotonti)
- **Documentation and description**: [https://abuyfile.com/ru/market/cotonti/plugs/marketpro](https://abuyfile.com/ru/market/cotonti/plugs/marketpro)
- **Support forum**: [https://abuyfile.com/ru/forums/cotonti/custom/marketpro](https://abuyfile.com/ru/forums/cotonti/custom/marketpro)
- **Cotonti Extrafields API**: [https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php](https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php)
- **Cotonti official site**: [https://www.cotonti.com/](https://www.cotonti.com/)

---

## 18. License

**BSD License**

Copyright (c) webitproff, 2026

Free use, modification, and distribution of the module is permitted provided that the copyright notice and license are preserved. The module is provided "as is", without any warranties.

Full license text — in the [LICENSE](https://github.com/webitproff/marketpro-cotonti/blob/main/LICENSE) file.

---

**Document version**: 1.0
**Last updated**: September 2026

___
> РУССКИЙ 
___


# Market PRO — модуль интернет-магазина и торговой площадки для Cotonti

[![PHP](https://img.shields.io/badge/PHP-8.5%2B-777bb3.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.4%2B-4479a1.svg)](https://www.mysql.com/)
[![Cotonti](https://img.shields.io/badge/Cotonti-%2B-2e7d32.svg)](https://www.cotonti.com/)
[![License](https://img.shields.io/badge/License-BSD-blue.svg)](https://github.com/webitproff/marketpro-cotonti/blob/main/LICENSE)

**Market PRO** — модуль для CMF Cotonti, реализующий функциональность интернет-магазина и мультивендорной торговой площадки. Полностью совместим с PHP 8.5+, MySQL 8.4+, использует строгую типизацию и namespace-пространства.

---

## Содержание

- [1. Общее описание](#1-общее-описание)
- [2. Ключевые возможности](#2-ключевые-возможности)
- [3. Требования](#3-требования)
- [4. Установка](#4-установка)
- [5. Структура файлов](#5-структура-файлов)
- [6. Архитектура](#6-архитектура)
  - [6.1. Классы и сервисы](#61-классы-и-сервисы)
  - [6.2. Файлы-обработчики хуков](#62-файлы-обработчики-хуков)
  - [6.3. Точки входа модуля](#63-точки-входа-модуля)
- [7. База данных](#7-база-данных)
- [8. Функции API](#8-функции-api)
  - [8.1. Работа с товарами](#81-работа-с-товарами)
  - [8.2. Формирование URL и прав](#82-формирование-url-и-прав)
  - [8.3. Генерация тегов шаблона](#83-генерация-тегов-шаблона)
  - [8.4. Работа со структурой](#84-работа-со-структурой)
  - [8.5. Дерево и виджеты](#85-дерево-и-виджеты)
  - [8.6. Утилиты](#86-утилиты)
- [9. Список хуков](#9-список-хуков)
- [10. Интеграция с ItemService](#10-интеграция-с-itemservice)
- [11. Дополнительные поля (extrafields)](#11-дополнительные-поля-extrafields)
- [12. Шаблоны](#12-шаблоны)
  - [12.1. Файлы шаблонов](#121-файлы-шаблонов)
  - [12.2. Индивидуальные шаблоны категорий](#122-индивидуальные-шаблоны-категорий)
  - [12.3. Кастомные header и footer](#123-кастомные-header-и-footer)
- [13. Кэширование](#13-кэширование)
- [14. Мультиязычность](#14-мультиязычность)
- [15. Расширение функционала](#15-расширение-функционала)
  - [15.1. Через плагины](#151-через-плагины)
  - [15.2. Через хуки](#152-через-хуки)
  - [15.3. Через дополнительные поля](#153-через-дополнительные-поля)
  - [15.4. Через пользовательские шаблоны](#154-через-пользовательские-шаблоны)
- [16. Совместимость с плагинами](#16-совместимость-с-плагинами)
- [17. Ссылки](#17-ссылки)
- [18. Лицензия](#18-лицензия)

---

## 1. Общее описание

Market PRO — модуль для Cotonti, реализующий функциональность интернет-магазина (один продавец) и мультивендорной торговой площадки (множество продавцов). Модуль написан на современном PHP с использованием:

- **Строгой типизации** (`declare(strict_types=1)`).
- **Пространств имён** (`cot\modules\market\inc\...`).
- **Сервисов** (Singleton, Repository, DTO).
- **Хуков** (полная совместимость с системой плагинов Cotonti).
- **Автозагрузки классов** через namespace-пути.

Модуль интегрируется с общими механизмами Cotonti: системой дополнительных полей, авторизацией, кэшем, языковыми файлами, а также с сервисом `ItemService` для участия в универсальных выборках (поиск, корзина, избранное).

---

## 2. Ключевые возможности

### Для пользователей
- Каталог товаров с иерархией категорий неограниченной вложенности.
- Карточка товара с полным описанием, галереей, комментариями.
- Поиск по названию, описанию, артикулу.
- Фильтрация и сортировка по любым полям.
- AJAX-подгрузка списков («Загрузить ещё»).
- Личная витрина продавца в профиле.
- Публикация товаров: черновик, модерация, публикация.
- Клонирование товаров.

### Для администраторов
- Управление всеми товарами с фильтрами по статусу.
- Массовые операции: утверждение, удаление.
- Модерация публикаций.
- Настройка прав по категориям.
- Управление дополнительными полями.
- Настройка сортировки и пагинации для каждой категории.

### Технические
- SEO: meta title, meta description, H1, алиасы, канонические URL.
- Кэширование: статическое, дисковое, кэш первого уровня в репозитории.
- Мультиязычность: языковые файлы + поддержка `i18n4marketpro`.
- Безопасность: CSRF-защита, подготовленные SQL-запросы, разграничение прав.
- Расширяемость через хуки и дополнительные поля.

---

## 3. Требования

| Компонент | Минимальная версия |
|-----------|-------------------|
| PHP | 8.5 |
| MySQL | 8.4 |
| Cotonti | Siena и выше |
| Обязательные модули Cotonti | `extrafields`, `forms`, `users` |
| Опциональные плагины | `comments`, `attacher`, `tgm4market`, `i18n4marketpro`, `aliasmarketpro` |

Дополнительные зависимости (composer-пакеты) не требуются.

---

## 4. Установка

1. Скопировать каталог `market` в `modules/` вашего сайта на Cotonti.
2. Зайти в **Администрирование → Расширения** и установить модуль Market.
3. При установке:
   - автоматически создаётся таблица `cot_market` (если её нет);
   - регистрируется таблица `market` в `Cot::$db`;
   - подключается таблица extrafields для `market`.
4. Настроить модуль в **Администрирование → Конфигурация → Market**.
5. Создать структуру категорий в **Администрирование → Структура → Market**.
6. При необходимости установить дополнительные плагины (комментарии, attacher и др.).

---

## 5. Структура файлов

```
modules/market/
├── market.php                              # главный файл модуля (точка входа)
├── market.admin.php                        # обработчик хука admin (админ-панель)
├── market.item.getItems.php                # обработчик хука item.getItems
├── market.itemService.getItems.php         # обработчик хука itemService.getItems
├── market.header.php                       # обработчик хука header.main (уведомления)
├── market.header.first.php                 # обработчик хука header.first (location)
├── market.header.tags.php                  # обработчик хука header.tags (title)
├── market.footer.first.php                 # обработчик хука footer.first (location)
├── market.userdetails.php                  # обработчик хука users.details.tags
├── inc/
│   ├── market.functions.php                # основной API модуля
│   ├── market.add.php                      # добавление товара
│   ├── market.edit.php                     # редактирование товара
│   ├── market.list.php                     # список товаров
│   ├── market.main.php                     # страница товара
│   ├── market.preview.php                  # страница предпросмотра
│   ├── market.counter.php                  # AJAX-счётчик просмотров
│   ├── MarketControlService.php            # сервис управления товарами
│   ├── MarketDictionary.php                # словарь констант
│   ├── MarketRepository.php                # репозиторий товаров
│   └── market.setup.php                    # настройки модуля
├── lang/
│   ├── market.ru.lang.php                  # русский
│   ├── market.en.lang.php                  # английский
│   └── market.uk.lang.php                  # украинский
└── tpl/
    ├── market.add.tpl                      # шаблон формы добавления
    ├── market.edit.tpl                     # шаблон формы редактирования
    ├── market.list.tpl                     # шаблон списка товаров
    ├── market.main.tpl                     # шаблон страницы товара
    ├── market.enum.tpl                     # шаблон виджета списка
    ├── market.tree.tpl                     # шаблон дерева категорий
    ├── market.userdetails.tpl              # шаблон витрины продавца
    └── market.pagination.tpl               # шаблон пагинации
```

---

## 6. Архитектура

### 6.1. Классы и сервисы

Модуль использует современный подход к организации кода. Все классы находятся в namespace `cot\modules\market\inc`.

#### `MarketDictionary`

Словарь констант модуля.

```php
namespace cot\modules\market\inc;

class MarketDictionary
{
    public const SOURCE_MARKET = 'market';

    public const STATE_PUBLISHED = 0;  // Опубликован
    public const STATE_PENDING   = 1;  // На модерации
    public const STATE_DRAFT     = 2;  // Черновик
}
```

#### `MarketRepository`

Репозиторий для работы с таблицей `cot_market`. Наследует `BaseRepository`. Содержит кэш первого уровня (в пределах одного HTTP-запроса).

```php
namespace cot\modules\market\inc;

class MarketRepository extends BaseRepository
{
    private static $cacheById = [];

    public static function getTableName(): string;
    public function getById(int $id, bool $useCache = true): ?array;
    protected function afterFetch(array $item): array;
}
```

Метод `getById($id)` возвращает массив данных товара или `null`.

#### `MarketControlService`

Сервис управления товарами. Реализует Singleton через трейт `GetInstanceTrait`. Отвечает за операции, требующие транзакций (например, удаление).

```php
namespace cot\modules\market\inc;

class MarketControlService
{
    use GetInstanceTrait;

    public function delete(int $id, array $itemData = []): bool|string;
}
```

Метод `delete()` возвращает сообщение об успехе или `false` при ошибке. Внутри:
- открывает транзакцию;
- удаляет связанные файлы extrafields;
- вызывает хуки `market.delete.first` и `market.delete.done`;
- удаляет запись из БД;
- обновляет счётчики структуры;
- уведомляет `ItemService::onDelete()`;
- очищает статический кэш страниц товара и категории.

#### `ItemDto`

DTO из ядра Cotonti, используется для интеграции с `ItemService`. Содержит поля: `source`, `sourceId`, `type`, `title`, `description`, `url`, `ownerId`, `categoryCode`, `categoryTitle`, `categoryUrl`, `data`.

### 6.2. Файлы-обработчики хуков

Эти файлы подключаются автоматически через блок `[BEGIN_COT_EXT]`:

| Файл | Хук | Назначение |
|------|-----|-----------|
| `market.admin.php` | `admin` | Админ-панель управления товарами |
| `market.item.getItems.php` | `item.getItems` | Отдача данных товаров через общий API Cotonti |
| `market.itemService.getItems.php` | `itemService.getItems` | Отдача данных товаров через ItemService |
| `market.header.php` | `header.main` | Уведомления о товарах в шапке сайта |
| `market.header.first.php` | `header.first` | Установка `Cot::$env['location']` для header-шаблонов |
| `market.header.tags.php` | `header.tags` | Переопределение `HEADER_TITLE` для категорий |
| `market.footer.first.php` | `footer.first` | Установка `Cot::$env['location']` для footer-шаблонов |
| `market.userdetails.php` | `users.details.tags` | Вкладка «Товары» в профиле пользователя |

### 6.3. Точки входа модуля

Все inc-файлы вызываются через `market.php`:

| Параметр `m` | Файл |
|--------------|------|
| `list` | `inc/market.list.php` |
| `main` | `inc/market.main.php` |
| `add` | `inc/market.add.php` |
| `edit` | `inc/market.edit.php` |
| `preview` | `inc/market.preview.php` |
| `counter` | `inc/market.counter.php` (AJAX) |

---

## 7. База данных

### Таблица `cot_market`

| Поле | Тип | Описание |
|------|-----|----------|
| `fieldmrkt_id` | INT AUTO_INCREMENT | Первичный ключ |
| `fieldmrkt_cat` | VARCHAR(255) | Код категории |
| `fieldmrkt_alias` | VARCHAR(255) | Алиас (ЧПУ) |
| `fieldmrkt_title` | VARCHAR(255) | Название товара |
| `fieldmrkt_desc` | VARCHAR(255) | Краткое описание |
| `fieldmrkt_text` | TEXT | Полный текст |
| `fieldmrkt_parser` | VARCHAR(64) | Парсер текста |
| `fieldmrkt_pcod` | VARCHAR(64) | Артикул |
| `fieldmrkt_costdflt` | DECIMAL | Цена по умолчанию |
| `fieldmrkt_cost_usd` | DECIMAL | Цена в USD |
| `fieldmrkt_date` | INT | Дата публикации (timestamp) |
| `fieldmrkt_begin` | INT | Дата начала активности |
| `fieldmrkt_expire` | INT | Дата окончания активности |
| `fieldmrkt_updated` | INT | Дата обновления |
| `fieldmrkt_ownerid` | INT | ID владельца |
| `fieldmrkt_count` | INT | Счётчик просмотров |
| `fieldmrkt_state` | TINYINT | Статус (0/1/2) |
| `fieldmrkt_metah1` | VARCHAR(255) | SEO H1 |
| `fieldmrkt_metatitle` | VARCHAR(255) | Meta title |
| `fieldmrkt_metadesc` | VARCHAR(255) | Meta description |

Также к таблице можно добавить любые поля через систему extrafields.

---

## 8. Функции API

Все функции находятся в `inc/market.functions.php`.

### 8.1. Работа с товарами

#### `cot_market_import($source, $ritem, $auth)`

Импортирует данные товара из запроса (GET/POST) в массив.

- `$source` — тип запроса (`'POST'`, `'GET'`, `'PATCH'`, `'D'` для direct).
- `$ritem` — массив исходных данных (для редактирования).
- `$auth` — массив прав.

Возвращает массив с полями `fieldmrkt_*`.

#### `cot_market_validate($ritem)`

Валидирует данные товара. Проверяет:
- наличие категории;
- длину названия;
- корректность алиаса;
- непустое описание (с учётом настройки категории).

Возвращает `true`/`false`.

#### `cot_market_add(&$ritem, $auth)`

Добавляет товар в БД. Возвращает ID нового товара или `null`.

Автоматически:
- проверяет уникальность алиаса;
- понижает статус до `STATE_PENDING`, если автор не админ;
- обновляет счётчики структуры;
- вызывает хуки `market.add.add.query`, `market.add.add.done`;
- очищает кэш.

#### `cot_market_update($id, &$ritem, $auth)`

Обновляет товар. Возвращает `true`/`false`.

Аналогично `cot_market_add()`, только для существующей записи.

#### `cot_market_status($state)`

Возвращает строковый статус: `'published'`, `'draft'`, `'pending'`.

### 8.2. Формирование URL и прав

#### `cot_market_url($data, $params = [], $tail = '', ...)`

Формирует URL товара. Учитывает алиас или ID, категорию и дополнительные параметры.

#### `cot_market_auth($cat = null)`

Возвращает массив прав для категории: `auth_read`, `auth_write`, `isadmin`, `auth_download`.

### 8.3. Генерация тегов шаблона

#### `cot_generate_markettags($item_data, $tag_prefix, $textLength, $admin_rights, ...)`

Возвращает массив тегов для шаблона. Все теги получают указанный префикс (например, `LIST_ROW_` или `MARKET_`).

Основные теги:
- `TITLE`, `DESCRIPTION`, `TEXT`, `TEXT_SHORT`, `TEXT_CUT`, `TEXT_IS_CUT`
- `URL`, `ID`, `ALIAS`, `PCOD`
- `STATE`, `STATUS`, `LOCAL_STATUS`
- `CAT`, `CAT_URL`, `CAT_TITLE`, `CAT_ICON`, `CAT_ICON_SRC`
- `COSTDFLT`, `COST_USD`, `COST_USD_FORMATTED`
- `CREATED`, `UPDATED`, `HITS`
- `META_H1`, `META_TITLE`
- `ADMIN`, `ADMIN_EDIT`, `ADMIN_DELETE`, `ADMIN_UNVALIDATE`
- `BREADCRUMBS`, `BREADCRUMBS_ITEM`

#### `cot_market_enum($categories, $count, $template, $order, $condition, $active_only, ...)`

Основной генератор виджета списка товаров. Все параметры описаны в разделе 8.5.

### 8.4. Работа со структурой

#### `cot_market_sync($category)`

Возвращает количество товаров в категории.

#### `cot_market_updateStructureCounters($category)`

Пересчитывает и обновляет счётчик структуры для категории. Очищает кэш структуры.

#### `cot_market_updatecat($oldcat, $newcat)`

Обновляет код категории у всех товаров. Используется при переименовании категорий.

#### `cot_market_count_with_children($cat)`

Возвращает количество активных товаров (state=0) в категории **и всех её подкатегориях**.

#### `cot_market_count_active_in_cat($cat)`

Возвращает количество активных товаров в конкретной категории.

### 8.5. Дерево и виджеты

#### `cot_build_structure_market_tree($parent, $selected, $level, $template)`

Строит иерархическое дерево категорий. Возвращает HTML.

#### `cot_market_enum(...)`

**Полная сигнатура:**

```php
function cot_market_enum(
    $categories = '',        // строка или массив кодов категорий, '' = все
    $count = 0,              // количество, 0 = все
    $template = '',          // имя части или путь к шаблону
    $order = '',             // SQL-сортировка
    $condition = '',         // дополнительное SQL-условие
    $active_only = true,     // только опубликованные и активные
    $use_subcat = true,      // включать подкатегории
    $exclude_current = false,// исключить текущий товар
    $blacklist = '',         // чёрный список категорий
    $pagination = '',        // имя GET-параметра пагинации
    $cache_ttl = null        // TTL кэша в секундах
)
```

**Пример использования в шаблоне:**

```
{PHP|cot_market_enum('', 5, '', 'fieldmrkt_date DESC')}
{PHP|cot_market_enum('electronics', 10, 'sidebar')}
{PHP|cot_market_enum('', 5, '', '', '', true, true, false, '', 'p', 3600)}
```

#### `cot_getmarketlist($template, $count, $sqlsearch, $order)`

Более простой виджет для вывода списка товаров на главной. Возвращает HTML.

### 8.6. Утилиты

#### `cot_cut_more_market($html)`

Обрезает текст по тегу `<!--more-->`, `[more]` или `<hr class="more">`.

#### `cot_readraw_market($file)`

Читает содержимое файла с защитой от обхода директорий.

#### `cot_market_config_order($adminpart)`

Возвращает массив доступных полей для сортировки товаров.

#### `cot_market_config_main_order()`

Callback для настройки сортировки на главной. Возвращает список вариантов.

#### `market_cat_has_header_tpl($catCode)`

Проверяет существование шаблона `header.market.<cat>.tpl`.

#### `market_cat_has_header_tpl_pageid($catCode)`

Проверяет существование шаблона `header.market.<cat>.pagehasid.tpl`.

#### `market_cat_has_footer_tpl($catCode)`

Проверяет существование шаблона `footer.market.<cat>.tpl`.

#### `market_cat_has_footer_tpl_pageid($catCode)`

Проверяет существование шаблона `footer.market.<cat>.pagehasid.tpl`.

#### `cot_market_selectbox_structure_select2($extension, $check, $name, ...)`

Рендерит `<select>` с поддержкой Select2 и отступами для вложенных категорий.

#### `cot_market_selectcat_select2($check, $name, $subcat, $hideprivate)`

Специализированный селект категорий для формы поиска.

---

## 9. Список хуков

Модуль предоставляет следующие хуки для плагинов:

### Основные
- `market.first` — в начале страницы товара.
- `market.main` — после загрузки данных товара.
- `market.tags` — перед финальным парсингом шаблона товара.
- `market.add.first` — начало страницы добавления.
- `market.add.add.first` — перед импортом данных POST.
- `market.add.add.import` — после импорта, до валидации.
- `market.add.add.error` — после валидации.
- `market.add.main` — перед созданием объекта XTemplate.
- `market.add.tags` — перед финальным парсингом.
- `market.edit.first` — начало страницы редактирования.
- `market.edit.update.first` — перед обработкой POST.
- `market.edit.update.import` — после импорта.
- `market.edit.update.error` — после валидации.
- `market.edit.main` — после подготовки шаблона.
- `market.edit.tags` — перед финальным парсингом.
- `market.list.first` — в начале списка.
- `market.list.query` — перед формированием SQL.
- `market.list.main` — после подготовки данных.
- `market.list.rowcat.first` — перед выводом подкатегорий.
- `market.list.rowcat.loop` — внутри цикла подкатегорий.
- `market.list.before_loop` — перед циклом товаров.
- `market.list.loop` — внутри цикла товаров.
- `market.list.tags` — перед финальным парсингом.

### Административные
- `market.admin.first` — в начале админ-панели.
- `market.admin.validate` — перед утверждением.
- `market.admin.validate.done` — после утверждения.
- `market.admin.unvalidate` — перед снятием с публикации.
- `market.admin.delete` — перед удалением.
- `market.admin.delete.done` — после удаления.
- `market.admin.checked_validate` — при массовом утверждении.
- `market.admin.checked_delete` — при массовом удалении.
- `market.admin.loop` — внутри цикла списка в админке.
- `market.admin.tags` — перед финальным парсингом админ-шаблона.

### Прочие
- `market.delete.first` — перед удалением товара (в сервисе).
- `market.delete.done` — после удаления.
- `market.enum.query` — в виджете cot_market_enum, перед формированием запроса.
- `market.enum.loop` — внутри цикла виджета.
- `market.enum.tags` — перед финальным парсингом виджета.
- `market.tree.first` — в начале построения дерева.
- `market.tree.main` — перед выводом дерева.
- `market.tree.loop` — внутри цикла дерева.
- `markettags.first` — перед генерацией тегов товара.
- `markettags.main` — в конце генерации тегов.
- `market.item.getItems` — в файле-обработчике item.getItems.
- `market.itemService.getItems` — в файле-обработчике itemService.getItems.
- `market.userdetails.query` — в витрине продавца, перед запросом.
- `market.userdetails.loop` — внутри цикла витрины.
- `market.userdetails.tags` — перед финальным парсингом витрины.

---

## 10. Интеграция с ItemService

Модуль автоматически регистрируется в системе `ItemService` Cotonti. Это позволяет товарам участвовать в универсальных механизмах:

- поиск;
- избранное;
- корзина (при наличии соответствующего плагина);
- уведомления;
- и другие сервисы, работающие с абстрактными элементами.

Два файла обеспечивают эту интеграцию:

- **`market.item.getItems.php`** — обработчик хука `item.getItems`.
- **`market.itemService.getItems.php`** — обработчик хука `itemService.getItems`.

Оба файла:
1. Проверяют, что источник запроса — `market`.
2. Нормализуют список ID товаров.
3. Загружают данные через `MarketRepository`.
4. Формируют объекты `ItemDto`.
5. Добавляют их в общий результирующий массив.
6. Вызывают соответствующие хуки для расширений.

---

## 11. Дополнительные поля (extrafields)

Модуль полностью поддерживает систему extrafields Cotonti. Регистрация происходит автоматически:

```php
Cot::$db->registerTable('market');
cot_extrafields_register_table('market');
```

### Работа с extrafields

В формах добавления и редактирования автоматически выводятся все дополнительные поля. Для этого в шаблоне предусмотрен блок `EXTRAFLD`:

```html
<!-- BEGIN: EXTRAFLD -->
<div class="form-group">
    <label>{MARKETADD_FORM_EXTRAFLD_TITLE}</label>
    <div>{MARKETADD_FORM_EXTRAFLD}</div>
    <small>{MARKETADD_FORM_EXTRAFLD_CODENAME}</small>
</div>
<!-- END: EXTRAFLD -->
```

Флаг `MARKETADD_HAS_EXTRAFIELDS` позволяет условно показать сообщение о наличии или отсутствии полей.

### Типы дополнительных полей

Поддерживаются все стандартные типы Cotonti:
- `input` — текстовое поле;
- `textarea` — многострочный текст;
- `select` — выпадающий список (с локализацией значений через языковые ключи);
- `checkbox` — флажок;
- `radio` — переключатели;
- `file` — файл;
- `datetime` — дата/время.

---

## 12. Шаблоны

### 12.1. Файлы шаблонов

Все шаблоны модуля находятся в `modules/market/tpl/`. Их можно переопределить в теме:

```
themes/<your_theme>/modules/market/<tpl_name>.tpl
```

Стандартные шаблоны:

| Шаблон | Назначение |
|--------|-----------|
| `market.list.tpl` | Список товаров в каталоге |
| `market.main.tpl` | Страница товара |
| `market.add.tpl` | Форма добавления |
| `market.edit.tpl` | Форма редактирования |
| `market.enum.tpl` | Виджет списка товаров |
| `market.tree.tpl` | Дерево категорий |
| `market.userdetails.tpl` | Витрина продавца в профиле |
| `market.pagination.tpl` | Пагинация |

### 12.2. Индивидуальные шаблоны категорий

Для каждой категории можно задать собственный шаблон через поле `tpl` в структуре. Тогда модуль будет искать:

- `market.list.<tpl>.tpl`
- `market.main.<tpl>.tpl`
- `market.add.<tpl>.tpl`
- `market.edit.<tpl>.tpl`

### 12.3. Кастомные header и footer

Для каждой категории можно создать собственные шаблоны:

- `header.market.<cat>.tpl` — общий header для страниц категории;
- `header.market.<cat>.pagehasid.tpl` — header для страницы товара в категории;
- `footer.market.<cat>.tpl` — общий footer;
- `footer.market.<cat>.pagehasid.tpl` — footer страницы товара.

Модуль автоматически определяет нужный `location` через обработчики `market.header.first.php` и `market.footer.first.php`.

---

## 13. Кэширование

Модуль поддерживает три уровня кэша:

### 13.1. Кэш первого уровня (в репозитории)

`MarketRepository::getById()` сохраняет результат в статическом массиве `$cacheById`. Повторные запросы одного и того же товара в рамках одного HTTP-запроса не идут в БД.

### 13.2. Дисковое кэширование

Через параметр `$cache_ttl` в `cot_market_enum()` результат рендеринга виджета сохраняется в дисковый кэш Cotonti. Ключ кэша зависит от шаблона, языка и SQL-запроса.

### 13.3. Статическое кэширование страниц

Модуль поддерживает статический кэш Cotonti:

- Автоматически очищает страницы товара и категории при редактировании.
- Очищает главную страницу при публикации нового товара.
- Использует AJAX-счётчик просмотров, чтобы кэшированные страницы всё равно учитывали статистику.

```php
if (Cot::$cache) {
    if (Cot::$cfg['cache_market']) {
        Cot::$cache->static->clearByUri(cot_market_url($itemData));
        Cot::$cache->static->clearByUri(cot_url('market', ['c' => $itemData['fieldmrkt_cat']]));
    }
    if (Cot::$cfg['cache_index']) {
        Cot::$cache->static->clear('index');
    }
}
```

---

## 14. Мультиязычность

### 14.1. Языковые файлы

Модуль поддерживает стандартный механизм языковых файлов Cotonti. Для каждого языка создаётся файл `market.<lang>.lang.php`. На данный момент доступны:

- `market.ru.lang.php` — русский;
- `market.en.lang.php` — английский;
- `market.uk.lang.php` — украинский.

### 14.2. Кастомные языковые файлы

Через функцию `cot_langfile_custom()` можно подключать свои файлы:

```php
if (function_exists('cot_langfile_custom')) {
    cot_langfile_custom('market', 'module');
}
```

Это позволяет хранить пользовательские строки в отдельном файле `market.custom.<lang>.lang.php` и не терять их при обновлении модуля.

### 14.3. Интеграция с i18n4marketpro

Для перевода названий и описаний категорий и товаров используется плагин `i18n4marketpro`. Модуль автоматически подхватывает переводы через функцию:

```php
cot_i18n4marketpro_get_cat($code, $locale)
```

Если перевод не найден, отображается значение по умолчанию.

---

## 15. Расширение функционала

### 15.1. Через плагины

Создание плагина, расширяющего Market PRO:

1. Регистрируете свой плагин в `plugins/<your_plugin>/`.
2. Указываете в блоке `[BEGIN_COT_EXT]` нужные хуки:

```
[BEGIN_COT_EXT]
Hooks=market.list.loop,market.add.main
[END_COT_EXT]
```

3. В обработчиках получаете доступ к переменным окружения и модифицируете их.

### 15.2. Через хуки

Практически на каждом этапе работы модуль вызывает хуки. Например, чтобы добавить дополнительный блок в карточку товара:

```php
/* === Hook === */
foreach (cot_getextplugins('market.main') as $pl) {
    include $pl;
}
/* ===== */
```

Ваш плагин может добавить свои переменные в `$t` перед парсингом.

### 15.3. Через дополнительные поля

Добавление нового поля к товару:

1. Зайти в **Администрирование → Прочее → Экстраполя**.
2. Выбрать таблицу `cot_market`.
3. Создать поле (например, `fieldmrkt_brand`).
4. Поле автоматически появится в формах добавления и редактирования.
5. В шаблонах оно будет доступно как `{MARKET_BRAND}` (с префиксом соответствующего блока).

### 15.4. Через пользовательские шаблоны

Создайте в своей теме файл с именем, соответствующим шаблону модуля. Например:

```
themes/mytheme/modules/market/market.list.tpl
```

Cotonti автоматически подхватит его вместо стандартного.

---

## 16. Совместимость с плагинами

### Обязательные модули
- `extrafields` — работа с дополнительными полями.
- `forms` — формы и валидация.
- `users` — пользователи и профили.

### Рекомендуемые плагины
- **comments** — комментарии к товарам.
- **attacher** — загрузка изображений и файлов.
- **tgm4market** — интеграция с Telegram-ботами.
- **i18n4marketpro** — мультиязычные переводы.
- **aliasmarketpro** — расширенная работа с ЧПУ.
- **xtradbrowmarket-cotonti** — расширенный вывод extrafields.

### Платёжные системы
Модуль совместим с системой платежей Cotonti. Валюта магазина автоматически подставляется из настроек платежей или настраивается вручную (зависит от плагина).

---

## 17. Ссылки

- **Исходный код и обновления**: [https://github.com/webitproff/marketpro-cotonti](https://github.com/webitproff/marketpro-cotonti)
- **Документация и описание**: [https://abuyfile.com/ru/market/cotonti/plugs/marketpro](https://abuyfile.com/ru/market/cotonti/plugs/marketpro)
- **Форум поддержки**: [https://abuyfile.com/ru/forums/cotonti/custom/marketpro](https://abuyfile.com/ru/forums/cotonti/custom/marketpro)
- **API Extrafields в Cotonti**: [https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php](https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php)
- **Официальный сайт Cotonti**: [https://www.cotonti.com/](https://www.cotonti.com/)

---

## 18. Лицензия

**BSD License**

Copyright (c) webitproff, 2026

Разрешается свободное использование, модификация и распространение модуля при условии сохранения уведомления об авторских правах и лицензии. Модуль предоставляется «как есть», без каких-либо гарантий.

Полный текст лицензии — в файле [LICENSE](https://github.com/webitproff/marketpro-cotonti/blob/main/LICENSE).

---

**Версия документа**: 1.0
**Последнее обновление**: Сентябрь 2026


