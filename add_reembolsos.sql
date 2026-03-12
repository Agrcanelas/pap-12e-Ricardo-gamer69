-- ============================================================
-- CORRE ESTE FICHEIRO NO phpMyAdmin > doa_plus > SQL
-- ============================================================

-- 1. Adicionar coluna foto_perfil (se ainda não existe)
ALTER TABLE utilizadores 
ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(255) DEFAULT NULL;

-- 2. Atualizar o ENUM das campanhas para incluir 'pendente'
ALTER TABLE campanhas 
MODIFY COLUMN status ENUM('pendente','ativa','pausada','concluida','cancelada') DEFAULT 'pendente';

-- 3. Criar tabela de reembolsos
CREATE TABLE IF NOT EXISTS reembolsos (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    id_doacao      INT NOT NULL,
    id_utilizador  INT NOT NULL,
    motivo         TEXT NOT NULL,
    estado         ENUM('pendente','aprovado','rejeitado') DEFAULT 'pendente',
    resposta_admin TEXT DEFAULT NULL,
    data_pedido    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_resposta  TIMESTAMP NULL,
    FOREIGN KEY (id_doacao)     REFERENCES doacoes(id)      ON DELETE CASCADE,
    FOREIGN KEY (id_utilizador) REFERENCES utilizadores(id) ON DELETE CASCADE
);
