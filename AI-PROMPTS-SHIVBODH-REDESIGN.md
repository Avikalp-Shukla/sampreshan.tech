# AI Prompt Pack — ShivBodh Trust Complete Redesign (shivbodhtrust.org)

> Yeh document AI tools (Cursor / GitHub Copilot / v0 / Claude / GPT) ko feed karne ke liye hai.
> Project root: `/Users/avikalpshukla/ShivBodh-Projects/shivbodhtrust.org`
> Stack: WordPress + Astra parent + `shivbodh-child` child theme (`wp-content/themes/shivbodh-child/`).
> Plugins already installed: Elementor + Elementor Pro, Rank Math SEO, LiteSpeed Cache, Optimole, FluentCRM, FluentSMTP, The Events Calendar, SeekAI AI, Feedzy.
> Context: Yeh ek Sanatana Dharma preservation trust hai — pooja booking nahi. Focus: 4 Amnaya Peethams, Dharmik petitions (Sampreshana), Save Cow initiative, community, events, donations, blog/gyaan kosh.
> Existing design tokens already in `style.css` (--sb-saffron, --sb-gold, --sb-maroon etc.) — REUSE them, do NOT introduce a new palette.
> Har prompt **self-contained** hai — seedha paste karo, koi extra context mat do.

---

## PROMPT 1 — Full Site Audit + Information Architecture (RUN FIRST)

You are a senior WordPress architect + UX strategist. I run shivbodhtrust.org — a Sanatana Dharma preservation trust. The site is currently broken / not loading properly. Before touching any code, do a COMPLETE static + dynamic audit and propose the new IA.

DO THIS
1. Walk the entire repo. For each theme file (`header.php`, `footer.php`, `front-page.php`, `single.php`, `archive.php`, `page-templates/*.php`, `template-parts/*`), list:
   - file path
   - what it renders
   - which CSS classes / JS handles it uses
   - any obvious bugs (closed-but-not-opened tags, missing `wp_footer()`, hardcoded URLs, repeated header markup that should live in `header.php`, missing escaping, etc.)
2. Inspect `wp-content/themes/shivbodh-child/inc/`, `js/`, `responsive.css`, `style.css` — list every enqueued asset (handle, src, deps, version, in_footer). Identify duplicates, missing versions, render-blocking scripts, accessibility issues.
3. List the page templates that exist (`template-about.php`, `template-contact.php`, `template-faq.php`, `template-peetham.php`, `template-save-cow.php`) and what each is supposed to show. Identify which are referenced by WordPress pages, which are orphans.
4. Check `functions.php` for: CPTs, custom taxonomies, REST endpoints, shortcodes, widgets, Elementor compatibility, FluentCRM integration, donation hooks.
5. From Rank Math / The Events Calendar / FluentCRM settings (read plugin config files only — do not assume DB), enumerate the content types we have.

DELIVER
A. `audit-report.md` with sections:
   1. Repo map
   2. Theme inventory (file → purpose → issues)
   3. Asset inventory (CSS/JS → load order → issues)
   4. Template inventory (template → linked pages → gaps)
   5. Plugins in scope (which ones affect frontend, which only admin)
   6. Broken/missing pieces (the "why the site is not loading" diagnosis)
B. A proposed IA for the redesign:
   - Top-level menu (Hindi + English labels)
   - Per-menu-item sub-items
   - For each: existing template reused OR new template required
   - For each: which CTA (Donate / Sign Petition / Join Volunteer / Register Event / Subscribe)
C. Risk register: any change that could break existing URLs, SEO, FluentCRM forms, LiteSpeed cache rules.

