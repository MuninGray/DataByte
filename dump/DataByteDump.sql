USE databyte;
CREATE TABLE admin_tecnico (
  cedula_admin int(11) NOT NULL,
  email varchar(50) NOT NULL,
  PrNom varchar(50) NOT NULL,
  PrApel varchar(50) NOT NULL,
  pass char(60) NOT NULL
);

CREATE TABLE asigna (
  id_ruta int(11) NOT NULL,
  matricula varchar(10) NOT NULL,
  fech date NOT NULL
);

CREATE TABLE chofer (
  cedula int(11) NOT NULL,
  nom_cuadrilla varchar(50) NOT NULL
);

CREATE TABLE contenedor (
  id_contdor int(11) NOT NULL,
  estado_optivo enum('funcional','roto','desbordado','desaparecido','otro') NOT NULL,
  en_uso enum('desplegado','en_stock') DEFAULT NULL,
  tipo varchar(50) NOT NULL,
  esq varchar(100) DEFAULT NULL,
  nmro int(11) NOT NULL,
  calle varchar(100) NOT NULL,
  codigo varchar(20) NOT NULL,
  matricula varchar(10) DEFAULT NULL,
  id_ruta int(11) DEFAULT NULL
);

CREATE TABLE cuadrilla (
  nom_cuadrilla varchar(50) NOT NULL,
  cedula_inspector int(11) NOT NULL
);

CREATE TABLE descarga (
  matricula varchar(10) NOT NULL,
  id_establcmto int(11) NOT NULL,
  hora time NOT NULL,
  peso decimal(10,2) NOT NULL
);

CREATE TABLE establecimiento (
  id_establcmto int(11) NOT NULL,
  nombre varchar(100) NOT NULL,
  calle varchar(100) NOT NULL,
  nmro int(11) NOT NULL,
  esq varchar(100) NOT NULL,
  tipo enum('vertedero','centro de acopio') NOT NULL,
  capac_actual decimal(10,2) NOT NULL,
  capac_max decimal(10,2) NOT NULL,
  tipo_res varchar(50) NOT NULL
);

CREATE TABLE incidencia (
  id_incidencia int(11) NOT NULL,
  id_contdor int(11) NOT NULL,
  tipo enum('rotura','desborde','falta de recoleccion','otro') NOT NULL,
  estado enum('abierta','en curso','resuelta') NOT NULL,
  fch_apert date NOT NULL,
  fch_resol date DEFAULT NULL,
  nom_cuadrilla varchar(50) DEFAULT NULL
);

CREATE TABLE inspector_municipal (
  cedula int(11) NOT NULL,
  codigo varchar(20) NOT NULL
);

CREATE TABLE maquinaria (
  id_maquinaria int(11) NOT NULL,
  nombre varchar(100) NOT NULL,
  en_uso enum('desplegado','en_stock') NOT NULL,
  id_establcmto int(11) NOT NULL,
  id_serv int(11) NOT NULL
);

CREATE TABLE municipio (
  codigo varchar(20) NOT NULL,
  nombre varchar(100) NOT NULL
);

CREATE TABLE operario_establcmto (
  cedula int(11) NOT NULL,
  id_establcmto int(11) NOT NULL
);

CREATE TABLE peon (
  cedula int(11) NOT NULL,
  nom_cuadrilla varchar(50) NOT NULL
);

CREATE TABLE ruta (
  id_ruta int(11) NOT NULL,
  nom varchar(100) NOT NULL,
  matricula varchar(10) NOT NULL
);

CREATE TABLE servicios_y_mantenimientos (
  id_serv int(11) NOT NULL,
  estado enum('pendiente','en_curso','finalizado') DEFAULT NULL,
  fecha date DEFAULT NULL,
  tipo enum('preventivo','correctivo','otro') DEFAULT NULL
);

CREATE TABLE usuario (
  cedula int(11) NOT NULL,
  email varchar(50) NOT NULL,
  pass char(60) NOT NULL,
  estado_habil enum('pendiente','aprobado','rechazado') NOT NULL,
  PrNom varchar(50) NOT NULL,
  PrApel varchar(50) NOT NULL,
  rol varchar(30) NOT NULL,
  cedula_admin int(11) NOT NULL
);

CREATE TABLE vehiculo (
  matricula varchar(10) NOT NULL,
  estado_optivo enum('funcional','en_reparacion','fuera_de_servicio') NOT NULL
);

ALTER TABLE admin_tecnico
  ADD PRIMARY KEY (cedula_admin);

ALTER TABLE asigna
  ADD PRIMARY KEY (id_ruta,matricula,fech),
  ADD KEY FK_Asigna_Vehiculo (matricula);

ALTER TABLE chofer
  ADD PRIMARY KEY (cedula),
  ADD KEY FK_Chofer_Cuadrilla (nom_cuadrilla);

ALTER TABLE contenedor
  ADD PRIMARY KEY (id_contdor),
  ADD KEY FK_Contenedor_Municipio (codigo),
  ADD KEY FK_Contenedor_Asigna_matricula (matricula),
  ADD KEY FK_Contenedor_Asigna_id_ruta (id_ruta);

ALTER TABLE cuadrilla
  ADD PRIMARY KEY (nom_cuadrilla),
  ADD KEY FK_Cuadrilla_Inspector (cedula_inspector);

ALTER TABLE descarga
  ADD PRIMARY KEY (matricula,id_establcmto,hora),
  ADD KEY FK_Descarga_Establecimiento (id_establcmto);

