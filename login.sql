-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 11 nov 2014 kl 16:43
-- Serverversion: 5.6.15-log
-- PHP-version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `login`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `token` varchar(40) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `agent` varchar(200) NOT NULL DEFAULT '',
  `cookietime` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumpning av Data i tabell `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `token`, `ip`, `agent`, `cookietime`) VALUES
(1, 'Admin', '4ca3aa7b6b16a2c71de2f1b38c466dab3d196f85', '579f5ffbade9cf47ab73a8db635ca3ae43ce9a63', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36', '1415634842'),
(2, '', '2891baceeef1652ee698294da0e71ba78a2a4064', '', '', '', ''),
(6, 'a', '2891baceeef1652ee698294da0e71ba78a2a4064', '', '', '', ''),
(7, 'admina', 'a274af2670912f8e6a750df53c9da89de49005aa', '', '', '', ''),
(8, 'Sandra', '685463f0c6fb6b89395eabcd882e4b7c3cbc3a8e', '', '', '', ''),
(9, 'Test', 'afba1cb7b43b04231538eaf5c8af8b6f5d79975b', '', '', '', ''),
(10, 'asdasd', '0a3e62956a01aaca0952e1863addce735e71e1ba', '', '', '', ''),
(13, 'sh222td', 'f0322954de9b2caa6ab6f0a7450c09724134777a', '', '', '', ''),
(14, '1', '302490378956df38eaf81a72ac76d13200100a3e', '', '', '', ''),
(18, 'NyttTest', 'afba1cb7b43b04231538eaf5c8af8b6f5d79975b', 'dd46748c4a8c2bde16390f4ba50ba91f21887bef', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36', '0');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
