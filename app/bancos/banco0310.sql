-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.4.3 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para barbearia
CREATE DATABASE IF NOT EXISTS `barbearia` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `barbearia`;

-- Copiando estrutura para tabela barbearia.agendamento
CREATE TABLE IF NOT EXISTS `agendamento` (
  `agen_id` int NOT NULL AUTO_INCREMENT,
  `agen_data_a` date NOT NULL,
  `agen_hora_a` time NOT NULL,
  `agen_data_c` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int NOT NULL,
  `corte_id` int NOT NULL,
  PRIMARY KEY (`agen_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `corte_id` (`corte_id`),
  CONSTRAINT `agendamento_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `agendamento_ibfk_2` FOREIGN KEY (`corte_id`) REFERENCES `corte` (`corte_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela barbearia.agendamento: ~0 rows (aproximadamente)

-- Copiando estrutura para evento barbearia.apaga_agendamentos_antigos
DELIMITER //
CREATE EVENT `apaga_agendamentos_antigos` ON SCHEDULE EVERY 1 DAY STARTS '2025-10-03 08:40:40' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM Agendamento WHERE agen_data_a < (CURDATE() - INTERVAL 7 DAY)//
DELIMITER ;

-- Copiando estrutura para evento barbearia.apaga_diasinativos_antigos
DELIMITER //
CREATE EVENT `apaga_diasinativos_antigos` ON SCHEDULE EVERY 1 DAY STARTS '2025-10-03 08:40:43' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM dia_inativo WHERE diaInativo_data_inativa < (CURDATE() - INTERVAL 7 DAY)//
DELIMITER ;

-- Copiando estrutura para tabela barbearia.corte
CREATE TABLE IF NOT EXISTS `corte` (
  `corte_id` int NOT NULL AUTO_INCREMENT,
  `corte_nome` varchar(255) NOT NULL,
  `corte_preco` decimal(10,2) NOT NULL,
  `corte_descricao` varchar(255) DEFAULT NULL,
  `corte_foto` varchar(255) NOT NULL,
  PRIMARY KEY (`corte_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela barbearia.corte: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela barbearia.dia_inativo
CREATE TABLE IF NOT EXISTS `dia_inativo` (
  `diaInativo_id` int NOT NULL AUTO_INCREMENT,
  `diaInativo_data_inativa` date DEFAULT NULL,
  `diaInativo_hora_inicio` time DEFAULT NULL,
  `diaInativo_hora_fim` time DEFAULT NULL,
  `diaInativo_motivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`diaInativo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela barbearia.dia_inativo: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela barbearia.estoque
CREATE TABLE IF NOT EXISTS `estoque` (
  `est_id` int NOT NULL AUTO_INCREMENT,
  `est_nome` varchar(255) NOT NULL,
  `est_qtd` int NOT NULL,
  PRIMARY KEY (`est_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela barbearia.estoque: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela barbearia.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `usuario_email` varchar(255) NOT NULL,
  `usuario_nome` varchar(255) NOT NULL,
  `usuario_cpf` varchar(15) NOT NULL,
  `usuario_tel` varchar(14) NOT NULL,
  `usuario_senha` varchar(255) NOT NULL,
  `usuario_tipo` tinyint(1) NOT NULL,
  `usuario_data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copiando dados para a tabela barbearia.usuario: ~6 rows (aproximadamente)
INSERT INTO `usuario` (`usuario_id`, `usuario_email`, `usuario_nome`, `usuario_cpf`, `usuario_tel`, `usuario_senha`, `usuario_tipo`, `usuario_data_cadastro`) VALUES
	(2, 'jonatas@jonatas.com', 'jonatas', '112312313123', '11984928172', '123', 0, '2025-10-03 17:22:39'),
	(3, 'jonatasw@jonatas.com', 'jonatas', '111111111', '123123123', '$2y$10$QcnxE3fuNkyYkpqGhPKkZORgeKnMO7uSVfX0.uLB05WR6DOZF6LIa', 0, '2025-10-03 17:22:39'),
	(4, 'oi@oi.com', 'oi', '123', '123', '$2y$10$.ZSMb9GBblnjTSmZRDu1vu9xzwPtaRxi7fCYSNqsvTOKPLE3Ki15K', 0, '2025-10-03 17:22:39'),
	(5, 'jonatas@jonatas1.com', 'jonatas', '123123123123', '123123123', '$2y$10$38ggx5NNV9dvuzXSFLRe9Ox7WNhZvCU8WUuFh7VgLTRbMd2NQd6Ri', 0, '2025-10-03 17:22:39'),
	(6, 'ganley@a.com', 'ganley', '12312312321', '123123123', '$2y$10$at7V6Mo8ZntPRMUGQAjKv.vqFx/gLv3xrKJWVbBw1hHVkTKYS4r32', 0, '2025-10-03 17:22:39'),
	(8, 'admin@admin.com', 'admin', '1212121212', '12121212121', '$2y$10$m6kytpn06pC0nUhkrcz9fuKjru/uzHDtkZWATdGACyhRe4sFlLDAe', 1, '2025-10-03 17:22:39');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
