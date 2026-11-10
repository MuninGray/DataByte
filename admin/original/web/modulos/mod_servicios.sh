#!/usr/bin/env bash
set -Eeuo pipefail
confirm(){ local a; read -r -p "$1 [s/N]: " a; [[ "$a" == [sS] || "$a" == [sS][iI] ]]; }
while true; do
  echo
  echo "--- SERVICIOS Y CONTENEDORES ---"
  echo "1. Estado de servicios"
  echo "2. Iniciar / detener / reiniciar servicio permitido"
  echo "3. Ver contenedores Docker"
  echo "4. Acciones Docker Compose (DataByte)"
  echo "0. Volver"
  read -r -p "Opción: " op
  case "$op" in
    1)
      for s in docker firewalld sshd mysqld; do
        printf '%-12s ' "$s"; systemctl is-active "$s" 2>/dev/null || true
      done ;;
    2)
      echo "Servicios permitidos: docker firewalld sshd"
      read -r -p "Servicio: " s
      case "$s" in docker|firewalld|sshd) ;; *) echo "Servicio no permitido."; continue ;; esac
      echo "1. start  2. stop  3. restart"; read -r -p "Acción: " a
      case "$a" in 1) action=start ;; 2) action=stop ;; 3) action=restart ;; *) echo "Opción inválida."; continue ;; esac
      if [[ "$s" == sshd && "$action" != start ]]; then
        echo "No se permite detener/reiniciar SSH desde el menú remoto."
        continue
      fi
      confirm "$action $s?" && sudo systemctl "$action" "$s" ;;
    3)
      command -v docker >/dev/null && docker ps -a --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}' || echo "Docker no disponible." ;;
    4)
      compose="/home/lore/DataByte/docker-compose.yml"
      [[ -f "$compose" ]] || { echo "No se encuentra $compose."; continue; }
      echo "1. ps  2. up -d  3. stop  4. restart"; read -r -p "Acción: " a
      case "$a" in
        1) docker compose -f "$compose" ps ;;
        2) confirm "Iniciar/actualizar stack DataByte?" && docker compose -f "$compose" up -d ;;
        3) confirm "Detener stack DataByte?" && docker compose -f "$compose" stop ;;
        4) confirm "Reiniciar stack DataByte?" && docker compose -f "$compose" restart ;;
        *) echo "Opción inválida." ;;
      esac ;;
    0) exit 0 ;;
    *) echo "Opción inválida." ;;
  esac
done
