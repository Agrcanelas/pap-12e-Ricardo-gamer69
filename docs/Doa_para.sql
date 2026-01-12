-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/01/2026 às 12:00
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pap_.i.`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Doa_para`
--

CREATE TABLE `doa_para` (
  `id_doacao` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_doacao` date NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `id_instituicao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Doa_para`
--

INSERT INTO `Doa_para` (`id_doacao`, `valor`, `data_doacao`, `id_utilizador`, `id_instituicao`) VALUES
(1, 25.00, '2024-02-05', 1, 1),
(2, 50.00, '2024-02-06', 2, 2),
(3, 15.00, '2024-02-07', 3, 3),
(4, 100.00, '2024-02-08', 4, 4),
(5, 30.00, '2024-02-09', 5, 5),
(6, 20.00, '2024-02-10', 6, 6),
(7, 75.00, '2024-02-11', 7, 7),
(8, 40.00, '2024-02-12', 8, 8),
(9, 60.00, '2024-02-13', 9, 9),
(10, 10.00, '2024-02-14', 10, 10);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Doa_para`
--
ALTER TABLE `Doa_para`
  ADD PRIMARY KEY (`id_doacao`),
  ADD KEY `fk_doa_utilizador` (`id_utilizador`),
  ADD KEY `fk_doa_instituicao` (`id_instituicao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Doa_para`
--
ALTER TABLE `Doa_para`
  MODIFY `id_doacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Doa_para`
--
ALTER TABLE `Doa_para`
  ADD CONSTRAINT `fk_doa_instituicao` FOREIGN KEY (`id_instituicao`) REFERENCES `instituicao` (`id_instituicao`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doa_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
