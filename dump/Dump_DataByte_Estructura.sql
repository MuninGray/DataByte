-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: databyte
-- ------------------------------------------------------
-- Server version	8.0.43


DROP TABLE IF EXISTS `admin_tecnico`;

CREATE TABLE `admin_tecnico` (
  `cedula_admin` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `PrNom` varchar(50) NOT NULL,
  `PrApel` varchar(50) NOT NULL,
  `pass` char(60) NOT NULL,
  PRIMARY KEY (`cedula_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `asigna`;

CREATE TABLE `asigna` (
  `id_ruta` int NOT NULL,
  `matricula` varchar(10) NOT NULL,
  `fech` date NOT NULL,
  PRIMARY KEY (`id_ruta`,`matricula`,`fech`),
  CONSTRAINT `FK_Asigna_Ruta` FOREIGN KEY (`id_ruta`) REFERENCES `ruta` (`id_ruta`),
  CONSTRAINT `FK_Asigna_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `contenedor`;

CREATE TABLE `contenedor` (
  `id_contdor` int NOT NULL,
  `estado_optivo` enum('funcional','roto','desbordado') NOT NULL,
  `en_uso` enum('desplegado','en_stock') DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `esq` varchar(100) DEFAULT NULL,
  `nmro` int NOT NULL,
  `calle` varchar(100) NOT NULL,
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL,
  `matricula` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_contdor`),
  KEY `FK_Contenedor_Municipio` (`codigo`),
  KEY `FK_Contenedor_Vehiculo` (`matricula`),
  CONSTRAINT `FK_Contenedor_Municipio` FOREIGN KEY (`codigo`) REFERENCES `municipio` (`codigo`),
  CONSTRAINT `FK_Contenedor_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `cuadrilla`;

CREATE TABLE `cuadrilla` (
  `nom_cuadrilla` varchar(50) NOT NULL,
  `cedula_inspector` int NOT NULL,
  PRIMARY KEY (`nom_cuadrilla`),
  KEY `FK_Cuadrilla_Inspector` (`cedula_inspector`),
  CONSTRAINT `FK_Cuadrilla_Inspector` FOREIGN KEY (`cedula_inspector`) REFERENCES `inspector_municipal` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `descarga`;

CREATE TABLE `descarga` (
  `matricula` varchar(10) NOT NULL,
  `id_establcmto` int NOT NULL,
  `hora` time NOT NULL,
  `peso` decimal(10,2) NOT NULL,
  PRIMARY KEY (`matricula`,`id_establcmto`,`hora`),
  KEY `FK_Descarga_Establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_Descarga_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_Descarga_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `establecimiento`;

CREATE TABLE `establecimiento` (
  `id_establcmto` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `calle` varchar(100) NOT NULL,
  `nmro` int NOT NULL,
  `esq` varchar(100) NOT NULL,
  `tipo` enum('vertedero','centro de acopio') NOT NULL,
  `capac_actual` decimal(10,2) NOT NULL,
  `capac_max` decimal(10,2) NOT NULL,
  `tipo_res` varchar(50) NOT NULL,
  PRIMARY KEY (`id_establcmto`),
  CONSTRAINT `CHK_Capacidad` CHECK ((`capac_actual` <= `capac_max`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `incidencia`;

CREATE TABLE `incidencia` (
  `id_incidencia` int NOT NULL,
  `id_contdor` int NOT NULL,
  `tipo` enum('rotura','desborde','falta de recoleccion') NOT NULL,
  `estado` enum('abierta','en curso','resuelta') NOT NULL,
  `fch_apert` date NOT NULL,
  `fch_resol` date DEFAULT NULL,
  `nom_cuadrilla` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_incidencia`,`id_contdor`),
  KEY `FK_Incidencia_Contenedor` (`id_contdor`),
  KEY `FK_Incidencia_Cuadrilla` (`nom_cuadrilla`),
  CONSTRAINT `FK_Incidencia_Contenedor` FOREIGN KEY (`id_contdor`) REFERENCES `contenedor` (`id_contdor`),
  CONSTRAINT `FK_Incidencia_Cuadrilla` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `inspector_municipal`;

CREATE TABLE `inspector_municipal` (
  `cedula` int NOT NULL,
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `FK_Inspector_Municipal` (`codigo`),
  CONSTRAINT `FK_Inspector_Municipal` FOREIGN KEY (`codigo`) REFERENCES `municipio` (`codigo`),
  CONSTRAINT `FK_Inspector_Usuario` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `maquinaria`;

CREATE TABLE `maquinaria` (
  `id_maquinaria` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `en_uso` tinyint(1) NOT NULL,
  `id_establcmto` int NOT NULL,
  PRIMARY KEY (`id_maquinaria`),
  KEY `FK_Maquinaria_Establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_Maquinaria_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `municipio`;

CREATE TABLE `municipio` (
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `operario_cuadrilla`;

CREATE TABLE `operario_cuadrilla` (
  `cedula` int NOT NULL,
  `nom_cuadrilla` varchar(50) NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `FK_OperarioCuadrilla_Cuadrilla` (`nom_cuadrilla`),
  CONSTRAINT `FK_OperarioCuadrilla_Cuadrilla` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`),
  CONSTRAINT `FK_OperarioCuadrilla_Usuario` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `operario_establcmto`;

CREATE TABLE `operario_establcmto` (
  `cedula` int NOT NULL,
  `id_establcmto` int NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `FK_OperarioEst_Establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_OperarioEst_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_OperarioEst_Usuario` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `ruta`;

CREATE TABLE `ruta` (
  `id_ruta` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ruta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `cedula` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass` char(60) NOT NULL,
  `estado_habil` enum('pendiente','aprobado','rechazado') NOT NULL,
  `PrNom` varchar(50) NOT NULL,
  `PrApel` varchar(50) NOT NULL,
  `rol` varchar(30) NOT NULL,
  `cedula_admin` int DEFAULT NULL,
  PRIMARY KEY (`cedula`),
  KEY `FK_Usuario_Admin_Tecnico` (`cedula_admin`),
  CONSTRAINT `FK_Usuario_Admin_Tecnico` FOREIGN KEY (`cedula_admin`) REFERENCES `admin_tecnico` (`cedula_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `vehiculo`;

CREATE TABLE `vehiculo` (
  `matricula` varchar(10) NOT NULL,
  `estado_optivo` enum('en servicio','en stock','en mantenimiento') NOT NULL,
  PRIMARY KEY (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dump completed on 2026-07-23  3:03:28
