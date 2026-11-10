#!/usr/bin/env bash
set -Eeuo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
sudo install -d -o root -g root -m 0755 /usr/local/lib/databyte-db/modulos
sudo install -o root -g root -m 0750 "$ROOT"/db/modulos/*.sh /usr/local/lib/databyte-db/modulos/
echo "Utilidades DB instaladas. No se modificó MySQL ni firewalld."
