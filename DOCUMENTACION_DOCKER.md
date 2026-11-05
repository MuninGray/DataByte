# Despliegue y administración de DataByte con Docker Compose

## 1. Objetivo

DataByte se ejecuta en cuatro contenedores administrados por Docker Compose. Apache sirve el frontend y actúa como proxy inverso; cada API PHP corre en su propio contenedor. La base MySQL existente permanece fuera de Docker, en la VM `192.168.1.51`.

La configuración evita instalar Apache o PHP directamente en la VM web. Estas dependencias forman parte de las imágenes Docker.

## 2. Servicios y responsabilidades

| Servicio Compose | Contenedor | Función | Puerto del contenedor |
|---|---|---|---:|
| `databyte-web` | `databyte-web` | Apache, frontend, HTTPS y proxy inverso | 80 y 443 |
| `api-usuarios` | `api-usuarios` | Apache + PHP para `API_Usuarios` | 80 interno |
| `api-gestion` | `api-gestion` | Apache + PHP para `API_Gestion` | 80 interno |
| `api-recoleccion` | `api-recoleccion` | Apache + PHP para `API_Recoleccion` | 80 interno |

Las tres imágenes de API parten de PHP 8.3 con Apache y habilitan la extensión MySQLi. `api-usuarios` también configura el directorio de sesiones PHP.

La imagen web copia las carpetas `Landing page/` y `assets/`, además de `mapa.html`, conservando las rutas relativas del frontend. Una solicitud a `/` redirige a la ubicación actual de la landing: `/Landing page/LandingPage/DataByte_LandingPage/LandingPage.html`.

## 3. Cómo circulan las solicitudes

El navegador se conecta a Apache en `databyte-web`. Apache sirve los archivos estáticos y dirige las rutas de API a los servicios Compose:

| Ruta que solicita el navegador | Servicio interno |
|---|---|
| `/API_Usuarios/public/` | `api-usuarios:80` |
| `/API_Gestion/public/` | `api-gestion:80` |
| `/API_Recoleccion/public/` | `api-recoleccion:80` |

Las reglas `ProxyPass` eliminan el prefijo público antes de enviar la solicitud. Por ejemplo, `/API_Usuarios/public/index.php?accion=login` llega al DocumentRoot de esa API como `/index.php?accion=login`. Se conservan el método HTTP y los parámetros de consulta que seleccionan las acciones.

Cada API configura `/var/www/html/public` como su `DocumentRoot`. Los puertos de las APIs no se publican en la VM; Apache de entrada recibe las conexiones externas y reenvía las solicitudes dentro de la red de Compose.

## 4. Puertos

El archivo `docker-compose.yml` publica únicamente estos puertos del servicio web:

| Puerto de la VM | Puerto del contenedor web | Uso |
|---:|---:|---|
| `80/TCP` | `80/TCP` | HTTP; Apache redirige a HTTPS |
| `443/TCP` | `443/TCP` | HTTPS para frontend y APIs |

Las APIs escuchan en el puerto 80 de sus contenedores y tienen `expose: 80` para documentar el acceso interno. No tienen una asignación `ports`, por lo que no publican puertos individuales en la VM.

MySQL no forma parte del Compose. Los contenedores PHP se conectan a `192.168.1.51:3306` por la red IP de las VMs. La VM de base de datos debe aceptar esa conexión desde DataByte-web y el usuario MySQL debe tener los permisos correspondientes.

## 5. Redes

Compose crea la red bridge `databyte-internal` y conecta los cuatro servicios. Apache resuelve las APIs por sus nombres de servicio (`api-usuarios`, `api-gestion` y `api-recoleccion`); no se fijan IPs de contenedor.

La red bridge conecta contenedores de esta aplicación en la VM web. No reemplaza la red privada entre VMs: para llegar a MySQL, las APIs usan la dirección externa de esa VM, `192.168.1.51`.

## 6. Volúmenes y persistencia

| Volumen/montaje | Servicio | Datos persistidos |
|---|---|---|
| `usuarios-sessions` | `api-usuarios` | Archivos de sesiones PHP en `/var/lib/php/sessions`; permite conservarlas al recrear el contenedor |
| `./certs:/etc/apache2/certs:Z` | `databyte-web` | Certificado y clave TLS en la VM. `:Z` solicita a Docker el etiquetado SELinux privado del montaje |

La aplicación no crea un contenedor ni un volumen MySQL: los datos siguen en la VM de base de datos. Los logs de Apache se escriben en los logs de los contenedores y se consultan con `docker compose logs`.

