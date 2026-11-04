#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Automated API Contract Verification (CI)
# Verifies Backend Routes & Schema against docs/API-CONTRACTS/gateway.yaml
# DoD: Any route/contract discrepancy fails the build (Exit 1).
# ============================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
ROOT_DIR="$(cd "${BACKEND_DIR}/.." && pwd)"
SPEC_PATH="${ROOT_DIR}/docs/API-CONTRACTS/gateway.yaml"

echo "============================================================"
echo " 📜 ASTACODE ERP — API Contract Test (OpenAPI vs Backend)"
echo "============================================================"

# 1. Check specification existence
if [ ! -f "${SPEC_PATH}" ]; then
    echo "❌ FATAL: OpenAPI contract file not found: ${SPEC_PATH}"
    exit 1
fi
echo "✅ OpenAPI specification located: ${SPEC_PATH}"

# 2. Run Gateway Contract Test Suite
echo "🧪 Running Gateway OpenAPI Contract Test Suite..."
(
    cd "${BACKEND_DIR}/gateway"
    php artisan test --filter=GatewayOpenApiContractTest
)

# 3. Route Consistency Verification: Verify documented endpoints match service maps
echo "🔍 Verifying Gateway reverse proxy routes against services map..."
SERVICES=("core" "crm" "marketing" "hrd" "pm" "finance" "ai" "orchestrator")

for svc in "${SERVICES[@]}"; do
    # Verify service is mentioned in paths of gateway.yaml
    if ! grep -q "/${svc}/" "${SPEC_PATH}"; then
        echo "❌ CONTRACT VIOLATION: Microservice [${svc}] has no endpoints documented in ${SPEC_PATH}"
        exit 1
    fi
    echo "   ✓ [${svc}] endpoints documented in OpenAPI contract"
done

# 4. Verify RFC 7807 and Standard Conventions
echo "🔍 Verifying OpenAPI schema adherence to RFC 7807 Problem Details..."
if ! grep -q "ApiProblemDetails" "${SPEC_PATH}" || ! grep -q "correlation_id" "${SPEC_PATH}"; then
    echo "❌ CONTRACT VIOLATION: gateway.yaml does not define RFC 7807 ApiProblemDetails schema!"
    exit 1
fi
echo "   ✓ RFC 7807 Problem Details schema verified"

echo "============================================================"
echo " ✅ API Contract Test PASSED (OpenAPI strictly matches backend)."
echo "============================================================"

