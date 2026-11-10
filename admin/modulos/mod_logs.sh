#!/usr/bin/env bash
set -Eeuo pipefail
cd "$DATABYTE_PROJECT_ROOT"
while true; do
  echo; echo '--- CONSULTA DE LOGS ---'
  echo '1. Últimos eventos del sistema'
  echo '2. Eventos SSH'
  echo '3. Eventos Docker Engine'
  echo '4. Eventos firewalld'
  echo '5. Logs del servicio Backup (último contenedor disponible)'
  echo '6. Logs de Docker Compose (todos los servicios)'
  echo '0. Volver'
  read -r -p 'Opción: ' op || exit 0
  case "$op" in
    1) journalctl -n 60 --no-pager ;;
    2) journalctl -u sshd -n 60 --no-pager ;;
    3) journalctl -u docker -n 60 --no-pager ;;
    4) journalctl -u firewalld -n 60 --no-pager ;;
    5) docker compose --profile backup logs --tail=60 backup || true; echo 'Los contenedores backup con --rm se eliminan al terminar; el archivo tar.gz queda en respaldos/.' ;;
    6) docker compose logs --tail=60 ;;
    0) exit 0 ;;
    *) echo 'Opción inválida' ;;
  esac
done
