-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 24-07-2026 a las 20:41:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `DataByte`
--

-- --------------------------------------------------------

--
-- 1. Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `DataByte` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

-- 2. Seleccionar la base de datos para trabajar en ella
USE `DataByte`;
-- Estructura de tabla para la tabla `admin_tecnico`
--

CREATE TABLE `admin_tecnico` (
  `cedula_admin` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `PrNom` varchar(50) NOT NULL,
  `PrApel` varchar(50) NOT NULL,
  `pass` char(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asigna`
--

CREATE TABLE `asigna` (
  `id_ruta` int(11) NOT NULL,
  `matricula` varchar(10) NOT NULL,
  `fech` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenedor`
--

CREATE TABLE `contenedor` (
  `id_contdor` int(11) NOT NULL,
  `estado_optivo` enum('funcional','roto','desbordado') NOT NULL,
  `en_uso` enum('desplegado','en_stock') DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `esq` varchar(100) DEFAULT NULL,
  `nmro` int(11) NOT NULL,
  `calle` varchar(100) NOT NULL,
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL,
  `matricula` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuadrilla`
--

CREATE TABLE `cuadrilla` (
  `nom_cuadrilla` varchar(50) NOT NULL,
  `cedula_inspector` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descarga`
--

CREATE TABLE `descarga` (
  `matricula` varchar(10) NOT NULL,
  `id_establcmto` int(11) NOT NULL,
  `hora` time NOT NULL,
  `peso` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `establecimiento`
--

CREATE TABLE `establecimiento` (
  `id_establcmto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `calle` varchar(100) NOT NULL,
  `nmro` int(11) NOT NULL,
  `esq` varchar(100) NOT NULL,
  `tipo` enum('vertedero','centro de acopio') NOT NULL,
  `capac_actual` decimal(10,2) NOT NULL,
  `capac_max` decimal(10,2) NOT NULL,
  `tipo_res` varchar(50) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia`
--

CREATE TABLE `incidencia` (
  `id_incidencia` int(11) NOT NULL,
  `id_contdor` int(11) NOT NULL,
  `tipo` enum('rotura','desborde','falta de recoleccion') NOT NULL,
  `estado` enum('abierta','en curso','resuelta') NOT NULL,
  `fch_apert` date NOT NULL,
  `fch_resol` date DEFAULT NULL,
  `nom_cuadrilla` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inspector_municipal`
--

CREATE TABLE `inspector_municipal` (
  `cedula` int(11) NOT NULL,
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maquinaria`
--

CREATE TABLE `maquinaria` (
  `id_maquinaria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `en_uso` tinyint(1) NOT NULL,
  `id_establcmto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipio`
--

CREATE TABLE `municipio` (
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operario_cuadrilla`
--

CREATE TABLE `operario_cuadrilla` (
  `cedula` int(11) NOT NULL,
  `nom_cuadrilla` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operario_establcmto`
--

CREATE TABLE `operario_establcmto` (
  `cedula` int(11) NOT NULL,
  `id_establcmto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ruta`
--

CREATE TABLE `ruta` (
  `id_ruta` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cedula` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass` char(60) NOT NULL,
  `estado_habil` enum('pendiente','aprobado','rechazado') NOT NULL,
  `PrNom` varchar(50) NOT NULL,
  `PrApel` varchar(50) NOT NULL,
  `rol` varchar(30) NOT NULL,
  `cedula_admin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura de tabla para la tabla `vehiculo`
--

CREATE TABLE `vehiculo` (
  `matricula` varchar(10) NOT NULL,
  `estado_optivo` enum('en servicio','en stock','en mantenimiento') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_tecnico`
--
ALTER TABLE `admin_tecnico`
  ADD PRIMARY KEY (`cedula_admin`);

--
-- Indices de la tabla `asigna`
--
ALTER TABLE `asigna`
  ADD PRIMARY KEY (`id_ruta`,`matricula`,`fech`),
  ADD KEY `FK_Asigna_Vehiculo` (`matricula`);

--
-- Indices de la tabla `contenedor`
--
ALTER TABLE `contenedor`
  ADD PRIMARY KEY (`id_contdor`),
  ADD KEY `FK_Contenedor_Municipio` (`codigo`),
  ADD KEY `FK_Contenedor_Vehiculo` (`matricula`);

--
-- Indices de la tabla `cuadrilla`
--
ALTER TABLE `cuadrilla`
  ADD PRIMARY KEY (`nom_cuadrilla`),
  ADD KEY `FK_Cuadrilla_Inspector` (`cedula_inspector`);

--
-- Indices de la tabla `descarga`
--
ALTER TABLE `descarga`
  ADD PRIMARY KEY (`matricula`,`id_establcmto`,`hora`),
  ADD KEY `FK_Descarga_Establecimiento` (`id_establcmto`);

--
-- Indices de la tabla `establecimiento`
--
ALTER TABLE `establecimiento`
  ADD PRIMARY KEY (`id_establcmto`);

--
-- Indices de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD PRIMARY KEY (`id_incidencia`,`id_contdor`),
  ADD KEY `FK_Incidencia_Contenedor` (`id_contdor`),
  ADD KEY `FK_Incidencia_Cuadrilla` (`nom_cuadrilla`);

--
-- Indices de la tabla `inspector_municipal`
--
ALTER TABLE `inspector_municipal`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `FK_Inspector_Municipal` (`codigo`);

--
-- Indices de la tabla `maquinaria`
--
ALTER TABLE `maquinaria`
  ADD PRIMARY KEY (`id_maquinaria`),
  ADD KEY `FK_Maquinaria_Establecimiento` (`id_establcmto`);

--
-- Indices de la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `operario_cuadrilla`
--
ALTER TABLE `operario_cuadrilla`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `FK_OperarioCuadrilla_Cuadrilla` (`nom_cuadrilla`);

--
-- Indices de la tabla `operario_establcmto`
--
ALTER TABLE `operario_establcmto`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `FK_OperarioEst_Establecimiento` (`id_establcmto`);

--
-- Indices de la tabla `ruta`
--
ALTER TABLE `ruta`
  ADD PRIMARY KEY (`id_ruta`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `FK_Usuario_Admin_Tecnico` (`cedula_admin`);

--
-- Indices de la tabla `vehiculo`
--
ALTER TABLE `vehiculo`
  ADD PRIMARY KEY (`matricula`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asigna`
--
ALTER TABLE `asigna`
  ADD CONSTRAINT `FK_Asigna_Ruta` FOREIGN KEY (`id_ruta`) REFERENCES `ruta` (`id_ruta`),
  ADD CONSTRAINT `FK_Asigna_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`);

--
-- Filtros para la tabla `contenedor`
--
ALTER TABLE `contenedor`
  ADD CONSTRAINT `FK_Contenedor_Municipio` FOREIGN KEY (`codigo`) REFERENCES `municipio` (`codigo`),
  ADD CONSTRAINT `FK_Contenedor_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`);

--
-- Filtros para la tabla `cuadrilla`
--
ALTER TABLE `cuadrilla`
  ADD CONSTRAINT `FK_Cuadrilla_Inspector` FOREIGN KEY (`cedula_inspector`) REFERENCES `inspector_municipal` (`cedula`);

--
-- Filtros para la tabla `descarga`
--
ALTER TABLE `descarga`
  ADD CONSTRAINT `FK_Descarga_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`),
  ADD CONSTRAINT `FK_Descarga_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`);

--
-- Filtros para la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD CONSTRAINT `FK_Incidencia_Contenedor` FOREIGN KEY (`id_contdor`) REFERENCES `contenedor` (`id_contdor`),
  ADD CONSTRAINT `FK_Incidencia_Cuadrilla` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`);

--
-- Filtros para la tabla `inspector_municipal`
--
ALTER TABLE `inspector_municipal`
  ADD CONSTRAINT `FK_Inspector_Municipal` FOREIGN KEY (`codigo`) REFERENCES `municipio` (`codigo`),
  ADD CONSTRAINT `FK_Inspector_Usuario` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`);

--
-- Filtros para la tabla `maquinaria`
--
ALTER TABLE `maquinaria`
  ADD CONSTRAINT `FK_Maquinaria_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`);

--
-- Filtros para la tabla `operario_cuadrilla`
--
ALTER TABLE `operario_cuadrilla`
  ADD CONSTRAINT `FK_OperarioCuadrilla_Cuadrilla` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`),
  ADD CONSTRAINT `FK_OperarioCuadrilla_Usuario` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`);

--
-- Filtros para la tabla `operario_establcmto`
--
ALTER TABLE `operario_establcmto`
  ADD CONSTRAINT `FK_OperarioEst_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`),
  ADD CONSTRAINT `FK_OperarioEst_Usuario` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_Usuario_Admin_Tecnico` FOREIGN KEY (`cedula_admin`) REFERENCES `admin_tecnico` (`cedula_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
