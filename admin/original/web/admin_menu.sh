#!/usr/bin/env bash
set -Eeuo pipefail
BASE="/usr/local/lib/databyte-web"
MOD="$BASE/modulos"
pause(){ read -r -p $'\nPresione ENTER para continuar...'; }
while true; do
  clear 2>/dev/null || true
  echo "=================================================="
  echo "       PANEL DE ADMINISTRACIÓN - DATABYTE"
  echo "=================================================="
  echo "1. Gestión de usuarios y grupos Linux"
  echo "2. Estado de red e interfaces"
  echo "3. Firewall (consultar y administrar puertos)"
  echo "4. Servicios y contenedores Docker"
  echo "5. Logs del sistema"
  echo "6. Estado y chequeos de MySQL (remoto)"
  echo "7. Respaldos y logs de Backup"
  echo "0. Salir"
  read -r -p "Seleccione una opción: " op
  case "$op" in
    1) bash "$MOD/mod_usuarios.sh"; pause ;;
    2) bash "$MOD/mod_red.sh"; pause ;;
    3) bash "$MOD/mod_firewall.sh"; pause ;;
    4) bash "$MOD/mod_servicios.sh"; pause ;;
    5) bash "$MOD/mod_logs.sh"; pause ;;
    6) bash "$MOD/mod_database.sh"; pause ;;
    7) bash "$MOD/mod_backup.sh"; pause ;;
    0) exit 0 ;;
    *) echo "Opción inválida."; sleep 1 ;;
  esac
done
