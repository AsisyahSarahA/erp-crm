#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Run Migrations for all Microservices
# ============================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

SERVICES=("core" "crm" "marketing" "hrd" "pm" "finance" "ai" "orchestrator")

echo "============================================================"
echo " Running database migrations across all 8 microservices..."
echo "============================================================"

for svc in "${SERVICES[@]}"; do
    SVC_DIR="${BACKEND_DIR}/services/${svc}"
    echo "📦 Migrating service: [${svc}]..."
    (
        cd "${SVC_DIR}"
        php artisan migrate --force || {
            echo "⚠️ Migration warning for ${svc} (Check MySQL connection)."
        }
    )
done

echo "============================================================"
echo " ✅ Migration process completed."
echo "============================================================"

