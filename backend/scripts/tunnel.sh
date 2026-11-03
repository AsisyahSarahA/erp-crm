#!/usr/bin/env bash
# ============================================================
# ASTACODE ERP — SSH Port Forwarding Tunnel for Remote MySQL
# Forwards local port 3307 to remote MySQL port 3306 on SSH server
# ============================================================

set -euo pipefail

SSH_USER="${SSH_USER:-asisyahs}"
SSH_HOST="${SSH_HOST:-127.0.0.1}"
SSH_PORT="${SSH_PORT:-22}"
LOCAL_PORT="${LOCAL_PORT:-3307}"
REMOTE_PORT="${REMOTE_PORT:-3306}"

echo "============================================================"
echo " Starting SSH Tunnel for ASTACODE MySQL 8 Database..."
echo " Forwarding 127.0.0.1:${LOCAL_PORT} -> ${SSH_USER}@${SSH_HOST}:${REMOTE_PORT}"
echo "============================================================"

# Check if tunnel already running
if lsof -Pi :${LOCAL_PORT} -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️ Port ${LOCAL_PORT} is already bound and active."
    exit 0
fi

ssh -N -L "${LOCAL_PORT}:127.0.0.1:${REMOTE_PORT}" -p "${SSH_PORT}" "${SSH_USER}@${SSH_HOST}"

