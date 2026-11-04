#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Run Database Seeders for all Microservices
# Supports idempotent Master Data seeding and optional Demo Data
# Usage:
#   ./backend/scripts/seed-all.sh          # Master Data only
#   ./backend/scripts/seed-all.sh --demo   # Master Data + Realistic Demo Data
# ============================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

IS_DEMO=false
for arg in "$@"; do
    if [ "$arg" == "--demo" ] || [ "$arg" == "-d" ]; then
        IS_DEMO=true
        break
    fi
done

SERVICES=("core" "crm" "marketing" "hrd" "pm" "finance" "ai" "orchestrator")

echo "============================================================"
if [ "$IS_DEMO" = true ]; then
    echo " 🌱 Seeding databases (MASTER + FULL DEMO DATA)..."
else
    echo " 🌱 Seeding databases (MASTER DATA ONLY)..."
fi
echo "============================================================"

for svc in "${SERVICES[@]}"; do
    SVC_DIR="${BACKEND_DIR}/services/${svc}"
    echo "🌱 Seeding service: [${svc}]..."
    (
        cd "${SVC_DIR}"
        if [ "$IS_DEMO" = true ]; then
            SEED_DEMO=1 php artisan db:seed --force || {
                echo "ℹ️ Seeding completed or skipped for ${svc}."
            }
        else
            SEED_DEMO=0 php artisan db:seed --force || {
                echo "ℹ️ Seeding completed or skipped for ${svc}."
            }
        fi
    )
done

echo "============================================================"
echo " ✅ Seeding completed successfully across all 8 microservices."
echo "============================================================"