DO NOT WRITE ANY NEW CODE IN THIS PROMPT. Audit + IA only.
```

---

## PROMPT 2 — Global Design System Lock + Premium Iconography (Hindi-first)

You are a senior product designer + frontend architect. Using the audit from PROMPT 1, lock a GLOBAL design system for shivbodhtrust.org and replace every emoji / generic icon with PREMIUM, ORIGINAL, 3D-textured, TRANSPARENT-BACKGROUND icons.

CONTEXT
- Child theme: `wp-content/themes/shivbodh-child/`. Template: Astra.
- Existing CSS custom properties in `style.css` (--sb-saffron #FF6B00, --sb-saffron-dark #E05500, --sb-gold #D4A017, --sb-maroon #8B1A1A, etc.) — KEEP them as the canonical palette.
- Existing class prefix: `.sb-*`. KEEP it. Do not introduce `.sbt-*` or `.vp-*`.
- Existing responsive stylesheet: `responsive.css`. Audit, refactor, do not duplicate.
- Site is bilingual visible (Hindi primary, English secondary). Typography must support Devanagari beautifully.

TYPOGRAPHY (global, enqueued once from `functions.php`)
- Hindi heading: "Tiro Devanagari Hindi" (Google Fonts)
- Hindi body: "Noto Sans Devanagari"
- English accent / numerals: "Cinzel"
- Sanskrit shloka / verse blocks: "Sanskrit Text" (Google Fonts)
- Single `@import` (or `wp_enqueue_style` with `media="all"`) — no per-page fallback chains.

GLOBAL TOKENS (extend the existing `:root` in `style.css` — do not start a new file unless necessary; if you do, call it `assets/css/tokens.css` and import it)
- spacing: 4 8 12 16 20 24 32 40 48 64 80 96
- radius: 6 10 14 20 28 (pill = 999)
- shadow: sm / md / lg / xl with saffron tint (rgba(193, 64, 12, 0.08) etc.)
- motion: 180ms ease-out (hover), 320ms cubic-bezier(.2,.8,.2,1) (menu/drawer)
- container max-width 1280, gutter 24 desktop / 16 mobile
- icon sizes: 24 32 40 56 80

ICON SET — design + ship as `assets/icons/sprite.svg` with `<symbol id="icon-{slug}">` per icon. Each icon: transparent background, `<linearGradient>` + `<filter>` for 3D sheen, inner highlight, soft shadow.

Menu slugs and required icons:
1. होम                slug: home              → illuminated temple bell, 3D brass
2. हमारे बारे में     slug: about             → open palm with Om, 3D gold
3. चार अम्नाय पीठ    slug: peetham           → four-arm dharmachakra, 3D rotating
        ↳ submenu: Sringeri, Dwarka, Puri, Jyotirmath — each with own page (template-peetham.php refactor)
4. धार्मिक याचिकाएँ  slug: petitions         → rolled scroll with seal, 3D maroon
        ↳ submenu: Active petitions, Start a petition, Signed petitions, Petition archive
5. गौ रक्षा           slug: save-cow          → cow with marigold garland, 3D (template-save-cow.php refactor)
6. कार्यक्रम           slug: events            → temple calendar with diya, 3D
        ↳ uses The Events Calendar — design custom card + filters
7. स्वयंसेवक          slug: volunteer         → joined hands with tilak, 3D
8. दान                 slug: donate            → open hand offering flower, 3D saffron
9. ब्लॉग / ज्ञानकोश  slug: blog              → open granth/book, 3D
10. संपर्क             slug: contact           → conch + bell, 3D
11. सदस्यता            slug: membership        → trishul-anchored badge, 3D
        ↳ FluentCRM integration — design custom registration form

For each icon also deliver PNG @1x/2x/3x fallback.

GLOBAL HEADER (single source — `header.php`)
- Sticky on scroll, transparent over hero, then solid saffron on scroll.
- Right side: हिंदी | English toggle (hreflang + WPML/Polylang if present, else JS-driven `<html lang>` swap of content + cookie).
- Primary CTA: "अभी दान करें" pill button — visible on every screen.
- Mobile: full-screen slide-down, accordion submenus, sticky bottom CTA bar (thumb-reach).

GLOBAL FOOTER (single source — `footer.php`)
- 4-column: about snippet / quick links / dharmic resources / contact
- Bottom strip: copyright, social icons, RSS, sitemap link
- "श्लोक ऑफ़ द डे" rotating widget that pulls from a custom post type `sb_shloka`

DELIVERABLES
A. Updated `style.css` with consolidated tokens + components.
B. `responsive.css` refactor — mobile-first.
C. `assets/icons/sprite.svg` + per-icon SVG files.
D. Updated `header.php` + `footer.php`.
E. `functions.php` updates: font enqueues (with proper preconnect), sprite injection, theme support flags, single global CSS handle.
F. `docs/DESIGN-SYSTEM.md` listing tokens, fonts, icon slugs, how to add a new icon.

CONSTRAINTS
- Lighthouse mobile ≥ 90.
- WCAG AA contrast.
- All SVGs optimized (svgo defaults).
- LiteSpeed Cache friendly: combine where possible, defer non-critical JS.
- Do NOT touch backend logic / CPTs / plugins in this prompt.

Output the file tree you will create/edit before writing code.
```

