# Development guide

This repository contains the Sampreshan.tech WordPress document root and the optional Framer sync utility.

## Repository administration

Repository owners and maintainers should follow [`docs/REPOSITORY-ADMINISTRATION.md`](docs/REPOSITORY-ADMINISTRATION.md) for:

- GitHub repository-level settings
- About, website and topics configuration
- Discussions and Projects
- Collaborator roles and least-privilege access
- `main` branch protection
- Non-collaborator read-only verification
- Security and access review cadence

That guide documents the intended policy; only a repository administrator can apply GitHub settings in the web interface.

## Static preview

Open `demo.html` directly for the static landing-page preview.

## WordPress development

1. Use a local PHP + MariaDB/WordPress environment.
2. Serve `public_html/` as the document root.
3. Copy `public_html/wp-config-sample.php` to a local `wp-config.php` and set local database credentials.
4. Import one of the SQL dumps only when a local database snapshot is needed.
5. Keep uploads, caches, archives, and credentials out of Git.

## Framer sync

```bash
cp .env.example .env
cd framer-sync
npm install
npm run framer:status
```

The `.env` file must contain the Framer credentials and is ignored by Git.
Use `npm run framer:pull` to refresh the local snapshot; publish/deploy commands
should only be used when explicitly intended.

## Deployment reference

See [`DEPLOYMENT-RUNBOOK.md`](DEPLOYMENT-RUNBOOK.md) for the repeatable release
and production-deployment checklist. Credentials remain outside the repository.
