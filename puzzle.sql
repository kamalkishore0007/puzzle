-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2016 at 03:08 PM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `puzzle`
--

-- --------------------------------------------------------

--
-- Table structure for table `puzzle_list`
--

CREATE TABLE IF NOT EXISTS `puzzle_list` (
  `puzzle_id` int(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `img` varchar(250) NOT NULL,
  `answer` varchar(250) NOT NULL,
  `timer` varchar(10) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`puzzle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `puzzle_list`
--

INSERT INTO `puzzle_list` (`puzzle_id`, `title`, `img`, `answer`, `timer`, `active`, `created_on`) VALUES
(1, 'Body Parts', 'body.jpg', 'Arrangement of body parts', '60', 1, '2016-02-02 23:48:21'),
(2, 'Taj Mahal Puzzle', 'taj1.jpg', 'This is Taj Mahal', '60', 1, '2016-02-03 09:19:28');

-- --------------------------------------------------------

--
-- Table structure for table `puzzle_score`
--

CREATE TABLE IF NOT EXISTS `puzzle_score` (
  `score_id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL,
  `puzzle_id` int(5) NOT NULL,
  `score` int(5) NOT NULL,
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY (`score_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `puzzle_score`
--

INSERT INTO `puzzle_score` (`score_id`, `user_id`, `puzzle_id`, `score`, `ip`) VALUES
(1, 1, 1, 230, '172.16.9.106'),
(2, 1, 2, 360, '172.16.9.106'),
(3, 3, 2, 310, '172.16.2.168');

-- --------------------------------------------------------

--
-- Table structure for table `puzzle_user`
--

CREATE TABLE IF NOT EXISTS `puzzle_user` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `age` varchar(3) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `puzzle_user`
--

INSERT INTO `puzzle_user` (`id`, `name`, `age`, `gender`, `email`, `state`, `ip`) VALUES
(1, 'kamal', '25', 'male', 'kamalkishore0007@gmail.com', 'delhi', '172.16.9.106'),
(2, 'bilal', '25', 'male', 'bilal@infobase.in', 'up', '172.16.9.109'),
(3, 'Alok', '23', 'male', 'alok@infobase.in', 'UP', '172.16.2.168');

-- --------------------------------------------------------

--
-- Table structure for table `puzzle_user_online`
--

CREATE TABLE IF NOT EXISTS `puzzle_user_online` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `timestamp` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `puzzle_user_online`
--

INSERT INTO `puzzle_user_online` (`id`, `ip`, `timestamp`) VALUES
(4, '172.16.9.106', '1454492242'),
(6, '172.16.2.168', '1454492259');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
