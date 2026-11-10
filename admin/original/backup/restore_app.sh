#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
usage(){ echo "Uso: $0 --verify-only FULL.tar.gz [INC.tar.gz ...] | --to-empty-dir DIR FULL.tar.gz [INC.tar.gz ...]" >&2; exit 2; }
(($# >= 2)) || usage
MODE="$1"; shift
case "$MODE" in --verify-only) DEST="" ;; --to-empty-dir) (($# >= 2)) || usage; DEST="$1"; shift ;; *) usage ;; esac
(($# >= 1)) || usage
FILES=("$@")
FULL_META="${FILES[0]}.meta"
[[ "$(meta_value type "$FULL_META")" == full && "$(meta_value kind "$FULL_META")" == app ]] || { echo "Falta FULL con metadatos" >&2; exit 3; }
CHAIN="$(meta_value chain "$FULL_META")"
[[ "$CHAIN" =~ ^[0-9]{8}T[0-9]{6}Z$ ]] || exit 3
previous=""
for i in "${!FILES[@]}"; do
  f="${FILES[$i]}"; [[ -f "$f" && -f "$f.sha256" && -f "$f.meta" ]] || { echo "Falta archivo, sha256 o meta: $f" >&2; exit 3; }
  [[ "$(meta_value kind "$f.meta")" == app && "$(meta_value chain "$f.meta")" == "$CHAIN" ]] || { echo "Cadena distinta: $f" >&2; exit 4; }
  expected=inc; ((i==0)) && expected=full
  [[ "$(meta_value type "$f.meta")" == "$expected" ]] || { echo "Orden inválido: $f" >&2; exit 4; }
  stamp="$(meta_value created "$f.meta")"
  [[ "$stamp" =~ ^[0-9]{8}_[0-9]{6}$ && ( -z "$previous" || "$stamp" > "$previous" ) ]] || { echo "Orden temporal inválido" >&2; exit 4; }
  previous="$stamp"
  (cd "$(dirname "$f")" && sha256sum -c "$(basename "$f").sha256") >/dev/null
  gzip -t "$f"
  # Rechazar rutas absolutas/traversal antes de extraer.
  if tar -tzf "$f" | grep -E '(^/|(^|/)\.\.(/|$))' >/dev/null; then echo "Ruta insegura en $f" >&2; exit 5; fi
done
if [[ -z "$DEST" ]]; then echo "Cadena aplicación verificada (${#FILES[@]} archivos). No se restauró nada."; exit 0; fi
[[ -d "$DEST" && -z "$(find "$DEST" -mindepth 1 -maxdepth 1 -print -quit)" ]] || { echo "Destino debe existir y estar vacío" >&2; exit 6; }
[[ "$(realpath "$DEST")" != / && "$(realpath "$DEST")" != "$(realpath "$APP_DIR" 2>/dev/null || echo /invalid)" ]] || { echo "Destino prohibido" >&2; exit 6; }
for f in "${FILES[@]}"; do tar --listed-incremental=/dev/null -xzf "$f" -C "$DEST" --no-same-owner --no-same-permissions; done
echo "Aplicación reconstruida en $DEST; revisar manualmente antes de usar."
