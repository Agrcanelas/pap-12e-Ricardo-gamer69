-- Dados de exemplo para DOA+
-- Execute este arquivo APÓS importar doa_plus.sql

USE doa_plus;

-- Inserir utilizadores de exemplo
INSERT INTO utilizadores (nome, email, senha, tipo_utilizador) VALUES
('Associação de Apoio Social', 'associacao@email.com', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'instituicao'),
('Banco Alimentar', 'bancoalimentar@email.com', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'instituicao'),
('João Silva', 'joao@email.com', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'doador'),
('Maria Santos', 'maria@email.com', '$2y$10$3K0XpXqvXVyGu.v0eXKAiOd.BX0mDlH6PmOqW8u.EEWqZ2K4mqKy2', 'doador');

-- Inserir campanhas de exemplo (todas ativas)
INSERT INTO campanhas (titulo, descricao, categoria, valor_objetivo, valor_angariado, instituicao, id_criador, data_inicio, data_fim, status) VALUES
('Ajuda às Famílias Carenciadas', 'Campanha para ajudar famílias em situação de vulnerabilidade social com alimentos, vestuário e apoio escolar. Precisamos da sua ajuda para continuar este trabalho essencial na nossa comunidade.', 'Social', 5000.00, 1250.00, 'Associação de Apoio Social', 2, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'ativa'),
('Refeições para Idosos', 'Programa de distribuição de refeições quentes para idosos isolados. Cada doação ajuda a garantir que nenhum idoso passe fome ou se sinta sozinho.', 'Alimentação', 3000.00, 850.00, 'Banco Alimentar', 3, '2026-01-15 00:00:00', '2026-06-30 23:59:59', 'ativa'),
('Educação para Todos', 'Apoio escolar para crianças de famílias de baixos rendimentos. Material didático, reforço escolar e acesso a atividades extracurriculares.', 'Educação', 8000.00, 3200.00, 'Associação de Apoio Social', 2, '2026-02-01 00:00:00', '2026-11-30 23:59:59', 'ativa'),
('Saúde Mental Comunitária', 'Programa de apoio psicológico e terapia para pessoas em situação de vulnerabilidade. Profissionais especializados disponíveis para consultas gratuitas.', 'Saúde', 6000.00, 1800.00, 'Centro de Saúde Mental', 2, '2026-03-01 00:00:00', '2026-10-31 23:59:59', 'ativa'),
('Habitação de Emergência', 'Alojamento temporário para famílias desalojadas. Cada doação contribui para manter estes serviços essenciais ativos.', 'Habitação', 10000.00, 4500.00, 'Associação de Apoio Social', 2, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'ativa'),
('Proteção Animal', 'Centro de recolha e tratamento de animais abandonados. Vacinas, alimentação e cuidados veterinários para animais necessitados.', 'Animais', 4000.00, 1200.00, 'Sociedade Protetora dos Animais', 3, '2026-02-15 00:00:00', '2026-08-15 23:59:59', 'ativa');

-- Inserir algumas doações de exemplo
INSERT INTO doacoes (id_campanha, id_doador, montante, mensagem, anonimo) VALUES
(1, 4, 50.00, 'Muito obrigado pelo trabalho que fazem!', FALSE),
(1, 5, 25.00, NULL, TRUE),
(2, 4, 30.00, 'Continuem este trabalho tão importante', FALSE),
(3, 5, 100.00, 'Educação é fundamental!', FALSE),
(4, 4, 75.00, NULL, FALSE),
(5, 5, 200.00, 'Orgulho-me de ajudar', FALSE),
(6, 4, 40.00, 'Amo animais!', FALSE);

-- Atualizar valores angariados nas campanhas
UPDATE campanhas SET valor_angariado = (
    SELECT COALESCE(SUM(montante), 0) FROM doacoes WHERE id_campanha = campanhas.id
) WHERE id IN (1,2,3,4,5,6);

('Saúde Mental Comunitária', 'Programa de apoio psicológico e terapia para pessoas em situação de vulnerabilidade. Profissionais especializados disponíveis para consultas gratuitas.', 'Saúde', 6000.00, 1800.00, 'Centro de Saúde Mental', 2, '2026-03-01 00:00:00', '2026-10-31 23:59:59', 'ativa'),

('Habitação de Emergência', 'Alojamento temporário para famílias desalojadas. Cada doação contribui para manter estes serviços essenciais ativos.', 'Habitação', 10000.00, 4500.00, 'Associação de Apoio Social', 2, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'ativa'),

('Proteção Animal', 'Centro de recolha e tratamento de animais abandonados. Vacinas, alimentação e cuidados veterinários para animais necessitados.', 'Animais', 4000.00, 1200.00, 'Sociedade Protetora dos Animais', 3, '2026-02-15 00:00:00', '2026-08-15 23:59:59', 'ativa');

-- Inserir algumas doações de exemplo
INSERT INTO doacoes (id_campanha, id_doador, montante, mensagem, anonimo) VALUES
(1, 4, 50.00, 'Muito obrigado pelo trabalho que fazem!', FALSE),
(1, 5, 25.00, NULL, TRUE),
(2, 4, 30.00, 'Continuem este trabalho tão importante', FALSE),
(3, 5, 100.00, 'Educação é fundamental!', FALSE),
(4, 4, 75.00, NULL, FALSE),
(5, 5, 200.00, 'Orgulho-me de ajudar', FALSE),
(6, 4, 40.00, 'Amo animais!', FALSE);

-- Atualizar valores angariados nas campanhas
UPDATE campanhas SET valor_angariado = (
    SELECT COALESCE(SUM(montante), 0) FROM doacoes WHERE id_campanha = campanhas.id
) WHERE id IN (1,2,3,4,5,6);</content>
<parameter name="filePath">c:\xampp\htdocs\pap-12e-Ricardo-gamer69\dados_exemplo.sql