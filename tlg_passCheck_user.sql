-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Авг 07 2022 г., 03:13
-- Версия сервера: 5.7.27-30-log
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `maxtogocma`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tlg_passCheck_user`
--

DROP TABLE IF EXISTS `tlg_passCheck_user`;
CREATE TABLE `tlg_passCheck_user` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT '0',
  `first_name` varchar(11) DEFAULT '0',
  `last_name` varchar(255) NOT NULL DEFAULT '0',
  `username` varchar(255) DEFAULT '0',
  `status` varchar(255) DEFAULT '0',
  `commands` varchar(255) DEFAULT '0',
  `count_check` varchar(255) DEFAULT '0',
  `startcheck` varchar(255) DEFAULT '0',
  `lastcheck` varchar(255) DEFAULT '0',
  `isAdmine` varchar(255) NOT NULL DEFAULT '0',
  `reply_to_message_id` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `tlg_passCheck_user`
--
ALTER TABLE `tlg_passCheck_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `tlg_passCheck_user`
--
ALTER TABLE `tlg_passCheck_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
