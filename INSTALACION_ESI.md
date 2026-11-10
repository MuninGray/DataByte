# DataByte / SiGeRU — Instalación de evaluación ESI 2026

## Alcance
Una sola máquina Linux con Docker Engine y Docker Compose. Servicios de web (Apache/PHP 8.3), tres APIs PHP, MySQL 8 y respaldo `.tar.gz` bajo demanda, comunicados mediante la red de Compose.

**Esta es una propuesta de instalación que todavía NO fue ejecutada ni validada en Docker.** El equipo debe verificar los pasos y demostrar las operaciones CRUD. Se conserva el código original; incluye un menú administrativo ESI de funciones limitadas en `admin/admin_menu.sh`; los scripts originales se conservan para revisión en `admin/original/` y no deben instalarse ni ejecutarse directamente.

## 1. Preparación (en Linux)

```bash
docker --version
docker compose version
# Clonar el repositorio o copiar el paquete a la VM
cd DataByte-ESI-instalacion
cp .env.example .env
chmod 600 .env
# Editar .env y sustituir TODOS los valores CAMBIAR_* por secretos reales, privados
nano .env
```

No subir `.env` a GitHub ni compartirlo en capturas. Las credenciales de usuarios de prueba están incluidas parcialmente en el SQL; confirmar acceso y no emplearlas para producción.

## 2. Inspección y despliegue

```bash
docker compose config --quiet
docker compose build
docker compose up -d
docker compose ps -a
docker compose logs --tail=60 db
docker compose logs --tail=60 api-usuarios
```

La primera creación del volumen `mysql-data` ejecuta `dump/DataByte-1-1.sql`. **No se vuelve a importar automáticamente cuando el volumen ya tiene datos**. El SQL original contiene `DROP DATABASE IF EXISTS`; jamás volver a correrlo sobre datos reales sin un respaldo probado.

## 3. Verificaciones

```bash
# Revisar servicios y tablas
docker compose ps -a
docker compose exec db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot -e "SHOW TABLES FROM DataByte"'
# Desde la VM
curl -IL http://127.0.0.1:8080/
# Cabeceras HTTP: no garantizan por sí solas que las APIs funcionen
curl -i 'http://127.0.0.1:8080/API_Gestion/public/index.php?accion=municipios'
```

Abrir `http://IP_DE_LA_VM:8080` en el navegador que use la evaluación (o `http://127.0.0.1:8080` dentro de la VM). Si la VM está en NAT y se quiere acceso desde el equipo anfitrión, agregar en VirtualBox un reenvío TCP puerto anfitrión 8080 → puerto invitado 8080. Probar login, alta, consulta, modificación y baja con datos de prueba.

## 4. Respaldo .tar.gz

```bash
mkdir -p respaldos
docker compose --profile backup run --rm backup
ls -lh respaldos/*.tar.gz
tar -tzf respaldos/databyte-*.tar.gz
```

El respaldo incluye `databyte.sql` y es una copia lógica de la BD. Probar restauración en una instancia separada antes de darlo por validado. El volumen `mysql-data` mantiene los datos cuando se reinician servicios. No garantiza persistencia frente a `docker compose down -v`.

## 5. Comprobación de persistencia

Crear un registro de prueba desde la aplicación. Después:

```bash
docker compose restart
docker compose ps -a
```

Consultar nuevamente el registro desde la interfaz. Una vez validado, apagar y encender la VM y repetir. **No ejecutar** `docker compose down -v`, `docker volume prune` ni `docker system prune --volumes`.

## Limitaciones / pendientes

- Docker no estaba disponible en el entorno de análisis; no fue posible compilar imágenes, importar SQL, comprobar login ni probar el respaldo de verdad.
- Se usa HTTP en puerto 8080 para pruebas en una VM local, sin TLS. No es un despliegue para redes públicas o producción.
- El SQL original crea y llena solo una cantidad pequeña de filas; verificar si los datos de prueba son suficientes para el CRUD. No se generaron registros adicionales.
- La arquitectura cumple conceptualmente el comunicado, pero el grupo debe validar recursos, puertos, operaciones y aceptar cualquier criterio adicional de los docentes.
- La documentación original `DOCUMENTACION_DOCKER.md` describe la instalación antigua de tres VMs y no reemplaza esta guía.

## 6. Panel administrativo (ESI, una VM)

```bash
bash admin/admin_menu.sh
```

El menú adaptado puede consultar Docker, MySQL y respaldos. No modifica automáticamente usuarios ni firewall. Los scripts originales, almacenados bajo `admin/original/`, requieren auditoría separada antes de usarlos.
