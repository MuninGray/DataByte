# Menú administrativo original adaptado a ESI (VM única)

Ejecución desde la raíz de DataByte:

```bash
sudo bash admin/admin_menu.sh
```

Se mantienen las 7 categorías del menú del paquete original. Los módulos originales de **usuarios/grupos** y **firewall** se conservan con sus opciones y validaciones. Deben probarse **solo con cuentas/puertos de prueba**; cambiar servicios, firewall o usuarios puede interrumpir el acceso al sistema.

Cambios imprescindibles: los accesos SSH a `192.168.1.50/51/52` se sustituyeron por el propio host Linux, `docker compose` y el servicio `db` de MySQL. No se instalan servicios `mysqld` ni backup externos. Se utiliza el respaldo SQL `.tar.gz` ya probado. Se incluyen alternativas de respaldo de archivos de la aplicación (completo e incremental) con GNU tar; **no equivalen a respaldo incremental de MySQL**, que requiere planificar binlogs/recuperación. Los incrementales de aplicación requieren primero generar una base incremental y conservar todos los incrementos en orden.

El apartado de restauración es **informativo**: no ejecuta cambios destructivos sobre el volumen MySQL ni sobre la aplicación activa. Los scripts originales de restauración se conservan en `admin/original` como documentación, no para ejecutarlos directamente en la arquitectura de una VM.

Este paquete solo sustituye `admin/admin_menu.sh`, agrega `admin/modulos/` y actualiza `admin/README.md`; no cambia el frontend, las APIs, el SQL, `docker-compose.yml`, ni los contenedores de DataByte. Requiere prueba funcional en Rocky Linux antes de usar las opciones que modifican el sistema.
