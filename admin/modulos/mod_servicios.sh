#!/usr/bin/env bash
set -Eeuo pipefail
confirm(){ local a; read -r -p "$1 [s/N]: " a; [[ "$a" =~ ^([sS]|[sS][iI])$ ]]; }
cd "$DATABYTE_PROJECT_ROOT"
while true; do
  echo; echo '--- SERVICIOS Y CONTENEDORES ---'
  echo '1. Estado de servicios'
  echo '2. Iniciar / detener / reiniciar servicio permitido'
  echo '3. Ver contenedores Docker'
  echo '4. Acciones Docker Compose (DataByte)'
  echo '0. Volver'
  read -r -p 'Opción: ' op || exit 0
  case "$op" in
    1) for s in docker firewalld sshd; do printf '%-12s ' "$s"; systemctl is-active "$s" 2>/dev/null || true; done
       echo 'MySQL funciona dentro del contenedor db:'; docker compose ps db ;;
    2) echo 'Servicios permitidos: docker firewalld sshd'; read -r -p 'Servicio: ' s
       case "$s" in docker|firewalld|sshd) ;; *) echo 'Servicio no permitido'; continue ;; esac
       echo '1. start  2. stop  3. restart'; read -r -p 'Acción: ' a
       case "$a" in 1) action=start;; 2) action=stop;; 3) action=restart;; *) echo 'Opción inválida'; continue;; esac
       [[ "$s" != sshd || "$action" == start ]] || { echo 'No se detiene/reinicia SSH desde este menú'; continue; }
       confirm "$action $s?" && systemctl "$action" "$s" || true ;;
    3) docker ps -a --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}' ;;
    4) echo '1. ps  2. up -d  3. stop  4. restart'; read -r -p 'Acción: ' a
       case "$a" in
         1) docker compose ps -a ;;
         2) confirm 'Iniciar/actualizar DataByte?' && docker compose up -d || true ;;
         3) confirm 'Detener DataByte?' && docker compose stop || true ;;
         4) confirm 'Reiniciar DataByte?' && docker compose restart || true ;;
         *) echo 'Opción inválida' ;;
       esac ;;
    0) exit 0 ;;
    *) echo 'Opción inválida' ;;
  esac
done
