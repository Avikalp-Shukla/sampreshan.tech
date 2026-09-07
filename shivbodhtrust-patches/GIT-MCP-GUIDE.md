# Git MCP Connector Setup

## Overview
Sets up Model Context Protocol (MCP) connectors for Git and GitHub operations on Avikalp-Shukla repos.

## Prerequisites

### 1. SSH Keys (already present)
```bash
ls ~/.ssh/id_ed25519.pub
# Key exists — already configured for GitHub
```

### 2. Test GitHub SSH connection
```bash
ssh -T git@github.com
# Expected: Hi Avikalp-Shukla! You've successfully authenticated...
```

## MCP Servers

Two servers are configured in `mcp-config.json`:

### Git MCP Server (`@modelcontextprotocol/server-git`)
- **Repo path:** `/Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech`
- Uses local SSH keys
- Operations: `git log`, `git diff`, `git commit`, `git status`, etc.

### GitHub MCP Server (`@modelcontextprotocol/server-github`)
- **Requires GitHub Personal Access Token**
- Operations: file create/update, issues, PRs, branches

## Setup Steps

### Step 1: Install MCP servers
```bash
npm install -g @modelcontextprotocol/server-git
npm install -g @modelcontextprotocol/server-github
```

### Step 2: Get GitHub Personal Access Token (if you don't have one)
```bash
# Visit: https://github.com/settings/tokens
# Generate classic token with scopes:
#   repo (full control of private repos)
#   read:org (read org data)
#   read:user (read user data)
```

### Step 3: Set environment variable
```bash
# Add to your shell profile (~/.bashrc, ~/.zshrc, etc.)
export GITHUB_TOKEN=ghp_your_token_here

# Or use Kilo's env management
```

### Step 4: Link configs
```bash
# Claude Desktop / Claude Code
mkdir -p ~/.claude
ln -sf /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/mcp-config.json \
       ~/.claude/mcp-settings.json

# Or Kilo config (already in kilo.jsonc)
```

### Step 5: Verify in Kilo
The `kilo.jsonc` already has github MCP enabled:
```jsonc
"mcp": {
    "github": {
        "enabled": true
    }
}
```

## Usage Examples

### Git operations (local repo)
After MCP is loaded:
```bash
# In Claude Code or Kilo with MCP tools:
claude mcp list  # shows connected MCP servers
```

### GitHub operations
```bash
# Create issue
create_issue.sh "Bug: header typo" "Fixed /aboout-us/ → /about-us/"

# Create file
create_file.sh "docs/new-feature.md" "# Content"

# List PRs
list_prs.sh
```

## Troubleshooting

| Problem | Fix |
|--------|-----|
| "Permission denied (publickey)" | Add SSH key: `ssh-add ~/.ssh/id_ed25519` |
| "Server not found" | Install: `npm install -g @modelcontextprotocol/server-git` |
| "Token invalid" | Regenerate PAT at github.com/settings/tokens |

## Files created
- `mcp-config.json` — MCP server configuration
- `setup-git-mcp.sh` — automated setup script
