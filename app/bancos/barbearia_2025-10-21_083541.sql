-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: barbearia
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `agendamento`
--

DROP TABLE IF EXISTS `agendamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agendamento` (
  `agen_id` int NOT NULL AUTO_INCREMENT,
  `agen_data_a` date NOT NULL,
  `agen_hora_a` time NOT NULL,
  `agen_data_c` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int NOT NULL,
  `corte_id` int NOT NULL,
  `agen_status` varchar(50) NOT NULL DEFAULT 'Confirmado',
  PRIMARY KEY (`agen_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `corte_id` (`corte_id`),
  CONSTRAINT `agendamento_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `agendamento_ibfk_2` FOREIGN KEY (`corte_id`) REFERENCES `corte` (`corte_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agendamento`
--

/*!40000 ALTER TABLE `agendamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `agendamento` ENABLE KEYS */;

--
-- Table structure for table `corte`
--

DROP TABLE IF EXISTS `corte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corte` (
  `corte_id` int NOT NULL AUTO_INCREMENT,
  `corte_nome` varchar(255) NOT NULL,
  `corte_preco` decimal(10,2) NOT NULL,
  `corte_descricao` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`corte_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corte`
--

/*!40000 ALTER TABLE `corte` DISABLE KEYS */;
INSERT INTO `corte` VALUES (2,'Corte Social',24.99,'O corte ideal para você!'),(3,'Navalhado',29.99,'Cabelo na régua total.'),(4,'Barba',19.99,'Barba feita com perfeição.'),(5,'Sobrancelha',9.99,'Definição precisa.'),(6,'Completo',59.99,'Corte + barba+ sobrancelha.'),(7,'Pintar',29.99,'Pintura capilar profissional.');
/*!40000 ALTER TABLE `corte` ENABLE KEYS */;

--
-- Table structure for table `dia_inativo`
--

DROP TABLE IF EXISTS `dia_inativo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dia_inativo` (
  `diaInativo_id` int NOT NULL AUTO_INCREMENT,
  `diaInativo_data_inativa` date DEFAULT NULL,
  `diaInativo_hora_inicio` time DEFAULT NULL,
  `diaInativo_hora_fim` time DEFAULT NULL,
  `diaInativo_motivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`diaInativo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dia_inativo`
--

/*!40000 ALTER TABLE `dia_inativo` DISABLE KEYS */;
/*!40000 ALTER TABLE `dia_inativo` ENABLE KEYS */;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estoque` (
  `est_id` int NOT NULL AUTO_INCREMENT,
  `est_nome` varchar(255) NOT NULL,
  `est_qtd` int NOT NULL,
  PRIMARY KEY (`est_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
/*!40000 ALTER TABLE `estoque` ENABLE KEYS */;

--
-- Table structure for table `historicoagendamento`
--

DROP TABLE IF EXISTS `historicoagendamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historicoagendamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agen_id` int NOT NULL,
  `usuario_nome` varchar(255) DEFAULT NULL,
  `status_antigo` varchar(50) DEFAULT NULL,
  `status_novo` varchar(50) DEFAULT NULL,
  `data_alteracao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historicoagendamento`
--

/*!40000 ALTER TABLE `historicoagendamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `historicoagendamento` ENABLE KEYS */;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `usuario_email` varchar(255) NOT NULL,
  `usuario_nome` varchar(255) NOT NULL,
  `usuario_user` varchar(255) NOT NULL,
  `usuario_cpf` varchar(15) NOT NULL,
  `usuario_tel` varchar(14) NOT NULL,
  `usuario_senha` varchar(255) NOT NULL,
  `usuario_tipo` tinyint(1) NOT NULL,
  `usuario_data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (12,'admin@admin.com','Admin da Silva','admin','50198196890','11984928172','$argon2id$v=19$m=65536,t=4,p=1$VnNhRzZZbEh4REt6ZzJ6Lw$Ckof4vcml/O8l4a44DLaKpdJ1zattt80DXTlFQUf2Yw',0,'2025-10-21 11:34:29'),(13,'userteste@userteste.com','Usuario Normal','userteste','73813877434','11986588444','$argon2id$v=19$m=65536,t=4,p=1$eE5ocW02YTN4ajhuNEdjOQ$sXeXVF7wpAmMmPpKdjr6ZB8dlgGLUlNba7GMfAgZA18',0,'2025-10-21 11:34:57');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;

--
-- Table structure for table `usuarios_deletados`
--

DROP TABLE IF EXISTS `usuarios_deletados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_deletados` (
  `usuario_id` int NOT NULL,
  `usuario_email` varchar(255) NOT NULL,
  `usuario_nome` varchar(255) NOT NULL,
  `usuario_cpf` varchar(15) NOT NULL,
  `usuario_tel` varchar(14) NOT NULL,
  `usuario_senha` varchar(255) NOT NULL,
  `usuario_tipo` tinyint(1) NOT NULL,
  `data_exclusao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_deletados`
--

/*!40000 ALTER TABLE `usuarios_deletados` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios_deletados` ENABLE KEYS */;

--
-- Dumping routines for database 'barbearia'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-21  8:35:58
