#!/usr/bin/env bash
set -Eeuo pipefail
echo "--- ESTADO MYSQL LOCAL ---"
sudo systemctl is-active mysqld
sudo mysql -e "SELECT VERSION(), @@server_id, @@log_bin, @@binlog_format;"
sudo mysql -e "SHOW MASTER STATUS; SHOW BINARY LOGS;"
