CREATE DATABASE Barbearia;
USE Barbearia;

CREATE TABLE usuario (
	usuario_id INT AUTO_INCREMENT Primary Key NOT NULL,
	usuario_email VARCHAR(255) NOT NULL,
	usuario_nome VARCHAR(255) NOT NULL,
	usuario_cpf VARCHAR(15) NOT NULL,
	usuario_tel VARCHAR(14) NOT NULL,
	usuario_senha VARCHAR(255) NOT NULL,
	usuario_tipo boolean NOT NULL
);

CREATE TABLE corte (
    corte_id INT AUTO_INCREMENT Primary Key NOT NULL,
    corte_nome VARCHAR(255) NOT NULL,
    corte_preco DECIMAL(10, 2) NOT NULL,
    corte_descricao VARCHAR(255),
    corte_foto VARCHAR(255) NOT NULL
);


CREATE TABLE agendamento (
    agen_id INT AUTO_INCREMENT Primary Key NOT NULL,
    agen_data_a DATE NOT NULL,
    agen_hora_a TIME NOT NULL,
    agen_data_c TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    corte_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario (usuario_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (corte_id) REFERENCES corte (corte_id) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE dia_inativo (
    diaInativo_id INT AUTO_INCREMENT PRIMARY KEY,
    diaInativo_data_inativa DATE,
    diaInativo_hora_inicio TIME,
    diaInativo_hora_fim TIME,
    diaInativo_motivo VARCHAR(255)
);


CREATE TABLE estoque (
	est_id INT AUTO_INCREMENT PRIMARY KEY,
	est_nome VARCHAR (255) NOT NULL,
	est_qtd INT NOT NULL
);
	


SET GLOBAL event_scheduler = ON;

CREATE EVENT IF NOT EXISTS apaga_agendamentos_antigos ON SCHEDULE EVERY 1 DAY DO DELETE FROM Agendamento WHERE agen_data_a < (CURDATE() - INTERVAL 7 DAY);
CREATE EVENT IF NOT EXISTS apaga_diasinativos_antigos ON SCHEDULE EVERY 1 DAY DO DELETE FROM dia_inativo WHERE diaInativo_data_inativa < (CURDATE() - INTERVAL 7 DAY);
