#!/usr/bin/env bash
# One-time Railway database initialization.
# Requires: railway CLI logged in and linked to the project.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SQL="$ROOT/database/railway-init.sql"

if ! command -v railway >/dev/null 2>&1; then
  echo "Install Railway CLI: npm install -g @railway/cli"
  exit 1
fi

if [ ! -f "$SQL" ]; then
  echo "Missing $SQL"
  exit 1
fi

echo "Importing schema and seed into Railway MySQL..."
railway connect mysql < "$SQL"
echo "Done. Test login: STU001 / Password123!"
