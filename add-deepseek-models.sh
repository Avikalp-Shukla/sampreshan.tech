#!/usr/bin/env bash
# Add DeepSeek free models to Claude CLI gateway config
# This script updates the gateway-models.json cache to include DeepSeek models

set -euo pipefail

CONFIG_FILE="$HOME/.claude/cache/gateway-models.json"
BAI_API_KEY="sk-canmbr2jyis9fkq46k8dec0c037mejhk"  # Your b.ai API key

echo "🔧 Updating Claude CLI model gateway config..."

# Create backup
cp "$CONFIG_FILE" "${CONFIG_FILE}.bak-$(date +%Y%m%d%H%M%S)"
echo "✅ Backup created: ${CONFIG_FILE}.bak-*"

# Use python to inject DeepSeek models into the JSON
python3 << 'PYEOF'
import json
import os

config_file = os.path.expanduser("~/.claude/cache/gateway-models.json")

with open(config_file, 'r') as f:
    config = json.load(f)

# DeepSeek free-tier models to add (from your pricing list)
# All have $0 input, $0 output cost = truly free
deepseek_models = [
    # Free models (all $0)
    {"id": "deepseek/deepseek-v4-flash", "name": "DeepSeek V4 Flash", "inputCost": 0, "outputCost": 0, "contextWindow": 64000, "isFree": True},
    {"id": "deepseek/deepseek-v4-flash-vision-exp", "name": "DeepSeek V4 Flash Vision", "inputCost": 0, "outputCost": 0, "contextWindow": 64000, "isFree": True},
    
    # Paid tier (for reference)
    {"id": "deepseek/deepseek-v4-pro", "name": "DeepSeek V4 Pro", "inputCost": 1.32, "outputCost": 1.32, "contextWindow": 64000, "isFree": False},
]

existing_ids = {m['id'] for m in config['models']}
added = []
for model in deepseek_models:
    if model['id'] not in existing_ids:
        config['models'].append(model)
        added.append(model['id'])

with open(config_file, 'w') as f:
    json.dump(config, f, indent=2)

print(f"✅ Added {len(added)} DeepSeek models: {added}")
print(f"   Total models now: {len(config['models'])}")
PYEOF

echo "🎉 Done! DeepSeek models added to Claude CLI gateway."
echo "Available now: deepseek/deepseek-v4-flash"
echo ""
echo "Usage examples:"
echo "  claude --model deepseek/deepseek-v4-flash"
echo "  claude --model deepseek/deepseek-v4-flash-vision-exp"
