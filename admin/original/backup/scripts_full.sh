#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
require tar; require sha256sum
TS="$(date +%Y%m%d_%H%M%S)"
DEST="$BACKUP_ROOT/scripts"
mkdir -p "$DEST"
OUT="$DEST/databyte_scripts_FULL_${TS}.tar.gz"
TMP="${OUT}.tmp"
tar -czf "$TMP" -C "$(dirname "$HERE")" "$(basename "$HERE")"
gzip -t "$TMP"
verify_and_publish "$TMP" "$OUT"
log "Respaldo completo de scripts generado: $OUT"
