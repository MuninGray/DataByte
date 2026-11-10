#!/usr/bin/env bash
set -Eeuo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
id lore >/dev/null 2>&1 || { echo "No existe el usuario lore."; exit 1; }
getent group wheel >/dev/null || { echo "No existe el grupo wheel."; exit 1; }
sudo install -d -o root -g wheel -m 0750 /usr/local/lib/databyte-web/modulos
sudo install -o root -g root -m 0755 "$ROOT/web/admin_menu.sh" /usr/local/bin/admin_menu.sh
sudo install -o root -g wheel -m 0750 "$ROOT"/web/modulos/*.sh /usr/local/lib/databyte-web/modulos/
sudo install -d -o root -g wheel -m 0750 /usr/local/etc/databyte
if [[ ! -f /usr/local/etc/databyte/hosts.conf ]]; then
  sudo install -o root -g wheel -m 0640 "$ROOT/config/hosts.conf.example" /usr/local/etc/databyte/hosts.conf
  echo "Editá /usr/local/etc/databyte/hosts.conf antes de usar el menú."
else
  echo "Se conserva la configuración existente /usr/local/etc/databyte/hosts.conf."
fi
echo "Instalación Web finalizada. Ejecutar: admin_menu.sh"
