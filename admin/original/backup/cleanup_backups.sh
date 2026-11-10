#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
# Nunca realizar borrado sin solicitud directa. Conserva como mínimo las dos últimas cadenas.
MODE="${1:---preview}"
[[ "$MODE" == --preview || "$MODE" == --apply ]] || { echo "Uso: $0 [--preview|--apply]" >&2; exit 2; }
if [[ "$MODE" == --apply && "${DATABYTE_ENABLE_EXPERIMENTAL_CLEANUP:-}" != YES ]]; then echo "Borrado bloqueado hasta validar restauración en Rocky Linux" >&2; exit 78; fi
if [[ "$MODE" == --apply && "${2:-}" != --confirm-delete ]]; then echo "Requiere --apply --confirm-delete" >&2; exit 2; fi
RETAIN="${RETENTION_WEEKLY_DAYS:-30}"
[[ "$RETAIN" =~ ^[0-9]+$ && "$RETAIN" -ge 7 ]] || { echo "RETENTION_WEEKLY_DAYS inválido" >&2; exit 2; }
for area in database application; do
  dir="$BACKUP_ROOT/$area"
  mapfile -t fulls < <(find "$dir/weekly" -maxdepth 1 -type f -name '*FULL*.gz' -printf '%f\n' | sort -r)
  ((${#fulls[@]} > 2)) || continue
  for full in "${fulls[@]:2}"; do
    f="$dir/weekly/$full"
    [[ -f "$f.meta" && -f "$f.sha256" ]] || { log "Sin metadatos de FULL: conservado $f"; continue; }
    cid="$(meta_value chain "$f.meta")"
    [[ "$cid" =~ ^[0-9]{8}T[0-9]{6}Z$ ]] || continue
    # Conservar la cadena si el FULL no excede la antigüedad autorizada.
    [[ -n "$(find "$f" -mtime +"$RETAIN" -print -quit)" ]] || continue
    files=("$f")
    unsafe=0
    while IFS= read -r -d '' inc; do
      [[ -f "$inc.meta" && -f "$inc.sha256" ]] || { unsafe=1; break; }
      expected_kind=app; [[ "$area" == database ]] && expected_kind=db
      [[ "$(meta_value kind "$inc.meta")" == "$expected_kind" ]] || { unsafe=1; break; }
      files+=("$inc")
    done < <(find "$dir/daily" -maxdepth 1 -type f -name '*INC*.gz' -print0 | sort -z)
    # Seleccionar solo los incrementales de esta cadena; presencia de metadatos
    # desconocidos en el repositorio impide la eliminación por prudencia.
    ((unsafe==0)) || { log "Se conservan archivos: incrementales sin metadatos"; continue; }
    selected=("$f")
    for inc in "${files[@]:1}"; do
      [[ "$(meta_value chain "$inc.meta")" == "$cid" ]] && selected+=("$inc")
    done
    # Una cadena activa jamás se elimina.
    current="$(cat "$dir/binlogs/current_chain" 2>/dev/null || cat "$dir/current_chain" 2>/dev/null || true)"
    [[ "$cid" != "$current" ]] || continue
    for item in "${selected[@]}"; do
      [[ -f "$item" && -f "$item.meta" && -f "$item.sha256" ]] || { unsafe=1; break; }
      (cd "$(dirname "$item")" && sha256sum -c "$(basename "$item").sha256") >/dev/null || { unsafe=1; break; }
    done
    ((unsafe==0)) || { log "Cadena con archivos incompletos: conservada $cid"; continue; }
    if [[ "$MODE" == --preview ]]; then
      printf 'CANDIDATA cadena=%s area=%s archivos=%s\n' "$cid" "$area" "${#selected[@]}"
    else
      for item in "${selected[@]}"; do rm -- "$item" "$item.meta" "$item.sha256"; done
      log "Eliminada cadena $cid ($area)"
    fi
  done
done
# Configuraciones, scripts y archivos desconocidos se conservan.
