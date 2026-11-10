#!/usr/bin/env bash
set -Eeuo pipefail
valid_user(){ [[ "$1" =~ ^[a-z_][a-z0-9_-]*[$]?$ ]]; }
valid_group(){ [[ "$1" =~ ^[a-z_][a-z0-9_-]*$ ]]; }
protected_user(){ [[ "$1" == "root" || "$1" == "lore" || "$(id -u "$1" 2>/dev/null || echo 0)" -lt 1000 ]]; }
protected_group(){ [[ "$1" == "root" || "$1" == "wheel" || "$1" == "docker" || "$1" == "lore" || "$(getent group "$1" | cut -d: -f3)" -lt 1000 ]]; }
confirm(){ local answer; read -r -p "$1 [s/N]: " answer; [[ "$answer" == [sS] || "$answer" == [sS][iI] ]]; }
while true; do
  echo
  echo "--- GESTIÓN DE USUARIOS Y GRUPOS LINUX ---"
  echo "1. Listar usuarios locales"
  echo "2. Listar grupos locales"
  echo "3. Consultar usuario y grupos"
  echo "4. Crear usuario"
  echo "5. Modificar shell de usuario"
  echo "6. Bloquear / desbloquear usuario"
  echo "7. Eliminar usuario"
  echo "8. Crear grupo"
  echo "9. Eliminar grupo"
  echo "10. Agregar usuario a grupo suplementario"
  echo "11. Quitar usuario de grupo suplementario"
  echo "12. Ver administradores (wheel)"
  echo "0. Volver"
  read -r -p "Opción: " op
  case "$op" in
    1) getent passwd | awk -F: '$3>=1000 && $3<60000 {printf "%-18s UID=%-6s HOME=%s\n",$1,$3,$6}' ;;
    2) getent group | awk -F: '{printf "%-22s GID=%s\n",$1,$3}' ;;
    3)
      read -r -p "Usuario: " u
      if id "$u" 2>/dev/null; then passwd -S "$u" 2>/dev/null || true; else echo "Usuario inexistente."; fi ;;
    4)
      read -r -p "Nuevo usuario: " u
      valid_user "$u" || { echo "Nombre no válido."; continue; }
      id "$u" >/dev/null 2>&1 && { echo "El usuario ya existe."; continue; }
      sudo useradd -m -s /bin/bash "$u" && {
        echo "Usuario creado."
        echo "Definí su contraseña con: sudo passwd $u"
      } ;;
    5)
      read -r -p "Usuario: " u
      id "$u" >/dev/null 2>&1 || { echo "Usuario inexistente."; continue; }
      protected_user "$u" && { echo "Cuenta protegida"; continue; }
      read -r -p "Shell (/bin/bash, /bin/sh, /sbin/nologin): " sh
      case "$sh" in /bin/bash|/bin/sh|/sbin/nologin) sudo usermod -s "$sh" "$u" ;; *) echo "Shell no permitido por este módulo." ;; esac ;;
    6)
      read -r -p "Usuario: " u
      id "$u" >/dev/null 2>&1 || { echo "Usuario inexistente."; continue; }
      ! protected_user "$u" || { echo "Por seguridad no se modifica root ni lore desde este menú."; continue; }
      echo "1. Bloquear  2. Desbloquear"; read -r -p "Acción: " a
      case "$a" in
        1) confirm "¿Bloquear $u?" && sudo passwd -l "$u" ;;
        2) confirm "¿Desbloquear $u?" && sudo passwd -u "$u" ;;
        *) echo "Opción inválida." ;;
      esac ;;
    7)
      read -r -p "Usuario a eliminar: " u
      valid_user "$u" || { echo "Nombre no válido."; continue; }
      id "$u" >/dev/null 2>&1 || { echo "Usuario inexistente."; continue; }
      ! protected_user "$u" || { echo "Por seguridad no se elimina root ni lore."; continue; }
      echo "Se eliminará la cuenta y se conservará su directorio HOME."
      confirm "¿Eliminar $u?" && sudo userdel "$u" ;;
    8)
      read -r -p "Nuevo grupo: " g
      valid_group "$g" || { echo "Nombre no válido."; continue; }
      getent group "$g" >/dev/null && { echo "El grupo ya existe."; continue; }
      sudo groupadd "$g" ;;
    9)
      read -r -p "Grupo a eliminar: " g
      valid_group "$g" || { echo "Nombre no válido."; continue; }
      getent group "$g" >/dev/null || { echo "Grupo inexistente."; continue; }
      ! protected_group "$g" || { echo "Grupo protegido."; continue; }
      confirm "¿Eliminar el grupo $g?" && sudo groupdel "$g" ;;
    10|11)
      read -r -p "Usuario: " u
      id "$u" >/dev/null 2>&1 || { echo "Usuario inexistente."; continue; }
      read -r -p "Grupo: " g
      getent group "$g" >/dev/null || { echo "Grupo inexistente."; continue; }
      if protected_user "$u" || protected_group "$g"; then echo "Usuario o grupo protegido"; continue; fi
      if [[ "$op" == 10 ]]; then sudo usermod -aG "$g" "$u"
      else sudo gpasswd -d "$u" "$g"; fi ;;
    12) getent group wheel || true ;;
    0) exit 0 ;;
    *) echo "Opción inválida." ;;
  esac
done
