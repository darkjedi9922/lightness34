-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Фев 14 2020 г., 15:10
-- Версия сервера: 10.1.38-MariaDB
-- Версия PHP: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `lightness34`
--

-- --------------------------------------------------------

--
-- Структура таблицы `articles`
--

CREATE TABLE `articles` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(225) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `date` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `module_id` int(2) UNSIGNED NOT NULL,
  `material_id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `answer_for_com_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `date` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `group_rights`
--

CREATE TABLE `group_rights` (
  `module_id` int(10) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `rights` bigint(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `member1_sorted_id` int(10) UNSIGNED NOT NULL,
  `member2_sorted_id` int(10) UNSIGNED NOT NULL,
  `from_id` int(10) UNSIGNED NOT NULL,
  `to_id` int(10) UNSIGNED NOT NULL,
  `date` int(11) UNSIGNED NOT NULL,
  `readed` tinyint(1) NOT NULL DEFAULT '0',
  `removed_by_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `message_texts`
--

CREATE TABLE `message_texts` (
  `id` int(10) UNSIGNED NOT NULL,
  `message_id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `readed_articles`
--

CREATE TABLE `readed_articles` (
  `article_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `read_tracking`
--

CREATE TABLE `read_tracking` (
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `what_id` int(10) UNSIGNED NOT NULL,
  `for_id` int(10) UNSIGNED DEFAULT NULL,
  `progress` int(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_actions`
--

CREATE TABLE `stat_actions` (
  `id` int(10) UNSIGNED NOT NULL,
  `class` varchar(255) COLLATE utf8_bin NOT NULL,
  `duration_sec` float UNSIGNED NOT NULL,
  `ajax` tinyint(1) NOT NULL,
  `data_json` text COLLATE utf8_bin NOT NULL,
  `response_type` tinyint(1) UNSIGNED NOT NULL,
  `response_info` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_cash_routes`
--

CREATE TABLE `stat_cash_routes` (
  `id` int(10) UNSIGNED NOT NULL,
  `route` varchar(255) COLLATE utf8_bin NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_cash_values`
--

CREATE TABLE `stat_cash_values` (
  `id` int(10) UNSIGNED NOT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `class` varchar(255) COLLATE utf8_bin NOT NULL,
  `value_key` varchar(255) COLLATE utf8_bin NOT NULL,
  `init_duration_sec` float UNSIGNED NOT NULL,
  `init_error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `call_count` smallint(5) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_dynamic_route_params`
--

CREATE TABLE `stat_dynamic_route_params` (
  `id` int(10) UNSIGNED NOT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `index` tinyint(3) UNSIGNED NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_event_emits`
--

CREATE TABLE `stat_event_emits` (
  `id` int(10) UNSIGNED NOT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `event` varchar(255) COLLATE utf8_bin NOT NULL,
  `args_json` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_event_emit_handles`
--

CREATE TABLE `stat_event_emit_handles` (
  `id` int(10) UNSIGNED NOT NULL,
  `emit_id` int(10) UNSIGNED NOT NULL,
  `subscriber_id` int(10) UNSIGNED NOT NULL,
  `duration_sec` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_event_routes`
--

CREATE TABLE `stat_event_routes` (
  `id` int(10) UNSIGNED NOT NULL,
  `route` varchar(255) COLLATE utf8_bin NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_event_subscribers`
--

CREATE TABLE `stat_event_subscribers` (
  `id` int(10) UNSIGNED NOT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `event` varchar(255) COLLATE utf8_bin NOT NULL,
  `class` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_queries`
--

CREATE TABLE `stat_queries` (
  `id` int(10) UNSIGNED NOT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `sql_text` varchar(1000) COLLATE utf8_bin NOT NULL,
  `sql_crc` int(10) UNSIGNED NOT NULL,
  `error` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `duration_sec` float UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_query_routes`
--

CREATE TABLE `stat_query_routes` (
  `id` int(10) UNSIGNED NOT NULL,
  `route` varchar(255) COLLATE utf8_bin NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_routes`
--

CREATE TABLE `stat_routes` (
  `id` int(10) UNSIGNED NOT NULL,
  `url` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `viewfile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `duration_sec` float UNSIGNED DEFAULT NULL,
  `ajax` tinyint(1) NOT NULL,
  `code` smallint(5) UNSIGNED DEFAULT '200',
  `code_info` varchar(255) COLLATE utf8_bin NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_views`
--

CREATE TABLE `stat_views` (
  `id` int(10) UNSIGNED NOT NULL,
  `route_id` int(10) UNSIGNED NOT NULL,
  `class` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `file` varchar(255) COLLATE utf8_bin NOT NULL,
  `layout_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `duration_sec` float UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_view_meta`
--

CREATE TABLE `stat_view_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `view_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `value` varchar(100) COLLATE utf8_bin NOT NULL,
  `type` varchar(100) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stat_view_routes`
--

CREATE TABLE `stat_view_routes` (
  `id` int(10) UNSIGNED NOT NULL,
  `route` varchar(255) COLLATE utf8_bin NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `transmitting`
--

CREATE TABLE `transmitting` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `login` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `avatar` varchar(50) DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `surname` varchar(50) NOT NULL DEFAULT '',
  `gender_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `sid` varchar(50) NOT NULL,
  `registration_date` int(11) UNSIGNED NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `last_online_time` int(11) UNSIGNED DEFAULT NULL,
  `last_user_agent` varchar(255) CHARACTER SET cp1251 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `email`, `avatar`, `name`, `surname`, `gender_id`, `group_id`, `sid`, `registration_date`, `online`, `last_online_time`, `last_user_agent`) VALUES
(1, 'Admin', '4a7d1ed414474e4033ac29ccb8653d9b', '', '', '', '', 2, 6, 'f57e1436a9726c71b59aeb3d36cdd56e', 1527321064, 1, 1581660460, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36 OPR/66.0.3515.72');

-- --------------------------------------------------------

--
-- Структура таблицы `user_genders`
--

CREATE TABLE `user_genders` (
  `id` int(2) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_genders`
--

INSERT INTO `user_genders` (`id`, `name`) VALUES
(1, 'Не указано'),
(2, 'Мужчина'),
(3, 'Женщина');

-- --------------------------------------------------------

--
-- Структура таблицы `user_groups`
--

CREATE TABLE `user_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_groups`
--

INSERT INTO `user_groups` (`id`, `name`, `icon`, `is_system`) VALUES
(1, 'Гость', 'public/images/icons/groups/guest.png', 1),
(2, 'Пользователь', 'public/images/icons/groups/user.png', 1),
(3, 'Модератор', 'public/images/icons/groups/moder.png', 0),
(4, 'Администратор', 'public/images/icons/groups/admin.png', 0),
(5, 'Главный Администратор', 'public/images/icons/groups/admin2.png', 0),
(6, 'Создатель', 'public/images/icons/groups/creator.png', 1),
(7, 'Заблокированный', 'public/images/icons/groups/blocked.png', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `answer_for_com_id` (`answer_for_com_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`from_id`);

--
-- Индексы таблицы `message_texts`
--
ALTER TABLE `message_texts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Индексы таблицы `stat_actions`
--
ALTER TABLE `stat_actions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_cash_routes`
--
ALTER TABLE `stat_cash_routes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_cash_values`
--
ALTER TABLE `stat_cash_values`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_dynamic_route_params`
--
ALTER TABLE `stat_dynamic_route_params`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_event_emits`
--
ALTER TABLE `stat_event_emits`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_event_emit_handles`
--
ALTER TABLE `stat_event_emit_handles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_event_routes`
--
ALTER TABLE `stat_event_routes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_event_subscribers`
--
ALTER TABLE `stat_event_subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_queries`
--
ALTER TABLE `stat_queries`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_query_routes`
--
ALTER TABLE `stat_query_routes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_routes`
--
ALTER TABLE `stat_routes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_views`
--
ALTER TABLE `stat_views`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_view_meta`
--
ALTER TABLE `stat_view_meta`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stat_view_routes`
--
ALTER TABLE `stat_view_routes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sex_id` (`gender_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Индексы таблицы `user_genders`
--
ALTER TABLE `user_genders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `message_texts`
--
ALTER TABLE `message_texts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_actions`
--
ALTER TABLE `stat_actions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_cash_routes`
--
ALTER TABLE `stat_cash_routes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_cash_values`
--
ALTER TABLE `stat_cash_values`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_dynamic_route_params`
--
ALTER TABLE `stat_dynamic_route_params`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_event_emits`
--
ALTER TABLE `stat_event_emits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_event_emit_handles`
--
ALTER TABLE `stat_event_emit_handles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_event_routes`
--
ALTER TABLE `stat_event_routes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_event_subscribers`
--
ALTER TABLE `stat_event_subscribers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_queries`
--
ALTER TABLE `stat_queries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_query_routes`
--
ALTER TABLE `stat_query_routes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_routes`
--
ALTER TABLE `stat_routes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_views`
--
ALTER TABLE `stat_views`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_view_meta`
--
ALTER TABLE `stat_view_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stat_view_routes`
--
ALTER TABLE `stat_view_routes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `user_genders`
--
ALTER TABLE `user_genders`
  MODIFY `id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
