-- Correr no phpMyAdmin se já tens a BD instalada
ALTER TABLE utilizadores ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(255) DEFAULT NULL;
