# b.ai Free Models — Configuration Guide

## Endpoint: `https://api.b.ai/v1`
API Key: `sk-canmbr2jyis9fkq46k8dec0c037mejhk`

### Alternative endpoints (if primary fails)
```
https://api.bairouter.io/v1
https://api.baigateway.io/v1
https://chat.b.ai/chat
https://chat.bankofai.io/chat
```

## Free Tier Models (all $0 input + $0 output)

| Model ID | Name | Vision | Price |
|----------|------|--------|-------|
| `deepseek/deepseek-v4-flash` | DeepSeek V4 Flash | No | Free |
| `deepseek/deepseek-v4-flash-vision-exp` | DeepSeek V4 Flash Vision | Yes | Free |

### Paid model
| `deepseek/deepseek-v4-pro` | DeepSeek V4 Pro | No | $1.32/1M tokens |

## Files created
- `claude-settings-full-free.json` → `~/.claude/settings.json`
- `kilo-config-full.jsonc` → `~/.config/kilo/kilo.jsonc`
- `apply-free-models.sh` → Run this to deploy

## Apply instructions

```bash
# One command deploy
./apply-free-models.sh

# Or manually:
cp claude-settings-full-free.json ~/.claude/settings.json
cp kilo-config-full.jsonc ~/.config/kilo/kilo.jsonc

# Restart Kilo / Claude CLI
```

## Usage

### In Kilo CLI
```bash
# Explicit model
kilo --model b/deepseek-v4-flash

# For subcommands (auto uses subagent_model = b/deepseek-v4-flash)
kilo task "..."  # uses subagent_model
```

### In Claude Code CLI
```bash
claude --model deepseek/deepseek-v4-flash
claude --model deepseek/deepseek-v4-flash-vision-exp
```

### In Claude Desktop (MCP config)
If using Claude Desktop MCP servers, point `baseURL` to `https://api.b.ai/v1`.

## Test manually
```bash
curl -sS https://api.b.ai/v1/models \
  -H "Authorization: Bearer sk-canmbr2jyis9fkq46k8dec0c037mejhk"
```

## Troubleshooting

| Error | Fix |
|-------|-----|
| `401 Unauthorized` | Check API key |
| `404 Not Found` | Wrong endpoint — try alternatives |
| `Could not resolve host` | Network offline — test in local terminal |
| Models missing | Wait 5 min after config reload |
