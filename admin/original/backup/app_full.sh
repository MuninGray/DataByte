#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
require ssh; require gzip; require sha256sum
STAMP="$(date +%Y%m%d_%H%M%S)"
CHAIN="$(chain_id)"
REMOTE="$SSH_USER@$WEB_IP"
SNAP_REMOTE="${APP_SNAPSHOT_REMOTE:-/home/lore/.cache/databyte-app.snar}"
TMP_REMOTE="${SNAP_REMOTE}.new.$$"
OUT="$BACKUP_ROOT/application/weekly/app_FULL_${STAMP}.tar.gz"
TMP_OUT="${OUT}.tmp"
R_SNAP="$(q "$SNAP_REMOTE")"; R_TMP="$(q "$TMP_REMOTE")"; R_DIR="$(q "$APP_DIR")"
# Se inicia desde cero una nueva cadena, preservando el snapshot anterior ante fallas.
R_CMD="set -e; mkdir -p \"\$(dirname $R_SNAP)\"; rm -f $R_TMP; tar --listed-incremental=$R_TMP --exclude='./.git' --exclude='./.env' --exclude='./certs/server.key' -czf - -C $R_DIR ."
trap 'rm -f "$TMP_OUT"; "${ssh_base[@]}" "$REMOTE" "rm -f $R_TMP" >/dev/null 2>&1 || true' EXIT
log "FULL semanal aplicación: $APP_DIR"
"${ssh_base[@]}" "$REMOTE" "bash -c $(q "$R_CMD")" > "$TMP_OUT"
verify_and_publish "$TMP_OUT" "$OUT"
# Actualizar snapshot solo después de publicar un respaldo verificado.
"${ssh_base[@]}" "$REMOTE" "mv -- $R_TMP $R_SNAP"
write_meta "$OUT" "kind=app" "type=full" "chain=$CHAIN" "created=$STAMP"
printf '%s\n' "$CHAIN" > "$BACKUP_ROOT/application/.current_chain.tmp"
mv "$BACKUP_ROOT/application/.current_chain.tmp" "$BACKUP_ROOT/application/current_chain"
trap - EXIT
log "FULL aplicación publicado: $OUT"
