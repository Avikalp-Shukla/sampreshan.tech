#!/usr/bin/env bash
# Test the Experiential Labs API
# Usage: ./test-explabs.sh [model] [prompt]
#   model default: qwen3.8-27b
#   prompt default: Hello

set -euo pipefail

MODEL="${1:-qwen3.8-27b}"
PROMPT="${2:-Hello}"

# Load API key from .env file if not in env
if [ -z "${EXPLABS_API_KEY:-}" ]; then
    ENV_FILE="$(dirname "$0")/.env"
    if [ -f "$ENV_FILE" ]; then
        # shellcheck disable=SC1090
        set -a; source "$ENV_FILE"; set +a
    fi
fi

if [ -z "${EXPLABS_API_KEY:-}" ]; then
    echo "ERROR: EXPLABS_API_KEY not set."
    echo "Set it in your shell:  export EXPLABS_API_KEY=sk-..."
    echo "Or put EXPLABS_API_KEY=sk-... in a .env file next to this script."
    exit 1
fi

echo "→ Model: $MODEL"
echo "→ Prompt: $PROMPT"
echo "→ Endpoint: https://api.experientiallabs.ai/v1/chat/completions"
echo

RESPONSE=$(curl -sS --max-time 60 "https://api.experientiallabs.ai/v1/chat/completions" \
    -H "Authorization: Bearer $EXPLABS_API_KEY" \
    -H "Content-Type: application/json" \
    -d "{\"model\": \"$MODEL\", \"messages\": [{\"role\": \"user\", \"content\": \"$PROMPT\"}]}")

echo "← Response:"
echo "$RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE"
