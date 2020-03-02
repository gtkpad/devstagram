-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 02-Mar-2020 às 17:22
-- Versão do servidor: 10.3.22-MariaDB-0+deb10u1
-- versão do PHP: 7.3.14-1~deb10u1

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
-- Estrutura da tabela `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `url` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `photos`
--

INSERT INTO `photos` (`id`, `id_user`, `url`, `created_at`) VALUES
(3, 1, 'GABRIEL - FOTO1', '2020-03-02 16:39:58'),
(4, 1, 'GABRIEL - FOTO2', '2020-03-02 16:39:58'),
(5, 3, 'TESTE - foto1', '2020-03-02 16:39:58'),
(6, 3, 'TESTE - foto2', '2020-03-02 16:39:58'),
(7, 4, 'TESTE2 - foto1', '2020-03-02 16:39:58'),
(8, 4, 'TESTE2 - foto2', '2020-03-02 16:39:58');

-- --------------------------------------------------------

--
-- Estrutura da tabela `photos_comments`
--

CREATE TABLE `photos_comments` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_photo` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `photos_comments`
--

INSERT INTO `photos_comments` (`id`, `id_user`, `id_photo`, `date`, `text`) VALUES
(5, 1, 6, '2020-03-02 17:02:32', 'HAHAHAHAH');

-- --------------------------------------------------------

--
-- Estrutura da tabela `photos_likes`
--

CREATE TABLE `photos_likes` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_photo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `photos_likes`
--

INSERT INTO `photos_likes` (`id`, `id_user`, `id_photo`) VALUES
(5, 1, 7),
(6, 3, 7);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `avatar` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `pass`, `avatar`) VALUES
(1, 'Gabriel Vargas Padilha', 'gabriel@email.com', '$2y$10$GhCS5mspWKyxgB2Fy.ss.uU6lPlaGQM9MePrMwBUbfN672ObB5wAi', NULL),
(3, 'Teste', 'user@email.com', '$2y$10$6KreCOarGq2WMJVkSqvQXuU7O.lEuIcDjqrsJrc636ugo6RuwX86O', NULL),
(4, 'Teste2', 'user2@email.com', '$2y$10$/6VvfzHICWT2AeR6F54qNu6zzp3CQbazim/oZxNZZVcADyGbBWM16', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users_following`
--

CREATE TABLE `users_following` (
  `id` int(11) NOT NULL,
  `id_user_active` int(11) NOT NULL,
  `id_user_passive` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `users_following`
--

INSERT INTO `users_following` (`id`, `id_user_active`, `id_user_passive`) VALUES
(3, 1, 3),
(4, 1, 4),
(5, 3, 1),
(6, 3, 4),
(7, 4, 1),
(8, 4, 3);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Índices para tabela `photos_comments`
--
ALTER TABLE `photos_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_photo` (`id_photo`),
  ADD KEY `id_user` (`id_user`);

--
-- Índices para tabela `photos_likes`
--
ALTER TABLE `photos_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_photo` (`id_photo`),
  ADD KEY `id_user` (`id_user`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `users_following`
--
ALTER TABLE `users_following`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user_active` (`id_user_active`,`id_user_passive`),
  ADD KEY `id_user_passive` (`id_user_passive`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `photos_comments`
--
ALTER TABLE `photos_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `photos_likes`
--
ALTER TABLE `photos_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `users_following`
--
ALTER TABLE `users_following`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `photos_comments`
--
ALTER TABLE `photos_comments`
  ADD CONSTRAINT `photos_comments_ibfk_1` FOREIGN KEY (`id_photo`) REFERENCES `photos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `photos_comments_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `photos_likes`
--
ALTER TABLE `photos_likes`
  ADD CONSTRAINT `photos_likes_ibfk_1` FOREIGN KEY (`id_photo`) REFERENCES `photos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `photos_likes_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `users_following`
--
ALTER TABLE `users_following`
  ADD CONSTRAINT `users_following_ibfk_1` FOREIGN KEY (`id_user_active`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_following_ibfk_2` FOREIGN KEY (`id_user_passive`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
