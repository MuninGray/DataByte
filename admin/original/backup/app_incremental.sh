#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
require gzip; require sha256sum; require ssh
[[ -n "$(find "$BACKUP_ROOT/application/weekly" -maxdepth 1 -name "app_FULL_*.tar.gz" -print -quit)" ]] || { log "Falta FULL semanal de aplicación; no se permite incremental."; exit 4; }
CHAIN_FILE="$BACKUP_ROOT/application/current_chain"
[[ -s "$CHAIN_FILE" ]] || { log "Sin cadena de aplicación; ejecutar FULL."; exit 4; }
CHAIN="$(cat "$CHAIN_FILE")"
[[ "$CHAIN" =~ ^[0-9]{8}T[0-9]{6}Z$ ]] || exit 4
STAMP="$(date +%Y%m%d_%H%M%S)"
REMOTE="$SSH_USER@$WEB_IP"
SNAP_REMOTE="${APP_SNAPSHOT_REMOTE:-/home/lore/.cache/databyte-app.snar}"
TMP_REMOTE="${SNAP_REMOTE}.tmp.$$"
OUT="$BACKUP_ROOT/application/daily/app_INC_${STAMP}.tar.gz"
TMP_OUT="${OUT}.tmp"

# Trabajamos sobre una copia temporal del snapshot. El maestro remoto solo se
# reemplaza después de que el archivo llegue completo y pase gzip -t.
R_SNAP="$(q "$SNAP_REMOTE")"
R_TMP="$(q "$TMP_REMOTE")"
R_DIR="$(q "$APP_DIR")"
R_CMD="set -e; mkdir -p \"\$(dirname $R_SNAP)\"; if [ -f $R_SNAP ]; then cp $R_SNAP $R_TMP; else echo Falta_snapshot_de_aplicacion >&2; exit 5; fi; tar --listed-incremental=$R_TMP --exclude='./.git' --exclude='./.env' --exclude='./certs/server.key' -czf - -C $R_DIR ."
log "Inicio incremental de aplicación: $APP_DIR"
if ! "${ssh_base[@]}" "$REMOTE" "bash -c $(q "$R_CMD")" > "$TMP_OUT"; then
  rm -f "$TMP_OUT"
  "${ssh_base[@]}" "$REMOTE" "rm -f $R_TMP" || true
  log "Falló la transferencia; snapshot maestro conservado."
  exit 8
fi
if ! gzip -t "$TMP_OUT"; then
  rm -f "$TMP_OUT"
  "${ssh_base[@]}" "$REMOTE" "rm -f $R_TMP" || true
  log "Archivo incremental corrupto; snapshot maestro conservado."
  exit 9
fi
verify_and_publish "$TMP_OUT" "$OUT"
"${ssh_base[@]}" "$REMOTE" "mv $R_TMP $R_SNAP"
write_meta "$OUT" "kind=app" "type=inc" "chain=$CHAIN" "created=$STAMP"
log "Incremental aplicación verificado: $OUT"
