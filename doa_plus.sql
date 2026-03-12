-- ============================================
-- DOA+ — Base de Dados Principal
-- Importa APENAS este ficheiro no phpMyAdmin
-- ============================================

CREATE DATABASE IF NOT EXISTS doa_plus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE doa_plus;

-- Tabela: utilizadores
CREATE TABLE IF NOT EXISTS utilizadores (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    nome             VARCHAR(100) NOT NULL,
    email            VARCHAR(120) UNIQUE NOT NULL,
    senha            VARCHAR(255) NOT NULL,
    tipo_utilizador  ENUM('utilizador','admin') DEFAULT 'utilizador',
    foto_perfil      VARCHAR(255) DEFAULT NULL,
    data_registo     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo            BOOLEAN DEFAULT TRUE
);

-- Tabela: campanhas
CREATE TABLE IF NOT EXISTS campanhas (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    titulo           VARCHAR(150) NOT NULL,
    descricao        LONGTEXT NOT NULL,
    categoria        VARCHAR(50) NOT NULL,
    valor_objetivo   DECIMAL(10,2) NOT NULL,
    valor_angariado  DECIMAL(10,2) DEFAULT 0.00,
    instituicao      VARCHAR(100) NOT NULL,
    id_criador       INT NOT NULL,
    data_criacao     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_inicio      DATETIME,
    data_fim         DATETIME,
    status           ENUM('pendente','ativa','concluida','cancelada') DEFAULT 'ativa',
    imagem           VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (id_criador) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Tabela: doacoes
CREATE TABLE IF NOT EXISTS doacoes (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    id_campanha   INT NOT NULL,
    id_doador     INT NOT NULL,
    montante      DECIMAL(10,2) NOT NULL,
    data_doacao   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    mensagem      TEXT DEFAULT NULL,
    anonimo       BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_campanha) REFERENCES campanhas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_doador)   REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Tabela: atualizacoes_campanha
CREATE TABLE IF NOT EXISTS atualizacoes_campanha (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    id_campanha      INT NOT NULL,
    id_autor         INT NOT NULL,
    titulo           VARCHAR(150) DEFAULT NULL,
    conteudo         LONGTEXT NOT NULL,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_campanha) REFERENCES campanhas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_autor)    REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Índices de performance
CREATE INDEX IF NOT EXISTS idx_campanhas_status    ON campanhas(status);
CREATE INDEX IF NOT EXISTS idx_campanhas_categoria ON campanhas(categoria);
CREATE INDEX IF NOT EXISTS idx_doacoes_campanha    ON doacoes(id_campanha);
CREATE INDEX IF NOT EXISTS idx_doacoes_doador      ON doacoes(id_doador);
CREATE INDEX IF NOT EXISTS idx_utilizadores_email  ON utilizadores(email);

-- ============================================
-- ADMIN pré-criado — Senha: admin123
-- ============================================
INSERT IGNORE INTO utilizadores (nome, email, senha, tipo_utilizador) VALUES
('Admin DOA+', 'admin@doaplus.pt', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'admin');

-- ============================================
-- DADOS DE EXEMPLO (remover em produção)
-- ============================================

-- Utilizadores de exemplo
INSERT IGNORE INTO utilizadores (id, nome, email, senha, tipo_utilizador) VALUES
(2, 'Associação de Apoio Social', 'associacao@doaplus.pt', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'utilizador'),
(3, 'Banco Alimentar Lisboa',     'bancoalimentar@doaplus.pt', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'utilizador'),
(4, 'João Silva',   'joao@doaplus.pt',  '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'utilizador'),
(5, 'Maria Santos', 'maria@doaplus.pt', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'utilizador');

-- Campanhas de exemplo
INSERT IGNORE INTO campanhas (id, titulo, descricao, categoria, valor_objetivo, valor_angariado, instituicao, id_criador, data_inicio, data_fim, status) VALUES
(1, 'Ajuda às Famílias Carenciadas',
   'Campanha para ajudar famílias em situação de vulnerabilidade social com alimentos, vestuário e apoio escolar. Precisamos da tua ajuda para continuar este trabalho essencial na nossa comunidade.',
   'Social', 5000.00, 1250.00, 'Associação de Apoio Social', 2, '2026-01-01', '2026-12-31', 'ativa'),

(2, 'Refeições para Idosos Isolados',
   'Programa de distribuição de refeições quentes para idosos isolados na cidade de Lisboa. Cada doação ajuda a garantir que nenhum idoso passe fome ou se sinta só.',
   'Alimentação', 3000.00, 850.00, 'Banco Alimentar Lisboa', 3, '2026-01-15', '2026-06-30', 'ativa'),

(3, 'Educação para Todos',
   'Apoio escolar e material didático para crianças de famílias de baixos rendimentos. Reforço escolar, acesso a atividades extracurriculares e material escolar gratuito.',
   'Educação', 8000.00, 3200.00, 'Associação de Apoio Social', 2, '2026-02-01', '2026-11-30', 'ativa'),

(4, 'Saúde Mental Comunitária',
   'Programa de apoio psicológico e terapia para pessoas em situação de vulnerabilidade. Profissionais especializados oferecem consultas gratuitas a quem mais precisa.',
   'Saúde', 6000.00, 1800.00, 'Centro de Saúde Mental', 2, '2026-03-01', '2026-10-31', 'ativa'),

(5, 'Habitação de Emergência',
   'Alojamento temporário e apoio a famílias desalojadas. Cada doação contribui para manter estes serviços essenciais operacionais e ajudar quem perdeu a sua casa.',
   'Habitação', 10000.00, 4500.00, 'Associação de Apoio Social', 2, '2026-01-01', '2026-12-31', 'ativa'),

(6, 'Proteção Animal',
   'Centro de recolha e tratamento de animais abandonados. Vacinas, alimentação, castração e cuidados veterinários para os animais mais necessitados do nosso município.',
   'Animais', 4000.00, 1200.00, 'Sociedade Protetora dos Animais', 3, '2026-02-15', '2026-08-15', 'ativa');

-- Doações de exemplo
INSERT IGNORE INTO doacoes (id_campanha, id_doador, montante, mensagem, anonimo) VALUES
(1, 4, 50.00,  'Muito obrigado pelo trabalho que fazem!', FALSE),
(1, 5, 25.00,  NULL, TRUE),
(2, 4, 30.00,  'Continuem este trabalho tão importante!', FALSE),
(3, 5, 100.00, 'Educação é o futuro!', FALSE),
(4, 4, 75.00,  NULL, FALSE),
(5, 5, 200.00, 'Orgulho-me de ajudar', FALSE),
(6, 4, 40.00,  'Adoro animais! Força!', FALSE);

SELECT 'DOA+ instalado com sucesso!' AS mensagem;
SHOW TABLES;

-- Adicionar coluna foto_perfil (correr se ainda não existir)
ALTER TABLE utilizadores ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(255) DEFAULT NULL;

-- Tabela: reembolsos
CREATE TABLE IF NOT EXISTS reembolsos (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    id_doacao     INT NOT NULL,
    id_utilizador INT NOT NULL,
    motivo        TEXT NOT NULL,
    estado        ENUM('pendente','aprovado','rejeitado') DEFAULT 'pendente',
    resposta_admin TEXT DEFAULT NULL,
    data_pedido   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_resposta TIMESTAMP NULL,
    FOREIGN KEY (id_doacao)     REFERENCES doacoes(id)      ON DELETE CASCADE,
    FOREIGN KEY (id_utilizador) REFERENCES utilizadores(id) ON DELETE CASCADE
);
