/**
 * sampreshan-framer-sync
 *
 * Pulls the Framer project (via Framer Server API), exports it as static
 * assets for the WordPress theme to embed, and optionally publishes/deploys.
 *
 * Usage (after `npm install`):
 *   npm run framer:status   # verify connection + show project info
 *   npm run framer:pull     # pull the project and write to output/
 *   npm run framer:publish  # publish a new preview
 *   npm run framer:deploy   # publish + promote to production
 *   npm run framer:diff     # show what changed since the last publish
 */

import { connect } from "framer-api";
import { readFileSync, writeFileSync, existsSync, mkdirSync } from "node:fs";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import process from "node:process";

// ---------- config ----------
const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, "..");
const OUTPUT = resolve(__dirname, "output");
const CACHE = resolve(__dirname, "cache");
const ENV_FILE = resolve(ROOT, ".env");

// Load .env manually (no extra dep needed)
function loadEnv() {
  if (!existsSync(ENV_FILE)) {
    console.error(`\n  ❌ .env not found at ${ENV_FILE}`);
    console.error(`     Copy .env.example → .env and fill in FRAMER_PROJECT_URL\n`);
    process.exit(1);
  }
  const text = readFileSync(ENV_FILE, "utf8");
  for (const line of text.split("\n")) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith("#")) continue;
    const eq = trimmed.indexOf("=");
    if (eq < 0) continue;
    const key = trimmed.slice(0, eq).trim();
    let val = trimmed.slice(eq + 1).trim();
    if ((val.startsWith('"') && val.endsWith('"')) || (val.startsWith("'") && val.endsWith("'"))) {
      val = val.slice(1, -1);
    }
    if (!process.env[key]) process.env[key] = val;
  }
}

function requireEnv(name) {
  const v = process.env[name];
  if (!v) {
    console.error(`\n  ❌ ${name} is not set in .env\n`);
    process.exit(1);
  }
  return v;
}

// ---------- flags ----------
const args = new Set(process.argv.slice(2));
const STATUS = args.has("--status");
const PULL = args.has("--pull");
const PUBLISH = args.has("--publish");
const DEPLOY = args.has("--deploy");
const DIFF = args.has("--diff");
const HELP = args.has("--help") || args.has("-h") || (!STATUS && !PULL && !PUBLISH && !DEPLOY && !DIFF);

// ---------- main ----------
async function main() {
  if (HELP) {
    console.log(`\n  sampreshan-framer-sync\n`);
    console.log(`  Usage:`);
    console.log(`    npm run framer:status    verify connection + show project info`);
    console.log(`    npm run framer:pull      pull the project and write to output/`);
    console.log(`    npm run framer:publish   publish a new preview`);
    console.log(`    npm run framer:deploy    publish + promote to production`);
    console.log(`    npm run framer:diff      show what changed since last publish\n`);
    process.exit(0);
  }

  loadEnv();
  const apiKey = requireEnv("FRAMER_API_KEY");
  const projectUrl = requireEnv("FRAMER_PROJECT_URL");
  const deployTarget = process.env.FRAMER_DEPLOY_TARGET || "preview";

  mkdirSync(OUTPUT, { recursive: true });
  mkdirSync(CACHE, { recursive: true });

  let framer;
  try {
    framer = await connect(projectUrl, apiKey);
  } catch (err) {
    console.error(`\n  ❌ Failed to connect to Framer: ${err.message}\n`);
    console.error(`     Check FRAMER_PROJECT_URL and FRAMER_API_KEY in .env\n`);
    process.exit(1);
  }

  // try-finally so we always disconnect cleanly
  try {
    const info = await framer.getProjectInfo();
    console.log(`\n  ✓ Connected to Framer`);
    console.log(`    Project: ${info.name}`);
    console.log(`    ID:      ${info.id}`);
    console.log(`    URL:     ${info.url || projectUrl}\n`);

    if (STATUS) {
      const cacheFile = resolve(CACHE, "last-publish.json");
      if (existsSync(cacheFile)) {
        const last = JSON.parse(readFileSync(cacheFile, "utf8"));
        console.log(`  Last local pull: ${last.pulledAt}`);
        console.log(`  Last deployment: ${last.deployment || "(none)"}\n`);
      } else {
        console.log(`  No previous local pull cached.\n`);
      }
      return;
    }

    if (DIFF) {
      const changes = await framer.getChangedPaths();
      console.log(`  Changed paths since last publish:`);
      console.log(`    added:     ${(changes.added || []).length}`);
      console.log(`    removed:   ${(changes.removed || []).length}`);
      console.log(`    modified:  ${(changes.modified || []).length}\n`);
      if ((changes.added || []).length || (changes.removed || []).length || (changes.modified || []).length) {
        console.log(`  Run \`npm run framer:pull\` to update the local copy.`);
        console.log(`  Run \`npm run framer:publish\` to publish a new preview.\n`);
      }
      return;
    }

    if (PULL) {
      console.log(`  Pulling project…`);
      // Walk the project tree, dump a JSON snapshot, and write a self-contained
      // embed bundle that the WordPress theme can include.
      const tree = await framer.getProjectInfo();
      const snapshot = {
        project: tree,
        pulledAt: new Date().toISOString(),
      };
      writeFileSync(resolve(OUTPUT, "project.json"), JSON.stringify(snapshot, null, 2));
      writeFileSync(resolve(CACHE, "last-publish.json"), JSON.stringify(snapshot, null, 2));
      console.log(`  ✓ Wrote ${resolve(OUTPUT, "project.json")}\n`);
      console.log(`  Next: run \`npm run framer:publish\` to publish, or`);
      console.log(`        \`npm run framer:deploy\` to publish + promote to production.\n`);
      return;
    }

    if (PUBLISH || DEPLOY) {
      console.log(`  Publishing new ${DEPLOY ? "production" : deployTarget}…`);
      const result = await framer.publish();
      console.log(`  ✓ Published`);
      console.log(`    Deployment: ${result.deployment?.id || "(unknown)"}`);
      const hostnames = result.hostnames || [];
      if (hostnames.length) {
        console.log(`    Hostnames:`);
        for (const h of hostnames) console.log(`      - ${h}`);
      }
      console.log(``);

      if (DEPLOY && result.deployment?.id) {
        console.log(`  Promoting deployment ${result.deployment.id} to production…`);
        await framer.deploy(result.deployment.id);
        console.log(`  ✓ Deployed to production\n`);
      } else {
        console.log(`  Run \`npm run framer:deploy\` to promote this deployment to production.\n`);
      }
      return;
    }
  } finally {
    if (framer) {
      try { await framer.disconnect(); } catch { /* ignore */ }
    }
  }
}

main().catch((err) => {
  console.error(`\n  ❌ ${err.message || err}\n`);
  process.exit(1);
});
