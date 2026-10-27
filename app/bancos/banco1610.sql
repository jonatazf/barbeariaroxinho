
create database barbearia;

use barbearia;

DROP TABLE IF EXISTS `agendamento`;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `agendamento` VALUES (2,'2025-10-11','12:00:00','2025-10-07 12:38:29',9,3,'Confirmado'),(3,'2025-10-10','12:00:00','2025-10-07 13:17:40',11,3,'Confirmado');

INSERT INTO `agendamento` VALUES (DEFAULT, '2025-09-30', '12:00:00', DEFAULT, 9, 3, 'Confirmado');


DROP TABLE IF EXISTS `corte`;

CREATE TABLE `corte` (
  `corte_id` int NOT NULL AUTO_INCREMENT,
  `corte_nome` varchar(255) NOT NULL,
  `corte_preco` decimal(10,2) NOT NULL,
  `corte_descricao` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`corte_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `corte` VALUES (2,'Corte Social',24.99,'O corte ideal para você!'),(3,'Navalhado',29.99,'Cabelo na régua total.'),(4,'Barba',19.99,'Barba feita com perfeição.'),(5,'Sobrancelha',9.99,'Definição precisa.'),(6,'Completo',59.99,'Corte + barba+ sobrancelha.'),(7,'Pintar',29.99,'Pintura capilar profissional.');

DROP TABLE IF EXISTS `dia_inativo`;
CREATE TABLE `dia_inativo` (
  `diaInativo_id` int NOT NULL AUTO_INCREMENT,
  `diaInativo_data_inativa` date DEFAULT NULL,
  `diaInativo_hora_inicio` time DEFAULT NULL,
  `diaInativo_hora_fim` time DEFAULT NULL,
  `diaInativo_motivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`diaInativo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `estoque`;

CREATE TABLE `estoque` (
  `est_id` int NOT NULL AUTO_INCREMENT,
  `est_nome` varchar(255) NOT NULL,
  `est_qtd` int NOT NULL,
  PRIMARY KEY (`est_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `usuario`;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuario` VALUES (2,'jonatas@jonatas.com','jonatas','112312313123','11984928172','123',1,'2025-10-03 20:22:39'),(3,'jonatasw@jonatas.com','jonatas','111111111','123123123','$2y$10$QcnxE3fuNkyYkpqGhPKkZORgeKnMO7uSVfX0.uLB05WR6DOZF6LIa',0,'2025-10-03 20:22:39'),(4,'oi@oi.com','oi','123','123','$2y$10$.ZSMb9GBblnjTSmZRDu1vu9xzwPtaRxi7fCYSNqsvTOKPLE3Ki15K',0,'2025-10-03 20:22:39'),(5,'jonatas@jonatas1.com','jonatas','123123123123','123123123','$2y$10$38ggx5NNV9dvuzXSFLRe9Ox7WNhZvCU8WUuFh7VgLTRbMd2NQd6Ri',0,'2025-10-03 20:22:39'),(8,'admin@admin.com','admin','1212121212','12121212121','$2y$10$m6kytpn06pC0nUhkrcz9fuKjru/uzHDtkZWATdGACyhRe4sFlLDAe',1,'2025-10-03 20:22:39'),(9,'jonatassoares@gmail.com','jontaz','50198196890','11984928172','$2y$10$QOelukFTCa4b7ktE7LAAtuMdwvhS.lWmu78bVkTNnPcGJdxWLM8V6',0,'2025-10-07 12:38:04'),(10,'Lusca@gmail.com','Lucas Rolim de Amorim','33899754879','11984928172','$2y$10$baLZslAneDSKsysLy4y6Rexr0Tp990FU/kavE87HmAUvx9/aqa6oW',1,'2025-10-07 13:12:48'),(11,'Luscass@gmail.com','Lusca','20202020202','20202002020','$2y$10$dQ9vn/hiyubEzSZxg34EdOl7yjsiuVfK1dmrQ2EHw5OhHmfVNrTcK',0,'2025-10-07 13:15:33');

CREATE TABLE HistoricoAgendamento (
	id INT AUTO_INCREMENT PRIMARY KEY,
	agen_id INT NOT NULL,
	usuario_nome VARCHAR(255),
	status_antigo VARCHAR(50),
	status_novo VARCHAR(50),
	data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE usuarios_deletados (
	usuario_id INT NOT NULL,
	usuario_email VARCHAR(255) NOT NULL,
	usuario_nome VARCHAR(255) NOT NULL,
	usuario_cpf VARCHAR(15) NOT NULL,
	usuario_tel VARCHAR(14) NOT NULL,
	usuario_senha VARCHAR(255) NOT NULL,
	usuario_tipo BOOLEAN NOT NULL,
	data_exclusao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);