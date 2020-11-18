-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 27, 2015 at 12:57 PM
-- Server version: 5.5.41-cll-lve
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `spssrru_gbtests`
--

-- --------------------------------------------------------

--
-- Table structure for table `web_rt_answers`
--

CREATE TABLE IF NOT EXISTS `web_rt_answers` (
  `uid` int(11) NOT NULL,
  `thisN` int(11) NOT NULL,
  `set_size` smallint(6) NOT NULL,
  `curColors` varchar(40) NOT NULL,
  `targetColor` smallint(6) NOT NULL,
  `targetPos` smallint(6) NOT NULL,
  `nrep` smallint(6) NOT NULL,
  `blockN` smallint(6) NOT NULL,
  `nTotal` smallint(6) NOT NULL,
  `nInBlock` smallint(6) NOT NULL,
  `rt` int(11) NOT NULL,
  `answer` varchar(10) NOT NULL,
  `correct` tinyint(4) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=162901 ;

-- --------------------------------------------------------

--
-- Table structure for table `web_rt_groups`
--

CREATE TABLE IF NOT EXISTS `web_rt_groups` (
  `id` int(10) NOT NULL DEFAULT '0',
  `type` varchar(18) DEFAULT NULL,
  `active` int(10) NOT NULL DEFAULT '0',
  `descr` tinytext,
  `forGraphs` varchar(32) DEFAULT NULL,
  `target_time` int(11) NOT NULL DEFAULT '1000',
  `react_time_dots` int(10) DEFAULT NULL,
  `react_time_words` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `web_rt_options`
--

CREATE TABLE IF NOT EXISTS `web_rt_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `optname` varchar(40) NOT NULL,
  `optval` text NOT NULL,
  `optdescr` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `web_rt_users`
--

CREATE TABLE IF NOT EXISTS `web_rt_users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `age` smallint(6) NOT NULL,
  `sex` tinyint(4) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `ref` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_id` varchar(256) NOT NULL,
  `ua` varchar(180) NOT NULL,
  `finished` tinyint(4) NOT NULL,
  `included` int(10) NOT NULL DEFAULT '1',
  `prev_studies` tinyint(4) NOT NULL,
  `toolate` float DEFAULT NULL,
  `repeated` int(10) NOT NULL,
  `finishedTime` timestamp NULL DEFAULT NULL,
  `accuracy` float DEFAULT NULL,
  `toofast` float DEFAULT NULL,
  `browser` varchar(15) NOT NULL,
  `browser_version` varchar(10) NOT NULL,
  `browser_major` varchar(10) NOT NULL,
  `os` varchar(10) NOT NULL,
  `os_version` varchar(10) NOT NULL,
  `monitor` varchar(30) NOT NULL,
  `refresh_rate` varchar(5) NOT NULL,
  `cpu` varchar(120) NOT NULL,
  `screen_size_y` varchar(10) NOT NULL,
  `screen_size_x` varchar(10) NOT NULL,
  `gpu` varchar(120) NOT NULL,
  `ram` varchar(10) NOT NULL,
  `keyboard` varchar(128) NOT NULL,
  `pc` varchar(10) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=630 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
