-- phpMyAdmin SQL Dump
-- version 4.4.15.8
-- https://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Oct 09, 2017 at 08:45 AM
-- Server version: 5.6.37
-- PHP Version: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
--

-- --------------------------------------------------------

--
-- Table structure for table `NonTakenList`
--

CREATE TABLE IF NOT EXISTS `NonTakenList` (
  `URL` text NOT NULL,
  `Title` text NOT NULL,
  `Rank` tinyint(4) NOT NULL,
  `LastAccess` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PendingScanList`
--

CREATE TABLE IF NOT EXISTS `PendingScanList` (
  `URL` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ScannedList`
--

CREATE TABLE IF NOT EXISTS `ScannedList` (
  `URL` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SearchRstList`
--

CREATE TABLE IF NOT EXISTS `SearchRstList` (
  `URL` text NOT NULL,
  `Title` text NOT NULL,
  `Description` text NOT NULL,
  `Keywords` text NOT NULL,
  `Rank` tinyint(4) NOT NULL,
  `LastAccess` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
