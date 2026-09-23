# Market PRO: An Overview for Those Planning to Launch a Marketplace

## Table of Contents

- [1. What Market PRO Is and Who This Article Is For](#chto-takoe-market-pro)
- [2. Multi-Vendor — The Core Idea of the Module](#multivendornost)
- [3. Vendor Showcase: A Personal Store Inside the Site](#vitrina-prodavtsa)
- [4. Vendor Catalog: A Unified Platform](#katalog-prodavtsov)
- [5. Roles, Permissions and Item Ownership](#roli-i-prava)
- [6. Items: Lifecycle and Statuses](#tovar-zhiznennyy-tsikl)
- [7. Categories and Market Structure](#kategorii-i-struktura)
- [8. Seller Dashboard Inside the Profile](#lichnyy-kabinet)
- [9. Moderation and Platform Administration](#moderatsiya)
- [10. Search, Filters and Buyer Navigation](#poisk-i-filtry)
- [11. Prices, Currencies and International Settlements](#tseny-i-valyuty)
- [12. Orders, Cart and Digital File Delivery](#zakazy-i-korzina)
- [13. Reviews, Ratings and Trust](#otzyvy-i-reytingi)
- [14. Multilingual and Multi-Regional Support](#multiyazychnost)
- [15. SEO and Showcase Promotion](#seo-i-prodvizhenie)
- [16. Three Real Scenarios: Freelancers, Farmers, Cooperatives](#tri-stsenariya)
- [17. What Can Be Connected Optionally](#chto-podklyuchaetsya)
- [18. Who Should Take a Closer Look at Market PRO](#komu-podhodit)

---

<a id="chto-takoe-market-pro"></a>
## 1. What Market PRO Is and Who This Article Is For

Market PRO is a module for the Cotonti CMS that turns a website into a full-fledged marketplace. The module is not tied to a single seller: its architecture is designed from the ground up for scenarios where **different people** publish goods and services, and the platform acts as an intermediary between them and the buyer. This is what fundamentally distinguishes it from "single-user store" modules, where the site represents the interests of only one owner.

This article is not technical documentation. It is not for the programmer who will deploy the module, nor for the content manager who will upload the first items. It is for someone who is **weighing an idea**: is it worth getting involved, what can the module do "out of the box", what kind of markets can actually be built on it, and where something else will need to be connected.

Below is an overview of capabilities — without promises and without fantasies. Everything described has already been implemented in the module's code and its standard extensions.

---

<a id="multivendornost"></a>
## 2. Multi-Vendor — The Core Idea of the Module

The word "multi-vendor" sounds clunky, but the essence is simple: **many different people** can sell on one site, and each of them gets their own showcase. This is not an add-on or a "crutch on top of the main store". Multi-vendor support is built into the very structure of the data:

- every item has an **owner** — a specific site user;
- every seller has a **personal showcase page** at an address like "/market/vendor/seller_name";
- there is a **general vendor catalog** listing all showcases on the platform;
- each seller can maintain **their own categories** in which they list items;
- in the admin panel, the **platform administrator** sees all items from all sellers, can filter them by status, category and owner, and can bulk-approve or reject them.

Simply put: the site owner sets up the platform, and the sellers themselves fill in the goods and their descriptions. This removes the routine of maintaining cards from the site owner's shoulders and allows the platform to scale without a proportional increase in administrative work.

This model is especially useful where **goods change quickly**, the assortment is updated daily, and it is physically impossible to manage everything by hand: farm products, freelancer services, equipment rentals, local services.

---

<a id="vitrina-prodavtsa"></a>
## 3. Vendor Showcase: A Personal Store Inside the Site

A vendor showcase is a separate page that looks like a personal mini-store. It opens by the seller's username. It contains:

- **A header with information about the seller:** avatar, username, link to their system profile, last activity date, registration date, counters — how many items the seller has in total and how many categories they sell in.
- **An "about the seller" block** — filled in through the extra-fields system and lets the seller tell about themselves, their business, their principles of work.
- **A left sidebar with categories** that this seller actually has. Only categories where they have published items are shown. If a seller only trades honey and wax, they will not see links to electronics in their tree — the tree will be compact and precise.
- **A search form** across the items of this specific seller. The buyer searches inside one store without being distracted by the entire platform's assortment.
- **A grid of item cards** with images, titles, short descriptions and prices.
- **Pagination.**

The vendor showcase is a standalone SEO unit. It is indexed by search engines as a separate page with its own title and meta description.

For the platform owner, this means: each seller essentially gets their own ready-made section on the site, to which they can drive their audience from social networks and messengers.

---

<a id="katalog-prodavtsov"></a>
## 4. Vendor Catalog: A Unified Platform

In addition to individual vendor showcases, the platform has a **general vendor catalog** — a page listing all active sellers. Each seller is represented by a card showing:

- avatar and username;
- description (filled in via profile extra-fields);
- number of listed items;
- number of categories they sell in;
- registration date.

The vendor catalog can be **sorted**: by the date of the last added item, by number of items, by name, by registration date, by last activity. There is also **search by seller username** and **filter by category**: you can find everyone who sells in a chosen category, for example, "all electric scooter sellers".

The seller card leads not to the user's profile in the general sense, but to their **showcase** — straight to the items. A separate button leads to the profile if the buyer cares about reputation and the user's history on the site.

For the buyer, this is the entry point "I don't know what I need — show me who sells here at all". For the platform owner — the platform's own showcase, a source of pride and a strong SEO section.

---

<a id="roli-i-prava"></a>
## 5. Roles, Permissions and Item Ownership

The module has several role levels:

- **Platform administrator** — sees all items, manages moderation, categories, extra fields, settings.
- **Seller** — an ordinary site user with permission to create items. Items are automatically linked to them as the owner.
- **Buyer** — a user without publishing rights. Can view items, leave reviews, place orders (if the corresponding plugins are connected).
- **Guest** — can view published items; depending on the platform settings, can place an order after registration.

Permissions are granted **by category**. That is, the platform can allow some sellers to publish items only in the "Farm Products" category, and others only in the "Electronics" category. This is used in highly specialized markets where you cannot let a seller accidentally end up in the wrong niche.

The owner of an item is always a specific user. Only they can edit their item, unpublish it, send it back for moderation. Nobody else (except the administrator) can modify or delete their item.

---

<a id="tovar-zhiznennyy-tsikl"></a>
## 6. Items: Lifecycle and Statuses

Every item goes through several states:

- **Draft** — the item is created but not yet shown to buyers. The seller can come back to it later, add to the description, change the price.
- **Pending moderation** — the item is sent for approval to the platform administrator. Buyers do not see it yet.
- **Published** — the item is visible to all buyers and participates in search.
- **Expired** — an item with an expired publication term (if a term was set).

The seller works with this cycle directly: saves as a draft, sends for moderation, publishes (if the platform administrator has allowed auto-publication for trusted sellers).

Every item has:
- a title and short description;
- full text with markup;
- an SKU or internal code;
- a price;
- a category;
- SEO title, meta description, SEF-URL alias;
- a set of extra fields configured for a specific category;
- attached files and images;
- a view counter;
- publication and update dates.

An item can be **cloned** — this is handy when you need to list several similar positions.

---

<a id="kategorii-i-struktura"></a>
## 7. Categories and Market Structure

Categories in Market PRO are hierarchical. This means a category can have subcategories, those can have their own subcategories, and so on. The nesting depth is not rigidly limited — the platform decides for itself how deeply to split the assortment.

Each category can have:
- a name and description;
- an icon;
- its own SEO title, meta description and keywords;
- its own default sorting (e.g., "newest first" or "cheapest first");
- its own number of items per page;
- a set of extra fields specific to that category.

Extra fields are a separate strength of the module. For example, for the "Cars" category you can set up fields "Year of manufacture", "Mileage", "Fuel type", and for the "Farm Products" category — "Package weight", "Shelf life", "Manufacturer". All of them will be displayed in the item card and participate in filtering.

Categories support **multilingualism**: the same category can have different names and descriptions in different site languages.

The category structure is displayed as a tree. There are several layout options: a base one, one for the sidebar of the item list, and one for a mobile "slide-out" menu. This lets the platform look equally good on a large monitor and on a smartphone.

---

<a id="lichnyy-kabinet"></a>
## 8. Seller Dashboard Inside the Profile

A separate strong feature of the module is the "Items" tab right inside a user's public profile. It is simultaneously:

- **the seller's dashboard**: they see their items, filter them by category, manage statuses;
- **the seller's public page**: buyers who visit the profile immediately see what this user sells.

The tab shows:
- a list of items with cards;
- category tabs with the number of items in each;
- a "Load more" button — the next batch of items loads without reloading the page;
- an "Add item" button — if the user has publishing rights.

From their own profile, the seller can immediately go to editing any item, unpublish it, send it back for review, or delete it.

This solution reduces the number of "places" where the seller needs to do something. Everything related to their items is available in one place — from the public profile.

---

<a id="moderatsiya"></a>
## 9. Moderation and Platform Administration

The module's admin panel is a separate screen listing all items on the platform. The administrator can:

- **Filter** items by status: all, awaiting approval, published, drafts, expired.
- **Search** items by title, full description, or SKU.
- **Filter by category** — useful when the platform has several large directions.
- **Sort** by any field: ID, date, author, title.
- **Bulk-approve** selected items — a whole batch in one click.
- **Bulk-delete** — also a batch.

For each item in the list, quick actions are available: open on the site, edit, approve, delete. There is a collapsible "more" block — with the full text and update date.

On the admin panel home page, the module shows a **statistics widget**:

- how many items are published, pending moderation, in drafts;
- how many items in total are on the platform;
- how many were added today, this week, this month;
- total number of views of all items;
- the share of published items in the overall catalog;
- an alert if a moderation queue has accumulated;
- top 5 categories by number of items;
- top 5 sellers by number of items;
- top 10 most viewed items;
- a feed of the latest added items.

There are also quick links from the admin panel: module settings, category structure, extra fields, item add form.

Such a panel lets the platform administrator keep a finger on the pulse without navigating through a dozen different sections.

---

<a id="poisk-i-filtry"></a>
## 10. Search, Filters and Buyer Navigation

Buyer search in Market PRO works in three modes, selectable right in the form:

- **by title** — fast and precise;
- **by title and description** — broader, finds items by mentioning a characteristic;
- **by SKU** — if the seller gave their items internal codes.

If the platform is multilingual, search automatically takes translations into account: a user with a Ukrainian interface will find an item even if the original title is in Russian, and vice versa.

Found words are **highlighted** in the results — the buyer immediately sees why this particular item was shown to them.

In addition to search, the buyer has access to filters by extra fields (via the same-name plugin) and sortings: by price, date, title, popularity, number of views.

For large catalogs, pagination works with a configurable number of items per page.

---

<a id="tseny-i-valyuty"></a>
## 11. Prices, Currencies and International Settlements

An item has **two prices**:

- **the main price** in the platform's base currency (what the buyer sees);
- **the price in an international currency** — usually in dollars, for cases where the supplier quotes in USD and the platform works in a local currency.

These two prices are linked by a **rate** configured in the module settings. The seller enters the price in dollars — the form automatically recalculates it into the base currency, and the seller just copies the resulting value. This is handy when the seller buys goods from a foreign supplier but sells in a local currency.

If the platform uses several currencies, a switcher can be connected — then the price will be recalculated on the buyer's side into their chosen currency.

For Schema.org microdata, the currency is specified separately — according to ISO 4217. This is important for correct price display in Google search results.

---

<a id="zakazy-i-korzina"></a>
## 12. Orders, Cart and Digital File Delivery

The Market PRO module itself is a showcase: it displays items and assembles them into a beautiful card. **Orders and the cart are connected by a separate plugin** — "Payordersmarket", which is installed on top of the module.

When the plugin is connected, an "Add to cart" button appears on every item card. In the cart, the buyer can place an order, pay for it (including with cryptocurrency), and get access to digital files if the item is a digital product.

The seller sees orders for their items in their dashboard: who ordered, when, in what status. If the item is a digital file, the seller can track how many times it was downloaded.

This bundle is especially useful for selling:
- digital goods (templates, courses, programs);
- services with prepayment;
- physical goods with delivery, where it is important to record the order and the address.

---

<a id="otzyvy-i-reytingi"></a>
## 13. Reviews, Ratings and Trust

On a platform where many different sellers trade, **reputation** is critically important. For this, there is the "Market Reviews" plugin — it is connected separately and provides:

- a star rating for every item;
- the ability to leave reviews and reply to them;
- aggregated metrics: average score, total number of reviews, the sum of all stars.

Reviews are displayed in the item card and in lists. On the seller's card in the showcase, average metrics across all their items are visible.

This is especially useful when the buyer makes a purchase decision not only by price but also by the experience of other buyers.

---

<a id="multiyazychnost"></a>
## 14. Multilingual and Multi-Regional Support

Market PRO supports **multiple languages** through a separate i18n plugin. What this gives in practice:

- the name and description of an item can be translated into different languages;
- categories have translated names and descriptions;
- search looks across all languages simultaneously;
- meta tags (title, description, keywords) are set for each category separately — this is important for promotion in different regions.

If the platform sells in several countries, or simply wants to be convenient for different audiences, multilingualism removes part of the burden: there is no need to create separate sites for each language.

---

<a id="seo-i-prodvizhenie"></a>
## 15. SEO and Showcase Promotion

Every item and every category in Market PRO is a full-fledged page optimized for search engines:

- **meta title**, **meta description**, **keywords** — set manually or inherited from the category settings;
- **SEF URL aliases** — a separate plugin generates human-readable URLs from the item's title;
- **Schema.org microdata** — prices, currency, category in a structured form for Google;
- **canonical URLs** — to avoid duplicates when sorting and paginating.

The vendor showcase is also indexed. This means that a seller who publishes items on the platform effectively promotes both their own showcase and the platform as a whole.

---

<a id="tri-stsenariya"></a>
## 16. Three Real Scenarios: Freelancers, Farmers, Cooperatives

### 16.1. A Freelancer Services Market

A platform where freelancers list their services. Each freelancer is a seller with their own showcase. Categories — by type of service: "Design", "Development", "Copywriting", "Marketing". Inside — subcategories.

Extra fields for a service: "Lead time", "Price from", "Work format" (remote/on-site), "Years of experience". The buyer searches by keywords, filters by price and deadlines.

The connected order plugin allows placing and paying for requests. The review plugin builds reputation: it is visible who does the work quickly and well.

Multi-vendor support here is not a luxury but a necessity. The platform cannot "hire" all freelancers as staff, and a freelancer alone will not build a whole site for a single service.

### 16.2. Farm Product Showcases with Delivery

Farmers list their products: honey, cheese, vegetables, eggs. Each farmer is a separate showcase with a short description of the farm. Platform categories: "Dairy", "Meat", "Vegetables and Fruits", "Honey and Jam", "Homemade Preserves".

Extra fields: "Weight", "Ingredients", "Shelf life", "Region of production", "Delivery method". The buyer filters by region and delivery method, reads reviews.

Multi-vendor support allows the platform to grow without additional administrative load: each farmer manages their items on their own, and the platform plays the role of showcase and logistics center.

The key benefit — the platform immediately shows **all** farmers operating in the region. The buyer does not have to visit ten separate sites.

### 16.3. A Cooperative Joint Sale of Equipment

A cooperative sells electric scooters and electric mopeds bought in a batch. Each cooperative member is a seller with their own showcase. Categories: "Electric Scooters", "Electric Mopeds", "Batteries", "Spare Parts", "Accessories".

Extra fields: "Mileage", "Year of manufacture", "Power", "Battery capacity", "Warranty". The buyer filters by characteristics, reads reviews, places an order.

The cooperative is not required to run sales centrally. Each member works with their own positions, while the shared platform provides economies of scale: the more sellers, the higher the search visibility, the more traffic.

The **auto-publication for trusted sellers** is especially useful here: proven cooperative members publish their items without manual moderation, while new ones go through the moderation queue.

---

<a id="chto-podklyuchaetsya"></a>
## 17. What Can Be Connected Optionally

Market PRO is a module with an extensible architecture. The base functionality covers the showcase, categories, search, and moderation. Additional capabilities are enabled by plugins:

- **Orders and cart** — checkout, payment, delivery.
- **Reviews and rating** — reputation of sellers and items.
- **Multilingualism** — translations of items and categories.
- **SEF URL aliases** — human-readable URLs from the item title.
- **Price generation** — recalculating the price from an international currency.
- **Parameter filters** — advanced filters for item cards.
- **Multi-categories** — one item in several categories at once.
- **Featured items, articles, forum topics** — promotional blocks.
- **Publication to Telegram** — item announcements in a channel, pulling in discussions.
- **Item extra fields** — your own characteristics structure.
- **Profile extra fields** — extended seller cards.
- **Files and images** — attaching galleries and enclosures.

Each plugin is connected independently. You can start with a basic showcase and gradually grow functionality.

---

<a id="komu-podhodit"></a>
## 18. Who Should Take a Closer Look at Market PRO

In short — those who are building a **platform for many sellers**, not a single-owner store. This includes:

- **local marketplaces** of services and goods;
- **niche cooperatives** — producers, farmers, artisans;
- **rental services** — scooters, bicycles, tools;
- **classified ads boards** with goods and services;
- **communities of craftspeople** — handmade work, custom items;
- **trading houses** — several directions under one roof.

Market PRO does not try to be "everything for everyone". It provides a specific set of functions: a multi-vendor showcase, categories with extra fields, moderation, search, SEO tools, and links to payments and reviews. If the idea has **many sellers** and needs order in their items — this module covers the main part of the task without custom development.

If, on the other hand, a single-seller store is being considered, the module will also handle it — but its main strength will remain unused.
