#!/usr/bin/env bash
# Test Experiential Labs AI API
# Usage: ./test-explabs-model.sh [model] [prompt] [reasoning]

set -euo pipefail

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

MODEL="${1:-gpt-6-astra}"
PROMPT="${2:-Hello from my product}"
REASONING="${3:-xhigh}"
ENDPOINT="https://api.experientiallabs.ai/v1/chat/completions"

# Load EXPLABS_API_KEY from .env if not set
if [ -z "${EXPLABS_API_KEY:-}" ]; then
    if [ -f .env ]; then
        set -a; source .env; set +a
    fi
fi

if [ -z "${EXPLABS_API_KEY:-}" ]; then
    echo -e "${RED}❌ EXPLABS_API_KEY not set${NC}"
    echo "Set it: export EXPLABS_API_KEY=your-key"
    exit 1
fi

echo -e "${BLUE}Testing Experiential Labs API${NC}"
echo "  Model:      $MODEL"
echo "  Reasoning:  $REASONING"
echo "  Endpoint:   $ENDPOINT"
echo

# Single request (non-streaming) — easier to debug
echo -e "${YELLOW}→ Sending single request...${NC}"
curl -sS --max-time 30 "$ENDPOINT" \
    -H "Authorization: Bearer $EXPLABS_API_KEY" \
    -H "Content-Type: application/json" \
    -d "{
        \"model\": \"$MODEL\",
        \"reasoning_effort\": \"$REASONING\",
        \"messages\": [
            {\"role\": \"user\", \"content\": \"$PROMPT\"}
        ]
    }" 2>&1 | python3 -m json.tool 2>/dev/null || echo "Failed"

echo
echo -e "${YELLOW}→ Testing streaming request...${NC}"
curl -sS --max-time 30 "$ENDPOINT" \
    -H "Authorization: Bearer $EXPLABS_API_KEY" \
    -H "Content-Type: application/json" \
    -d "{
        \"model\": \"$MODEL\",
        \"stream\": true,
        \"reasoning_effort\": \"$REASONING\",
        \"messages\": [
            {\"role\": \"user\", \"content\": \"$PROMPT\"}
        ]
    }" 2>&1 | head -100 || echo "Streaming failed"

echo
echo -e "${BLUE}Models endpoint:${NC}"
curl -sS --max-time 10 "https://api.experientiallabs.ai/v1/models" \
    -H "Authorization: Bearer $EXPLABS_API_KEY" \
    -H "Content-Type: application/json" 2>&1 | python3 -m json.tool 2>/dev/null || echo "Models endpoint unreachable"
