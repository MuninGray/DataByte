#!/usr/bin/env bash
set -Eeuo pipefail
confirm(){ local a; read -r -p "$1 [s/N]: " a; [[ "$a" == [sS] || "$a" == [sS][iI] ]]; }
command -v firewall-cmd >/dev/null 2>&1 || { echo "firewalld no está instalado."; exit 1; }
sudo -v
while true; do
  echo
  echo "--- FIREWALLD ---"
  echo "1. Ver zonas activas y reglas runtime"
  echo "2. Ver reglas permanentes"
  echo "3. Abrir puerto TCP (runtime + permanente)"
  echo "4. Cerrar puerto TCP (runtime + permanente)"
  echo "0. Volver"
  read -r -p "Opción: " op
  case "$op" in
    1) sudo firewall-cmd --get-active-zones; sudo firewall-cmd --list-all ;;
    2) sudo firewall-cmd --permanent --list-all ;;
    3|4)
      read -r -p "Puerto TCP (1-65535): " port
      [[ "$port" =~ ^[0-9]{1,5}$ ]] && ((port>=1 && port<=65535)) || { echo "Puerto inválido."; continue; }
      sudo firewall-cmd --get-active-zones
      read -r -p "Zona activa a modificar (escribir nombre): " zone
      [[ "$zone" =~ ^[A-Za-z0-9_-]+$ ]] || { echo "Zona inválida"; continue; }
      sudo firewall-cmd --get-active-zones | grep -Eq "^[[:space:]]*$zone$" || { echo "Zona no activa. Cancelado."; continue; }
      if [[ "$op" == 3 ]]; then
        confirm "Abrir ${port}/tcp en zona $zone?" || continue
        sudo firewall-cmd --zone="$zone" --add-port="${port}/tcp" &&
          sudo firewall-cmd --permanent --zone="$zone" --add-port="${port}/tcp"
      else
        confirm "Cerrar ${port}/tcp en zona $zone?" || continue
        sudo firewall-cmd --zone="$zone" --remove-port="${port}/tcp" &&
          sudo firewall-cmd --permanent --zone="$zone" --remove-port="${port}/tcp"
      fi
      ;;
    0) exit 0 ;;
    *) echo "Opción inválida." ;;
  esac
done
