CREATE DATABASE Barbearia;
USE Barbearia;

CREATE TABLE usuario(
usuario_id INT AUTO_INCREMENT Primary Key NOT NULL,
usuario_email VARCHAR(255) NOT NULL,
usuario_nome VARCHAR(255) NOT NULL,
usuario_senha VARCHAR(255) NOT NULL,
usuario_cpf VARCHAR (15) NOT NULL,
usuario_tel VARCHAR(14) NOT NULL
usuario_adm boolean NOT NULL,
);


CREATE TABLE Corte(
Corte_id INT AUTO_INCREMENT Primary Key NOT NULL,
Corte_nome VARCHAR(255) NOT NULL,
corte_foto VARCHAR(255) NOT NULL,
Corte_preco DECIMAL(10,2) NOT NULL,
Corte_descricao VARCHAR(255)
);

CREATE TABLE Agendamento(
Agen_id INT AUTO_INCREMENT Primary Key NOT NULL,
Agen_horario TIME NOT NULL,
Agen_dia DATE NOT NULL,
Cli_id INT NOT NULL,
Cor_id INT NOT NULL,
FOREIGN KEY (Cli_id) REFERENCES Cliente (Cli_id) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (Cor_id) REFERENCES Corte (Cor_id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO Cliente (Cli_email, Cli_nome, Cli_cpf, Cli_tel)
VALUE
('Lucas@gmail.com', 'Lucas Rolim', '496.356.214-58', '11 97026-7440'),
('Carlos@gmail.com', 'Carlos Oliveira', '895.645.478-89', '11 96456-5326');

INSERT INTO Administrador (Adm_usuario, Adm_senha, Adm_email)
VALUE
('Roxinho', '12345R', 'Roxinho@agmail.com'),
('Ajudante', '54321A', 'Ajudante@gmail.com');

INSERT INTO Corte (Cor_nome, Cor_preco, Cor_descricao)
VALUE
('Social', 29.99, 'Social baixo'),
('Completo', 59.99, 'AAAAAAAA');

INSERT INTO Agendamento (Agen_horario, Agen_dia, Cli_id, Cor_id)
VALUE
('15:00:00', '2025-05-16', 2, 2),
('16:30:00', '2025-02-17', 1, 1);
