#!/bin/sh
set -eu

cert_dir="/etc/apache2/certs"
cert_file="${cert_dir}/server.crt"
key_file="${cert_dir}/server.key"

mkdir -p "$cert_dir"

if [ ! -e "$cert_file" ] && [ ! -e "$key_file" ]; then
    echo "No se encontraron certificados TLS; se genera un certificado autofirmado para 192.168.1.50." >&2
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout "$key_file" \
        -out "$cert_file" \
        -subj "/CN=192.168.1.50" \
        -addext "subjectAltName=IP:192.168.1.50"
    chmod 0600 "$key_file"
elif [ ! -s "$cert_file" ] || [ ! -s "$key_file" ]; then
    echo "Se requieren ambos archivos: server.crt y server.key en ${cert_dir}." >&2
    exit 1
fi

exec apache2-foreground