---

## PROMPT 3 — Content + Per-Peetham / Per-Petition / Per-Event Page Blueprint

Using the menu locked in PROMPT 2, produce the COMPLETE content + page blueprint for every section. Goal: each Amnaya Peetham, each major petition, each Save Cow initiative, each event category gets a dedicated SEO page with internal links.

DELIVER
1. `data/peethams.json` (in `wp-content/themes/shivbodh-child/data/`) — schema:
```
{
  "slug": "sringeri",
  "name_hi": "श्रृंगेरी शारदा पीठ",
  "name_en": "Sringeri Sharada Peetham",
  "shankaracharya_current": "...",
  "location": {"state": "Karnataka", "city": "Sringeri", "lat": ..., "lng": ...},
  "established_year": "8th century CE",
  "lineage": "...",
  "key_teachings": ["Advaita", "..."],
  "current_initiatives": ["..."],
  "hero_image_alt_hi": "...",
  "meta_title": "...", "meta_description": "..."
}
```
Cover all 4: Sringeri, Dwarka, Puri, Jyotirmath.

2. For every petition create a template-ready record:
```
{
  "slug": "...", "title_hi": "...", "summary_hi": "...",
  "category": "civilizational" | "ecological" | "legal" | "cultural",
  "started_on": "YYYY-MM-DD", "target_signatures": int,
  "current_signatures": int, "lead_pandit": "...", "status": "active|achieved|paused",
  "related_peetham_slug": "...", "cta_copy_hi": "..."
}
```

3. For Save Cow initiative: 5 sub-pages (gaushala network, legal aid, awareness campaigns, donations, volunteer).

4. For Events (The Events Calendar): design 3 custom templates — list, single, recurring. Hook into `tribe_events` post type, add custom fields for: associated peetham, language, online/offline, donation-driven yes/no.

5. Hindi long-form outlines (≥ 1200 words) for each Peetham page:
   - Itihaas (history)
   - Sthapna (founding story)
   - Sampradaya (lineage + key acharyas)
   - Samagri (temple + relics)
   - Vartaman anushtan (current activities)
   - 5 FAQs
   - CTA: दान / स्वयंसेवक / याचिका

6. SEO meta for every page (≤ 60 char title, ≤ 160 desc, OG image alt, canonical, JSON-LD: ReligiousOrganization + FAQPage + Event).

