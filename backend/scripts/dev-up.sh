#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Development Startup Script (Without Docker)
# Starts API Gateway and all 8 microservices in background
# ============================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
PID_FILE="${BACKEND_DIR}/storage/dev.pid"
LOG_DIR="${BACKEND_DIR}/storage/logs/dev"

mkdir -p "${LOG_DIR}"
mkdir -p "${BACKEND_DIR}/storage"

echo "============================================================"
echo " Starting ASTACODE ERP Microservices (Without Docker)..."
echo "============================================================"

if [ -f "${PID_FILE}" ]; then
    echo "⚠️ Services seem to be already running. Running dev-down.sh first..."
    "${SCRIPT_DIR}/dev-down.sh"
fi

touch "${PID_FILE}"

# Determine Gateway Port (Default: 8000; Fallback: 8080 if 8000 occupied by host environment)
GATEWAY_PORT="${GATEWAY_PORT:-8000}"
if [ "${GATEWAY_PORT}" = "8000" ]; then
    if ! php -r "\$s = @stream_socket_server('tcp://127.0.0.1:8000'); if (!\$s) exit(1); fclose(\$s);" 2>/dev/null; then
        echo "ℹ️ Port 8000 is currently occupied in local environment; using port 8080 for Gateway."
        GATEWAY_PORT=8080
    fi
fi

start_service() {
    local name="$1"
    local dir="$2"
    local port="$3"

    echo "🚀 Starting ${name} on port ${port}..."
    (
        cd "${dir}"
        php artisan serve --host=127.0.0.1 --port="${port}" >> "${LOG_DIR}/${name}.log" 2>&1
    ) &
    local pid=$!
    echo "${pid}:${name}:${port}" >> "${PID_FILE}"
}

# 1. Start API Gateway (Port 8000 / 8080)
start_service "gateway" "${BACKEND_DIR}/gateway" "${GATEWAY_PORT}"

# 2. Start Core Service (Port 8001)
start_service "core" "${BACKEND_DIR}/services/core" 8001

# 3. Start CRM Service (Port 8002)
start_service "crm" "${BACKEND_DIR}/services/crm" 8002

# 4. Start Marketing Service (Port 8003)
start_service "marketing" "${BACKEND_DIR}/services/marketing" 8003

# 5. Start HRD Service (Port 8004)
start_service "hrd" "${BACKEND_DIR}/services/hrd" 8004

# 6. Start PM Service (Port 8005)
start_service "pm" "${BACKEND_DIR}/services/pm" 8005

# 7. Start Finance Service (Port 8006)
start_service "finance" "${BACKEND_DIR}/services/finance" 8006

# 8. Start AI Service (Port 8007)
start_service "ai" "${BACKEND_DIR}/services/ai" 8007

# 9. Start Orchestrator Service (Port 8008)
start_service "orchestrator" "${BACKEND_DIR}/services/orchestrator" 8008

echo "============================================================"
echo " Waiting 2 seconds for services to boot..."
sleep 2

echo " Checking service health..."
"${SCRIPT_DIR}/status.sh" || true

echo "============================================================"
echo " ✅ All 9 services launched in background."
echo " PIDs recorded in: ${PID_FILE}"
echo " Logs stored in:   ${LOG_DIR}/*.log"
echo " To stop services: ./backend/scripts/dev-down.sh"
echo "============================================================"
