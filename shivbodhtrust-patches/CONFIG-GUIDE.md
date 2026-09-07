# Claude CLI + Kilo — Model Configuration

## ✅ What was added

### Claude CLI (`~/.claude/settings.json`)
Added 3 DeepSeek models under a `models` key:
- `deepseek/deepseek-v4-flash` — free, fast
- `deepseek/deepseek-v4-flash-vision-exp` — free, vision support  
- `deepseek/deepseek-v4-pro` — paid, high quality

**Usage after config update:**
```bash
claude --model deepseek/deepseek-v4-flash
claude --model deepseek/deepseek-v4-flash-vision-exp
```

### Kilo (`~/.config/kilo/kilo.jsonc`)
- Replaced b.ai API key with your new key: `sk-canmbr2jyis9fkq46k8dec0c037mejhk`
- Added `deepseek-v4-flash-vision-exp` to provider `b` models
- Changed default model to `b/deepseek-v4-flash`

---

## 📊 Full pricing list (from your data)

### Free models ($0)
| Provider | Model | Input | Output | Context |
|----------|-------|-------|--------|---------|
| DeepSeek | DeepSeek-V4-Flash | $0 | $0 | 64K |
| DeepSeek | DeepSeek-V4-Flash-Vision-Exp | $0 | $0 | 64K |

### Ultra-low cost models
| Provider | Model | Input | Output | Context |
|----------|-------|-------|--------|---------|
| OpenAI | GPT-5 nano | $0.05 | $0.05 | 14K |
| OpenAI | GPT-5.4 nano | $0.2 | $0.2 | 14K |
| Qwen | Qwen3.8-27B | $0.22 | $0.22 | 1.6K |
| OpenAI | GPT-5.6 Luna | $0.2 | $0.25 | 1.2K |
| Claude | Claude Haiku 4.5 | $1 | $1.25 | 5 |
| Gemini | Gemini 3 Flash | $0.5 | $0.5 | 3 |
| MiniMax | MiniMax-M3 | $0.3 | $0.3 | 1.2K |
| MiMo | MiMo-V2.5-Pro | $0.435 | $0.435 | 0.87K |

---

## 🔧 Apply instructions

Sandbox is blocking writes to `~/.claude/` and `~/.config/kilo/`. Run these manually:

```bash
# 1. Claude CLI settings
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/claude-settings-updated.json \
   ~/.claude/settings.json

# 2. Kilo config
cp /Users/avikalpshukla/ShivBodh-Projects/sampreshan-tech/kilo-config-updated.jsonc \
   ~/.config/kilo/kilo.jsonc

# 3. Restart Claude CLI / Kilo to pick up changes
```

After that:
- `claude --model deepseek/deepseek-v4-flash`  → uses free DeepSeek
- Default Kilo model is now `b/deepseek-v4-flash`
