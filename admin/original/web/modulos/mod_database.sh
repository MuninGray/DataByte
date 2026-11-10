#!/usr/bin/env bash
set -Eeuo pipefail
CFG="/usr/local/etc/databyte/hosts.conf"
[[ -r "$CFG" ]] || { echo "No se puede leer $CFG"; exit 1; }
# shellcheck disable=SC1090
source "$CFG"
: "${SSH_KEY:?}" "${SSH_USER:?}" "${DB_IP:?}" "${DB_NAME:?}"
[[ "$DB_NAME" =~ ^[A-Za-z0-9_]+$ ]] || { echo "DB_NAME inválido" >&2; exit 2; }
while true; do
  echo
  echo "--- MYSQL REMOTO: $DB_IP ---"
  echo "1. Estado y variables de binlog"
  echo "2. Listar bases de datos"
  echo "3. Tablas y motores de $DB_NAME"
  echo "4. Chequeo de tablas"
  echo "0. Volver"
  read -r -p "Opción: " op
  case "$op" in
    1)
      ssh -t -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$DB_IP" \
        "sudo mysql -e 'SELECT VERSION() AS version, @@server_id AS server_id, @@log_bin AS log_bin, @@binlog_format AS binlog_format; SHOW MASTER STATUS; SHOW BINARY LOGS;'" ;;
    2)
      ssh -t -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$DB_IP" "sudo mysql -e 'SHOW DATABASES;'" ;;
    3)
      ssh -t -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$DB_IP" \
        "sudo mysql -e \"SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA='$DB_NAME' ORDER BY TABLE_NAME;\"" ;;
    4)
      ssh -t -i "$SSH_KEY" -o BatchMode=yes "$SSH_USER@$DB_IP" "sudo mysqlcheck --check '$DB_NAME'" ;;
    0) exit 0 ;;
    *) echo "Opción inválida." ;;
  esac
done
