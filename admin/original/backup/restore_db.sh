#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
[[ "${1:-}" == --verify-only && $# -ge 2 ]] || { echo "Uso: $0 --verify-only FULL.sql.gz [INC.sql.gz ...]" >&2; exit 2; }
shift
FILES=("$@")
F="${FILES[0]}"
[[ "$(meta_value kind "$F.meta")" == db && "$(meta_value type "$F.meta")" == full ]] || { echo "FULL DB no reconocido" >&2; exit 3; }
CHAIN="$(meta_value chain "$F.meta")"
[[ "$CHAIN" =~ ^[0-9]{8}T[0-9]{6}Z$ ]] || exit 3
prev_file="$(meta_value end_file "$F.meta")"
prev_pos="$(meta_value end_pos "$F.meta")"
[[ "$prev_file" =~ ^[A-Za-z0-9._-]+$ && "$prev_pos" =~ ^[0-9]+$ ]] || exit 3
previous=""
for i in "${!FILES[@]}"; do
 f="${FILES[$i]}"; [[ -f "$f" && -f "$f.sha256" && -f "$f.meta" ]] || { echo "Archivo incompleto: $f" >&2; exit 3; }
 [[ "$(meta_value kind "$f.meta")" == db && "$(meta_value chain "$f.meta")" == "$CHAIN" ]] || { echo "Cadena incorrecta $f" >&2; exit 4; }
 expected=inc; ((i==0)) && expected=full
 [[ "$(meta_value type "$f.meta")" == "$expected" ]] || exit 4
 stamp="$(meta_value created "$f.meta")"
 [[ "$stamp" =~ ^[0-9]{8}_[0-9]{6}$ && ( -z "$previous" || "$stamp" > "$previous" ) ]] || exit 4
 previous="$stamp"
 (cd "$(dirname "$f")" && sha256sum -c "$(basename "$f").sha256") >/dev/null
 gzip -t "$f"
 if ((i > 0)); then
   [[ "$(meta_value start_file "$f.meta")" == "$prev_file" && "$(meta_value start_pos "$f.meta")" == "$prev_pos" ]] || { echo "Hueco entre binlogs: $f" >&2; exit 5; }
   prev_file="$(meta_value end_file "$f.meta")"; prev_pos="$(meta_value end_pos "$f.meta")"
   [[ "$prev_file" =~ ^[A-Za-z0-9._-]+$ && "$prev_pos" =~ ^[0-9]+$ ]] || exit 5
 fi
done
echo "Cadena DB verificada (${#FILES[@]} archivos): checkpoints continuos y checksums correctos. NO se restauró MySQL."
echo "Para validar recuperación, importar FULL e incrementales EN ORDEN en una instancia MySQL AISLADA, con credenciales de recuperación separadas."
