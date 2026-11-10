#!/usr/bin/env bash
set -Eeuo pipefail
CFG="/usr/local/etc/databyte/hosts.conf"
[[ -r "$CFG" ]] || { echo "No se puede leer $CFG"; exit 1; }
# shellcheck disable=SC1090
source "$CFG"
: "${SSH_KEY:?}" "${SSH_USER:?}" "${BACKUP_IP:?}" "${BACKUP_ROOT:?}"
echo "--- RESPALDOS Y LOGS DE DATABYTE ---"
echo "1. Ejecutar ciclo de respaldo ahora"
echo "2. Ver últimos registros"
echo "3. Ver uso del repositorio"
read -r -p "Opción: " op
case "$op" in
  1) ssh -t -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$BACKUP_IP" \
       "/usr/local/sbin/databyte-backup-run all" ;;
  2) ssh -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$BACKUP_IP" \
       "tail -n 40 '$BACKUP_ROOT/logs/backup.log'" ;;
  3) ssh -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$BACKUP_IP" \
       "du -sh '$BACKUP_ROOT'/* 2>/dev/null; df -h '$BACKUP_ROOT'" ;;
  *) echo "Opción inválida." ;;
esac
