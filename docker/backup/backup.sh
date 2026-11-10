#!/usr/bin/env bash
set -Eeuo pipefail
umask 077
mkdir -p /respaldos
stamp="$(date -u +%Y%m%dT%H%M%SZ)"
folder="$(mktemp -d /respaldos/.databyte-backup.XXXXXX)"
trap 'rm -rf "$folder"' EXIT
# MYSQL_PWD is supplied through the container environment; do not print it.
mysqldump --single-transaction --quick --routines --events --triggers   -h "${DB_HOST:-db}" -u "${DB_USER:-root}" --databases "${DB_NAME:-DataByte}"   > "$folder/databyte.sql"
test -s "$folder/databyte.sql"
archive="/respaldos/databyte-${stamp}.tar.gz"
tar -C "$folder" -czf "$archive" databyte.sql
tar -tzf "$archive" | grep -Fxq databyte.sql
printf 'Respaldo creado y verificado: %s\n' "$archive"
