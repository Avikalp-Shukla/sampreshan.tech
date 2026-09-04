# Framer ↔ sampreshan.tech Sync

Permanent connection between the [Framer](https://framer.com) project (where the design lives) and the live WordPress site. Push from Framer → render in WordPress, on demand, by a single CLI command.

## What this does

- Pulls the Framer project via the **Framer Server API**
- Caches a JSON snapshot of the project locally (`output/project.json`)
- Registers a WordPress embedder (`sp_framer_render()`) that renders the Framer design as the full homepage UI — either by **iframe** (using the Framer hostname) or **bridge** mode (lazy-loading the Framer runtime)
- Optionally **publishes** a new preview and **promotes to production**

## Layout

```
sampreshan-tech/
├── .env                            ← gitignored, holds FRAMER_API_KEY + FRAMER_PROJECT_URL
├── framer-sync/                    ← this directory
│   ├── package.json
│   ├── sync.js                     ← CLI: status, pull, publish, deploy, diff
│   ├── output/project.json         ← written by `npm run framer:pull`
│   └── cache/last-publish.json     ← written on every pull
└── buddyboss-theme-child/
    ├── inc/framer-embed.php        ← WordPress side: sp_framer_render(), shortcode
    ├── assets/js/framer-bridge.js  ← bridge-mode loader
    └── template-framer-front.php   ← optional full-site override
```

## One-time setup

1. **Generate a Framer API key** in Framer: `Project → Site Settings → General → API Keys`.
2. **Add the project URL** — copy the URL from your Framer project page (it looks like `https://framer.com/projects/<id>`).
3. **Fill in `.env`** at the repo root (already gitignored):
   ```env
   FRAMER_API_KEY=fr_xxxxxxxxxxxxxxxxxxxxx
   FRAMER_PROJECT_URL=https://framer.com/projects/<your-id>
   FRAMER_DEPLOY_TARGET=preview
   ```
4. **Install dependencies**:
   ```bash
   cd framer-sync
   bash install.sh        # or: npm install
   ```
   > If `npm install` fails with `ENOTFOUND registry.npmjs.org`, you're in a sandboxed environment that blocks DNS. Run the install outside the sandbox (a normal terminal will work).

## Daily use

```bash
npm run framer:status    # verify connection + show project name
npm run framer:diff      # see what changed since last publish
npm run framer:pull      # refresh the local cache (output/project.json)
npm run framer:publish   # publish a new preview (Framer side)
npm run framer:deploy    # publish + promote to production
```

After `framer:pull`, WordPress automatically picks up the new snapshot (no theme update needed). The homepage uses the Framer design when the snapshot exists, or falls back to the hand-built `front-page.php` otherwise.

## Render modes (WordPress side)

| Mode | When | Trade-offs |
|------|------|------------|
| **iframe** | A Framer hostname is in the snapshot (after publish) | Fastest, no JS. Framer's CDN serves everything. SEO depends on the Framer project settings. |
| **bridge** | No hostname yet (draft only) | Loads Framer's runtime into a div. Lazy-mounted via `IntersectionObserver`. ~30 KB JS, no extra request on first paint. |
| **hybrid** | Combine: iframe + WordPress menus/footer | Render `[sampreshan_framer]` for the body, keep your header/footer from `front-page.php`. |

To force a mode in any template:
```php
<?php sp_framer_render( array( 'mode' => 'iframe', 'title' => 'SampreShan' ) ); ?>
```

Or via shortcode in any page/post:
```
[sampreshan_framer title="SampreShan" height="100vh"]
```

## Recommended workflow

1. Design in Framer (live preview, designer-friendly).
2. `npm run framer:publish` — get a preview URL.
3. Test the preview on the Framer hostname.
4. When ready for production: `npm run framer:deploy`.
5. The WordPress homepage automatically serves the latest deployment.

## Security notes

- **`.env` is gitignored** and chmod 600. The Framer API key only authenticates pull/publish — it does not grant write access to the Framer project itself.
- The embedder runs inside an `<iframe sandbox>` with `allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox allow-downloads allow-modals` — restrictive enough to prevent most cross-origin abuse while still letting the Framer app function.
- The Framer bridge script loads from `framer.com` over HTTPS; it cannot read your WordPress cookies or session.

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| `❌ FRAMER_API_KEY is not set in .env` | Fill in `.env` at the repo root. |
| `❌ Failed to connect to Framer: 401` | API key is wrong or revoked. Generate a new one. |
| `❌ Failed to connect to Framer: 404` | `FRAMER_PROJECT_URL` is wrong — open your Framer project, copy the URL bar. |
| WordPress still shows old design | Run `npm run framer:pull` to refresh the cache, then hard-reload. |
| Iframe shows a blank page | The Framer deployment may have failed. Check the Framer dashboard, then re-run `npm run framer:publish`. |

## See also

- [Framer Server API docs](https://www.framer.com/docs/server-api/)
- [server-api-examples on GitHub](https://github.com/framer/server-api-examples)
- BuddyBoss child theme → `inc/framer-embed.php` for the WordPress-side API.
