-- Corre este ficheiro no phpMyAdmin para corrigir o erro de registo
-- Vai a: phpMyAdmin > doa_plus > SQL > cola isto e clica Executar

USE doa_plus;

-- Corrigir o ENUM da tabela utilizadores
ALTER TABLE utilizadores 
MODIFY COLUMN tipo_utilizador ENUM('utilizador','admin') DEFAULT 'utilizador';

-- Atualizar utilizadores existentes que tinham 'doador' ou 'instituicao'
UPDATE utilizadores SET tipo_utilizador = 'utilizador' 
WHERE tipo_utilizador NOT IN ('utilizador', 'admin');

-- Verificar resultado
SELECT id, nome, email, tipo_utilizador FROM utilizadores;
