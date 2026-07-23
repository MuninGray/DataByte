CREATE DATABASE  IF NOT EXISTS `DataByte` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `DataByte`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: DataByte
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_tecnico`
--

DROP TABLE IF EXISTS `admin_tecnico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_tecnico` (
  `cedula_admin` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `PrNom` varchar(50) NOT NULL,
  `PrApel` varchar(50) NOT NULL,
  `pass` char(60) NOT NULL,
  PRIMARY KEY (`cedula_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_tecnico`
--

LOCK TABLES `admin_tecnico` WRITE;
/*!40000 ALTER TABLE `admin_tecnico` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_tecnico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asigna`
--

DROP TABLE IF EXISTS `asigna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asigna` (
  `id_ruta` int NOT NULL,
  `matricula` varchar(10) NOT NULL,
  `fech` date NOT NULL,
  PRIMARY KEY (`id_ruta`,`matricula`,`fech`),
  KEY `FK_Asigna_Vehiculo` (`matricula`),
  CONSTRAINT `FK_Asigna_Ruta` FOREIGN KEY (`id_ruta`) REFERENCES `ruta` (`id_ruta`),
  CONSTRAINT `FK_Asigna_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asigna`
--

LOCK TABLES `asigna` WRITE;
/*!40000 ALTER TABLE `asigna` DISABLE KEYS */;
/*!40000 ALTER TABLE `asigna` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contenedor`
--

DROP TABLE IF EXISTS `contenedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contenedor`
--

LOCK TABLES `contenedor` WRITE;
/*!40000 ALTER TABLE `contenedor` DISABLE KEYS */;
/*!40000 ALTER TABLE `contenedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuadrilla`
--

DROP TABLE IF EXISTS `cuadrilla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuadrilla` (
  `nom_cuadrilla` varchar(50) NOT NULL,
  `cedula_inspector` int NOT NULL,
  PRIMARY KEY (`nom_cuadrilla`),
  KEY `FK_Cuadrilla_Inspector` (`cedula_inspector`),
  CONSTRAINT `FK_Cuadrilla_Inspector` FOREIGN KEY (`cedula_inspector`) REFERENCES `inspector_municipal` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuadrilla`
--

LOCK TABLES `cuadrilla` WRITE;
/*!40000 ALTER TABLE `cuadrilla` DISABLE KEYS */;
/*!40000 ALTER TABLE `cuadrilla` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descarga`
--

DROP TABLE IF EXISTS `descarga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `descarga` (
  `matricula` varchar(10) NOT NULL,
  `id_establcmto` int NOT NULL,
  `hora` time NOT NULL,
  `peso` decimal(10,2) NOT NULL,
  PRIMARY KEY (`matricula`,`id_establcmto`,`hora`),
  KEY `FK_Descarga_Establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_Descarga_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_Descarga_Vehiculo` FOREIGN KEY (`matricula`) REFERENCES `vehiculo` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descarga`
--

LOCK TABLES `descarga` WRITE;
/*!40000 ALTER TABLE `descarga` DISABLE KEYS */;
/*!40000 ALTER TABLE `descarga` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `establecimiento`
--

DROP TABLE IF EXISTS `establecimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `establecimiento`
--

LOCK TABLES `establecimiento` WRITE;
/*!40000 ALTER TABLE `establecimiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `establecimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incidencia`
--

DROP TABLE IF EXISTS `incidencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidencia`
--

LOCK TABLES `incidencia` WRITE;
/*!40000 ALTER TABLE `incidencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `incidencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inspector_municipal`
--

DROP TABLE IF EXISTS `inspector_municipal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inspector_municipal` (
  `cedula` int NOT NULL,
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `FK_Inspector_Municipal` (`codigo`),
  CONSTRAINT `FK_Inspector_Municipal` FOREIGN KEY (`codigo`) REFERENCES `municipio` (`codigo`),
  CONSTRAINT `FK_Inspector_Usuario` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inspector_municipal`
--

LOCK TABLES `inspector_municipal` WRITE;
/*!40000 ALTER TABLE `inspector_municipal` DISABLE KEYS */;
/*!40000 ALTER TABLE `inspector_municipal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria`
--

DROP TABLE IF EXISTS `maquinaria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maquinaria` (
  `id_maquinaria` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `en_uso` tinyint(1) NOT NULL,
  `id_establcmto` int NOT NULL,
  PRIMARY KEY (`id_maquinaria`),
  KEY `FK_Maquinaria_Establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_Maquinaria_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria`
--

LOCK TABLES `maquinaria` WRITE;
/*!40000 ALTER TABLE `maquinaria` DISABLE KEYS */;
/*!40000 ALTER TABLE `maquinaria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `municipio`
--

DROP TABLE IF EXISTS `municipio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `municipio` (
  `codigo` enum('A','B','C','CH','D','E','F','G') NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `municipio`
--

LOCK TABLES `municipio` WRITE;
/*!40000 ALTER TABLE `municipio` DISABLE KEYS */;
/*!40000 ALTER TABLE `municipio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operario_cuadrilla`
--

DROP TABLE IF EXISTS `operario_cuadrilla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operario_cuadrilla` (
  `cedula` int NOT NULL,
  `nom_cuadrilla` varchar(50) NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `FK_OperarioCuadrilla_Cuadrilla` (`nom_cuadrilla`),
  CONSTRAINT `FK_OperarioCuadrilla_Cuadrilla` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`),
  CONSTRAINT `FK_OperarioCuadrilla_Usuario` FOREIGN KEY (`nom_cuadrilla`) REFERENCES `cuadrilla` (`nom_cuadrilla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operario_cuadrilla`
--

LOCK TABLES `operario_cuadrilla` WRITE;
/*!40000 ALTER TABLE `operario_cuadrilla` DISABLE KEYS */;
/*!40000 ALTER TABLE `operario_cuadrilla` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operario_establcmto`
--

DROP TABLE IF EXISTS `operario_establcmto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operario_establcmto` (
  `cedula` int NOT NULL,
  `id_establcmto` int NOT NULL,
  PRIMARY KEY (`cedula`),
  KEY `FK_OperarioEst_Establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_OperarioEst_Establecimiento` FOREIGN KEY (`id_establcmto`) REFERENCES `establecimiento` (`id_establcmto`),
  CONSTRAINT `FK_OperarioEst_Usuario` FOREIGN KEY (`cedula`) REFERENCES `usuario` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operario_establcmto`
--

LOCK TABLES `operario_establcmto` WRITE;
/*!40000 ALTER TABLE `operario_establcmto` DISABLE KEYS */;
/*!40000 ALTER TABLE `operario_establcmto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ruta`
--

DROP TABLE IF EXISTS `ruta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ruta` (
  `id_ruta` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ruta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ruta`
--

LOCK TABLES `ruta` WRITE;
/*!40000 ALTER TABLE `ruta` DISABLE KEYS */;
/*!40000 ALTER TABLE `ruta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculo`
--

DROP TABLE IF EXISTS `vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculo` (
  `matricula` varchar(10) NOT NULL,
  `estado_optivo` enum('en servicio','en stock','en mantenimiento') NOT NULL,
  PRIMARY KEY (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculo`
--

LOCK TABLES `vehiculo` WRITE;
/*!40000 ALTER TABLE `vehiculo` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehiculo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'databyte'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-23  2:58:46
