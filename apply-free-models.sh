#!/usr/bin/env bash
# Apply b.ai free models to Claude CLI and Kilo
# Usage: ./apply-free-models.sh

set -euo pipefail

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

WORKSPACE="/Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech"

echo -e "${BLUE}🔧 Applying b.ai free model configuration${NC}"
echo

# Step 1: Test primary endpoint
echo -e "${BLUE}1. Testing b.ai API endpoint...${NC}"
echo "   Endpoint: https://api.b.ai/v1"
echo "   Key: sk-canmbr2...mejhk"
echo "   Free models:"
echo "     • DeepSeek V4 Flash (text)"
echo "     • DeepSeek V4 Flash Vision (image+voice)"
echo "     • DeepSeek V4 Pro (paid only)"
echo

B_ENDPOINT="https://api.b.ai/v1"
# Alternative endpoints to try:
# B_ENDPOINT_ALT1="https://api.bairouter.io/v1"
# B_ENDPOINT_ALT2="https://api.baigateway.io/v1"
# B_ENDPOINT_ALT3="https://chat.b.ai/chat"
# B_ENDPOINT_ALT4="https://chat.bankofai.io/chat"

echo -e "${YELLOW}2. Applying to Kilo config...${NC}"
if [ -f "$WORKSPACE/kilo-config-full.jsonc" ]; then
    cp "$WORKSPACE/kilo-config-full.jsonc" ~/.config/kilo/kilo.jsonc
    echo -e "   ${GREEN}✅ Copied to ~/.config/kilo/kilo.jsonc${NC}"
    echo "   Default model: b/deepseek-v4-flash"
    echo "   Subagent model: b/deepseek-v4-flash"
else
    echo -e "   ${RED}❌ kilo-config-full.jsonc not found${NC}"
fi
echo

echo -e "${YELLOW}3. Applying to Claude CLI settings...${NC}"
if [ -f "$WORKSPACE/claude-settings-full-free.json" ]; then
    cp "$WORKSPACE/claude-settings-full-free.json" ~/.claude/settings.json
    echo -e "   ${GREEN}✅ Copied to ~/.claude/settings.json${NC}"
    echo "   Available models:"
    echo "     claude --model deepseek/deepseek-v4-flash"
    echo "     claude --model deepseek/deepseek-v4-flash-vision-exp"
    echo "     claude --model deepseek/deepseek-v4-pro"
else
    echo -e "   ${RED}❌ claude-settings-full-free.json not found${NC}"
fi
echo

echo -e "${BLUE}4. Testing API connectivity (if network available)...${NC}"
RESPONSE=$(curl -sS --max-time 10 "$B_ENDPOINT/models" \
    -H "Authorization: Bearer sk-canmbr2jyis9fkq46k8dec0c037mejhk" \
    -H "Content-Type: application/json" 2>&1) || true

if echo "$RESPONSE" | grep -qE "(DeepSeek|deepseek|model)"; then
    echo -e "   ${GREEN}✅ API responding at $B_ENDPOINT${NC}"
else
    echo -e "   ${YELLOW}⚠️  Cannot reach $B_ENDPOINT from here${NC}"
    echo "   Test manually:"
    echo "   curl -H \"Authorization: Bearer sk-canmbr2jyis9fkq46k8dec0c037mejhk\" \\"
    echo "        -H \"Content-Type: application/json\" \\"
    echo "        \"$B_ENDPOINT/models\""
    echo
    echo -e "   ${YELLOW}Alternative endpoints to try if primary fails:${NC}"
    echo "   • https://api.bairouter.io/v1"
    echo "   • https://api.baigateway.io/v1"
    echo "   • https://chat.b.ai/chat"
    echo "   • https://chat.bankofai.io/chat"
fi
echo

echo -e "${GREEN}✅ Configuration applied!${NC}"
echo
echo -e "${BLUE}To verify in Kilo:${NC}"
echo "  kilo --model b/deepseek-v4-flash"
echo
echo -e "${BLUE}To verify in Claude Code:${NC}"
echo "  claude --model deepseek/deepseek-v4-flash"
echo
echo -e "${BLUE}Or use the web UI:${NC}"
echo "  Open: https://chat.b.ai/chat"
echo "  Or: https://chat.bankofai.io/chat"
