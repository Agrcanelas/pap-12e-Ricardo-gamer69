-- Base de Dados DOA+ - Plataforma de Donativos
-- Criada para o projeto PAP

-- Criar base de dados
CREATE DATABASE IF NOT EXISTS doa_plus;
USE doa_plus;

-- Tabela de utilizadores
CREATE TABLE IF NOT EXISTS utilizadores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_utilizador ENUM('doador', 'instituicao', 'admin') DEFAULT 'doador',
    data_registo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
);

-- Tabela de campanhas
CREATE TABLE IF NOT EXISTS campanhas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    descricao LONGTEXT NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    valor_objetivo DECIMAL(10, 2) NOT NULL,
    valor_angariado DECIMAL(10, 2) DEFAULT 0,
    instituicao VARCHAR(100) NOT NULL,
    id_criador INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_inicio DATETIME,
    data_fim DATETIME,
    status ENUM('pendente', 'ativa', 'concluida', 'cancelada') DEFAULT 'pendente',
    imagem VARCHAR(255),
    FOREIGN KEY (id_criador) REFERENCES utilizadores(id)
);

-- Tabela de doações
CREATE TABLE IF NOT EXISTS doacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_campanha INT NOT NULL,
    id_doador INT NOT NULL,
    montante DECIMAL(10, 2) NOT NULL,
    data_doacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    mensagem TEXT,
    anonimo BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_campanha) REFERENCES campanhas(id),
    FOREIGN KEY (id_doador) REFERENCES utilizadores(id)
);

-- Tabela para rastreio de pagamentos (opcional)
CREATE TABLE IF NOT EXISTS pagamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_doacao INT NOT NULL UNIQUE,
    metodo_pagamento ENUM('cartao_credito', 'paypal', 'transferencia', 'outro') NOT NULL,
    referencia_pagamento VARCHAR(100),
    status ENUM('pendente', 'confirmado', 'recusado') DEFAULT 'pendente',
    data_pagamento TIMESTAMP,
    FOREIGN KEY (id_doacao) REFERENCES doacoes(id)
);

-- Tabela de comentarios/atualizacoes nas campanhas
CREATE TABLE IF NOT EXISTS atualizacoes_campanha (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_campanha INT NOT NULL,
    id_autor INT NOT NULL,
    titulo VARCHAR(150),
    conteudo LONGTEXT NOT NULL,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_campanha) REFERENCES campanhas(id),
    FOREIGN KEY (id_autor) REFERENCES utilizadores(id)
);

-- Índices para melhor performance
CREATE INDEX idx_campanhas_status ON campanhas(status);
CREATE INDEX idx_campanhas_categoria ON campanhas(categoria);
CREATE INDEX idx_doacoes_campanha ON doacoes(id_campanha);
CREATE INDEX idx_doacoes_doador ON doacoes(id_doador);
CREATE INDEX idx_doacoes_data ON doacoes(data_doacao);
CREATE INDEX idx_utilizadores_email ON utilizadores(email);

-- ✅ Inserir conta de Admin (pré-criada)
-- Senha: admin123 (encriptada com bcrypt)
INSERT INTO utilizadores (nome, email, senha, tipo_utilizador) VALUES 
('Admin DOA+', 'admin@doaplus.pt', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'admin');

-- Mostrar tabelas criadas
SHOW TABLES;
