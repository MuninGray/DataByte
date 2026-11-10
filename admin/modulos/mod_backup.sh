#!/usr/bin/env bash
set -Eeuo pipefail
cd "$DATABYTE_PROJECT_ROOT"
mkdir -p respaldos
umask 077
confirm(){ local a; read -r -p "$1 [s/N]: " a; [[ "$a" =~ ^([sS]|[sS][iI])$ ]]; }
archive_app(){
 local mode="$1" stamp dest temp state
 stamp="$(date -u +%Y%m%dT%H%M%SZ)"
 dest="$DATABYTE_PROJECT_ROOT/respaldos/databyte-app-${mode}-${stamp}.tar.gz"
 # App backup intentionally omits .env, secrets, Git internals, and database backups.
 if [[ "$mode" == full ]]; then
   tar -C "$DATABYTE_PROJECT_ROOT" -czf "$dest" \
     --exclude='./.git' --exclude='./.env' --exclude='./respaldos' \
     --exclude='./secrets' .
 else
   state="$DATABYTE_PROJECT_ROOT/respaldos/.app-incremental.snar"
   [[ -f "$state" ]] || { echo 'Primero es necesario generar una base incremental'; return 1; }
   temp="$(mktemp "$DATABYTE_PROJECT_ROOT/respaldos/.app-state.XXXXXX")"
   cp "$state" "$temp"
   if tar -C "$DATABYTE_PROJECT_ROOT" --listed-incremental="$temp" -czf "$dest" \
     --exclude='./.git' --exclude='./.env' --exclude='./respaldos' \
     --exclude='./secrets' .; then
     mv "$temp" "$state"
   else
     rm -f "$temp" "$dest"; return 1
   fi
 fi
 tar -tzf "$dest" >/dev/null
 echo "Respaldo aplicación creado: $dest"
}
init_incremental(){
 local state dest stamp
 state="$DATABYTE_PROJECT_ROOT/respaldos/.app-incremental.snar"
 [[ ! -f "$state" ]] || { echo 'Ya existe un ciclo incremental; no se sobrescribe'; return 1; }
 stamp="$(date -u +%Y%m%dT%H%M%SZ)"; dest="$DATABYTE_PROJECT_ROOT/respaldos/databyte-app-base-${stamp}.tar.gz"
 tar -C "$DATABYTE_PROJECT_ROOT" --listed-incremental="$state" -czf "$dest" \
   --exclude='./.git' --exclude='./.env' --exclude='./respaldos' --exclude='./secrets' .
 tar -tzf "$dest" >/dev/null
 echo "Base de ciclo incremental creada: $dest"
}
while true; do
  echo; echo '--- RESPALDOS Y LOGS DE DATABYTE (VM ÚNICA) ---'
  echo '1. Ejecutar respaldo completo de MySQL (.tar.gz)'
  echo '2. Respaldo completo de aplicación (.tar.gz)'
  echo '3. Crear base para incrementales de aplicación'
  echo '4. Respaldo incremental de aplicación'
  echo '5. Ver últimos archivos de respaldo'
  echo '6. Ver uso del repositorio de respaldos'
  echo '7. Verificar integridad de respaldos .tar.gz'
  echo '8. Restauración: instrucciones de prueba (no modifica producción)'
  echo '0. Volver'
  read -r -p 'Opción: ' op || exit 0
  case "$op" in
    1) confirm '¿Exportar la base MySQL ahora?' && docker compose --profile backup run --rm backup || true ;;
    2) confirm '¿Respaldar código y configuraciones no secretas?' && archive_app full || true ;;
    3) confirm '¿Crear ciclo incremental desde cero?' && init_incremental || true ;;
    4) confirm '¿Generar incremento de aplicación?' && archive_app incremental || true ;;
    5) find respaldos -maxdepth 1 -type f -name '*.tar.gz' -printf '%TY-%Tm-%Td %TH:%TM %f (%s bytes)\n' | sort -r | head -20 ;;
    6) du -sh respaldos; df -h . ;;
    7) found=0; while IFS= read -r -d '' f; do found=1; printf 'Revisando %s: ' "$(basename "$f")"; if gzip -t "$f" && tar -tzf "$f" >/dev/null; then echo OK; else echo ERROR; fi; done < <(find respaldos -maxdepth 1 -type f -name '*.tar.gz' -print0); (( found )) || echo 'No hay archivos tar.gz'; ;;
    8) echo 'No se restaura automáticamente sobre MySQL ni sobre archivos de la VM.'
       echo 'Para ensayar restauración, usar una base/VM de prueba separada y validar allí el SQL.'
       echo 'Los scripts de restauración antiguos están conservados en admin/original/ y NO deben ejecutarse en esta VM.' ;;
    0) exit 0 ;;
    *) echo 'Opción inválida' ;;
  esac
done
