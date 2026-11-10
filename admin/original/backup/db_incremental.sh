#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
require mysqlbinlog; require mysql; require sha256sum; require gzip; require awk

STATE="$BACKUP_ROOT/database/binlogs/current_checkpoint.tsv"
[[ -s "$STATE" ]] || { log "No existe checkpoint FULL. Ejecutar db_full.sh."; exit 4; }
IFS=$'\t' read -r START_FILE START_POS < "$STATE"
CHAIN_FILE="$BACKUP_ROOT/database/binlogs/current_chain"
[[ -s "$CHAIN_FILE" ]] || { log "Falta ID de cadena DB"; exit 4; }
CHAIN="$(cat "$CHAIN_FILE")"
[[ "$CHAIN" =~ ^[0-9]{8}T[0-9]{6}Z$ ]] || exit 4
[[ "$START_FILE" =~ ^[A-Za-z0-9._-]+$ && "$START_POS" =~ ^[0-9]+$ ]] || { log "Checkpoint inválido."; exit 4; }

STATUS="$("${mysql_cmd[@]}" -NBe "SHOW MASTER STATUS")"
read -r END_FILE END_POS _ <<< "$STATUS"
[[ -n "${END_FILE:-}" && "$END_POS" =~ ^[0-9]+$ ]] || { log "No se pudo leer SHOW MASTER STATUS."; exit 5; }

if [[ "$START_FILE" == "$END_FILE" && "$START_POS" == "$END_POS" ]]; then
  log "Sin cambios nuevos en MySQL ($START_FILE:$START_POS). Incremental omitido."
  exit 0
fi

LOGS="$("${mysql_cmd[@]}" -NBe "SHOW BINARY LOGS")"
mapfile -t FILES < <(awk -v s="$START_FILE" -v e="$END_FILE" '
  $1==s {found=1}
  found {print $1}
  $1==e {exit}
' <<< "$LOGS")
((${#FILES[@]} > 0)) || { log "El binlog inicial $START_FILE ya no existe o no se encuentra el rango."; exit 6; }
[[ "${FILES[0]}" == "$START_FILE" && "${FILES[-1]}" == "$END_FILE" ]] || {
  log "Falta algún binlog de la cadena $START_FILE -> $END_FILE. No se avanza checkpoint."; exit 6;
}

TS="$(date +%Y%m%d_%H%M%S)"
RAW="$BACKUP_ROOT/database/binlogs/.inc_${TS}.sql"
TMP="$BACKUP_ROOT/database/daily/.${DB_NAME}_INC_${TS}.sql.gz.tmp"
OUT="$BACKUP_ROOT/database/daily/${DB_NAME}_INC_${TS}.sql.gz"
log "Incremental DB $START_FILE:$START_POS -> $END_FILE:$END_POS"

# mysqlbinlog aplica start-position al primer archivo y stop-position al último.
# Los límites de posición solo aplican a los binlogs inicial y final.
# Invocar una vez por binlog: si se usan ambos límites en todos los archivos,
# se pierden eventos al rotar el archivo binario.
: > "$RAW"
for i in "${!FILES[@]}"; do
  file="${FILES[$i]}"
  opts=()
  if ((i == 0)); then opts+=(--start-position="$START_POS"); fi
  if ((i == ${#FILES[@]} - 1)); then opts+=(--stop-position="$END_POS"); fi
  mysqlbinlog --login-path="$MYSQL_LOGIN_PATH" --read-from-remote-server \
    --host="$DB_IP" "${opts[@]}" "$file" >> "$RAW"
done
[[ -s "$RAW" ]] || { rm -f "$RAW"; log "Binlog vacío; no se publica ni avanza checkpoint."; exit 7; }
gzip -c "$RAW" > "$TMP"
rm -f "$RAW"
gzip -t "$TMP"
verify_and_publish "$TMP" "$OUT"
printf '%s\t%s\n' "$END_FILE" "$END_POS" > "$STATE.tmp"
mv "$STATE.tmp" "$STATE"
write_meta "$OUT" "kind=db" "type=inc" "chain=$CHAIN" "created=$TS" "start_file=$START_FILE" "start_pos=$START_POS" "end_file=$END_FILE" "end_pos=$END_POS"
log "Incremental DB verificado: $OUT; nuevo checkpoint=$END_FILE:$END_POS"
