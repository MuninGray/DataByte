#!/usr/bin/env bash
# Panel de consulta para la instalación ESI de una sola VM.
# No realiza cambios en cuentas Linux ni firewall.
set -Eeuo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"
# Require intended project, avoid operating on an unrelated Compose file.
[[ -f docker-compose.yml ]] || { echo 'No se encontró docker-compose.yml'; exit 1; }
pause() { read -r -p $'\nENTER para continuar...' _ || true; }
confirm() { local a; read -r -p "$1 [s/N]: " a; [[ "$a" =~ ^([sS]|[sS][iI])$ ]]; }
while true; do
  printf '\n=== PANEL DATABYTE - ESI (UNA VM) ===\n'
  printf '%s\n' \
    '1. Usuarios y grupos Linux (consulta)' \
    '2. Red e interfaces (consulta)' \
    '3. Firewall (consulta)' \
    '4. Servicios y contenedores Docker' \
    '5. Logs del sistema y Docker' \
    '6. Estado y tablas de MySQL 8' \
    '7. Respaldos .tar.gz' \
    '0. Salir'
  read -r -p 'Opción: ' op || exit 0
  case "$op" in
    1) echo '-- Usuarios locales --'; getent passwd | awk -F: '$3>=1000 && $3<60000 {print $1, "UID=" $3}'
       echo '-- Grupo wheel --'; getent group wheel || true; pause ;;
    2) ip -br addr; ip route; echo '-- Redes Docker --'; docker network ls; pause ;;
    3) if command -v firewall-cmd >/dev/null; then
         firewall-cmd --state || true; firewall-cmd --list-all || true
       else echo 'firewall-cmd no disponible'; fi; pause ;;
    4) echo '1) Ver estado  2) Iniciar  3) Detener  4) Reiniciar  0) Volver'
       read -r -p 'Acción: ' a
       case "$a" in
         1) docker compose ps -a ;;
         2) confirm '¿Iniciar servicios?' && docker compose up -d ;;
         3) confirm '¿Detener servicios?' && docker compose stop ;;
         4) confirm '¿Reiniciar servicios?' && docker compose restart ;;
       esac; pause ;;
    5) echo '1) Docker Compose  2) Docker Engine  3) Sistema'
       read -r -p 'Origen: ' a
       case "$a" in
         1) docker compose logs --tail=80 ;;
         2) journalctl -u docker -n 80 --no-pager || true ;;
         3) journalctl -n 80 --no-pager || true ;;
       esac; pause ;;
    6) echo '-- Versión --'
       docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot -e "SELECT VERSION(); SHOW DATABASES;"'
       echo '-- Tablas --'
       docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot -e "SHOW TABLES FROM DataByte;"'
       pause ;;
    7) echo '1) Generar respaldo  2) Listar respaldos  3) Verificar tar.gz  0) Volver'
       read -r -p 'Acción: ' a
       case "$a" in
         1) confirm '¿Generar respaldo SQL comprimido?' && docker compose --profile backup run --rm backup ;;
         2) find respaldos -maxdepth 1 -type f -name '*.tar.gz' -printf '%f (%s bytes)\n' | sort ;;
         3) find respaldos -maxdepth 1 -type f -name '*.tar.gz' -print0 | while IFS= read -r -d '' f; do
              echo "Validando: $f"; gzip -t "$f" && tar -tzf "$f"; done ;;
       esac; pause ;;
    0) exit 0 ;;
    *) echo 'Opción inválida.' ;;
  esac
done
