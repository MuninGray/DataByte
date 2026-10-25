(function () {
    const traducciones = {
        'Idioma': 'Language',
        'Panel': 'Dashboard',
        'Panel | Administrador': 'Panel | Administrator',
        'Panel | Inspector municipal': 'Panel | Municipal inspector',
        'Panel | Operario de cuadrilla': 'Panel | Crew operator',
        'Panel del inspector municipal': 'Municipal inspector dashboard',
        'Panel del operario de cuadrilla': 'Crew operator dashboard',
        'Panel de administración': 'Administration dashboard',
        'Panel de control': 'Dashboard',
        'Desde aquí el administrador gestiona usuarios, cuadrillas, vehículos, rutas, incidencias, municipios, establecimientos, maquinaria y contenedores.': 'Administrators can manage users, crews, vehicles, routes, incidents, municipalities, facilities, machinery, and containers here.',
        'Podés consultar contenedores de tu municipio, rutas, cuadrillas y establecimientos, crear incidencias y actualizar su estado. No tenés acceso a usuarios ni a la configuración del sistema.': 'View containers in your municipality, routes, crews, and facilities, create incidents, and update their status. You cannot access users or system settings.',
        'Podés consultar la ruta asignada, los contenedores a recorrer y las incidencias de tu cuadrilla, actualizar el estado de una incidencia y registrar el recorrido realizado. No tenés acceso a la creación de usuarios ni a la edición de datos maestros (rutas, vehículos, contenedores, establecimientos, cuadrillas).': 'View your assigned route, containers to visit, and your crew’s incidents; update incident status and record completed routes. You cannot create users or edit core records (routes, vehicles, containers, facilities, or crews).',
        'Mapa interactivo': 'Interactive map',
        'Usuarios': 'Users',
        'Cuadrillas': 'Crews',
        'Vehículos': 'Vehicles',
        'Municipios': 'Municipalities',
        'Servicios y mantenimientos': 'Services and maintenance',
        'Ruta asignada': 'Assigned route',
        'Contenedores a recorrer': 'Containers to visit',
        'Incidencias de mi cuadrilla': 'My crew’s incidents',
        'Registrar recorrido': 'Record route',
        'Registrar recorrido realizado': 'Record completed route',
        'Crear incidencia': 'Create incident',
        'Crear nuevo administrador técnico': 'Create a new technical administrator',
        'Filtrar': 'Filter',
        'Ver todos': 'Show all',
        'Ver todas': 'Show all',
        'Recargar': 'Refresh',
        'Actualizar': 'Update',
        'Asignar': 'Assign',
        'Crear': 'Create',
        'Guardar': 'Save',
        'Eliminar': 'Delete',
        'Editar': 'Edit',
        'Acciones': 'Actions',
        'Código de municipio': 'Municipality code',
        'ID Contenedor': 'Container ID',
        'ID Incidencia': 'Incident ID',
        'ID Ruta': 'Route ID',
        'ID Establecimiento': 'Facility ID',
        'Estado operativo': 'Operational status',
        'Fecha apertura': 'Opening date',
        'Fecha resolución': 'Resolution date',
        'Cédula inspector': 'Inspector ID',
        'Nombre de cuadrilla': 'Crew name',
        'No hay datos para mostrar.': 'There is no data to display.',
        'Operario de establecimiento': 'Facility operator',
        'Inspector municipal': 'Municipal inspector',
        'Operario de cuadrilla': 'Crew operator',
        'Establecimientos': 'Facilities',
        'Contenedores': 'Containers',
        'Incidencias': 'Incidents',
        'Rutas': 'Routes',
        'Maquinaria': 'Machinery',
        'Panel del operario de establecimiento': 'Facility operator dashboard',
        'Capacidad del establecimiento': 'Facility capacity',
        'Capacidad de los establecimientos': 'Facility capacities',
        'Volumen recolectado': 'Collected volume',
        'Código': 'Code',
        'Calle': 'Street',
        'Número': 'Number',
        'Esquina': 'Cross street',
        'Latitud': 'Latitude',
        'Longitud': 'Longitude',
        'Capacidad actual': 'Current capacity',
        'Capacidad máxima': 'Maximum capacity',
        'Tipo de residuo': 'Waste type',
        'Tipo residuo': 'Waste type',
        'Cuadrilla asignada': 'Assigned crew',
        'Matrícula vehículo': 'Vehicle license plate',
        'ID Servicio': 'Service ID',
        'ID Maquinaria': 'Machinery ID',
        'ID Municipio': 'Municipality ID',
        'Estado': 'Status',
        'Fecha': 'Date',
        'Fecha resolución': 'Resolution date',
        'Fecha apertura': 'Opening date',
        'En uso': 'In use',
        'Disponible': 'Available',
        'ocupado': 'occupied',
        'libre': 'available',
        'activo': 'active',
        'inactivo': 'inactive',
        'funcional': 'functional',
        'en_reparacion': 'under repair',
        'fuera_de_servicio': 'out of service',
        'roto': 'broken',
        'desbordado': 'overflowing',
        'desaparecido': 'missing',
        'vertedero': 'landfill',
        'centro de acopio': 'collection center',
        'desplegado': 'deployed',
        'en_stock': 'in stock',
        'en_curso': 'in progress',
        'finalizado': 'completed',
        'pendiente': 'pending',
        'preventivo': 'preventive',
        'correctivo': 'corrective',
        'funcional': 'functional',
        'fuera de servicio': 'out of service',
        'abierto': 'open',
        'cerrado': 'closed',
        'reparacion': 'repair',
        'mantenimiento': 'maintenance',
        'Seleccioná una opción': 'Select an option',
        'Elegir ubicación en el mapa': 'Choose location on the map',
        'Registros': 'Records',
        'No filtra por municipio, así que se trae toda la lista y se filtra acá por código de municipio.': 'The server does not filter by municipality, so the full list is loaded and filtered here by municipality code.',
        'Datos simulados': 'Simulated data',
        'Sin datos': 'No data',
        'Sin datos suficientes para identificar zonas críticas.': 'Not enough data to identify critical areas.',
        'No hay establecimientos registrados.': 'There are no registered facilities.',
        'Capacidad completa o sin máximo informado': 'At capacity or no maximum provided',
        'Escenario +2%/día:': 'Scenario +2%/day:',
        'días': 'days',
        'No disponible': 'Not available',
        'Actualizando…': 'Updating…',
        'Sin pesos registrados': 'No weights recorded',
        'sin pesos registrados': 'no weights recorded',
        'sin estado': 'no status',
        'sin fecha': 'no date',
        'kg registrados (sin serie temporal)': 'kg recorded (no time series)',
        'No hay incidencias registradas.': 'There are no registered incidents.',
        'Falta completar un campo obligatorio.': 'A required field is missing.',
        'Ingresá un ID de contenedor válido.': 'Enter a valid container ID.',
        'Completá tipo, estado y fecha de apertura.': 'Enter the type, status, and opening date.',
        'La fecha de apertura no puede ser anterior al día de hoy.': 'The opening date cannot be earlier than today.',
        'Ingresá latitud y longitud juntas.': 'Enter both latitude and longitude.',
        'Ingresá latitud y longitud juntas, o dejá ambas vacías.': 'Enter both latitude and longitude, or leave both empty.',
        'La latitud debe estar entre -90 y 90.': 'Latitude must be between -90 and 90.',
        'La longitud debe estar entre -180 y 180.': 'Longitude must be between -180 and 180.',
        'La capacidad actual no puede superar la capacidad máxima.': 'Current capacity cannot exceed maximum capacity.',
        'Completá los campos obligatorios.': 'Complete all required fields.',
        'Completá la ruta, la matrícula y la fecha.': 'Enter the route, license plate, and date.',
        'La fecha no puede ser anterior al día de hoy.': 'The date cannot be earlier than today.',
        'Ingresá un ID de establecimiento entero y mayor a cero.': 'Enter a whole facility ID greater than zero.',
        'Ingresá una hora válida.': 'Enter a valid time.',
        'El peso debe ser mayor a cero.': 'Weight must be greater than zero.',
        'No se pudo crear la incidencia': 'Could not create the incident.',
        'Incidencia creada correctamente': 'Incident created successfully.',
        'No se pudo actualizar el estado': 'Could not update the status.',
        'Estado actualizado correctamente': 'Status updated successfully.',
        'No se pudo registrar el recorrido': 'Could not record the route.',
        'Recorrido registrado correctamente': 'Route recorded successfully.',
        'Registro creado correctamente': 'Record created successfully.',
        'Registro actualizado correctamente': 'Record updated successfully.',
        'Registro eliminado correctamente': 'Record deleted successfully.',
        'No hay registros.': 'There are no records.',
        'Sin registros.': 'No records found.',
        'No hay contenedores para mostrar.': 'There are no containers to display.',
        'No hay incidencias para mostrar.': 'There are no incidents to display.',
        'No hay rutas para mostrar.': 'There are no routes to display.',
        'No hay asignaciones para mostrar.': 'There are no assignments to display.',
        'No hay recorridos registrados.': 'There are no recorded routes.',
        'Sin rutas.': 'No routes found.',
        'Sin contenedores.': 'No containers found.',
        'Sin incidencias.': 'No incidents found.',
        'Sin asignaciones.': 'No assignments found.',
        'Sin recorridos registrados.': 'No routes recorded.',
        'Filtrar por ID de establecimiento': 'Filter by facility ID',
        'Filtrar por ID Establecimiento': 'Filter by facility ID',
        'Ver toda': 'Show all machinery',
        'Ver todos': 'Show all',
        'Ver todas': 'Show all',
        'Datos incompletos': 'Some required data is missing or invalid.',
        'CI': 'National ID',
        'Contraseña': 'Password',
        'Contenedores de mi municipio': 'Containers in my municipality',
        'Incidencias existentes': 'Existing incidents',
        'No se creó porque ya existe un registro con esos datos.': 'It was not created because a record with that data already exists.',
        'No hay descargas registradas.': 'There are no registered deliveries.',
        'No hay maquinaria disponible.': 'There is no machinery available.',
        'No hay establecimientos para mostrar.': 'There are no facilities to display.',
        'No hay maquinaria para mostrar.': 'There is no machinery to display.',
        'Sin maquinaria.': 'No machinery found.',
        'Sin establecimientos.': 'No facilities found.',
        'Sin descargas.': 'No deliveries found.',
        'No se encontró el contenedor.': 'Container not found.',
        'No hay usuario autenticado. Volvé al login.': 'No authenticated user. Return to the login page.',
        'Completá los campos obligatorios: ': 'Complete the required fields: ',
        'La fecha no puede ser anterior al día de hoy.': 'The date cannot be earlier than today.',
        'Las coordenadas están fuera de rango. Latitud: -90 a 90; longitud: -180 a 180.': 'Coordinates are out of range. Latitude: -90 to 90; longitude: -180 to 180.',
        'Iniciá sesión nuevamente.': 'Log in again.',
        'Rechazar': 'Reject',
        'Aprobar': 'Approve',
        'Pendiente': 'Pending',
        'Aprobado': 'Approved',
        'Rechazado': 'Rejected',
        'activo': 'active',
        'inactivo': 'inactive',
        'Ingresá un ID de contenedor válido.': 'Enter a valid container ID.',
        'Ingresá el ID del contenedor para cargar sus coordenadas.': 'Enter the container ID to load its coordinates.',
        'La matrícula debe tener 3 letras y 4 números (ejemplo: ABC1234).': 'The license plate must have 3 letters and 4 numbers (example: ABC1234).',
        'Formato: 3 letras y 4 números, por ejemplo SBC1234': 'Format: 3 letters and 4 numbers, for example SBC1234',
        'El municipio no existe': 'The municipality does not exist.',
        'Contenedor creado exitosamente': 'Container created successfully.',
        'Contenedor no encontrado': 'Container not found.',
        'Contenedores no encontrados': 'No containers found.',
        'Contenedor actualizado exitosamente': 'Container updated successfully.',
        'Contenedor eliminado exitosamente': 'Container deleted successfully.',
        'Establecimiento creado exitosamente': 'Facility created successfully.',
        'Establecimiento no encontrado': 'Facility not found.',
        'Establecimientos no encontrados': 'No facilities found.',
        'Establecimiento actualizado exitosamente': 'Facility updated successfully.',
        'Establecimiento eliminado exitosamente': 'Facility deleted successfully.',
        'Servicio de mantenimiento creado exitosamente': 'Maintenance service created successfully.',
        'Servicio de mantenimiento no encontrado': 'Maintenance service not found.',
        'Servicios de mantenimiento no encontrados': 'No maintenance services found.',
        'La fecha no puede ser anterior al día de hoy.': 'The date cannot be earlier than today.',
        'Servicio de mantenimiento actualizado exitosamente': 'Maintenance service updated successfully.',
        'Servicio de mantenimiento eliminado exitosamente': 'Maintenance service deleted successfully.',
        'El establecimiento no existe': 'The facility does not exist.',
        'El servicio de mantenimiento no existe': 'The maintenance service does not exist.',
        'Maquinaria creada exitosamente': 'Machinery created successfully.',
        'Maquinaria no encontrada': 'Machinery not found.',
        'Maquinarias no encontradas': 'No machinery found.',
        'Maquinaria actualizada exitosamente': 'Machinery updated successfully.',
        'Maquinaria eliminada exitosamente': 'Machinery deleted successfully.',
        'Municipio creado exitosamente': 'Municipality created successfully.',
        'Municipio no encontrado': 'Municipality not found.',
        'Municipios no encontrados': 'No municipalities found.',
        'Municipio actualizado exitosamente': 'Municipality updated successfully.',
        'Municipio eliminado exitosamente': 'Municipality deleted successfully.',
        'Cuadrilla creada exitosamente': 'Crew created successfully.',
        'Cuadrillas no encontradas': 'No crews found.',
        'Cuadrilla actualizada exitosamente': 'Crew updated successfully.',
        'Cuadrilla eliminada exitosamente': 'Crew deleted successfully.',
        'Nombre de cuadrilla requerido': 'Crew name is required.',
        'Descarga creada exitosamente': 'Delivery created successfully.',
        'Descargas no encontradas': 'No deliveries found.',
        'Descarga actualizada exitosamente': 'Delivery updated successfully.',
        'Descarga eliminada exitosamente': 'Delivery deleted successfully.',
        'Incidencia creada exitosamente': 'Incident created successfully.',
        'Incidencias no encontradas': 'No incidents found.',
        'Elegí un estado válido: abierta, en curso o resuelta.': 'Choose a valid status: open, in progress, or resolved.',
        'Las fechas nuevas no pueden ser anteriores al día de hoy.': 'New dates cannot be earlier than today.',
        'Incidencia actualizada exitosamente': 'Incident updated successfully.',
        'Incidencia eliminada exitosamente': 'Incident deleted successfully.',
        'ID de incidencia y código de contenedor requeridos': 'Incident ID and container code are required.',
        'Falta completar nombre y matrícula.': 'Enter the name and license plate.',
        'Ya existe una ruta con ese nombre y la misma matrícula.': 'A route with that name and license plate already exists.',
        'La matrícula ingresada no pertenece a un vehículo registrado.': 'The entered license plate does not belong to a registered vehicle.',
        'Ruta creada exitosamente': 'Route created successfully.',
        'Rutas no encontradas': 'No routes found.',
        'Falta completar el ID de la ruta, el nombre y la matrícula.': 'Enter the route ID, name, and license plate.',
        'Ya existe otra ruta con ese nombre y la misma matrícula.': 'Another route with that name and license plate already exists.',
        'Ruta actualizada exitosamente': 'Route updated successfully.',
        'Ruta eliminada exitosamente': 'Route deleted successfully.',
        'ID Ruta requerido': 'Route ID is required.',
        'La ruta no existe': 'The route does not exist.',
        'El vehículo no existe': 'The vehicle does not exist.',
        'Asignación creada exitosamente': 'Assignment created successfully.',
        'Asignación no creada': 'Assignment was not created.',
        'No se pudieron obtener las asignaciones': 'Could not retrieve assignments.',
        'No se encontraron asignaciones': 'No assignments found.',
        'No se pudo eliminar la asignación': 'Could not delete the assignment.',
        'Asignación eliminada exitosamente': 'Assignment deleted successfully.',
        'La asignación no existe': 'The assignment does not exist.',
        'ID Ruta, matrícula y fecha requeridos': 'Route ID, license plate, and date are required.',
        'No se puede crear el vehículo porque ya existe una matrícula registrada.': 'The vehicle cannot be created because that license plate is already registered.',
        'Vehículo creado exitosamente': 'Vehicle created successfully.',
        'Vehículos no encontrados': 'No vehicles found.',
        'Vehículo actualizado exitosamente': 'Vehicle updated successfully.',
        'Vehículo eliminado exitosamente': 'Vehicle deleted successfully.',
        'Matrícula requerida': 'License plate is required.',
        'La creación de administradores está protegida y requiere un secreto válido.': 'Administrator creation is protected and requires a valid secret.',
        'Para crear un administrador debes enviar cedula_admin válida.': 'A valid cedula_admin is required to create an administrator.',
        'Ya existe un usuario con esa cédula o ese correo.': 'A user with that ID or email already exists.',
        'El nombre y el apellido deben tener al menos 2 caracteres cada uno.': 'First and last names must each contain at least 2 characters.',
        'El correo ingresado no es válido': 'The entered email address is invalid.',
        'La contraseña debe tener al menos 6 caracteres': 'The password must contain at least 6 characters.',
        'Las contraseñas no coinciden': 'The passwords do not match.',
        'Usuario creado exitosamente': 'User created successfully.',
        'La creación de administradores requiere el secreto del servidor.': 'Administrator creation requires the server secret.',
        'Solo un administrador autenticado puede crear otro administrador.': 'Only an authenticated administrator can create another administrator.',
        'No se pudo preparar la creación del administrador técnico.': 'Could not prepare technical administrator creation.',
        'No se pudo guardar el administrador técnico.': 'Could not save the technical administrator.',
        'No se pudo crear el usuario administrador.': 'Could not create the administrator account.',
        'Administrador técnico creado correctamente': 'Technical administrator created successfully.',
        'No se encontraron usuarios': 'No users found.',
        'correo': 'email',
        'nombre': 'first name',
        'apellido': 'last name',
        'contraseña': 'password',
        'confirmación de contraseña': 'password confirmation',
        'secreto del servidor': 'server secret',
        'CI': 'National ID',
        'Usuario actualizado exitosamente': 'User updated successfully.',
        'Usuario eliminado exitosamente': 'User deleted successfully.',
        'Ingresá un correo válido, nombre y apellido de hasta 50 caracteres.': 'Enter a valid email address, first name, and last name of up to 50 characters.',
        'Para cambiar la contraseña ingresá la actual y una nueva de 8 a 72 caracteres, repetida correctamente.': 'To change the password, enter the current one and confirm a new password of 8 to 72 characters.',
        'No se pudo verificar la contraseña actual.': 'Could not verify the current password.',
        'La cédula y la contraseña son obligatorias.': 'National ID and password are required.',
        'La contraseña debe tener al menos 6 caracteres.': 'The password must contain at least 6 characters.',
        'Rol no válido. Elegí un rol permitido.': 'Invalid role. Choose an allowed role.',
        'No se recibió una cédula válida para el usuario seleccionado.': 'No valid national ID was received for the selected user.',
        'No se pudo comprobar el usuario seleccionado.': 'Could not verify the selected user.',
        'El usuario seleccionado ya no existe.': 'The selected user no longer exists.',
        'Para asignar el rol de inspector es obligatorio indicar el código del municipio.': 'A municipality code is required to assign the inspector role.',
        'Para asignar este rol es obligatorio indicar la cuadrilla.': 'A crew is required to assign this role.',
        'Para este rol debe indicarse un ID de establecimiento válido.': 'A valid facility ID is required for this role.',
        'No se pudo asignar el rol. Revisá los datos relacionados.': 'Could not assign the role. Check the related data.',
        'Usuario rechazado correctamente': 'User rejected successfully.',
        'No se pudo rechazar el usuario': 'Could not reject the user.',
        'Acción no válida': 'Invalid action.',
        'Método no permitido': 'Method not allowed.',
        'No se creó porque ya existe un registro con esos datos.': 'The record was not created because one with that information already exists.',
        'Falta completar nombre y matrícula.': 'The name and license plate are required.',
        'Ya existe una ruta con ese nombre y la misma matrícula.': 'A route with that name and license plate already exists.',
        'La matrícula ingresada no pertenece a un vehículo registrado.': 'The license plate does not belong to a registered vehicle.',
        'Las fechas nuevas no pueden ser anteriores al día de hoy.': 'New dates cannot be earlier than today.',
        'ID de incidencia y código de contenedor requeridos': 'Incident ID and container code are required.',
        'El correo ingresado no es válido': 'The entered email address is invalid.',
        'No se pudo verificar la contraseña actual.': 'Could not verify the current password.',
        'No se pudo guardar el administrador técnico.': 'Could not save the technical administrator.',
        'No se pudo crear el usuario administrador.': 'Could not create the administrator account.',
        'No se encontraron usuarios': 'No users found.',
        'No se recibió una cédula válida para el usuario seleccionado.': 'No valid national ID was received for the selected user.',
        'El usuario seleccionado ya no existe.': 'The selected user no longer exists.',
        'Para asignar el rol de inspector es obligatorio indicar el código del municipio.': 'A municipality code is required to assign the inspector role.',
        'Para asignar este rol es obligatorio indicar la cuadrilla.': 'A crew is required to assign this role.',
        'Para este rol debe indicarse un ID de establecimiento válido.': 'A valid facility ID is required for this role.',
        'No se pudo asignar el rol. Revisá los datos relacionados.': 'Could not assign the role. Check the related data.',
        'No se pudieron obtener las asignaciones': 'Could not retrieve assignments.',
        'No se encontraron asignaciones': 'No assignments found.',
        'No se pudo eliminar la asignación': 'Could not delete the assignment.',
        'La asignación no existe': 'The assignment does not exist.',
        'La fecha no puede ser anterior al día de hoy.': 'The date cannot be earlier than today.',
        'Gráfico semanal de volumen simulado': 'Simulated weekly volume chart',
        'Entero (kg)': 'Integer (kg)',
        'Datos no disponibles': 'Data unavailable',
        'Usuario no encontrado': 'User not found.',
        'Contraseña incorrecta': 'Incorrect password.',
        'La cuenta aún no fue aprobada por un administrador.': 'The account has not yet been approved by an administrator.',
        'La solicitud fue rechazada por el administrador.': 'The request was rejected by the administrator.',
        'Login correcto': 'Login successful.',
        'Faltan datos obligatorios: ': 'Required information is missing: ',
        'Usuario autenticado': 'User authenticated.',
        'Sesión cerrada': 'Session closed.',
        'Una vista rápida de los recursos y la actividad registrados.': 'A quick overview of registered resources and activity.',
        'Cargando datos…': 'Loading data…',
        'Abrir mapa': 'Open map',
        'registrados en el sistema': 'registered in the system',
        'Incidencias abiertas': 'Open incidents',
        'incluye casos en curso': 'includes in progress',
        'rutas configuradas': 'configured routes',
        'centros y vertederos': 'facilities and landfills',
        'RESUMEN OPERATIVO': 'OPERATIONAL SUMMARY',
        'SEGUIMIENTO': 'TRACKING',
        'Estado de incidencias': 'Incident status',
        '— total': '— total',
        'resueltas': 'resolved',
        'Abiertas': 'Open',
        'En curso': 'In progress',
        'Resueltas': 'Resolved',
        'Incidencias recientes': 'Recent incidents',
        'Cargando incidencias…': 'Loading incidents…',
        'RECOLECCIÓN': 'COLLECTION',
        'Volumen recolectado': 'Collected volume',
        'Datos simulados': 'Simulated data',
        'kg': 'kg',
        'estimados esta semana': 'estimated this week',
        'Lun': 'Mon',
        'Mar': 'Tue',
        'Mié': 'Wed',
        'Jue': 'Thu',
        'Vie': 'Fri',
        'Sáb': 'Sat',
        'Dom': 'Sun',
        'Estimación demostrativa; el sistema no registra el peso por fecha para calcular una serie histórica real.': 'Demonstration estimate; the system does not record dated weights to calculate a real historical series.',
        'ZONAS CRÍTICAS': 'CRITICAL AREAS',
        'Más incidencias': 'Most incidents',
        'Cargando zonas…': 'Loading areas…',
        'Agrupadas por municipio según el código de los contenedores asociados.': 'Grouped by municipality using the codes of associated containers.',
        'CAPACIDAD': 'CAPACITY',
        'Centros y vertederos': 'Facilities and landfills',
        'Cargando capacidad…': 'Loading capacity…',
        'Se muestra capacidad, tipo de residuo y un escenario ilustrativo de llenado. La proyección no es un pronóstico operativo: requiere un historial de mediciones.': 'Capacity, waste type, and an illustrative fill scenario are shown. This projection is not an operational forecast; it requires a history of measurements.',
        'INDICADORES': 'INDICATORS',
        'Operación y reportes': 'Operations and reports',
        'Tiempo medio de resolución': 'Average resolution time',
        'calculado con incidencias resueltas': 'calculated from resolved incidents',
        'Frecuencia de recolección': 'Collection frequency',
        'requiere fechas por recolección': 'requires dates for each collection',
        'Registros de descarga': 'Delivery records',
        'consultando datos registrados': 'based on recorded data',
        'La frecuencia es una referencia estimada; las descargas actuales no guardan fecha, solo hora. La clasificación de residuos se resume en el detalle de capacidad cuando hay tipos informados.': 'Frequency is an estimate; current deliveries store a time but not a date. Waste classification appears in the capacity details when types are available.',
        'Usuarios registrados': 'Registered users',
        'Recargar usuarios': 'Refresh users',
        'Correo': 'Email',
        'Confirmar contraseña': 'Confirm password',
        'Cédula del admin responsable': 'Responsible administrator ID',
        'Secreto del servidor': 'Server secret',
        'Crear administrador': 'Create administrator',
        'Alta, baja, modificación y consulta de cuadrillas de recolección.': 'Create, delete, update, and view collection crews.',
        'Alta, baja, modificación y consulta de vehículos.': 'Create, delete, update, and view vehicles.',
        'Alta, baja, modificación y consulta de rutas.': 'Create, delete, update, and view routes.',
        'Asignar ruta a vehículo': 'Assign a route to a vehicle',
        'Asignaciones existentes': 'Existing assignments',
        'Recargar asignaciones': 'Refresh assignments',
        'Alta, baja, modificación y consulta de incidencias reportadas sobre contenedores.': 'Create, delete, update, and view incidents reported for containers.',
        'Alta, baja y consulta de municipios (el código es la clave del municipio).': 'Create, delete, and view municipalities (the code is the municipality key).',
        'Alta, baja, modificación y consulta de establecimientos.': 'Create, delete, update, and view facilities.',
        'Alta, baja, modificación y consulta de maquinaria asociada a un establecimiento.': 'Create, delete, update, and view machinery assigned to a facility.',
        'Alta, baja, modificación y consulta de servicios y mantenimientos asociados a la maquinaria.': 'Create, delete, update, and view services and maintenance for machinery.',
        'Alta, baja, modificación y consulta de contenedores.': 'Create, delete, update, and view containers.',
        'Podés registrar las descargas de residuos que ingresan al establecimiento (peso y hora de recepción) y consultar la capacidad del establecimiento y la maquinaria disponible. No tenés acceso a la gestión de rutas, de usuarios ni de incidencias.': 'Register incoming waste deliveries, including their weight and arrival time, and check facility capacity and available machinery. You cannot manage routes, users, or incidents.',
        'No filtra por municipio, así que se trae toda la lista y se filtra acá por código de municipio.': 'The server does not filter by municipality, so the full list is loaded and filtered here by municipality code.',
        'Contenedores registrados': 'Registered containers',
        'Seleccioná un tipo': 'Select a type',
        'rotura': 'breakage',
        'desborde': 'overflow',
        'falta de recoleccion': 'missed collection',
        'otro': 'other',
        'Seleccioná un estado': 'Select a status',
        'abierta': 'open',
        'en curso': 'in progress',
        'resuelta': 'resolved',
        'Elegir ubicación en el mapa': 'Choose location on the map',
        'Al ingresar el ID del contenedor se cargarán sus coordenadas.': 'Enter the container ID to load its coordinates.',
        'Cuadrilla (opcional)': 'Crew (optional)',
        'Listado de rutas existentes. Podés filtrar por la matrícula del vehículo de tu cuadrilla para ver qué ruta tiene asignada ese vehículo y en qué fecha.': 'List of existing routes. Filter by your crew vehicle’s license plate to see its assigned route and date.',
        'Detalle de rutas': 'Route details',
        'No filtra por vehículo, así que se trae toda la lista y se filtra acá por la matrícula del vehículo de tu cuadrilla o por código de municipio.': 'The server does not filter by vehicle, so the full list is loaded and filtered here by your crew vehicle’s license plate or municipality code.',
        'Ingresá el nombre de tu cuadrilla para ver únicamente las incidencias que tiene asignadas. Podés actualizar el estado de cada una, pero no crearlas ni eliminarlas.': 'Enter your crew name to see its assigned incidents. You can update their status, but cannot create or delete them.',
        'Nombre de mi cuadrilla': 'My crew name',
        'Registrá la descarga del vehículo al finalizar el recorrido: vehículo, establecimiento de destino, hora y peso recolectado.': 'Record the vehicle delivery at the end of the route: vehicle, destination facility, time, and collected weight.',
        'Recorridos registrados': 'Recorded routes',
        'No filtra por vehículo, así que se trae toda la lista y se filtra acá por la matrícula del vehículo de tu cuadrilla o por código de municipio.': 'The full list is loaded and filtered here by your crew vehicle’s license plate or municipality code.',
        'No hay rutas para mostrar.': 'There are no routes to display.',
        'No hay asignaciones para mostrar.': 'There are no assignments to display.',
        'No hay contenedores para mostrar.': 'There are no containers to display.',
        'No hay incidencias para mostrar.': 'There are no incidents to display.',
        'No hay recorridos registrados.': 'There are no recorded routes.',
        'No hay contenedores registrados con código de municipio "{codigo}".': 'No containers are registered with municipality code "{codigo}".',
        'No hay rutas con la matrícula "{matricula}".': 'There are no routes for license plate "{matricula}".',
        'No hay rutas asignadas al vehículo "{matricula}".': 'No routes are assigned to vehicle "{matricula}".',
        'No hay incidencias asignadas a la cuadrilla "{nomCuadrilla}".': 'No incidents are assigned to crew "{nomCuadrilla}".',
        'Ingresá el nombre de tu cuadrilla para ver sus incidencias.': 'Enter your crew name to view its incidents.',
        'No hay contenedores que coincidan con el filtro.': 'No containers match the filter.',
        'No hay incidencias registradas.': 'There are no incidents registered.',
        'No hay asignaciones registradas.': 'There are no assignments registered.',
        'No hay usuarios registrados.': 'There are no registered users.',
        'No hay registros.': 'There are no records.',
        'Sin registros.': 'No records found.',
        'Sin rutas.': 'No routes found.',
        'Sin contenedores.': 'No containers found.',
        'Sin incidencias.': 'No incidents found.',
        'Sin asignaciones.': 'No assignments found.',
        'Sin recorridos registrados.': 'No routes recorded.',
        'Ingresá un ID de contenedor válido.': 'Enter a valid container ID.',
        'Completá tipo, estado y fecha de apertura.': 'Enter the type, status, and opening date.',
        'La fecha de apertura no puede ser anterior al día de hoy.': 'The opening date cannot be earlier than today.',
        'Ingresá latitud y longitud juntas.': 'Enter both latitude and longitude.',
        'Las coordenadas están fuera de rango. Latitud: -90 a 90; longitud: -180 a 180.': 'Coordinates are out of range. Latitude: -90 to 90; longitude: -180 to 180.',
        'No se encontró el contenedor.': 'Container not found.',
        'El contenedor existe, pero no tiene una ubicación guardada.': 'The container exists, but has no saved location.',
        'Latitud y longitud cargadas desde el contenedor.': 'Latitude and longitude loaded from the container.',
        'Buscando la ubicación del contenedor…': 'Looking up the container location…',
        'No se pudo actualizar el estado': 'Could not update the status.',
        'Estado actualizado correctamente': 'Status updated successfully.',
        'Incidencia creada correctamente': 'Incident created successfully.',
        'No se pudo crear la incidencia': 'Could not create the incident.',
        'Error de conexión: ': 'Connection error: ',
        'Sin nombre': 'Unnamed',
        'Sin matrícula': 'No license plate',
        'Completá la ruta, la matrícula y la fecha.': 'Enter the route, license plate, and date.',
        'La fecha no puede ser anterior al día de hoy.': 'The date cannot be earlier than today.',
        'La ruta seleccionada no existe.': 'The selected route does not exist.',
        'Matrícula autocompletada para la ruta': 'License plate filled in for route',
        'Ruta asignada correctamente': 'Route assigned successfully.',
        'Asignación eliminada correctamente': 'Assignment deleted successfully.',
        'Error de conexión': 'Connection error',
        'No hay usuario autenticado': 'No authenticated user',
        'No hay usuario autenticado. Volvé al login.': 'No authenticated user. Return to login.',
        'Faltan campos obligatorios: ': 'Required fields are missing: ',
        'La cédula debe tener al menos 8 dígitos.': 'The national ID must contain at least 8 digits.',
        'Ingresa un correo electrónico válido.': 'Enter a valid email address.',
        'El nombre y el apellido deben tener al menos 2 caracteres.': 'First and last names must contain at least 2 characters.',
        'La contraseña debe tener al menos 6 caracteres.': 'The password must contain at least 6 characters.',
        'La contraseña y la confirmación deben coincidir.': 'The password and confirmation must match.',
        'Administrador creado correctamente.': 'Administrator created successfully.',
        'No se puede borrar un administrador del sistema.': 'A system administrator cannot be deleted.',
        '¿Seguro que deseas eliminar este usuario?': 'Are you sure you want to delete this user?',
        'Usuario eliminado correctamente': 'User deleted successfully.',
        'No se puede editar un administrador del sistema.': 'A system administrator cannot be edited.',
        'Rol inválido.': 'Invalid role.',
        'Se canceló: falta el código de municipio para asignar el rol de inspector.': 'Cancelled: municipality code is required to assign the inspector role.',
        'Se canceló: falta el nombre de la cuadrilla para asignar el rol.': 'Cancelled: crew name is required to assign the role.',
        'Se canceló: falta el ID de establecimiento para asignar el rol de operario.': 'Cancelled: facility ID is required to assign the operator role.',
        'Rol asignado correctamente': 'Role assigned successfully.',
        'Estado inválido.': 'Invalid status.',
        'Estado actualizado correctamente': 'Status updated successfully.',
        '¿Seguro que deseas eliminar este registro?': 'Are you sure you want to delete this record?',
        'Registro creado correctamente': 'Record created successfully.',
        'Registro actualizado correctamente': 'Record updated successfully.',
        'Registro eliminado correctamente': 'Record deleted successfully.',
        'Error de conexión: ': 'Connection error: ',
        'Ingresá el ID del contenedor para cargar sus coordenadas.': 'Enter the container ID to load its coordinates.',
        'Buscando la ubicación del contenedor…': 'Looking up the container location…',
        'El contenedor existe, pero no tiene una ubicación guardada.': 'The container exists, but has no saved location.',
        'Latitud y longitud cargadas desde el contenedor.': 'Latitude and longitude loaded from the container.',
        'La fecha no puede ser anterior al día de hoy.': 'The date cannot be earlier than today.',
        'No se pudo registrar el recorrido': 'Could not record the route.',
        'Recorrido registrado correctamente': 'Route recorded successfully.',
        'Panel | Operario de establecimiento': 'Panel | Facility operator',
        'DataByte, inicio': 'DataByte, home',
        'Mi cuenta': 'My account',
        'Ver mis datos y editarlos': 'View and edit my details',
        'Cerrar sesión': 'Log out',
        'Mis datos': 'My details',
        'Cédula': 'National ID',
        'Rol': 'Role',
        'Correo electrónico': 'Email address',
        'Nombre': 'First name',
        'Apellido': 'Last name',
        'Cambiar contraseña (opcional)': 'Change password (optional)',
        'Contraseña actual': 'Current password',
        'Nueva contraseña': 'New password',
        'Repetir nueva contraseña': 'Confirm new password',
        'Cancelar': 'Cancel',
        'Guardar cambios': 'Save changes',
        'Panel del operario de establecimiento': 'Facility operator dashboard',
        'Podés registrar las descargas de residuos que ingresan al establecimiento (peso y hora de recepción) y consultar la capacidad del establecimiento y la maquinaria disponible. No tenés acceso a la gestión de rutas, de usuarios ni de incidencias.': 'Register incoming waste deliveries, including their weight and arrival time, and check facility capacity and available machinery. You do not have access to routes, users, or incidents.',
        'Descargas': 'Deliveries',
        'Capacidad del establecimiento': 'Facility capacity',
        'Maquinaria disponible': 'Available machinery',
        'Registrar descarga de residuos': 'Register a waste delivery',
        'Ingresá el vehículo que descarga, el establecimiento receptor, la hora y el peso recibido.': 'Enter the vehicle, receiving facility, arrival time, and received weight.',
        'Matrícula del vehículo': 'Vehicle license plate',
        'ID Establecimiento': 'Facility ID',
        'Hora de recepción': 'Arrival time',
        'Peso recibido (kg)': 'Received weight (kg)',
        'Registrar descarga': 'Register delivery',
        'Descargas registradas': 'Registered deliveries',
        'Recargar': 'Refresh',
        'Filtrar por ID Establecimiento': 'Filter by facility ID',
        'Filtrar': 'Filter',
        'Ver todas': 'Show all deliveries',
        'Matrícula': 'License plate',
        'Hora': 'Time',
        'Peso (kg)': 'Weight (kg)',
        'Capacidad de los establecimientos': 'Facility capacities',
        'Consultá la capacidad actual y máxima de cada establecimiento. Podés filtrar por ID para ver solo el tuyo.': 'Check the current and maximum capacity of each facility. Filter by ID to view yours.',
        'Ver todos': 'Show all',
        'Ver toda': 'Show all machinery',
        'ID': 'ID',
        'Nombre': 'Name',
        'Calle': 'Street',
        'Número': 'Number',
        'Esquina': 'Cross street',
        'Tipo': 'Type',
        'Capacidad actual': 'Current capacity',
        'Capacidad máxima': 'Maximum capacity',
        'Tipo residuo': 'Waste type',
        'Maquinaria': 'Machinery',
        'Consultá la maquinaria del establecimiento y si está disponible o en uso. Podés filtrar por ID de establecimiento.': 'Check the facility machinery and whether it is available or in use. Filter by facility ID.',
        'ID Maquinaria': 'Machinery ID',
        'En uso': 'In use',
        'Disponible': 'Available',
        'No hay descargas para mostrar.': 'There are no deliveries to show.',
        'Sin descargas.': 'No deliveries found.',
        'No hay establecimientos para mostrar.': 'There are no facilities to show.',
        'Sin establecimientos.': 'No facilities found.',
        'No hay maquinaria para mostrar.': 'There is no machinery to show.',
        'Sin maquinaria.': 'No machinery found.',
        'Descarga registrada correctamente': 'Delivery registered successfully.',
        'Descarga registrada exitosamente': 'Delivery registered successfully.',
        'No se pudo registrar la descarga': 'Could not register the delivery.',
        'Descarga no creada': 'Delivery was not created.',
        'Datos incompletos': 'Some required data is missing or invalid.',
        'Falta un dato obligatorio.': 'A required field is missing.',
        'La operación no se puede completar porque hay datos relacionados que no existen o no son válidos.': 'The operation cannot be completed because related data is missing or invalid.',
        'Ya existe un registro con ese valor.': 'A record with that value already exists.',
        'La matrícula debe tener 3 letras y 4 números (ejemplo: SBC1234).': 'The license plate must have 3 letters and 4 numbers (example: SBC1234).',
        'Ingresá un ID de establecimiento entero y mayor a cero.': 'Enter a whole facility ID greater than zero.',
        'Ingresá una hora válida.': 'Enter a valid time.',
        'El peso debe ser mayor a cero.': 'The weight must be greater than zero.',
        'Verificá la matrícula, la hora y que el peso sea mayor a cero.': 'Check the license plate and time, and make sure the weight is greater than zero.',
        'La sesión expiró. Iniciá sesión nuevamente.': 'Your session expired. Please log in again.',
        'Para cambiar la contraseña, ingresá la actual y repetí una nueva de al menos 8 caracteres.': 'To change your password, enter the current one and confirm a new password of at least 8 characters.',
        'Guardando…': 'Saving…',
        'Los datos se actualizaron correctamente.': 'Your details were updated successfully.',
        'Tus datos se actualizaron correctamente.': 'Your details were updated successfully.',
        'No se pudieron guardar los datos.': 'Could not save your details.',
        'No se pudieron guardar los cambios.': 'Could not save your changes.',
        'Cambios guardados.': 'Changes saved.',
        'Ese correo ya está asociado a otra cuenta.': 'That email is already linked to another account.',
        'La contraseña actual no es correcta.': 'The current password is incorrect.'
    };

    const atributos = {
        'Ej: 1': 'E.g. 1',
        'Ej: A': 'E.g. A',
        'Ej: SBC1234': 'E.g. SBC1234',
        'Ej: Cuadrilla Norte': 'E.g. North Crew',
        'Ingrese el apellido': 'Enter last name',
        'Ingrese el correo': 'Enter email address',
        'Ingrese el nombre': 'Enter first name',
        'Ingrese el secreto del servidor': 'Enter the server secret',
        'Ingrese la CI': 'Enter national ID',
        'Ingrese la contraseña': 'Enter password',
        'Ingrese la cédula del admin': 'Enter the administrator ID',
        'Repita la contraseña': 'Repeat the password',
        'Formato: 3 letras y 4 números, por ejemplo SBC1234': 'Format: 3 letters and 4 numbers, for example SBC1234',
        'DataByte, inicio': 'DataByte, home'
    };
    const traduccionesInversas = Object.fromEntries(Object.entries(traducciones).map(([es, en]) => [en, es]));
    const atributosInversos = Object.fromEntries(Object.entries(atributos).map(([es, en]) => [en, es]));
    const idiomaGuardado = localStorage.getItem('databyte-language') || 'es';
    let idioma = idiomaGuardado === 'en' ? 'en' : 'es';
    let observador;
    const textosOriginales = new WeakMap();
    const textosAplicados = new WeakMap();

    function textoTraducido(texto, diccionario) {
        const limpio = texto.trim();
        if (!limpio) return texto;
        let resultado = diccionario[limpio];
        if (resultado === undefined) {
            let coincidencia;
            if (diccionario === traducciones) {
                coincidencia = limpio.match(/^(\d+) abiertas · (\d+) en curso$/);
                if (coincidencia) resultado = `${coincidencia[1]} open · ${coincidencia[2]} in progress`;
                coincidencia = limpio.match(/^(\d+) total$/);
                if (coincidencia) resultado = `${coincidencia[1]} total`;
                coincidencia = limpio.match(/^Incidencia #(\d+) · contenedor (.+)$/);
                if (coincidencia) resultado = `Incident #${coincidencia[1]} · container ${coincidencia[2]}`;
                coincidencia = limpio.match(/^No hay incidencias registradas\.$/);
                if (coincidencia) resultado = 'There are no registered incidents.';
                coincidencia = limpio.match(/^Estado de incidencia (.+)$/);
                if (coincidencia) resultado = `Incident status ${coincidencia[1]}`;
                coincidencia = limpio.match(/^Error de conexión: (.+)$/);
                if (coincidencia) resultado = `Connection error: ${coincidencia[1]}`;
                coincidencia = limpio.match(/^No hay contenedores registrados con código de municipio "(.+)"\.$/);
                if (coincidencia) resultado = `No containers are registered with municipality code "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No hay rutas con la matrícula "(.+)"\.$/);
                if (coincidencia) resultado = `There are no routes for license plate "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No hay rutas asignadas al vehículo "(.+)"\.$/);
                if (coincidencia) resultado = `No routes are assigned to vehicle "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No hay incidencias asignadas a la cuadrilla "(.+)"\.$/);
                if (coincidencia) resultado = `No incidents are assigned to crew "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No hay incidencias para la cuadrilla "(.+)"\.$/);
                if (coincidencia) resultado = `There are no incidents for crew "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^Matrícula autocompletada para la ruta (.+)\.$/);
                if (coincidencia) resultado = `License plate filled in for route ${coincidencia[1]}.`;
                coincidencia = limpio.match(/^Escenario \+2%\/día: ≈(.+) días$/);
                if (coincidencia) resultado = `Scenario +2%/day: ≈${coincidencia[1]} days`;
                coincidencia = limpio.match(/^Actualizado (.+)$/);
                if (coincidencia) resultado = `Updated ${coincidencia[1]}`;
                coincidencia = limpio.match(/^Algunos informes no pudieron actualizarse \((.+)\)\. Los demás datos se muestran normalmente\.$/);
                if (coincidencia) resultado = `Some reports could not be updated (${coincidencia[1]}). The remaining data is shown normally.`;
                coincidencia = limpio.match(/^El campo "(.+)" debe ser un número válido\.$/);
                if (coincidencia) resultado = `The field "${traducciones[coincidencia[1]] || coincidencia[1]}" must be a valid number.`;
                coincidencia = limpio.match(/^El campo "(.+)" debe ser (un número entero|un número) válido(?: y mayor o igual a (.+))?\.$/);
                if (coincidencia) resultado = `The field "${traducciones[coincidencia[1]] || coincidencia[1]}" must be a valid ${coincidencia[2] === 'un número entero' ? 'whole number' : 'number'}${coincidencia[3] ? ` and greater than or equal to ${coincidencia[3]}` : ''}.`;
                coincidencia = limpio.match(/^El campo "(.+)" debe tener un correo válido\.$/);
                if (coincidencia) resultado = `The field "${traducciones[coincidencia[1]] || coincidencia[1]}" must contain a valid email address.`;
                coincidencia = limpio.match(/^El campo "(.+)" tiene una fecha inválida\.$/);
                if (coincidencia) resultado = `The field "${traducciones[coincidencia[1]] || coincidencia[1]}" has an invalid date.`;
                coincidencia = limpio.match(/^El campo "(.+)" no puede ser anterior al día de hoy\.$/);
                if (coincidencia) resultado = `The field "${traducciones[coincidencia[1]] || coincidencia[1]}" cannot be earlier than today.`;
                coincidencia = limpio.match(/^Completá los campos obligatorios: (.+)\.$/);
                if (coincidencia) resultado = `Complete the required fields: ${coincidencia[1].split(', ').map(campo => traducciones[campo] || campo).join(', ')}.`;
                coincidencia = limpio.match(/^Faltan campos obligatorios: (.+)$/);
                if (coincidencia) resultado = `Required fields are missing: ${coincidencia[1].split(', ').map(campo => traducciones[campo] || campo).join(', ')}`;
                coincidencia = limpio.match(/^¿Eliminar la asignación de la ruta (.+) al vehículo (.+) \((.+)\)\?$/);
                if (coincidencia) resultado = `Delete the assignment of route ${coincidencia[1]} to vehicle ${coincidencia[2]} (${coincidencia[3]})?`;
                coincidencia = limpio.match(/^No hay descargas registradas para el establecimiento "(.+)"\.$/);
                if (coincidencia) resultado = `No deliveries are registered for facility "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No hay un establecimiento con ID "(.+)"\.$/);
                if (coincidencia) resultado = `There is no facility with ID "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No hay maquinaria registrada para el establecimiento "(.+)"\.$/);
                if (coincidencia) resultado = `No machinery is registered for facility "${coincidencia[1]}".`;
            } else {
                coincidencia = limpio.match(/^(\d+) open · (\d+) in progress$/);
                if (coincidencia) resultado = `${coincidencia[1]} abiertas · ${coincidencia[2]} en curso`;
                coincidencia = limpio.match(/^(\d+) total$/);
                if (coincidencia) resultado = `${coincidencia[1]} total`;
                coincidencia = limpio.match(/^Incident #(\d+) · container (.+)$/);
                if (coincidencia) resultado = `Incidencia #${coincidencia[1]} · contenedor ${coincidencia[2]}`;
                coincidencia = limpio.match(/^There are no registered incidents\.$/);
                if (coincidencia) resultado = 'No hay incidencias registradas.';
                coincidencia = limpio.match(/^Incident status (.+)$/);
                if (coincidencia) resultado = `Estado de incidencia ${coincidencia[1]}`;
                coincidencia = limpio.match(/^Connection error: (.+)$/);
                if (coincidencia) resultado = `Error de conexión: ${coincidencia[1]}`;
                coincidencia = limpio.match(/^No containers are registered with municipality code "(.+)"\.$/);
                if (coincidencia) resultado = `No hay contenedores registrados con código de municipio "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^There are no routes for license plate "(.+)"\.$/);
                if (coincidencia) resultado = `No hay rutas con la matrícula "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No routes are assigned to vehicle "(.+)"\.$/);
                if (coincidencia) resultado = `No hay rutas asignadas al vehículo "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No incidents are assigned to crew "(.+)"\.$/);
                if (coincidencia) resultado = `No hay incidencias asignadas a la cuadrilla "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^There are no incidents for crew "(.+)"\.$/);
                if (coincidencia) resultado = `No hay incidencias para la cuadrilla "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^License plate filled in for route (.+)\.$/);
                if (coincidencia) resultado = `Matrícula autocompletada para la ruta ${coincidencia[1]}.`;
                coincidencia = limpio.match(/^Scenario \+2%\/day: ≈(.+) days$/);
                if (coincidencia) resultado = `Escenario +2%/día: ≈${coincidencia[1]} días`;
                coincidencia = limpio.match(/^Updated (.+)$/);
                if (coincidencia) resultado = `Actualizado ${coincidencia[1]}`;
                coincidencia = limpio.match(/^The field "(.+)" must be a valid number\.$/);
                if (coincidencia) resultado = `El campo "${coincidencia[1]}" debe ser un número válido.`;
                coincidencia = limpio.match(/^The field "(.+)" must be a valid (whole number|number)(?: and greater than or equal to (.+))?\.$/);
                if (coincidencia) resultado = `El campo "${coincidencia[1]}" debe ser ${coincidencia[2] === 'whole number' ? 'un número entero' : 'un número'} válido${coincidencia[3] ? ` y mayor o igual a ${coincidencia[3]}` : ''}.`;
                coincidencia = limpio.match(/^The field "(.+)" must contain a valid email address\.$/);
                if (coincidencia) resultado = `El campo "${coincidencia[1]}" debe tener un correo válido.`;
                coincidencia = limpio.match(/^The field "(.+)" has an invalid date\.$/);
                if (coincidencia) resultado = `El campo "${coincidencia[1]}" tiene una fecha inválida.`;
                coincidencia = limpio.match(/^The field "(.+)" cannot be earlier than today\.$/);
                if (coincidencia) resultado = `El campo "${coincidencia[1]}" no puede ser anterior al día de hoy.`;
                coincidencia = limpio.match(/^Complete the required fields: (.+)\.$/);
                if (coincidencia) resultado = `Completá los campos obligatorios: ${coincidencia[1]}.`;
                coincidencia = limpio.match(/^Required fields are missing: (.+)$/);
                if (coincidencia) resultado = `Faltan campos obligatorios: ${coincidencia[1]}`;
                coincidencia = limpio.match(/^Delete the assignment of route (.+) to vehicle (.+) \((.+)\)\?$/);
                if (coincidencia) resultado = `¿Eliminar la asignación de la ruta ${coincidencia[1]} al vehículo ${coincidencia[2]} (${coincidencia[3]})?`;
                coincidencia = limpio.match(/^No deliveries are registered for facility "(.+)"\.$/);
                if (coincidencia) resultado = `No hay descargas registradas para el establecimiento "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^There is no facility with ID "(.+)"\.$/);
                if (coincidencia) resultado = `No hay un establecimiento con ID "${coincidencia[1]}".`;
                coincidencia = limpio.match(/^No machinery is registered for facility "(.+)"\.$/);
                if (coincidencia) resultado = `No hay maquinaria registrada para el establecimiento "${coincidencia[1]}".`;
            }
        if (resultado === undefined && limpio === 'No se encontró el establecimiento para el ID ingresado.') resultado = 'No facility was found for the entered ID.';
        if (resultado === undefined && limpio === 'No facility was found for the entered ID.') resultado = 'No se encontró el establecimiento para el ID ingresado.';
        }
        if (resultado === undefined) return texto;
        const inicio = texto.match(/^\s*/)?.[0] || '';
        const final = texto.match(/\s*$/)?.[0] || '';
        return inicio + resultado + final;
    }

    function traducirNodo(nodo) {
        if (nodo.nodeType === Node.TEXT_NODE) {
            if (nodo.parentElement?.closest('table, .language-switch')) return;
            let original = textosOriginales.get(nodo);
            if (original === undefined || (textosAplicados.has(nodo) && nodo.nodeValue !== textosAplicados.get(nodo))) {
                original = nodo.nodeValue;
                textosOriginales.set(nodo, original);
            }
            const nuevoTexto = idioma === 'en' ? textoTraducido(original, traducciones) : original;
            if (nuevoTexto !== nodo.nodeValue) nodo.nodeValue = nuevoTexto;
            textosAplicados.set(nodo, nuevoTexto);
            return;
        }
        if (!(nodo instanceof Element) || nodo.matches('.language-switch, .language-switch *, table, table *')) return;
        const diccionarioAtributos = idioma === 'en' ? atributos : atributosInversos;
        ['placeholder', 'title', 'aria-label'].forEach(nombre => {
            if (nodo.hasAttribute(nombre)) {
                const valor = nodo.getAttribute(nombre);
                const traducido = textoTraducido(valor, diccionarioAtributos);
                if (traducido !== valor) nodo.setAttribute(nombre, traducido);
            }
        });
        if (nodo.childNodes) nodo.childNodes.forEach(traducirNodo);
    }

    function cambiarIdioma(nuevoIdioma) {
        idioma = nuevoIdioma;
        localStorage.setItem('databyte-language', idioma);
        document.documentElement.lang = idioma;
        document.title = textoTraducido(document.title, idioma === 'en' ? traducciones : traduccionesInversas);
        traducirNodo(document.body);
        document.querySelectorAll('.language-switch button').forEach(button => {
            const activo = button.dataset.language === idioma;
            button.classList.toggle('active', activo);
            button.setAttribute('aria-pressed', String(activo));
        });
        document.querySelectorAll('.language-switch').forEach(selector => {
            selector.setAttribute('aria-label', idioma === 'en' ? 'Language' : 'Idioma');
        });
    }

    function instalarSelector() {
        document.querySelectorAll('.language-switch button').forEach(button => {
            button.addEventListener('click', () => cambiarIdioma(button.dataset.language));
        });
    }

    instalarSelector();
    cambiarIdioma(idioma);
    observador = new MutationObserver(cambios => {
        cambios.forEach(cambio => {
            if (cambio.type === 'characterData') traducirNodo(cambio.target);
            else cambio.addedNodes.forEach(traducirNodo);
        });
    });
    observador.observe(document.body, { childList: true, characterData: true, subtree: true });
})();
