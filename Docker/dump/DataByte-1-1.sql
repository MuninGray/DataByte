drop database if exists DataByte;
create database DataByte;

use DataByte;

create table Admin_Tecnico(
    cedula_admin int not null,
    email varchar(50) not null,
    PrNom varchar(50) not null,
    PrApel varchar(50) not null,
    pass char(60) not null,
    constraint PK_Admin_Tecnico primary key (cedula_admin)
);

create table Usuario(
    cedula int not null,
    email varchar(50) not null,
    pass char(60) not null,
    estado_habil enum('pendiente','aprobado','rechazado') not null,
    PrNom varchar(50) not null,
    PrApel varchar(50) not null,
    rol enum('USUARIO','INSPECTOR','CHOFER','PEON','OPERARIO_ESTABLECIMIENTO') not null,
    cedula_admin int not null,
    constraint PK_Usuario primary key (cedula)
);

create table Municipio(
    codigo varchar(20) not null,
    nombre varchar(100) not null,
    coordenadas varchar(100),
    constraint PK_Municipio primary key (codigo)
);

create table Inspector_Municipal(
    cedula int not null,
    codigo varchar(20) not null,
    constraint PK_Inspector_Municipal primary key (cedula)
);

create table Establecimiento(
    id_establcmto int not null,
    nombre varchar(50) not null,
    calle varchar(100) not null,
    nmro int not null,
    esq varchar(100) not null,
    tipo enum('vertedero','centro de acopio') not null,
    capac_actual decimal(10,2) not null,
    capac_max decimal(10,2) not null,
    tipo_res varchar(50) not null,
    constraint PK_Establecimiento primary key (id_establcmto)
);

create table Cuadrilla(
    nom_cuadrilla varchar(50) not null,
    cedula_inspector int not null,
    constraint PK_Cuadrilla primary key (nom_cuadrilla)
);

create table Chofer(
    cedula int not null,
    nom_cuadrilla varchar(50) not null,
    constraint PK_Chofer primary key (cedula)
);

create table Peon(
    cedula int not null,
    nom_cuadrilla varchar(50) not null,
    constraint PK_Peon primary key (cedula)
);

create table Operario_Establcmto(
    cedula int not null,
    id_establcmto int not null,
    constraint PK_Operario_Establcmto primary key (cedula)
);

create table Vehiculo(
    matricula varchar(10) not null,
    estado_optivo enum('funcional','en_reparacion','fuera_de_servicio') not null,
    constraint PK_Vehiculo primary key (matricula)
);

create table Ruta(
    id_ruta int not null,
    nom varchar(100) not null,
    matricula varchar(10) not null,
    constraint PK_Ruta primary key (id_ruta)
);

create table Servicios_Y_Mantenimientos(
    id_serv int not null,
    estado enum('en_curso','finalizado') not null,
    fecha date not null,
    tipo enum('pendiente','preventivo','correctivo','otro') not null,
    constraint PK_Servicios_Y_Mantenimientos primary key (id_serv)
);

create table Maquinaria(
    id_maquinaria int not null,
    nombre varchar(100) not null,
    en_uso enum('desplegado','en_stock') not null,
    id_establcmto int not null,
    id_serv int not null,
    constraint PK_Maquinaria primary key (id_maquinaria)
);

create table Asigna(
    id_ruta int not null,
    matricula varchar(10) not null,
    fech date not null,
    constraint PK_Asigna primary key (id_ruta, matricula, fech)
);

create table Contenedor(
    id_contdor int not null,
    estado_optivo enum('funcional','roto','desbordado','desaparecido','otro') not null,
    en_uso enum('desplegado','en_stock'),
    tipo varchar(50) not null,
    esq varchar(100),
    nmro int not null,
    calle varchar(100) not null,
    codigo varchar(20) not null,
    matricula varchar(10),
    id_ruta int,
    constraint PK_Contenedor primary key (id_contdor)
);

create table Incidencia(
    id_incidencia int not null,
    id_contdor int not null,
    tipo enum('rotura','desborde','falta de recoleccion','otro') not null,
    estado enum('abierta','en curso','resuelta') not null,
    fch_apert date not null,
    fch_resol date,
    nom_cuadrilla varchar(50),
    constraint PK_Incidencia primary key (id_incidencia)
);

create table Descarga(
    matricula varchar(10) not null,
    id_establcmto int not null,
    hora time not null,
    peso decimal(10,2) not null,
    constraint PK_Descarga primary key (matricula, id_establcmto, hora)
);

alter table Usuario
add constraint FK_Usuario_Admin_Tecnico
foreign key (cedula_admin) references Admin_Tecnico(cedula_admin);

alter table Inspector_Municipal
add constraint FK_Inspector_Usuario
foreign key (cedula) references Usuario(cedula);

alter table Inspector_Municipal
add constraint FK_Inspector_Municipio
foreign key (codigo) references Municipio(codigo);

alter table Cuadrilla
add constraint FK_Cuadrilla_Inspector
foreign key (cedula_inspector) references Inspector_Municipal(cedula);

alter table Chofer
add constraint FK_Chofer_Usuario
foreign key (cedula) references Usuario(cedula);

alter table Chofer
add constraint FK_Chofer_Cuadrilla
foreign key (nom_cuadrilla) references Cuadrilla(nom_cuadrilla);

alter table Peon
add constraint FK_Peon_Usuario
foreign key (cedula) references Usuario(cedula);

alter table Peon
add constraint FK_Peon_Cuadrilla
foreign key (nom_cuadrilla) references Cuadrilla(nom_cuadrilla);

alter table Operario_Establcmto
add constraint FK_OperarioEst_Usuario
foreign key (cedula) references Usuario(cedula);

alter table Operario_Establcmto
add constraint FK_OperarioEst_Establecimiento
foreign key (id_establcmto) references Establecimiento(id_establcmto);

alter table Maquinaria
add constraint FK_Maquinaria_Establecimiento
foreign key (id_establcmto) references Establecimiento(id_establcmto);

alter table Maquinaria
add constraint FK_Maquinaria_Servicio
foreign key (id_serv) references Servicios_Y_Mantenimientos(id_serv);

alter table Ruta
add constraint FK_Ruta_Vehiculo
foreign key (matricula) references Vehiculo(matricula);

alter table Asigna
add constraint FK_Asigna_Ruta
foreign key (id_ruta) references Ruta(id_ruta);

alter table Asigna
add constraint FK_Asigna_Vehiculo
foreign key (matricula) references Vehiculo(matricula);

alter table Contenedor
add constraint FK_Contenedor_Municipio
foreign key (codigo) references Municipio(codigo);

alter table Contenedor
add constraint FK_Contenedor_Asigna_matricula
foreign key (matricula) references Asigna(matricula);

alter table Contenedor
add constraint FK_Contenedor_Asigna_id_ruta
foreign key (id_ruta) references Asigna(id_ruta);

alter table Incidencia
add constraint FK_Incidencia_Contenedor
foreign key (id_contdor) references Contenedor(id_contdor);

alter table Incidencia
add constraint FK_Incidencia_Cuadrilla
foreign key (nom_cuadrilla) references Cuadrilla(nom_cuadrilla);

alter table Descarga
add constraint FK_Descarga_Vehiculo
foreign key (matricula) references Vehiculo(matricula);

alter table Descarga
add constraint FK_Descarga_Establecimiento
foreign key (id_establcmto) references Establecimiento(id_establcmto);

alter table Establecimiento
add constraint CHK_Capacidad
check (capac_actual <= capac_max);