ALTER TABLE establecimiento
  ADD PRIMARY KEY (id_establcmto);

ALTER TABLE incidencia
  ADD PRIMARY KEY (id_incidencia),
  ADD KEY FK_Incidencia_Contenedor (id_contdor),
  ADD KEY FK_Incidencia_Cuadrilla (nom_cuadrilla);

ALTER TABLE inspector_municipal
  ADD PRIMARY KEY (cedula),
  ADD KEY FK_Inspector_Municipio (codigo);

ALTER TABLE maquinaria
  ADD PRIMARY KEY (id_maquinaria),
  ADD KEY FK_Maquinaria_Establecimiento (id_establcmto),
  ADD KEY FK_Maquinaria_Servicio (id_serv);

ALTER TABLE municipio
  ADD PRIMARY KEY (codigo);

ALTER TABLE operario_establcmto
  ADD PRIMARY KEY (cedula),
  ADD KEY FK_OperarioEst_Establecimiento (id_establcmto);

ALTER TABLE peon
  ADD PRIMARY KEY (cedula),
  ADD KEY FK_Peon_Cuadrilla (nom_cuadrilla);

ALTER TABLE ruta
  ADD PRIMARY KEY (id_ruta),
  ADD KEY FK_Ruta_Vehiculo (matricula);
  
ALTER TABLE servicios_y_mantenimientos
  ADD PRIMARY KEY (id_serv);

ALTER TABLE usuario
  ADD PRIMARY KEY (cedula),
  ADD KEY FK_Usuario_Admin_Tecnico (cedula_admin);

ALTER TABLE vehiculo
  ADD PRIMARY KEY (matricula);

ALTER TABLE asigna
  ADD CONSTRAINT FK_Asigna_Ruta FOREIGN KEY (id_ruta) REFERENCES ruta (id_ruta),
  ADD CONSTRAINT FK_Asigna_Vehiculo FOREIGN KEY (matricula) REFERENCES vehiculo (matricula);

ALTER TABLE chofer
  ADD CONSTRAINT FK_Chofer_Cuadrilla FOREIGN KEY (nom_cuadrilla) REFERENCES cuadrilla (nom_cuadrilla),
  ADD CONSTRAINT FK_Chofer_Usuario FOREIGN KEY (cedula) REFERENCES usuario (cedula);

ALTER TABLE contenedor
  ADD CONSTRAINT FK_Contenedor_Asigna_id_ruta FOREIGN KEY (id_ruta) REFERENCES asigna (id_ruta),
  ADD CONSTRAINT FK_Contenedor_Asigna_matricula FOREIGN KEY (matricula) REFERENCES asigna (matricula),
  ADD CONSTRAINT FK_Contenedor_Municipio FOREIGN KEY (codigo) REFERENCES municipio (codigo);

ALTER TABLE cuadrilla
  ADD CONSTRAINT FK_Cuadrilla_Inspector FOREIGN KEY (cedula_inspector) REFERENCES inspector_municipal (cedula);

ALTER TABLE descarga
  ADD CONSTRAINT FK_Descarga_Establecimiento FOREIGN KEY (id_establcmto) REFERENCES establecimiento (id_establcmto),
  ADD CONSTRAINT FK_Descarga_Vehiculo FOREIGN KEY (matricula) REFERENCES vehiculo (matricula);

ALTER TABLE incidencia
  ADD CONSTRAINT FK_Incidencia_Contenedor FOREIGN KEY (id_contdor) REFERENCES contenedor (id_contdor),
  ADD CONSTRAINT FK_Incidencia_Cuadrilla FOREIGN KEY (nom_cuadrilla) REFERENCES cuadrilla (nom_cuadrilla);

ALTER TABLE inspector_municipal
  ADD CONSTRAINT FK_Inspector_Municipio FOREIGN KEY (codigo) REFERENCES municipio (codigo),
  ADD CONSTRAINT FK_Inspector_Usuario FOREIGN KEY (cedula) REFERENCES usuario (cedula);

ALTER TABLE maquinaria
  ADD CONSTRAINT FK_Maquinaria_Establecimiento FOREIGN KEY (id_establcmto) REFERENCES establecimiento (id_establcmto),
  ADD CONSTRAINT FK_Maquinaria_Servicio FOREIGN KEY (id_serv) REFERENCES servicios_y_mantenimientos (id_serv);

ALTER TABLE operario_establcmto
  ADD CONSTRAINT FK_OperarioEst_Establecimiento FOREIGN KEY (id_establcmto) REFERENCES establecimiento (id_establcmto),
  ADD CONSTRAINT FK_OperarioEst_Usuario FOREIGN KEY (cedula) REFERENCES usuario (cedula);

ALTER TABLE peon
  ADD CONSTRAINT FK_Peon_Cuadrilla FOREIGN KEY (nom_cuadrilla) REFERENCES cuadrilla (nom_cuadrilla),
  ADD CONSTRAINT FK_Peon_Usuario FOREIGN KEY (cedula) REFERENCES usuario (cedula);
  
ALTER TABLE ruta
  ADD CONSTRAINT FK_Ruta_Vehiculo FOREIGN KEY (matricula) REFERENCES vehiculo (matricula);

ALTER TABLE usuario
  ADD CONSTRAINT FK_Usuario_Admin_Tecnico FOREIGN KEY (cedula_admin) REFERENCES admin_tecnico (cedula_admin);
  
COMMIT;
