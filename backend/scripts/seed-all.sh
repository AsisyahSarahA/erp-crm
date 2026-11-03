#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Run Database Seeders for all Microservices
# ============================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

SERVICES=("core" "crm" "marketing" "hrd" "pm" "finance" "ai" "orchestrator")

echo "============================================================"
echo " Seeding databases across all 8 microservices..."
echo "============================================================"

for svc in "${SERVICES[@]}"; do
    SVC_DIR="${BACKEND_DIR}/services/${svc}"
    echo "🌱 Seeding service: [${svc}]..."
    (
        cd "${SVC_DIR}"
        php artisan db:seed --force || {
            echo "ℹ️ Seeding skipped or empty for ${svc}."
        }
    )
done

echo "============================================================"
echo " ✅ Seeding completed."
echo "============================================================"

