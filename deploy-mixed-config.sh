#!/usr/bin/env bash
# Deploy Experiential Labs + b.ai configs
set -euo pipefail

GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

WORKSPACE="/Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech"

echo -e "${BLUE}🚀 Deploying mixed provider config (Experiential Labs + b.ai)${NC}"

TS=$(date +%Y%m%d%H%M%S)

# Claude CLI (backup + deploy)
[ -f ~/.claude/settings.json ] && cp ~/.claude/settings.json ~/.claude/settings.json.bak-$TS
cp "$WORKSPACE/claude-settings-mixed.json" ~/.claude/settings.json
echo -e "${GREEN}✅ Claude CLI: ~/.claude/settings.json — default model: gpt-6-astra${NC}"

# Kilo (backup + deploy)
[ -f ~/.config/kilo/kilo.jsonc ] && cp ~/.config/kilo/kilo.jsonc ~/.config/kilo/kilo.jsonc.bak-$TS
cp "$WORKSPACE/kilo-config-mixed.jsonc" ~/.config/kilo/kilo.jsonc
echo -e "${GREEN}✅ Kilo: ~/.config/kilo/kilo.jsonc — default model: explabs/gpt-6-astra${NC}"

# OpenCode (merge providers, preserve existing like omniroute + mcp)
if [ -f "$WORKSPACE/opencode-mixed.json" ]; then
    [ -f ~/.config/opencode/opencode.json ] && cp ~/.config/opencode/opencode.json ~/.config/opencode/opencode.json.bak-$TS
    python3 - "$WORKSPACE/opencode-mixed.json" << 'PYEOF'
import json, sys
gpath = "/Users/avikalpshukla/.config/opencode/opencode.json"
m = json.load(open(sys.argv[1]))
g = json.load(open(gpath))
g.setdefault("provider", {}).update(m["provider"])
g["$schema"] = "https://opencode.ai/config.json"
json.dump(g, open(gpath, "w"), indent=2)
print("providers:", ", ".join(sorted(g["provider"].keys())))
PYEOF
    echo -e "${GREEN}✅ OpenCode: ~/.config/opencode/opencode.json — merged explabs + b providers${NC}"
fi

echo
echo -e "${BLUE}Usage:${NC}"
echo "  claude --model gpt-6-astra                              # Experiential Labs primary"
echo "  claude --model deepseek/deepseek-v4-flash               # b.ai primary (api.b.ai)"
echo "  claude --model deepseek-v4-free-router                  # b.ai fallback (api.bairouter.io)"
echo "  claude --model deepseek-v4-free-gateway                 # b.ai fallback (api.baigateway.io)"
echo "  kilo --model explabs/gpt-6-astra"
echo "  kilo --model b/deepseek-v4-flash"
echo "  kilo --model b-router/deepseek-v4-flash"
echo "  kilo --model b-gateway/deepseek-v4-flash"
echo
echo -e "${BLUE}Web chat (browser, not API):${NC}"
echo "  https://chat.b.ai/chat"
echo "  https://chat.bankofai.io/chat"
echo
echo -e "${BLUE}Test the model:${NC}"
echo "  ./test-explabs-model.sh"
