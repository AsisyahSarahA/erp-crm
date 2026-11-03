#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — Development Teardown Script
# Stops all running background microservices gracefully
# ============================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
PID_FILE="${BACKEND_DIR}/storage/dev.pid"

echo "============================================================"
echo " Stopping ASTACODE ERP Microservices..."
echo "============================================================"

if [ ! -f "${PID_FILE}" ]; then
    echo "ℹ️ No running services PID file found at ${PID_FILE}."
    exit 0
fi

while IFS=':' read -r pid name port || [ -n "$pid" ]; do
    if [ -n "$pid" ]; then
        if kill -0 "$pid" 2>/dev/null; then
            echo "🛑 Stopping ${name} (PID: ${pid}, Port: ${port})..."
            kill "$pid" 2>/dev/null || true
        else
            echo "ℹ️ Process ${name} (PID: ${pid}) was already stopped."
        fi
    fi
done < "${PID_FILE}"

rm -f "${PID_FILE}"

echo "============================================================"
echo " ✅ All ASTACODE services have been stopped."
echo "============================================================"

