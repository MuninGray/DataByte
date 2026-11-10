#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
require mysqldump; require gzip; require sha256sum; require mysql; require awk
"${mysql_cmd[@]}" -NBe "SELECT 1" >/dev/null

TS="$(date +%Y%m%d_%H%M%S)"
CHAIN="$(chain_id)"
WEEKLY="$BACKUP_ROOT/database/weekly"
MONTHLY="$BACKUP_ROOT/database/monthly"
mkdir -p "$WEEKLY" "$MONTHLY"
TMP="$WEEKLY/.${DB_NAME}_${TS}.sql.gz.tmp"
OUT="$WEEKLY/${DB_NAME}_FULL_${TS}.sql.gz"
log "Inicio FULL de $DB_NAME"

# --source-data=2 inserta en el dump la coordenada exacta para iniciar el PITR.
mysqldump --login-path="$MYSQL_LOGIN_PATH" --host="$DB_IP" --single-transaction --source-data=2 \
  --no-tablespaces --quick --triggers --routines --events --hex-blob \
  --databases "$DB_NAME" | gzip -c > "$TMP"
gzip -t "$TMP"
COORD="$(gzip -dc "$TMP" | awk '/CHANGE (MASTER|REPLICATION SOURCE) TO/ {line=$0} END {print line}')"
FILE="$(sed -nE "s/.*(MASTER_LOG_FILE|SOURCE_LOG_FILE)='([^']+)'.*/\\2/p" <<< "$COORD")"
POS="$(sed -nE "s/.*(MASTER_LOG_POS|SOURCE_LOG_POS)=([0-9]+).*/\\2/p" <<< "$COORD")"
[[ -n "$FILE" && "$POS" =~ ^[0-9]+$ ]] || {
  rm -f "$TMP"; log "No se pudo extraer la coordenada del dump; no se publica checkpoint."; exit 5;
}
verify_and_publish "$TMP" "$OUT"
printf '%s\t%s\n' "$FILE" "$POS" > "$BACKUP_ROOT/database/binlogs/.checkpoint.tmp"
mv "$BACKUP_ROOT/database/binlogs/.checkpoint.tmp" "$BACKUP_ROOT/database/binlogs/current_checkpoint.tsv"
cp "$BACKUP_ROOT/database/binlogs/current_checkpoint.tsv" "$BACKUP_ROOT/database/binlogs/full_checkpoint.tsv"
write_meta "$OUT" "kind=db" "type=full" "chain=$CHAIN" "created=$TS" "end_file=$FILE" "end_pos=$POS"
printf '%s\n' "$CHAIN" > "$BACKUP_ROOT/database/binlogs/.current_chain.tmp"
mv "$BACKUP_ROOT/database/binlogs/.current_chain.tmp" "$BACKUP_ROOT/database/binlogs/current_chain"

# Primera copia dominical del mes también se conserva como mensual.
if [[ "$(date +%u)" == 7 && "$(date +%d)" -le 7 ]]; then
  cp --reflink=auto "$OUT" "$MONTHLY/$(basename "$OUT")"
  (cd "$MONTHLY" && sha256sum "$(basename "$OUT")" > "$(basename "$OUT").sha256")
  cp -- "${OUT}.meta" "$MONTHLY/$(basename "$OUT").meta"
fi
log "FULL DB verificado: $OUT; checkpoint=$FILE:$POS"
