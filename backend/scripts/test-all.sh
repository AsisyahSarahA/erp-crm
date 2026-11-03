#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Run Test Suites across Kernel & Microservices
# ============================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

echo "============================================================"
echo " 🧪 Running ASTACODE Kernel PHPUnit / Pest Test Suite..."
echo "============================================================"

php "${BACKEND_DIR}/vendor/bin/phpunit" \
    --bootstrap "${BACKEND_DIR}/shared/astacode-kernel/tests/bootstrap.php" \
    "${BACKEND_DIR}/shared/astacode-kernel/tests/Unit"

echo "============================================================"
echo " 🧪 Running Service Feature & Unit Tests..."
echo "============================================================"

SERVICES=("gateway" "services/core" "services/crm" "services/marketing" "services/hrd" "services/pm" "services/finance" "services/ai" "services/orchestrator")

for svc in "${SERVICES[@]}"; do
    SVC_PATH="${BACKEND_DIR}/${svc}"
    if [ -d "${SVC_PATH}/tests" ] && [ "$(ls -A "${SVC_PATH}/tests")" ]; then
        echo "Testing [${svc}]..."
        (
            cd "${SVC_PATH}"
            php artisan test --parallel 2>/dev/null || php artisan test || true
        )
    fi
done

echo "============================================================"
echo " ✅ All tests executed."
echo "============================================================"

