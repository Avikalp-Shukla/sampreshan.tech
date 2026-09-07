# SampreShan Icon System

Permanent, resolution-independent 3D icon layer for the entire sampreshan.tech site. One file, every breakpoint, every device — no rasterization, no retina duplicates, no broken paths.

## Why this exists

- **One source of truth.** All icons live in `assets/icons/3d/`. Templates use a single helper; the CSS handles all sizing.
- **Vector = responsive forever.** SVG icons scale crisply from a 320 px phone to a 4K display without quality loss or extra HTTP requests.
- **Single HTTP request per page.** Icons are inlined into the HTML at render time. No 52 separate `.svg` requests.
- **Accessibility built in.** Every icon has `role="img"`, an `aria-label`, and respects `prefers-reduced-motion`.
- **Dark mode + print + high-DPI aware.**

## Quick start

```php
// Inside any template
<?php sp_icon_e( 'home', 'sp-icon--md sp-icon--saffron' ); ?>

// Or return as string
$icon = sp_icon( 'dharma', 'sp-icon--xl sp-icon--gold sp-icon--float', 'Dharma mark' );
```

The first arg is the icon name (file in `assets/icons/3d/` without `.svg`). The second is a space-separated class list. The optional third arg overrides the aria-label.

## Class reference

### Sizes (responsive at every breakpoint)

| Class | Phone (≤479) | Phablet (480-639) | Tablet (640-767) | Tablet L (768-1023) | Desktop (1024-1279) | Large (1280-1535) | 4K (1536+) |
|-------|--------------|-------------------|------------------|---------------------|----------------------|-------------------|------------|
| `sp-icon--xxs` | 12 px | 12 px | 12 px | 12 px | 12 px | 12 px | 12 px |
| `sp-icon--xs` | 14 px | 14 px | 14 px | 14 px | 14 px | 14 px | 14 px |
| `sp-icon--sm` | 16 px | 18 px | 18 px | 18 px | 18 px | 20 px | 22 px |
| `sp-icon--md` | 20 px | 22 px | 24 px | 24 px | 24 px | 26 px | 28 px |
| `sp-icon--lg` | 28 px | 32 px | 36 px | 40 px | 44 px | 48 px | 52 px |
| `sp-icon--xl` | 40 px | 48 px | 56 px | 60 px | 64 px | 72 px | 80 px |
| `sp-icon--2xl` | 56 px | 64 px | 72 px | 84 px | 96 px | 104 px | 112 px |
| `sp-icon--3xl` | 80 px | 96 px | 110 px | 128 px | 144 px | 160 px | 176 px |

### Colors

`sp-icon--saffron` · `sp-icon--saffron-soft` · `sp-icon--blue` · `sp-icon--blue-soft` · `sp-icon--gold` · `sp-icon--white` · `sp-icon--muted` · `sp-icon--danger` · `sp-icon--success` · `sp-icon--warning` · `sp-icon--info`

Or just set `color:` on the parent — icons inherit `currentColor`.

### Animations

`sp-icon--float` (gentle up-down) · `sp-icon--pulse` (scale) · `sp-icon--bounce` (small hop) · `sp-icon--spin` (rotate, for loaders) · `sp-icon--glow` (drop-shadow pulse)

All animations auto-disable when `prefers-reduced-motion: reduce` is set.

## Available icons

| Category | Icons |
|----------|-------|
| **Brand / Mission** | `dharma`, `lotus`, `om`, `diya` |
| **Dharmic Causes (premium 3D)** | `mandir` (Temple Preservation), `pothi` (Cultural Heritage), `vedagranth` (Religious Education + Sanskrit & Vedic Studies), `peepal` (Environment), `seva` (Community Welfare), `gaumata` (Gau Seva), `tirtha` (Pilgrimage & Tirtha) |
| **Sacred Actions** | `shankh` (raise-your-voice / shankhnaad), `mobile` (OTP verification) |
| **Navigation** | `home`, `feed`, `network`, `petition`, `account`, `group`, `menu`, `globe` |
| **Truth Social tools** | `heart`, `comment`, `reply`, `repost`, `chart`, `share`, `send`, `bookmark`, `dots`, `plus`, `pen`, `bell`, `search`, `settings`, `eye`, `flag` |
| **Change.org tools** | `megaphone`, `target`, `trending`, `hand`, `shield`, `link`, `spark`, `download`, `upload`, `logout` |
| **Profile / Auth** | `profile`, `verified`, `mail`, `lock` |
| **Content** | `document`, `image`, `video` |
| **Status** | `check`, `close`, `info`, `warning`, `star` |
| **Time / Place** | `calendar`, `location` |

