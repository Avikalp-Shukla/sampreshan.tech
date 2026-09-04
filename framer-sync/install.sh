#!/usr/bin/env bash
# framer-sync/install.sh
# One-time install helper for environments where `npm install` needs to be
# run outside a sandbox (DNS blocked, etc).
#
# Usage:
#   bash framer-sync/install.sh

set -euo pipefail

cd "$(dirname "$0")"

echo "→ Installing framer-api + dotenv into ./node_modules"
npm install --no-audit --no-fund

echo ""
echo "✓ Done. Verify with:"
echo "    npm run framer:status"
echo ""
echo "If you see 'FRAMER_PROJECT_URL is not set', fill it in at the repo"
echo "root in .env (already gitignored)."
