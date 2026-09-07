#!/usr/bin/env bash
# Automated Git MCP connector setup
# Usage: ./setup-git-mcp.sh

set -euo pipefail

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Setting up Git MCP connector for Avikalp-Shukla repos${NC}"
echo

# Step 1: Check SSH key
echo -e "${BLUE}1. Checking SSH key...${NC}"
if [ -f ~/.ssh/id_ed25519.pub ]; then
    echo -e "  ${GREEN}✅ SSH key exists${NC}"
    echo "  Public key: $(cat ~/.ssh/id_ed25519.pub)"
else
    echo -e "  ${RED}❌ No SSH key found. Generating...${NC}"
    ssh-keygen -t ed25519 -C "shivbodhtrust@gmail.com" -f ~/.ssh/id_ed25519 -N ""
    echo -e "  ${YELLOW}⚠️ Copy public key to GitHub:${NC}"
    echo "  cat ~/.ssh/id_ed25519.pub | pbcopy"
    echo "  Then paste in: https://github.com/settings/keys"
    exit 0
fi
echo

# Step 2: Test GitHub SSH
echo -e "${BLUE}2. Testing GitHub SSH connection...${NC}"
if ssh -T git@github.com -o StrictHostKeyChecking=no 2>&1 | grep -q "successfully authenticated"; then
    echo -e "  ${GREEN}✅ GitHub SSH connection successful${NC}"
else
    echo -e "  ${YELLOW}⚠️  SSH connection test inconclusive (expected if first time)${NC}"
fi
echo

# Step 3: Install MCP servers
echo -e "${BLUE}3. Installing MCP servers...${NC}"
if npm ls -g @modelcontextprotocol/server-git >/dev/null 2>&1; then
    echo -e "  ${GREEN}✅ Git MCP server already installed${NC}"
else
    npm install -g @modelcontextprotocol/server-git
    echo -e "  ${GREEN}✅ Git MCP server installed${NC}"
fi

if npm ls -g @modelcontextprotocol/server-github >/dev/null 2>&1; then
    echo -e "  ${GREEN}✅ GitHub MCP server already installed${NC}"
else
    npm install -g @modelcontextprotocol/server-github
    echo -e "  ${GREEN}✅ GitHub MCP server installed${NC}"
fi
echo

# Step 4: Check GitHub PAT
echo -e "${BLUE}4. Checking GitHub token...${NC}"
if [ -n "${GITHUB_TOKEN:-}" ]; then
    echo -e "  ${GREEN}✅ GITHUB_TOKEN environment variable is set${NC}"
else
    echo -e "  ${YELLOW}⚠️  GITHUB_TOKEN not set${NC}"
    echo "  Get a token from: https://github.com/settings/tokens"
    echo "  Then run: export GITHUB_TOKEN=ghp_..."
    echo "  (Scopes needed: repo, read:org, read:user)"
fi
echo

# Step 5: Verify config file
echo -e "${BLUE}5. Verifying MCP config...${NC}"
CONFIG_FILE="/Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/mcp-config.json"
if [ -f "$CONFIG_FILE" ]; then
    echo -e "  ${GREEN}✅ mcp-config.json exists${NC}"
    echo "  Repo path: $(grep -o '"GIT_REPO_PATH": "[^"]*"' "$CONFIG_FILE" | cut -d'"' -f4)"
else
    echo -e "  ${RED}❌ mcp-config.json not found${NC}"
    exit 1
fi
echo

# Step 6: Check kilo.jsonc
echo -e "${BLUE}6. Checking Kilo MCP config...${NC}"
if grep -q '"github".*"enabled": true' /Users/avikalpshukla/.config/kilo/kilo.jsonc 2>/dev/null; then
    echo -e "  ${GREEN}✅ GitHub MCP enabled in kilo.jsonc${NC}"
else
    echo -e "  ${YELLOW}⚠️  GitHub MCP not found in kilo.jsonc${NC}"
fi
echo

echo -e "${GREEN}✅ Setup complete!${NC}"
echo
echo -e "${BLUE}Next steps:${NC}"
echo "1. Set GITHUB_TOKEN: export GITHUB_TOKEN=ghp_..."
echo "2. Restart Claude CLI / Kilo"
echo "3. Test: claude mcp list"
echo
echo -e "${BLUE}Useful commands:${NC}"
echo "  git log --oneline -5          # Recent commits"
echo "  git status                    # Working tree status"
echo "  gh pr list                    # Open PRs on GitHub"
