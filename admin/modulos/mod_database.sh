#!/usr/bin/env bash
set -Eeuo pipefail
cd "$DATABYTE_PROJECT_ROOT"
mysql_query(){
 local query="$1"
 docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot --batch --table' <<< "$query"
}
while true; do
 echo; echo '--- MYSQL 8: CONTENEDOR LOCAL db ---'
 echo '1. Estado y variables de binlog'
 echo '2. Listar bases de datos'
 echo '3. Tablas y motores de DataByte'
 echo '4. Chequeo de tablas'
 echo '0. Volver'
 read -r -p 'Opción: ' op || exit 0
 case "$op" in
   1) docker compose ps db; mysql_query 'SELECT VERSION() AS version, @@server_id AS server_id, @@log_bin AS log_bin, @@binlog_format AS binlog_format;' ;;
   2) mysql_query 'SHOW DATABASES;' ;;
   3) mysql_query "SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA='DataByte' ORDER BY TABLE_NAME;" ;;
   4) docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqlcheck -uroot --check DataByte' ;;
   0) exit 0 ;;
   *) echo 'Opción inválida' ;;
 esac
done