Do NOT write the donation flow yet — only page structure and copy.
```

---

## PROMPT 4 — Donation / Membership / Petition-Sign Flow (Frontend + Backend)

Build TRUST-grade flows for: one-time donation, recurring membership, petition signing. Implemented as WordPress-native (CPTs + REST + shortcodes) since the stack is WordPress.

DONATION (CPT `sb_donation`):
- meta: donor_id (or 0 for guest), amount_paise, currency, frequency (once|monthly|yearly), cause_slug, payment_gateway, payment_id, payment_status, pan_card_masked, 80g_certificate_id, receipt_pdf_url, is_anonymous
- REST `/wp-json/sb/v1/donation/draft` → returns order id + amount
- REST `/wp-json/sb/v1/donation/confirm` → after payment webhook
- REST `/wp-json/sb/v1/donation/receipt` → generates 80G PDF server-side
- Hook into LiteSpeed cache: bypass cache for donation pages
- Hook into FluentCRM: tag donor → add to "Donors" list, trigger email sequence (config in FluentCRM UI, not in code)

MEMBERSHIP (CPT `sb_member`):
- tiers: Sevak / Yajman / Trustee (configurable in admin)
- meta: tier, joined_on, valid_till, auto_renew, payment_subscription_id
- REST endpoints: join, upgrade, downgrade, cancel
- FluentCRM sync: tier-based tagging

PETITION SIGN (CPT `sb_petition` + `sb_signature`):
- `sb_petition`: title_hi, summary_hi, target_signatures, current_count, related_peetham_slug, status
- `sb_signature`: petition_id, signer_name, signer_email, signer_phone, signer_city, signer_country, signed_at, ip_hash, verification_token
- REST `/wp-json/sb/v1/petition/sign` → double opt-in via email, increments count only after confirmation
- REST `/wp-json/sb/v1/petition/{slug}/signers` → public count + paginated anonymized list

FRONTEND FLOWS
1. /donate
   - Step 1: cause selector (general / peetham / save-cow / events)
   - Step 2: amount (preset chips + custom)
   - Step 3: frequency
   - Step 4: identity (PAN for 80G, anonymous toggle)
   - Step 5: payment gateway (Razorpay preferred; abstract behind PaymentGatewayInterface so Stripe/PayU can plug in)
   - Step 6: success page → download receipt, share on WhatsApp
2. /membership — tier comparison cards, FAQ, CTA
3. /<petition-slug> — story block, signature meter, sign form, share buttons, related petitions

PAYMENT ABSTRACTION
- `PaymentGatewayInterface` with methods: `createOrder`, `verifyWebhook`, `refund`
- Razorpay implementation for v1
- MockGateway for local dev
- All webhook URLs: `/wp-json/sb/v1/webhook/<gateway>`
- HMAC signature verification mandatory; reject on mismatch

SECURITY
- Nonce + capability checks on every endpoint
- Rate-limit per IP + per user (transient-based)
- PAN stored only as masked; full PAN kept encrypted at rest (use `wp_encrypt` with key from `wp-config.php`)
- IP stored only as hash

OUTPUT
A. All PHP files with docblocks
B. `.http` / Postman collection for the 12 endpoints
C. README in `docs/` covering: how to test donations locally with Razorpay test keys, how to test membership, how to test petition sign
D. FluentCRM tag-mapping table (CSV)

DO NOT touch SeekAI AI plugin in this prompt — that's PROMPT 6.
```

---

## PROMPT 5 — The Events Calendar Custom Skin + Save Cow Microsite

Wire The Events Calendar (TEC) into the new design system and build the Save Cow microsite.

TEC CUSTOM SKIN
- Replace TEC default styles via override in `style.css` (`#tribe-events`, `.tribe-events-*` selectors) — keep selector specificity low.
- Custom event card component with: hero image, date chip, associated-peetham badge, language pill, donate/join CTA.
- Single event template: hero, full description, location map (OpenStreetMap embed via Leaflet — no Google Maps key needed), related events, share, "आयोजक से संपर्क करें" form (CF7 or Fluent Forms).
- Recurring event series view.
- SEO: JSON-LD Event on every single, sitemap chunk via Rank Math.

SAVE COW MICROSITE (template-save-cow.php redesign)
- Hero with mission statement
- Live counter: goshalas supported, cows rescued, volunteers active — pulled from `sb_save_cow_stats` CPT (one record, periodic update)
- Gaushala directory: filter by state, capacity, accepted donations
- Legal aid: PDF resources, contact form
- Awareness: campaign videos (YouTube embed), downloadable posters
- Volunteer signup
- Donation widget (calls donation flow with `cause_slug=save-cow`)

OUTPUT
A. `template-save-cow.php` redesign
B. `assets/js/save-cow/leaflet-map.js` + corresponding PHP loader
C. TEC override CSS section + custom shortcodes `[sb_event_card]` `[sb_event_countdown]`
D. README updates

