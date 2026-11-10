#!/usr/bin/env bash
set -Eeuo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
id lore >/dev/null 2>&1 || { echo "No existe el usuario lore en Backup."; exit 1; }
sudo install -d -o root -g root -m 0755 /usr/local/lib/databyte-backup
sudo install -d -o lore -g lore -m 0700 /var/backups/databyte
sudo install -d -o root -g lore -m 0750 /usr/local/lib/databyte-backup
sudo install -o root -g lore -m 0750 "$ROOT"/backup/*.sh /usr/local/lib/databyte-backup/
sudo install -o root -g lore -m 0750 "$ROOT/backup/backup_run" /usr/local/sbin/databyte-backup-run
if [[ ! -f /etc/databyte-backup.conf ]]; then
  sudo install -o root -g lore -m 0640 "$ROOT/backup/databyte-backup.conf.example" /etc/databyte-backup.conf
  echo "Revisá /etc/databyte-backup.conf; debe ser legible por lore y contener valores correctos."
fi
sudo chown root:lore /etc/databyte-backup.conf
sudo chmod 0640 /etc/databyte-backup.conf
echo "Instalación de scripts lista. Cron debe instalarse en el crontab de lore (no root)."
