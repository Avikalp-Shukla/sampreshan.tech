# AI WordPress Workspace Access

This workspace contains local development access for the ShivBodh WordPress projects.

Configured local roots:

- `Avikalp-Shukla_varanasipooja.com`
- `sampreshan-tech`
- `shivbodhtrust.org`
- `varanasi-pooja-chat`
- `varanasigangaaarti.com`
- `varanasipooja.com`

The active workspace MCP file is maintained at the parent workspace level:
`/Users/avikalpshukla/ShivBodh-Projects/.mcp.json`.

The GitHub MCP uses `GITHUB_TOKEN` from the environment. WordPress and SFTP
credentials are per-site and must be supplied through environment variables or a
secret manager, never committed here.

See `wordpress-sites.env.example` for variable names only.
