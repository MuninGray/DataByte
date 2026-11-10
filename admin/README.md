# Panel administrativo — adaptación ESI 2026

- `admin_menu.sh` es una adaptación **limitada** para una sola VM. Se ejecuta en el host Linux, dentro del repositorio, con `bash admin/admin_menu.sh`.
- Incluye **consultas** de usuarios, red, firewall, logs y MySQL, acciones confirmadas de Compose y respaldo `.tar.gz`.
- Los originales se conservan en `original/` **solo para auditoría**. **No ejecutar sus instaladores** ni los scripts de backup original: están destinados a tres servidores, SSH y rutas específicas, y no han sido probados en la VM nueva.
- La gestión completa de usuarios y la modificación de firewall del menú antiguo **no se han portado**. Las consultas pueden requerir permisos apropiados. El menú no reemplaza la administración formal del sistema.
- No se ha ejecutado una prueba integral de Docker/MySQL ni una restauración real: validar antes de la instalación evaluada.
