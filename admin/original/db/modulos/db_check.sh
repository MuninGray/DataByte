#!/usr/bin/env bash
set -Eeuo pipefail
echo "--- CHEQUEO DE TABLAS DATABYTE ---"
sudo mysqlcheck --check DataByte
