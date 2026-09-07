# ShivBodh Trust — Deployment Guide

## What's in this folder

```
shivbodhtrust-patches/
├── AUDIT-REPORT.md           ← Read first. Diagnoses the broken site.
├── 01-header.php             ← Patched header (fixes /aboout-us/ + logo fallback)
├── 02-footer.php             ← Patched footer (fixes /aboout-us/ + social SVGs + removes walker)
├── 03-inc-nav-walker.php     ← NEW file: walker class moved out of footer
├── 04-functions.php          ← Patched functions (loads walker, adds 4 CPTs, REST endpoints, AJAX)
├── template-petitions.php    ← NEW template: petition archive
├── template-donate.php       ← NEW template: donation form
├── template-volunteer.php    ← NEW template: volunteer signup
├── template-membership.php   ← NEW template: membership tiers
└── DEPLOY.md                 ← THIS FILE
```

## Critical bugs fixed

1. **Site won't load — fatal walker error.** Header.php uses `ShivBodh_Nav_Walker` but class was defined inside footer.php after `wp_footer()`. Fixed by moving class to `inc/class-nav-walker.php` and `require_once`-ing it from `functions.php`.

2. **`/aboout-us/` typo** in both header.php and footer.php. Fixed to `/about-us/`.

3. **Missing logo fallback** caused broken image. Now safely checks `file_exists()`.

4. **Broken social icons** (`&#102;`, `&#120143;`, `&#9737;`) replaced with proper SVG glyphs.

## How to deploy

### Option A — Manual copy (safest)

```bash
cd /Users/avikalpshukla/ShivBodh-Projects/shivbodhtrust.org/wp-content/themes/shivbodh-child/

# 1. Walker class (NEW directory + NEW file)
mkdir -p inc
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/03-inc-nav-walker.php \
   inc/class-nav-walker.php

# 2. Replace header.php (BACKUP first!)
cp header.php header.php.bak
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/01-header.php header.php

# 3. Replace footer.php (BACKUP first!)
cp footer.php footer.php.bak
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/02-footer.php footer.php

# 4. Merge functions.php (recommended: manually merge the 04 file into existing functions.php)
cp functions.php functions.php.bak
# Open 04-functions.php, copy the new blocks (CPTs, REST, AJAX) into functions.php
# OR replace entirely if you accept the diff:
# cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/04-functions.php functions.php

# 5. New templates
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/template-petitions.php   page-templates/template-petitions.php
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/template-donate.php      page-templates/template-donate.php
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/template-volunteer.php   page-templates/template-volunteer.php
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches/template-membership.php  page-templates/template-membership.php
```

### Option B — Shell one-liner

```bash
SBT=/Users/avikalpshukla/ShivBodh-Projects/shivbodhtrust.org/wp-content/themes/shivbodh-child
PATCH=/Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/shivbodhtrust-patches

mkdir -p "$SBT/inc" "$SBT/page-templates"
cp "$PATCH/03-inc-nav-walker.php" "$SBT/inc/class-nav-walker.php"
cp "$PATCH/01-header.php"        "$SBT/header.php"
cp "$PATCH/02-footer.php"        "$SBT/footer.php"
cp "$PATCH/template-petitions.php"   "$SBT/page-templates/template-petitions.php"
cp "$PATCH/template-donate.php"      "$SBT/page-templates/template-donate.php"
cp "$PATCH/template-volunteer.php"   "$SBT/page-templates/template-volunteer.php"
cp "$PATCH/template-membership.php"  "$SBT/page-templates/template-membership.php"

# Functions.php needs manual merge (see below)
echo ">>> Now manually merge 04-functions.php into $SBT/functions.php <<<"
```

## functions.php merge — manual steps

Since `functions.php` already exists, don't replace it wholesale. Add these blocks:

**1. Right after the `define()` block (around line 16):**

```php
require_once SHIVBODH_CHILD_DIR . '/inc/class-nav-walker.php';
```

**2. Add the CPT registration function:**

```php
function shivbodh_child_register_cpts() {
    // ... (copy from 04-functions.php, the `shivbodh_child_register_cpts` function)
}
add_action( 'init', 'shivbodh_child_register_cpts' );
```

**3. Add the taxonomy registration:**

```php
function shivbodh_child_register_taxonomies() {
    // ... (copy from 04-functions.php)
}
add_action( 'init', 'shivbodh_child_register_taxonomies' );
```

**4. Add the REST routes + verify handler + AJAX shloka** — all from `04-functions.php`.

**5. Optional: add the defer script filter** for better performance:

```php
function shivbodh_child_defer_scripts( $tag, $handle ) {
    $defer_handles = array( 'shivbodh-main' );
    if ( in_array( $handle, $defer_handles, true ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'shivbodh_child_defer_scripts', 10, 2 );
```

## Post-deploy checklist

After copying files:

1. **Clear all caches:**
   - LiteSpeed Cache → Purge All
   - Browser hard-refresh (Cmd+Shift+R / Ctrl+Shift+R)
   - WP Engine / Hostinger cache if applicable

2. **Test in WP Admin:**
   - Settings → Permalinks → Save Changes (flushes rewrite rules for new CPTs)
   - Appearance → Menus → re-save the `primary` menu (rebuilds menu cache)
   - CPTs should appear in sidebar: Petitions, Donations, Members, Shlokas

3. **Create pages with new templates:**
   - Pages → Add New
     - Title: "याचिकाएँ", Template: **Petitions**, Publish
     - Title: "दान करें", Template: **Donate**, Publish
     - Title: "स्वयंसेवक बनें", Template: **Volunteer**, Publish
     - Title: "सदस्यता", Template: **Membership**, Publish

4. **Verify each top-nav link:**
   - Home → loads
   - Shankaracharya Ji → loads
   - Sacred Peethams (dropdown) → loads each child
   - Dharmasevaka → loads
   - Articles → loads
   - About Us → loads (was `/aboout-us/`)
   - Contact → loads

5. **Check console for errors** (F12 → Console):
   - No "Class not found" → walker fix worked
   - No 404 on logo → file_exists fallback worked

## Risk mitigation

- All `.bak` backups created before copy. If anything breaks, restore with `cp *.bak originalname`.
- Add a 301 redirect in `.htaccess` for the old `/aboout-us/` typo to preserve any external links:
  ```
  Redirect 301 /aboout-us/ /about-us/
  ```

## What's NOT done yet (next prompts)

- **PROMPT 2:** Premium iconography (SVG sprite with 3D deity icons) — needs design + asset pipeline
- **PROMPT 3:** Per-peetham content blueprints (Hindi long-form outlines)
- **PROMPT 5:** The Events Calendar custom skin + Save Cow microsite
- **PROMPT 6:** SeekAI AI + PWA + push notifications
- **PROMPT 7:** Performance hardening + schema audit

The patches above are the foundation. Subsequent prompts build on top.
