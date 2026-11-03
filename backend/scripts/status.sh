#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Services Health & Status Monitor
# Pings /internal/health for Gateway and all 8 Microservices
# ============================================================

set -euo pipefail

# ANSI Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Detect Gateway Port (8000 or fallback 8080)
GW_PORT=8000
if ! curl -s --max-time 1 "http://127.0.0.1:8000/internal/health" >/dev/null 2>&1; then
    if curl -s --max-time 1 "http://127.0.0.1:8080/internal/health" >/dev/null 2>&1; then
        GW_PORT=8080
    fi
fi

SERVICES=(
    "gateway:${GW_PORT}:API Gateway (Single Entry Point)"
    "core:8001:Core Service (Auth/RBAC/Files)"
    "crm:8002:CRM Service (Leads/RAB/Quotation)"
    "marketing:8003:Marketing Service (Content/Ads)"
    "hrd:8004:HRD Service (Staff/Payroll/Workload)"
    "pm:8005:PM Service (Projects/Kanban/QA)"
    "finance:8006:Finance Service (AR/AP/P&L/COA)"
    "ai:8007:AI Gateway Service (LLM/Prompts)"
    "orchestrator:8008:Orchestrator (EventBus/Sagas)"
)

printf "\n%-15s %-6s %-12s %-10s %-35s\n" "SERVICE" "PORT" "STATUS" "LATENCY" "DESCRIPTION"
printf "%s\n" "--------------------------------------------------------------------------------------"

ALL_HEALTHY=true

for svc_entry in "${SERVICES[@]}"; do
    IFS=':' read -r svc port desc <<< "${svc_entry}"
    URL="http://127.0.0.1:${port}/internal/health"
    
    START=$(date +%s%N 2>/dev/null || date +%s)
    HTTP_CODE=$(curl -s -o /tmp/health_resp.json -w "%{http_code}" --max-time 2 "${URL}" 2>/dev/null || echo "000")
    END=$(date +%s%N 2>/dev/null || date +%s)
    
    LATENCY_MS=$(( (END - START) / 1000000 ))
    if [ "$LATENCY_MS" -le 0 ]; then LATENCY_MS=1; fi

    if [ "${HTTP_CODE}" = "200" ]; then
        STATUS="${GREEN}● UP${NC}"
    elif [ "${HTTP_CODE}" = "503" ]; then
        STATUS="${YELLOW}● DEGRADED${NC}"
        ALL_HEALTHY=false
    else
        STATUS="${RED}● DOWN${NC}"
        ALL_HEALTHY=false
    fi

    printf "%-15s %-6s %-20b %-10s %-35s\n" "${svc}" "${port}" "${STATUS}" "${LATENCY_MS}ms" "${desc}"
done

printf "%s\n\n" "--------------------------------------------------------------------------------------"

if [ "${ALL_HEALTHY}" = true ]; then
    echo -e "${GREEN}✅ All 9 ASTACODE services are UP and healthy!${NC}\n"
    exit 0
else
    echo -e "${YELLOW}ℹ️ Some services are offline or degraded. Run ./dev-up.sh to boot all services.${NC}\n"
    exit 1
fi
