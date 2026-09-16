# Sampreshan Deployment Runbook

This is the master reference for releasing `sampreshan.tech` safely.

## Verified project facts

- Git repository: `Avikalp-Shukla/sampreshan.tech`
- Default release branch: `main`
- Local project root: `/Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech`
- WordPress document root in this snapshot: `public_html/`
- Active child theme source: `buddyboss-theme-child/`
- Web stack: WordPress, BuddyBoss, Elementor, LiteSpeed, MariaDB
- GitHub Pages demo URL: `https://avikalp-shukla.github.io/sampreshan.tech/`
- GitHub Pages workflow: `.github/workflows/pages.yml`
- Current theme release: `1.6.0`

## Local validation

Run from the repository root:

```bash
find buddyboss-theme-child -type f -name '*.php' -not -path '*/vendor/*' -print0 \
  | xargs -0 -n1 php -l
git diff --check
```

For a WordPress browser check, serve `public_html/` with a local PHP/WordPress
environment and verify the homepage, login, dashboard, profile, petition, and
mobile layout. Clear LiteSpeed and browser caches after theme deployment.

## Git release flow

```bash
git status
git diff --check
git add buddyboss-theme-child
git commit -m "Release Sampreshan theme X.Y.Z"
git push origin main
```

The Pages workflow is a static demo deployment. Theme-only pushes do not match
its path filter, so trigger it manually when the demo must be refreshed:

```bash
gh workflow run pages.yml --ref main
gh run list --workflow pages.yml --limit 1
```

## WordPress production deployment

Production deployment is **not currently automated in this repository**. The
local `public_html/` snapshot must not be copied to production blindly because
it contains WordPress core, plugins, uploads, caches, and database-specific
files. Deploy the child theme only after confirming the live target.

Required non-secret deployment details:

- Production URL and staging URL, if available
- Hosting provider and deployment method: SFTP, SSH/rsync, cPanel, or hosting API
- Remote WordPress document root
- Remote child-theme path
- Whether a staging backup and rollback snapshot are required
- Cache purge method and post-deploy smoke-test URLs

Required secret environment variables, stored only in a local secret manager or
CI/CD secret store:

```text
SAMPRESHAN_SFTP_HOST
SAMPRESHAN_SFTP_PORT
SAMPRESHAN_SFTP_USER
SAMPRESHAN_SFTP_KEY_FILE
SAMPRESHAN_REMOTE_THEME_PATH
SAMPRESHAN_PRODUCTION_URL
```

Never commit passwords, private keys, WordPress salts, GitHub PATs, API keys, or
database credentials. Rotate any credential that has appeared in chat, scripts,
screenshots, logs, or committed history.

## Production checklist

1. Confirm the target and take a verified backup.
2. Run PHP syntax and diff checks locally.
3. Deploy only the intended child-theme files.
4. Purge WordPress/LiteSpeed/CDN caches.
5. Check homepage hero/art, login, dashboard, profile, single petition, and mobile layout.
6. Check browser console and PHP error logs.
7. Record the commit SHA, deployment time, target, and rollback location.

## Current blocker

No production SFTP/SSH/hosting target is configured in this repository. GitHub
Pages is live for the static demo, but it does not publish the WordPress site.
Production can be automated once the non-secret target details above are known
and credentials are supplied through environment variables or CI secrets.
