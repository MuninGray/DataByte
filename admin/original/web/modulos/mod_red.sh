#!/usr/bin/env bash
set -Eeuo pipefail
echo "--- ESTADO DE RED ---"
ip -4 -br addr
echo
echo "--- RUTAS ---"
ip route
echo
echo "--- PERFILES NETWORKMANAGER ---"
if command -v nmcli >/dev/null 2>&1; then
  nmcli -t -f NAME,TYPE,DEVICE connection show --active
else
  echo "nmcli no está instalado."
fi
echo
echo "--- CONECTIVIDAD ENTRE SERVIDORES ---"
for ipaddr in 192.168.1.50 192.168.1.51 192.168.1.52; do
  printf '%s: ' "$ipaddr"
  ping -c 2 -W 2 "$ipaddr" >/dev/null 2>&1 && echo "OK" || echo "SIN RESPUESTA ICMP"
done
echo
echo "Este módulo es de diagnóstico. No cambia IP/rutas para evitar cortar la sesión remota."
