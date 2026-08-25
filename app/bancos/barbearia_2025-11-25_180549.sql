-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: barbearia
-- ------------------------------------------------------
-- Server version	8.4.3

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
  `agen_status` enum('pendente','concluido','excluido') NOT NULL,
  PRIMARY KEY (`agen_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `corte_id` (`corte_id`),
  CONSTRAINT `agendamento_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `agendamento_ibfk_2` FOREIGN KEY (`corte_id`) REFERENCES `corte` (`corte_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agendamento`
--

/*!40000 ALTER TABLE `agendamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `agendamento` ENABLE KEYS */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `registrahistoricoAgendamento` AFTER UPDATE ON `agendamento` FOR EACH ROW BEGIN
  DECLARE nome_cliente VARCHAR(255);
  IF OLD.agen_status <> NEW.agen_status THEN

    SELECT usuario_nome INTO nome_cliente
    FROM usuario
    WHERE usuario_id = NEW.usuario_id;
    INSERT INTO HistoricoAgendamento (agen_id, usuario_nome, status_antigo, status_novo, data_alteracao) 
    VALUES (OLD.agen_id, nome_cliente, OLD.agen_status, NEW.agen_status, NOW());
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corte`
--

/*!40000 ALTER TABLE `corte` DISABLE KEYS */;
INSERT INTO `corte` VALUES (1,'Corte Social',24.99,'O corte ideal para você!'),(2,'Navalhado',29.99,'Cabelo na régua total.'),(3,'Barba',19.99,'Barba feita com perfeição.'),(4,'Sobrancelha',9.99,'Definição precisa.'),(5,'Completo',59.99,'Corte + barba+ sobrancelha.'),(6,'Pintar',29.99,'Pintura capilar profissional.'),(7,'Corte Social',24.99,'O corte ideal para você!'),(8,'Navalhado',29.99,'Cabelo na régua total.'),(9,'Barba',19.99,'Barba feita com perfeição.'),(10,'Sobrancelha',9.99,'Definição precisa.'),(11,'Completo',59.99,'Corte + barba+ sobrancelha.'),(12,'Pintar',29.99,'Pintura capilar profissional.');
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
  `usuario_id` int NOT NULL,
  PRIMARY KEY (`diaInativo_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `dia_inativo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `usuario_data_cadastro` timestamp NOT NULL,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (12,'admin@admin.com','Admin da Silva','admin','50198196890','11984928172','$argon2id$v=19$m=65536,t=4,p=1$ZWdkcUZ5YjF5cGwxYjVYUA$VQohFYgAoJICJ7xecuPrT2kZw8MEXEsk+0GOFC8b+4I',1,'2025-10-21 14:39:49'),(13,'usuario@usuario.com','Usuario Normal','usuarioteste','73813877434','11986588444','$argon2id$v=19$m=65536,t=4,p=1$ZTA4SWt0R2YzNHQ0YlFuOA$0rOkg2+plTFwgUdUTiljGTxCq0bAkRLjqUsSU7HzurY',0,'2025-10-21 14:40:16'),(14,'bruno.oliveira1@example.com','Bruno Oliveira','bruno.oliveira1','11122233301','11988887701','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-25 13:15:33'),(15,'carla.souza2@example.com','Carla Souza','carla.souza2','22233344402','21977776602','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-10 21:40:12'),(16,'daniel.costa3@example.com','Daniel Costa','daniel.costa3','33344455503','31966665503','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-19 12:22:05'),(17,'eduarda.pereira4@example.com','Eduarda Pereira','eduarda.pereira4','44455566604','41955554404','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-03 17:50:48'),(18,'felipe.almeida5@example.com','Felipe Almeida','felipe.almeida5','55566677705','51944443305','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-03-28 23:11:19'),(19,'gabriela.lima6@example.com','Gabriela Lima','gabriela.lima6','66677788806','61933332206','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-07-15 14:33:55'),(20,'heitor.martins7@example.com','Heitor Martins','heitor.martins7','77788899907','71922221107','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-01 19:20:30'),(21,'isabela.barbosa8@example.com','Isabela Barbosa','isabela.barbosa8','88899900008','81911110008','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-02-18 11:05:10'),(22,'joao.rodrigues9@example.com','João Rodrigues','joao.rodrigues9','99900011109','91900009909','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-06-08 01:45:00'),(23,'admin.chefe10@example.com','Admin Chefe','admin.chefe10','10111213110','11999998810','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2024-10-18 22:50:00'),(24,'lucas.ferreira11@example.com','Lucas Ferreira','lucas.ferreira11','12131415111','21988887711','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-04-12 16:00:21'),(25,'mariana.goncalves12@example.com','Mariana Gonçalves','mariana.goncalves12','13141516112','31977776612','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-29 22:55:43'),(26,'nicolas.ribeiro13@example.com','Nicolas Ribeiro','nicolas.ribeiro13','14151617113','41966665513','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-05 10:10:00'),(27,'otavio.carvalho14@example.com','Otávio Carvalho','otavio.carvalho14','15161718114','51955554414','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-03-22 15:30:15'),(28,'pietra.dias15@example.com','Pietra Dias','pietra.dias15','16171819115','61944443315','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-07-10 02:59:59'),(29,'quelvin.rocha16@example.com','Quelvin Rocha','quelvin.rocha16','17181920116','71933332216','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-25 13:00:00'),(30,'rafaela.barros17@example.com','Rafaela Barros','rafaela.barros17','18192021117','81922221117','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-01 03:00:01'),(31,'sofia.cunha18@example.com','Sofia Cunha','sofia.cunha18','19202122118','91911110018','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-18 18:45:18'),(32,'thiago.mendes19@example.com','Thiago Mendes','thiago.mendes19','20212223119','11900009919','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-03 00:00:00'),(33,'admin.suporte20@example.com','Admin Suporte','admin.suporte20','21222324120','21999998820','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2024-11-20 22:50:00'),(34,'vinicius.nogueira21@example.com','Vinicius Nogueira','vinicius.nogueira21','22232425121','31988887721','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-02-04 20:30:00'),(35,'yasmin.pinto22@example.com','Yasmin Pinto','yasmin.pinto22','23242526122','41977776622','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-06-21 14:11:11'),(36,'zoe.santos23@example.com','Zoe Santos','zoe.santos23','24252627123','51966665523','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-08 17:00:00'),(37,'arthur.silva24@example.com','Arthur Silva','arthur.silva24','25262728124','61955554424','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-11-11 14:11:11'),(38,'bento.oliveira25@example.com','Bento Oliveira','bento.oliveira25','26272829125','71944443325','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-03-01 12:00:00'),(39,'caio.souza26@example.com','Caio Souza','caio.souza26','27282930126','81933332226','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-07-18 21:30:00'),(40,'davi.costa27@example.com','Davi Costa','davi.costa27','28293031127','91922221127','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-04 23:00:00'),(41,'enzo.pereira28@example.com','Enzo Pereira','enzo.pereira28','29303132128','11911110028','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-02-21 19:15:00'),(42,'gael.almeida29@example.com','Gael Almeida','gael.almeida29','30313233129','21900009929','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-06-10 16:45:00'),(43,'admin.financeiro30@example.com','Admin Financeiro','admin.financeiro30','31323334130','31999998830','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2024-12-24 22:50:00'),(44,'helena.lima31@example.com','Helena Lima','helena.lima31','32333435131','41988887731','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-04-20 13:20:30'),(45,'laura.martins32@example.com','Laura Martins','laura.martins32','33343536132','51977776632','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-06 20:00:00'),(46,'manuela.barbosa33@example.com','Manuela Barbosa','manuela.barbosa33','34353637133','61966665533','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-11-01 11:30:00'),(47,'theo.rodrigues34@example.com','Theo Rodrigues','theo.rodrigues34','35363738134','71955554434','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-02-17 17:10:00'),(48,'valentina.ferreira35@example.com','Valentina Ferreira','valentina.ferreira35','36373839135','81944443335','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-06-05 23:20:20'),(49,'alice.goncalves36@example.com','Alice Gonçalves','alice.goncalves36','37383940136','91933332236','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-21 12:45:00'),(50,'bernardo.ribeiro37@example.com','Bernardo Ribeiro','bernardo.ribeiro37','38394041137','11922221137','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-18 14:00:00'),(51,'cecilia.carvalho38@example.com','Cecília Carvalho','cecilia.carvalho38','39404142138','21911110038','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-04-06 19:50:00'),(52,'davi.dias39@example.com','Davi Dias','davi.dias39','40414243139','31900009939','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-24 01:15:00'),(53,'admin.marketing40@example.com','Admin Marketing','admin.marketing40','41424344140','41999998840','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2025-01-15 22:50:00'),(54,'esther.rocha41@example.com','Esther Rocha','esther.rocha41','42434445141','51988887741','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-02 13:10:10'),(55,'felipe.barros42@example.com','Felipe Barros','felipe.barros42','43444546142','61977776642','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-18 21:00:00'),(56,'giovanna.cunha43@example.com','Giovanna Cunha','giovanna.cunha43','44454647143','71966665543','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-15 12:30:00'),(57,'gustavo.mendes44@example.com','Gustavo Mendes','gustavo.mendes44','45464748144','81955554444','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-04-01 18:00:00'),(58,'heloisa.nogueira45@example.com','Heloísa Nogueira','heloisa.nogueira45','46474849145','91944443345','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-19 00:45:00'),(59,'isaac.pinto46@example.com','Isaac Pinto','isaac.pinto46','47484950146','11933332246','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-14 15:00:00'),(60,'julia.santos47@example.com','Júlia Santos','julia.santos47','48495051147','21922221147','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-20 17:30:00'),(61,'lara.silva48@example.com','Lara Silva','lara.silva48','49505152148','31911110048','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-08 22:15:00'),(62,'miguel.oliveira49@example.com','Miguel Oliveira','miguel.oliveira49','50515253149','41900009949','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-24 11:45:00'),(63,'admin.geral50@example.com','Admin Geral','admin.geral50','51525354150','51999998850','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2025-02-28 22:50:00'),(64,'noah.souza51@example.com','Noah Souza','noah.souza51','52535455151','61988887751','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-06-16 14:20:00'),(65,'olivia.costa52@example.com','Olivia Costa','olivia.costa52','53545556152','71977776652','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-02 19:00:00'),(66,'pedro.pereira53@example.com','Pedro Pereira','pedro.pereira53','54555657153','81966665553','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-08 23:30:00'),(67,'ravi.almeida54@example.com','Ravi Almeida','ravi.almeida54','55565758154','91955554454','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-25 12:10:00'),(68,'samuel.lima55@example.com','Samuel Lima','samuel.lima55','56575859155','11944443355','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-10 17:00:00'),(69,'valentim.martins56@example.com','Valentim Martins','valentim.martins56','57585960156','21933332256','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-28 20:45:00'),(70,'yuri.barbosa57@example.com','Yuri Barbosa','yuri.barbosa57','58596061157','31922221157','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-04-17 02:00:00'),(71,'anaclara.rodrigues58@example.com','Ana Clara Rodrigues','anaclara.rodrigues58','59606162158','41911110058','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-02 11:00:00'),(72,'benjamin.ferreira59@example.com','Benjamin Ferreira','benjamin.ferreira59','60616263159','51900009959','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-19 13:30:00'),(73,'admin.pessoal60@example.com','Admin Pessoal','admin.pessoal60','61626364160','61999998860','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2025-03-31 22:50:00'),(74,'clarice.goncalves61@example.com','Clarice Gonçalves','clarice.goncalves61','62636465161','71988887761','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-07-18 18:15:15'),(75,'dom.ribeiro62@example.com','Dom Ribeiro','dom.ribeiro62','63646566162','81977776662','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-05 16:00:00'),(76,'elisa.carvalho63@example.com','Elisa Carvalho','elisa.carvalho63','64656667163','91966665563','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-22 22:45:00'),(77,'frederico.dias64@example.com','Frederico Dias','frederico.dias64','65666768164','11955554464','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-10 11:15:00'),(78,'guilherme.rocha65@example.com','Guilherme Rocha','guilherme.rocha65','66676869165','21944443365','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-26 15:45:00'),(79,'hadassa.barros66@example.com','Hadassa Barros','hadassa.barros66','67686970166','31933332266','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-03 01:00:00'),(80,'icaro.cunha67@example.com','Ícaro Cunha','icaro.cunha67','68697071167','41922221167','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-03-20 20:30:00'),(81,'joaquim.mendes68@example.com','Joaquim Mendes','joaquim.mendes68','69707172168','51911110068','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-07-07 10:07:07'),(82,'liz.nogueira69@example.com','Liz Nogueira','liz.nogueira69','70717273169','61900009969','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-23 18:30:00'),(83,'admin.master70@example.com','Admin Master','admin.master70','71727374170','71999998870','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2025-04-01 22:50:00'),(84,'matheus.pinto71@example.com','Matheus Pinto','matheus.pinto71','72737475171','81988887771','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-19 22:00:00'),(85,'nathan.santos72@example.com','Nathan Santos','nathan.santos72','73747576172','91977776672','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-06 21:45:00'),(86,'oliver.silva73@example.com','Oliver Silva','oliver.silva73','74757677173','11966665573','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-23 13:45:00'),(87,'perola.oliveira74@example.com','Pérola Oliveira','perola.oliveira74','75767778174','21955554474','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-11 14:55:00'),(88,'ryan.souza75@example.com','Ryan Souza','ryan.souza75','76777879175','31944443375','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-27 19:30:00'),(89,'sarah.costa76@example.com','Sarah Costa','sarah.costa76','77787980176','41933332276','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-03 17:00:00'),(90,'tomas.pereira77@example.com','Tomás Pereira','tomas.pereira77','78798081177','51922221177','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-03-21 23:00:00'),(91,'ulisses.almeida78@example.com','Ulisses Almeida','ulisses.almeida78','79808182178','61911110078','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-07-08 12:00:00'),(92,'vitoria.lima79@example.com','Vitória Lima','vitoria.lima79','80818283179','71900009979','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-24 16:13:13'),(93,'admin.contas80@example.com','Admin Contas','admin.contas80','81828384180','81999998880','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2025-05-15 22:50:00'),(94,'william.martins81@example.com','William Martins','william.martins81','82838485181','91988887781','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-01 13:00:00'),(95,'xavier.barbosa82@example.com','Xavier Barbosa','xavier.barbosa82','83848586182','11977776682','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-11-15 18:30:00'),(96,'yago.rodrigues83@example.com','Yago Rodrigues','yago.rodrigues83','84858687183','21966665583','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-03-03 21:18:18'),(97,'ziraldo.ferreira84@example.com','Ziraldo Ferreira','ziraldo.ferreira84','85868788184','31955554484','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-07-20 23:20:20'),(98,'anajulia.goncalves85@example.com','Ana Julia Gonçalves','anajulia.goncalves85','86878889185','41944443385','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-07 12:09:09'),(99,'breno.ribeiro86@example.com','Breno Ribeiro','breno.ribeiro86','87888990186','51933332286','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-24 19:45:00'),(100,'caua.carvalho87@example.com','Cauã Carvalho','caua.carvalho87','88899091187','61922221187','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-12 15:12:12'),(101,'elisa.dias88@example.com','Elisa Dias','elisa.dias88','89909192188','71911110088','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-28 20:17:17'),(102,'francisco.rocha89@example.com','Francisco Rocha','francisco.rocha89','90919293189','81900009989','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-04 22:30:00'),(103,'admin.vendas90@example.com','Admin Vendas','admin.vendas90','91929394190','91999998890','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2025-06-20 22:50:00'),(104,'gael.barros91@example.com','Gael Barros','gael.barros91','92939495191','11988887791','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-09 13:40:00'),(105,'heloisa.cunha92@example.com','Heloísa Cunha','heloisa.cunha92','93949596192','21977776692','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-01-26 16:20:00'),(106,'isis.mendes93@example.com','Isis Mendes','isis.mendes93','94959697193','31966665593','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-05-14 23:50:00'),(107,'jose.nogueira94@example.com','José Nogueira','jose.nogueira94','95969798194','41955554494','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-09-29 14:30:00'),(108,'kaique.pinto95@example.com','Kaique Pinto','kaique.pinto95','96979899195','51944443395','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2024-12-06 17:14:14'),(109,'luan.santos96@example.com','Luan Santos','luan.santos96','97989900196','61933332296','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-04-22 20:00:00'),(110,'mariaalice.silva97@example.com','Maria Alice Silva','mariaalice.silva97','98990001197','71922221197','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-08-08 11:08:08'),(111,'nicolas.oliveira98@example.com','Nicolas Oliveira','nicolas.oliveira98','99000102198','81911110098','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-10-25 13:10:10'),(112,'otavio.souza99@example.com','Otávio Souza','otavio.souza99','00010203199','91900009999','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',0,'2025-02-12 02:30:00'),(113,'admin.final100@example.com','Admin Final','admin.final100','01020304100','11999998800','$2y$10$3f2d2gH.i9j8k7L.m6n5o4p3q2r1s0t.u9v8w7x6y5z4a3b2c1d0e',1,'2025-07-31 22:50:00');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_usuario_delete` AFTER DELETE ON `usuario` FOR EACH ROW BEGIN
    INSERT INTO usuarios_deletados (
        usuario_id, 
        usuario_email, 
        usuario_nome, 
        usuario_cpf, 
        usuario_tel, 
        usuario_senha, 
        usuario_tipo, 
        data_exclusao
    )
    VALUES (
        OLD.usuario_id, 
        OLD.usuario_email, 
        OLD.usuario_nome, 
        OLD.usuario_cpf, 
        OLD.usuario_tel, 
        OLD.usuario_senha, 
        OLD.usuario_tipo, 
        NOW()
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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

-- Dump completed on 2025-11-25 18:06:07
