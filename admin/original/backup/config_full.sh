#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
require tar; require ssh; require sha256sum; require gzip
TS="$(date +%Y%m%d_%H%M%S)"
for NODE in "web:$WEB_IP" "db:$DB_IP"; do
  NAME="${NODE%%:*}"; IP="${NODE#*:}"
  DEST="$BACKUP_ROOT/configuration/$NAME"
  mkdir -p "$DEST"
  OUT="$DEST/${NAME}_config_FULL_${TS}.tar.gz"
  TMP="${OUT}.tmp"
  log "Captura de configuración no secreta: $NAME ($IP)"
  # No incluye /etc/shadow, claves privadas, perfiles con contraseñas ni .env.
  REMOTE_CMD="tar -czf - --ignore-failed-read /etc/NetworkManager/NetworkManager.conf /etc/NetworkManager/system-connections/README /etc/ssh/sshd_config /etc/ssh/sshd_config.d /etc/firewalld /etc/systemd/system /etc/fstab /etc/hosts /etc/hostname /etc/passwd /etc/group"
  "${ssh_base[@]}" "$SSH_USER@$IP" "$REMOTE_CMD" > "$TMP"
  gzip -t "$TMP"
  verify_and_publish "$TMP" "$OUT"
done
log "Configuraciones capturadas. Secretos excluidos deliberadamente."
