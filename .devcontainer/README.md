# GitHub Codespaces development

This repository includes a PHP 8.2 Codespaces configuration in `.devcontainer/`.

## Start

1. Open **Code → Create codespace on main**.
2. In the Codespace terminal, configure a local WordPress database if needed.
3. Run:

```bash
bash .devcontainer/start-wordpress.sh
```

The development server is exposed on port `8080`.

## Important

- The production `wp-config.php` is never committed.
- Do not copy production database credentials or uploads into the Codespace.
- Use Codespaces Secrets or environment variables for local integrations.
- The Codespace is for development; deployment to the CyberPanel VPS must be performed separately and backed up first.
