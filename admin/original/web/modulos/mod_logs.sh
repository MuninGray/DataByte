#!/usr/bin/env bash
set -Eeuo pipefail
CFG="/usr/local/etc/databyte/hosts.conf"
while true; do
  echo
  echo "--- CONSULTA DE LOGS ---"
  echo "1. Últimos eventos del sistema"
  echo "2. Eventos SSH"
  echo "3. Eventos Docker"
  echo "4. Eventos firewalld"
  echo "5. Logs de respaldo (servidor Backup)"
  echo "0. Volver"
  read -r -p "Opción: " op
  case "$op" in
    1) sudo journalctl -n 60 --no-pager ;;
    2) sudo journalctl -u sshd -n 60 --no-pager ;;
    3) sudo journalctl -u docker -n 60 --no-pager ;;
    4) sudo journalctl -u firewalld -n 60 --no-pager ;;
    5)
      [[ -r "$CFG" ]] || { echo "No se puede leer $CFG"; continue; }
      # shellcheck disable=SC1090
      source "$CFG"
      ssh -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$BACKUP_IP" \
        "tail -n 60 '$BACKUP_ROOT/logs/backup.log'" ;;
    0) exit 0 ;;
    *) echo "Opción inválida." ;;
  esac
done
