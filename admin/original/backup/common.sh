#!/usr/bin/env bash
set -Eeuo pipefail
CONF="${DATABYTE_BACKUP_CONF:-/etc/databyte-backup.conf}"
[[ -r "$CONF" ]] || { echo "Falta configuración: $CONF" >&2; exit 2; }
# shellcheck disable=SC1090
source "$CONF"
: "${WEB_IP:?}" "${DB_IP:?}" "${SSH_USER:?}" "${SSH_KEY:?}" "${APP_DIR:?}"
: "${DB_NAME:?}" "${MYSQL_LOGIN_PATH:?}" "${BACKUP_ROOT:?}"
umask 077
mkdir -p "$BACKUP_ROOT"/{database/{daily,weekly,monthly,binlogs},application/{daily,weekly},configuration/{web,db},checksums,logs,scripts,restore}
LOG="$BACKUP_ROOT/logs/backup.log"
log(){ printf '[%s] %s\n' "$(date '+%F %T')" "$*" | tee -a "$LOG"; }
require(){ command -v "$1" >/dev/null 2>&1 || { log "Falta comando: $1"; exit 3; }; }
ssh_base=(ssh -i "$SSH_KEY" -o BatchMode=yes -o ConnectTimeout=10 -o StrictHostKeyChecking=yes)
mysql_cmd=(mysql --login-path="$MYSQL_LOGIN_PATH" --host="$DB_IP")
q(){ printf '%q' "$1"; }

# Evita reutilizar nombres de archivos en ejecuciones concurrentes.
verify_and_publish() {
  local tmp="$1" out="$2"
  gzip -t "$tmp" || { rm -f "$tmp"; return 1; }
  [[ ! -e "$out" && ! -e "$out.sha256" ]] || { echo "Destino existente: $out" >&2; return 1; }
  mv -- "$tmp" "$out"
  (cd "$(dirname "$out")" && sha256sum "$(basename "$out")" > "$(basename "$out").sha256")
}

# Metadatos de cadena: campos controlados, sin evaluar archivos como shell.
chain_id(){ date -u '+%Y%m%dT%H%M%SZ'; }
write_meta(){
  local file="$1"; shift
  { printf '%s\n' "$@"; } > "${file}.meta.tmp"
  mv -- "${file}.meta.tmp" "${file}.meta"
  chmod 0600 "${file}.meta"
}
meta_value(){
  local key="$1" file="$2"
  [[ -r "$file" ]] || return 1
  sed -n "s/^${key}=//p" "$file" | head -n 1
}
