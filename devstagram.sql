-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 01/03/2020 às 23:47
-- Versão do servidor: 10.3.22-MariaDB-0+deb10u1
-- Versão do PHP: 7.3.14-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `devstagram`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `url` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `photos_comments`
--

CREATE TABLE `photos_comments` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_photo` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `photos_likes`
--

CREATE TABLE `photos_likes` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_photo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `avatar` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `pass`, `avatar`) VALUES
(1, 'Gabriel Vargas Padilha', 'gabriel@email.com', '$2y$10$GhCS5mspWKyxgB2Fy.ss.uU6lPlaGQM9MePrMwBUbfN672ObB5wAi', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users_following`
--

CREATE TABLE `users_following` (
  `id` int(11) NOT NULL,
  `id_user_active` int(11) NOT NULL,
  `id_user_passive` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `photos_comments`
--
ALTER TABLE `photos_comments`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `photos_likes`
--
ALTER TABLE `photos_likes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users_following`
--
ALTER TABLE `users_following`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `photos_comments`
--
ALTER TABLE `photos_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `photos_likes`
--
ALTER TABLE `photos_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users_following`
--
ALTER TABLE `users_following`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
