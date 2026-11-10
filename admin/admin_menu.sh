#!/usr/bin/env bash
set -Eeuo pipefail
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MOD="$BASE/modulos"
ROOT="$(cd "$BASE/.." && pwd)"
export DATABYTE_PROJECT_ROOT="$ROOT"
[[ -f "$ROOT/docker-compose.yml" ]] || { echo "No se encuentra docker-compose.yml en $ROOT" >&2; exit 1; }
pause(){ read -r -p $'\nPresione ENTER para continuar...' _ || true; }
while true; do
  echo
  echo '=================================================='
  echo '       PANEL DE ADMINISTRACIÓN - DATABYTE'
  echo '=================================================='
  echo '1. Gestión de usuarios y grupos Linux'
  echo '2. Estado de red e interfaces'
  echo '3. Firewall (consultar y administrar puertos)'
  echo '4. Servicios y contenedores Docker'
  echo '5. Logs del sistema'
  echo '6. Estado y chequeos de MySQL (Docker local)'
  echo '7. Respaldos y logs de Backup'
  echo '0. Salir'
  read -r -p 'Seleccione una opción: ' op || exit 0
  case "$op" in
    [1-7]) bash "$MOD/mod_$(case "$op" in 1) echo usuarios;; 2) echo red;; 3) echo firewall;; 4) echo servicios;; 5) echo logs;; 6) echo database;; 7) echo backup;; esac).sh" || echo '[Aviso] La operación terminó con error.'; pause ;;
    0) exit 0 ;;
    *) echo 'Opción inválida.' ;;
  esac
done