DO NOT touch FluentCRM automations — those remain in plugin UI.
```

---

## PROMPT 6 — SeekAI AI Integration + Content Engine + PWA

Connect SeekAI AI plugin to the new content workflows and ship a PWA so devotees can read blog posts offline, get push notifications for new petitions, daily shloka, upcoming events.

SEEKAI AI USE CASES
1. Auto-summary on long blog posts (Hindi TL;DR + English TL;DR) stored in `sb_post_summary` post meta
2. Petition copy assist — admin clicks "Generate Hindi draft" → SeekAI → review queue
3. Shloka of the day auto-curation from a corpus
4. FAQ auto-draft from Peetham long-form content (admin review before publish)

CONSTRAINTS
- All AI output goes to `pending` review status — never auto-publishes.
- Hook into Rank Math: AI drafts include SEO meta suggestions (admin still approves).
- Log every AI call to `sb_ai_log` custom table for audit.

PWA
- manifest.webmanifest: Hindi name, English name, saffron theme color, 8 icon sizes + maskable.
- Service worker (Workbox):
    stale-while-revalidate for `/wp-content/themes/shivbodh-child/assets/`
    network-first with offline fallback for `/blog/*`, `/peetham/*`, `/save-cow/*` (cache last 20 each)
    network-only for `/donate/*`, `/wp-json/*`, `/wp-admin/*`
- Push via VAPID — store endpoint + keys per user via FluentCRM custom fields.
- Daily 5:30 AM push: "आज का श्लोक" + "आज के कार्यक्रम".
- New-petition push to opted-in members within 10 minutes of publish.
- Install prompt: after 2nd blog read in a week OR after first donation.
- Notification permission: graceful fallback to in-app bell.

OUTPUT
A. SeekAI integration plugin glue (small mu-plugin under `wp-content/mu-plugins/sb-seekai-bridge/`)
B. `wp-content/mu-plugins/sb-pwa/` with sw.js, manifest, offline templates
C. README: how to generate VAPID keys, how to test push on local
D. Audit log table SQL
```

---

## PROMPT 7 — Performance Hardening + SEO + Analytics (FINAL)

Ship the production hardening pass.

PERFORMANCE
- LiteSpeed Cache rules: bypass for `/donate/*`, `/membership/*`, `/dashboard/*`, `/wp-json/sb/*`
- Critical CSS extraction for above-the-fold (header + hero)
- Defer non-critical JS (TEC, Elementor animations, Leaflet)
- Image strategy: Optimole already installed — configure WebP/AVIF, responsive `srcset`
- Font strategy: `font-display: swap`, preload Tiro Devanagari Hindi

SEO (Rank Math already installed)
- Schema audit per page type: ReligiousOrganization (site-wide), FAQPage (Peethams, FAQs), Event (TEC), Article (blog), DonateAction (donation pages)
- Breadcrumbs design (override Rank Math default to match new design system)
- Internal-link graph audit (no orphan pages, every page has ≥ 3 inbound internal links)

ANALYTICS
- Google Site Kit already installed — verify GA4 + Search Console wired
- Add event tracking: petition_sign, donation_complete, membership_join, pwa_install
- Cookie consent (LiteSpeed-compatible, Hindi + English)

SECURITY
- Hardening checklist (already partially done by Hostinger): file permissions, disable file editing in wp-admin, limit login attempts (if not already), security headers via `.htaccess.live` template
- Backup verification: ensure `vps-incremental-backup.sh` covers `wp-content/uploads`, `wp-content/themes/shivbodh-child`, `wp-content/plugins`, database

OUTPUT
A. `.htaccess` snippet (compatible with `.htaccess.live`)
B. LiteSpeed Cache config export
C. `wp-config.php` recommended additions (encrypted PAN key, VAPID keys)
D. Final handover doc: `docs/HANDOVER.md` covering all 7 prompts, what shipped, what to monitor post-launch
```

---

## USAGE ORDER

1. PROMPT 1 — audit + IA (NO code changes)
2. PROMPT 2 — global design system + icons
3. PROMPT 3 — content blueprints
4. PROMPT 4 — donation / membership / petition flow
5. PROMPT 5 — TEC skin + Save Cow microsite
6. PROMPT 6 — SeekAI + PWA
7. PROMPT 7 — perf + SEO + analytics (FINAL)

Each prompt is self-contained — paste into your AI tool independently. Tell the AI: "Do not output anything until you have read all of the prompt above." Then review the file-tree-before-code step it produces before it writes code.
