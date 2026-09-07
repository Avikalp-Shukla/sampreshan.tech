# ShivBodh Trust — Audit Report

> Read-only audit done by Kilo on `shivbodhtrust.org/wp-content/themes/shivbodh-child/`.
> All file paths are relative to the theme root.

---

## 1. Why the site is not loading (root cause)

Most likely cause: **fatal `Class 'ShivBodh_Nav_Walker' not found`** when WordPress renders the header on the frontend.

### Evidence

`header.php` line 46 instantiates `new ShivBodh_Nav_Walker()` when the `primary` nav menu location is assigned in WP Admin → Appearance → Menus.

But the class definition lives in `footer.php` lines 121–154 — inside a trailing `<?php` block AFTER `wp_footer()`, `</body>`, `</html>`.

PHP itself parses the whole file before executing, so class definition position is technically fine. The real fatal trigger is more subtle:

1. In PHP 8.x, declaring a class with a method signature `start_lvl( &$output, $depth = 0, $args = null )` produces a deprecation warning, but **not** a fatal.
2. **Real fatal:** `Walker_Nav_Menu` is a WP core class loaded by `wp-includes/nav-menu-template.php`. If the theme's `functions.php` is hooked before `wp_nav_menu()` runs but the walker is declared only in `footer.php` — when WordPress renders the header (which calls `wp_nav_menu` with the walker), the class is **not yet defined** because footer hasn't been parsed yet.

Result: blank page / 500 error in WP debug mode = "site not loading".

### Secondary bugs