Si no existen `certs/server.crt` y `certs/server.key`, el entrypoint de Apache genera allí un certificado autofirmado para `192.168.1.50`. Esto cifra la conexión, pero los navegadores mostrarán una advertencia de confianza. Para un despliegue normal, se deben colocar en `certs/` un certificado confiable y su clave privada con esos nombres. Si solo existe uno de los dos archivos, el contenedor web no arranca.

La configuración cifra el tramo navegador–Apache. El proxy usa HTTP entre contenedores de la misma VM. El cifrado de la conexión entre las APIs y MySQL requiere, si se exige, habilitar TLS también en MySQL y configurarlo en el cliente PHP.

## 7. Variables de entorno

Compose lee las variables de sustitución desde un archivo `.env` junto al `docker-compose.yml`. Ese archivo debe mantenerse local en la VM de despliegue, fuera de GitHub. En el estado actual del repositorio no hay un `.env.example`, por lo que se crea `.env` manualmente con estos nombres:

```dotenv
DB_HOST=192.168.1.51
DB_PORT=3306
DB_NAME=DataByte
DB_USER=REEMPLAZAR_POR_EL_USUARIO_MYSQL
DB_PASSWORD=REEMPLAZAR_POR_LA_CONTRASENA_MYSQL
DATA_BYTE_ADMIN_SECRET=REEMPLAZAR_POR_EL_SECRETO_DE_ADMIN
```

Los valores `DB_USER`, `DB_PASSWORD` y `DATA_BYTE_ADMIN_SECRET` son obligatorios en Compose. `DB_HOST`, `DB_PORT` y `DB_NAME` tienen valores por defecto en el Compose, pero se muestran para que la configuración quede explícita. No se deben copiar contraseñas reales en este documento, Dockerfiles ni imágenes.

## 8. Despliegue inicial

Ejecutar en la VM DataByte-web, desde la carpeta que contiene `docker-compose.yml`:

1. Crear o editar `.env` con los valores de la VM y del usuario de aplicación MySQL. No sobrescribir un `.env` que ya exista.
2. Construir las imágenes y crear/iniciar los servicios:

```bash
docker compose build
docker compose up -d
```

3. Comprobar el estado y los logs:

```bash
docker compose ps
docker compose logs --tail=100
```

Al primer arranque, Docker debe poder descargar la imagen base PHP/Apache y construir las imágenes. No se ejecuta ningún dump SQL ni migración.

## 9. Administración habitual

```bash
# Ver el estado de los cuatro servicios
docker compose ps

# Seguir los logs de todos los servicios
docker compose logs -f

# Seguir los logs de una API o de Apache web
docker compose logs -f api-usuarios
docker compose logs -f databyte-web

# Reiniciar un servicio
docker compose restart api-gestion

# Reconstruir imágenes después de cambios de código o Dockerfile
docker compose up -d --build

# Detener y retirar contenedores y red del proyecto
docker compose down
```

`docker compose down` conserva los volúmenes nombrados. No agregar `-v` salvo que se quiera borrar también la persistencia de sesiones de Usuarios. El montaje `./certs` es una carpeta de la VM y no se elimina con `down`.

## 10. Comprobaciones de funcionamiento

```bash
# Los cuatro contenedores deben aparecer como activos
docker compose ps

# Confirmar que PHP tiene MySQLi en las tres APIs
docker compose exec api-usuarios php -m | grep -i mysqli
docker compose exec api-gestion php -m | grep -i mysqli
docker compose exec api-recoleccion php -m | grep -i mysqli

# Probar la landing; -k es necesario mientras se use el certificado autofirmado
curl -k -L -I https://192.168.1.50/

# Consultas GET para probar la ruta del proxy y la conexión con cada API
curl -k -sS -o /dev/null -w '%{http_code}\n' 'https://192.168.1.50/API_Usuarios/public/index.php?accion=usuarioActual'
curl -k -sS -o /dev/null -w '%{http_code}\n' 'https://192.168.1.50/API_Gestion/public/index.php?accion=municipios'
curl -k -sS -o /dev/null -w '%{http_code}\n' 'https://192.168.1.50/API_Recoleccion/public/index.php?accion=rutas'
```

Las tres últimas consultas son GET y no crean ni modifican registros. Un código HTTP por sí solo no garantiza que la consulta de base haya sido exitosa; si hay un error, revisar los logs de la API correspondiente y confirmar conectividad a `192.168.1.51:3306`.
