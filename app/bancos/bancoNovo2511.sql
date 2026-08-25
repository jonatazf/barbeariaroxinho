CREATE DATABASE Barbearia;
USE Barbearia;

SET GLOBAL event_scheduler = ON;


CREATE TABLE IF NOT EXISTS usuario (
	usuario_id INT AUTO_INCREMENT Primary Key NOT NULL,
	usuario_email VARCHAR(255) NOT NULL,
	usuario_nome VARCHAR(255) NOT NULL,
	usuario_user VARCHAR(255) NOT NULL,
	usuario_cpf VARCHAR(15) NOT NULL,
	usuario_tel VARCHAR(14) NOT NULL,
	usuario_senha VARCHAR(255) NOT NULL,
	usuario_tipo boolean NOT NULL,
	usuario_data_cadastro TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS corte (
    corte_id INT AUTO_INCREMENT Primary Key NOT NULL,
    corte_nome VARCHAR(255) NOT NULL,
    corte_preco DECIMAL(10, 2) NOT NULL,
    corte_descricao VARCHAR(255)
);


CREATE TABLE IF NOT EXISTS agendamento (
	agen_id INT AUTO_INCREMENT Primary Key NOT NULL,
	agen_data_a DATE NOT NULL,
	agen_hora_a TIME NOT NULL,
	agen_data_c TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	usuario_id INT NOT NULL,
	corte_id INT NOT NULL,
	agen_status ENUM ('pendente', 'concluido', 'excluido') NOT NULL,
	FOREIGN KEY (usuario_id) REFERENCES usuario (usuario_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (corte_id) REFERENCES corte (corte_id) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS dia_inativo (
	diaInativo_id INT AUTO_INCREMENT PRIMARY KEY,
	diaInativo_data_inativa DATE,
	diaInativo_hora_inicio TIME,
	diaInativo_hora_fim TIME,
	diaInativo_motivo VARCHAR(255),
	usuario_id INT NOT NULL,
	FOREIGN KEY (usuario_id) REFERENCES usuario (usuario_id) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS estoque (
	est_id INT AUTO_INCREMENT PRIMARY KEY,
	est_nome VARCHAR (255) NOT NULL,
	est_qtd INT NOT NULL
);

CREATE TABLE IF NOT EXISTS HistoricoAgendamento (
	id INT AUTO_INCREMENT PRIMARY KEY,
	agen_id INT NOT NULL,
	usuario_nome VARCHAR(255),
	status_antigo VARCHAR(50),
	status_novo VARCHAR(50),
	data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS usuarios_deletados (
	usuario_id INT NOT NULL,
	usuario_email VARCHAR(255) NOT NULL,
	usuario_nome VARCHAR(255) NOT NULL,
	usuario_cpf VARCHAR(15) NOT NULL,
	usuario_tel VARCHAR(14) NOT NULL,
	usuario_senha VARCHAR(255) NOT NULL,
	usuario_tipo BOOLEAN NOT NULL,
	data_exclusao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE EVENT IF NOT EXISTS apaga_agendamentos_antigos ON SCHEDULE EVERY 1 MONTH
DO DELETE FROM agendamento WHERE agen_status < (CURDATE() - INTERVAL 5 YEAR);

CREATE EVENT IF NOT EXISTS apaga_diasinativos_antigos ON SCHEDULE EVERY 1 MONTH 
DO DELETE FROM dia_inativo WHERE diaInativo_data_inativa < (CURDATE() - INTERVAL 5 YEAR);


DELIMITER $$

CREATE TRIGGER after_usuario_delete
AFTER DELETE ON usuario
FOR EACH ROW
BEGIN
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
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER registrahistoricoAgendamento
AFTER UPDATE ON agendamento
FOR EACH ROW
BEGIN
  DECLARE nome_cliente VARCHAR(255);

  IF OLD.agen_status <> NEW.agen_status THEN

    SELECT usuario_nome INTO nome_cliente
    FROM usuario
    WHERE usuario_id = NEW.usuario_id;

    INSERT INTO HistoricoAgendamento (agen_id, usuario_nome, status_antigo, status_novo, data_alteracao) 
    VALUES (OLD.agen_id, nome_cliente, OLD.agen_status, NEW.agen_status, NOW());

  END IF;
END$$

DELIMITER ;