## One topic = one symbol (house rule)

A symbol repeats **only** when the *same* topic, menu, or point is published
again — a cause card, a category hero, a petition badge, a section heading
for that same cause. Never reuse a topic's symbol for a different topic.

`sp_cause_icon( $slug_or_name )` in `inc/icon-helper.php` is the single
source of truth: cause term slug/name → icon name. Templates must call it
instead of hardcoding cause icons, so the mapping can never drift between
the homepage grid, category heroes, petition cards, and single-petition pages.

All files live in `assets/icons/3d/`. Adding a new icon: drop the SVG there, give it a unique gradient `id` (since they're inlined together, ids must not clash).

## How it works under the hood

1. **PHP helper (`inc/icon-helper.php`)** — `sp_icon()` reads the SVG file, injects accessibility attributes, and returns inline markup. No `<img>` tag, no separate HTTP request.
2. **CSS (`assets/css/icon-system.css`)** — `.sp-icon` is a 1em square that flexes to its size. Every size modifier is a CSS custom property (`--sp-icon-size`) that gets remapped at each of 7 breakpoints.
3. **Browser renders** — the inline SVG is recolored by `currentColor`, gets a soft drop-shadow, and animates if requested.

## Icons8 3D Fluency imports (responsive PNG)

`assets/icons/icons8/` holds 45 Icons8 3D Fluency icons (transparent PNG) at
3 sizes: `-48` (mobile), `-96` (tablet), `-192` (desktop/retina) — 135 files.

```php
<?php sp_icon_auto( 'trending', 'sp-icon--md', '' ); ?>
```

`sp_icon_auto()` = same signature as `sp_icon_e()`. It serves the PNG
`srcset` when imported, else falls back to our custom SVG. Brand icons
(`om`, `lotus`, `diya`, `dharma`, `petition`, `network`, `video`, `flag`,
`image`, `warning`, `logout`, `share`, `users`) stay 100% custom SVG.

**License:** Icons8 free plan — PNG use requires attribution, credited in
the site footer ("3D icons by Icons8" linking https://icons8.com).

## Favicon

`assets/brand/favicon.svg` is registered in `wp_head` as a vector favicon (supported by all modern browsers). It uses the same dharma-lotus mark as the hero. `favicon-32.svg` is registered as a PNG fallback for older browsers.

## Adding a new icon

1. Create `assets/icons/3d/my-icon.svg` using the existing templates (64x64 viewBox, gradients with unique IDs, soft drop-shadow filter).
2. Use it: `<?php sp_icon_e( 'my-icon', 'sp-icon--md sp-icon--saffron' ); ?>`
3. Done — it's instantly responsive at every breakpoint, every device, every DPI.

## Performance budget

- **0** extra HTTP requests per icon (inlined).
- **~1-3 KB** per icon SVG, gzipped.
- **1** CSS file (`icon-system.css`, ~7 KB) handles all sizes/colors/animations.
- **Cache-friendly**: `SAMPRESHAN_CHILD_VERSION` bumps invalidate everything at once.

## Migration notes

Old pattern (removed):
```html
<img class="icon-3d icon-3d--sm icon-3d--saffron" src="/wp-content/themes/buddyboss-theme-child/assets/icons/3d/home.svg" alt="" />
```

New pattern:
```php
<?php sp_icon_e( 'home', 'sp-icon--sm sp-icon--saffron', 'Home' ); ?>
```

The five flat icons in `assets/icons/` (home, feed, network, petition, profile) are now removed — all callers use the 3D versions.
