# GitHub Codespaces development

This repository includes a PHP 8.2 + Node.js 20 Codespaces configuration in `.devcontainer/`.

## Start a Codespace

1. Open [Create a Codespace for this repository](https://github.com/codespaces/new?hide_repo_select=true&ref=main&repo=1355927108), or open **Code → Create codespace on main** on GitHub.
2. Wait for the container to build. Dependencies for the Framer utility are installed automatically.
3. Configure a local WordPress database when you need the full application:

```bash
cp public_html/wp-config-sample.php public_html/wp-config.php
# Edit public_html/wp-config.php with local database credentials.
```

4. Start the local server:

```bash
bash .devcontainer/start-wordpress.sh
```

The development server is exposed on port `8080`; Codespaces will offer an **Open in Browser** link.

## Framer sync utility

```bash
cp .env.example .env
npm run framer:status --prefix framer-sync
```

Keep API keys, database credentials, production uploads, and backups out of Git. Use Codespaces Secrets or environment variables for integrations.

## Important

The Codespace is for development only. Production deployment to the CyberPanel VPS must be performed separately and backed up first.
