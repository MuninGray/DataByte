#!/usr/bin/env bash
set -Eeuo pipefail
echo '--- ESTADO DE RED (VM ÚNICA) ---'
ip -4 -br addr
echo; echo '--- RUTAS ---'; ip route
echo; echo '--- NETWORKMANAGER ---'
if command -v nmcli >/dev/null 2>&1; then nmcli -t -f NAME,TYPE,DEVICE connection show --active; else echo 'nmcli no disponible'; fi
echo; echo '--- REDES DOCKER ---'
docker network ls || true
echo; echo '--- CONECTIVIDAD HACIA EL CONTENEDOR MySQL ---'
cd "$DATABYTE_PROJECT_ROOT"
docker compose ps db
# Check service presence through internal Docker network; no external IP assumptions.
echo 'Los servicios de esta instalación utilizan la red interna Docker, no 192.168.1.50/.51/.52.'