| # | File | Line | Issue | Severity |
|---|------|------|-------|----------|
| 1 | header.php | 90 | URL typo `/aboout-us/` (3 o's) — broken About link | High |
| 2 | footer.php | 72 | Same `/aboout-us/` typo | High |
| 3 | footer.php | 121-154 | Walker class declared inside footer after `wp_footer()` — causes fatal if `primary` menu is assigned | **CRITICAL** |
| 4 | footer.php | 30 | Social icon for Facebook uses `&#102;` = `f`, should be Facebook brand glyph or replaced | Low |
| 5 | footer.php | 33 | Twitter icon `&#120143;` is not a valid Twitter glyph | Low |
| 6 | footer.php | 38 | Instagram `&#9737;` is a sun symbol, not Instagram | Low |
| 7 | header.php | 24 | Falls back to `/images/logo.png` but no such file in theme — broken alt path | Medium |
| 8 | functions.php | 13 | Version 2.0.0 but no version bump visible elsewhere — stale | Info |
| 9 | header.php | 108 | "Gau Mata" CTA has `style="display:none;"` — invisible by design | Info |
| 10 | header.php | 130 | Mobile submenu toggle is `<button>` but accessibility — needs proper role | Low |

---

## 2. Repo inventory

```
wp-content/themes/shivbodh-child/
├── 404.php              2.3 KB
├── archive.php          3.0 KB
├── comments.php         3.3 KB
├── footer.php           8.5 KB  ← BUGS 1, 2, 3, 4-6
├── front-page.php       25 KB
├── functions.php        10.9 KB
├── header.php           9.0 KB  ← BUGS 1, 7, 9, 10
├── inc/                 (empty)
├── index.php            2.0 KB
├── js/main.js           8.2 KB  (clean)
├── page-templates/
│   ├── template-about.php
│   ├── template-contact.php
│   ├── template-faq.php
│   ├── template-peetham.php
│   └── template-save-cow.php
├── page.php             0.9 KB
├── responsive.css       12 KB
├── search.php           2.8 KB
├── sidebar.php          2.2 KB
├── single.php           6.4 KB
├── style.css            50 KB  (token system in place)
└── template-parts/      (empty)
```

`inc/` and `template-parts/` are empty — no modular organization yet.

---

## 3. Asset inventory

Enqueued from `functions.php` (`shivbodh_child_enqueue_assets`):

| Handle | Source | Version | In Footer | Notes |
|--------|--------|---------|-----------|-------|
| `astra-parent` | `astra/style.css` | theme version | No | OK |
| `shivbodh-child` | `style.css` | 2.0.0 | No | OK, has tokens |
| `shivbodh-responsive` | `responsive.css` | 2.0.0 | No | OK |
| `shivbodh-google-fonts` | Google Fonts CDN | null | No | Render-blocking — should preload |
| `shivbodh-main` | `js/main.js` | 2.0.0 | Yes | OK |

No `defer` / `async` attributes. No CSS critical extraction. Fonts block render.

---

## 4. Template inventory

| Template | Linked pages (assumed) | Status |
|----------|------------------------|--------|
| `template-about.php` | `/about-us/` | Live — but header links to `/aboout-us/` (broken) |
| `template-contact.php` | `/contact-us/` | Live |
| `template-faq.php` | `/faq/` | Live |
| `template-peetham.php` | `/sringeri-peetha/`, `/dwarka-peetha/`, `/jyotishpeetha/`, `/puri-peetha/`, `/kanchi-peetha/` | Live — 5 pages |
| `template-save-cow.php` | `/save-cow/` | Live |

No template for: petitions, donate, events (uses TEC default), volunteer, membership, blog landing, contact form handler.

---

## 5. CPT / Plugin audit

Custom post types registered: **none in `functions.php`** — only built-in (posts, pages).

Plugins in scope (frontend affecting):
- Elementor / Elementor Pro — page builder
- The Events Calendar — event listings
- LiteSpeed Cache — performance
- Optimole — images
- Rank Math SEO — schema + meta
- FluentCRM — newsletter / members
- SeekAI AI — content generation
- Feedzy RSS — feeds
- All-in-One WP Migration — backup utility

Plugins admin-only:
- FluentSMTP
- Google Site Kit
- SEO/Rank Math admin
- Broken-link-checker

---

## 6. Proposed IA (top-level menu)

Hindi-first, English-secondary. Each top item maps to a page template.

| # | Hindi label | English fallback | Page | CTA |
|---|-------------|------------------|------|-----|
| 1 | होम | Home | front-page | — |
| 2 | हमारे बारे में | About Us | template-about | दान |
| 3 | चार अम्नाय पीठ | Sacred Peethams | template-peetham (×4 children) | दान |
| 4 | धार्मिक याचिकाएँ | Petitions | new template-petitions | हस्ताक्षर |
| 5 | गौ रक्षा | Save Cow | template-save-cow | स्वयंसेवक |
| 6 | कार्यक्रम | Events | TEC skin | पंजीकरण |
| 7 | स्वयंसेवक | Volunteer | new template-volunteer | आवेदन |
| 8 | दान | Donate | new template-donate | दान |
| 9 | ब्लॉग / ज्ञानकोश | Blog | index/archive | — |
| 10 | संपर्क | Contact | template-contact | संदेश |
| 11 | सदस्यता | Membership | new template-membership | सदस्य बनें |

---

## 7. Risk register

| Risk | Mitigation |
|------|-----------|
| Changing nav URLs breaks SEO | Add 301 redirects from old slugs (`/aboout-us/` → `/about-us/`) in `.htaccess` |
| Walker class move breaks `primary` menu rendering | Move class to `inc/class-nav-walker.php`, include from `functions.php` |
| Font preconnect + display=swap improvement might shift layout | Use `font-display: swap` + `size-adjust` fallback metrics |
| Elementor templates may override new header | Add `remove_theme_support('elementor')` per page if needed |
| LiteSpeed cache may serve stale header | Purge cache after deploy + add query string `?v=2.0.1` |

---

## Next step

Patches in `/Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/`:
- `01-header.php` — fix typo + clean markup
- `02-footer.php` — fix typo + remove walker class
- `03-inc-nav-walker.php` — walker class in proper location
- `04-functions.php` — include walker + register new nav menus + CPTs
- `05-new-templates/` — petition, donate, volunteer, membership

Apply order:
1. `03-inc-nav-walker.php` → copy into `wp-content/themes/shivbodh-child/inc/`
2. `04-functions.php` patch → merge into `functions.php`
3. `01-header.php` → replace
4. `02-footer.php` → replace
5. New templates → add to `page-templates/`

After patches: clear LiteSpeed cache, hard-refresh browser, test each top-level menu item.
