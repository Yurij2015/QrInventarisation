-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 19 2019 г., 08:00
-- Версия сервера: 10.1.32-MariaDB
-- Версия PHP: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `qrdb`
--

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `idcategory` int(11) NOT NULL,
  `category_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(245) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `employee`
--

CREATE TABLE `employee` (
  `idemployee` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `info` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position_idposition` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `material`
--

CREATE TABLE `material` (
  `idmaterial` int(11) NOT NULL,
  `namematerial` varchar(245) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qrcode_idqrcode` int(11) NOT NULL,
  `category_idcategory` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `position`
--

CREATE TABLE `position` (
  `idposition` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(245) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `qrcode`
--

CREATE TABLE `qrcode` (
  `idqrcode` int(11) NOT NULL,
  `description` varchar(245) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qrlink` char(245) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `revision`
--

CREATE TABLE `revision` (
  `idrevision` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `info` varchar(145) COLLATE utf8_unicode_ci DEFAULT NULL,
  `material_idmaterial` int(11) NOT NULL,
  `employee_idemployee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `name` varchar(145) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login` varchar(145) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` char(90) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(145) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`idcategory`);

--
-- Индексы таблицы `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`idemployee`),
  ADD KEY `fk_employee_position1_idx` (`position_idposition`);

--
-- Индексы таблицы `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`idmaterial`),
  ADD KEY `fk_material_qrcode_idx` (`qrcode_idqrcode`),
  ADD KEY `fk_material_category1_idx` (`category_idcategory`);

--
-- Индексы таблицы `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`idposition`);

--
-- Индексы таблицы `qrcode`
--
ALTER TABLE `qrcode`
  ADD PRIMARY KEY (`idqrcode`);

--
-- Индексы таблицы `revision`
--
ALTER TABLE `revision`
  ADD PRIMARY KEY (`idrevision`),
  ADD KEY `fk_revision_material1_idx` (`material_idmaterial`),
  ADD KEY `fk_revision_employee1_idx` (`employee_idemployee`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `idcategory` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `employee`
--
ALTER TABLE `employee`
  MODIFY `idemployee` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `material`
--
ALTER TABLE `material`
  MODIFY `idmaterial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `position`
--
ALTER TABLE `position`
  MODIFY `idposition` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `idqrcode` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `fk_employee_position1` FOREIGN KEY (`position_idposition`) REFERENCES `position` (`idposition`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `fk_material_category1` FOREIGN KEY (`category_idcategory`) REFERENCES `category` (`idcategory`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_material_qrcode` FOREIGN KEY (`qrcode_idqrcode`) REFERENCES `qrcode` (`idqrcode`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `revision`
--
ALTER TABLE `revision`
  ADD CONSTRAINT `fk_revision_employee1` FOREIGN KEY (`employee_idemployee`) REFERENCES `employee` (`idemployee`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_revision_material1` FOREIGN KEY (`material_idmaterial`) REFERENCES `material` (`idmaterial`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
