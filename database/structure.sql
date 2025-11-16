-- SABi Estrutura do Banco de Dados
-- Sistema Acadêmico de Biblioteca Inteligente

CREATE DATABASE IF NOT EXISTS sabi_academico;
USE sabi_academico;

-- Tabelas baseadas na estrutura real do projeto
CREATE TABLE IF NOT EXISTS usuarios_personalizados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_login VARCHAR(60) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    display_name VARCHAR(250),
    user_type ENUM('aluno', 'professor', 'admin') DEFAULT 'aluno',
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS livro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(500) NOT NULL,
    autor VARCHAR(300) NOT NULL,
    isbn VARCHAR(20),
    editora VARCHAR(200),
    ano_publicacao INT,
    descricao TEXT,
    imagem_capa VARCHAR(500),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classificacao_cdd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_cdd VARCHAR(50) NOT NULL,
    descricao VARCHAR(5
);

CREATE TABLE IF NOT EXISTS localizacao_fisica (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livro_id INT,
    lado ENUM('esquerdo', 'direito') NOT NULL,
    prateleira INT NOT NULL,
    posicao INT NOT NULL,
    FOREIGN KEY (livro_id) REFERENCES livro(id)
);

CREATE TABLE IF NOT EXISTS estante_pessoal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    livro_id INT,
    status_leitura ENUM
('quero_ler', 'lendo', 'lido', 'pausado') DEFAULT 'quero_ler',
    data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios_personalizados(id),
    FOREIGN KEY (livro_id) REFERENCES livro(id)
);

CREATE TABLE IF NOT EXISTS avaliacoes_sabi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    livro_id INT,
    avaliacao INT NOT NULL CHECK (avaliacao BETWEEN 1 AND 5),
    comentario TEXT,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios_personalizados(id),
    FOREIGN KEY (livro_id) REFERENCES livro(id)
);

-- Dados de exemplo para CDD
INSERT INTO classificacao_cdd (codigo_cdd, descricao) VALUES
('000', 'Ciência da Computação, Informação e Obras Gerais'),
('100', 'Filosofia e Psicologia'),
('300', 'Ciências Sociais'),
('500', 'Ciências'),
('600', 'Tecnologia'),
('800', 'Literatura');-- SABi Database Structure
-- Academic Library Management System
-- Tables: books, users, shelves, ratings, etc.

CREATE DATABASE IF NOT EXISTS sabi_academico;
USE sabi_academico;

