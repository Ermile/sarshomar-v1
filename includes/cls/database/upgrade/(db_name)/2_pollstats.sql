-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2016 at 01:10 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tejarak`
--

-- --------------------------------------------------------

--
-- Table structure for table `pollstats`
--

CREATE TABLE `pollstats` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` bigint(10) UNSIGNED NOT NULL,
  `total` text,
  `gender` text,
  `marrital` text,
  `parental` text,
  `exercise` text,
  `employment` text,
  `business` text,
  `industry` text,
  `devices` text,
  `internet_usage` text,
  `birthdate` text,
  `age` text,
  `range` text,
  `graduation` text,
  `course` text,
  `countrybirth` text,
  `country` text,
  `provincebirth` text,
  `province` text,
  `birthcity` text,
  `city` text,
  `citybirth` text,
  `language` text,
  `meta` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pollstats`
--
ALTER TABLE `pollstats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_post` (`post_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pollstats`
--
ALTER TABLE `pollstats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `pollstats`
--
ALTER TABLE `pollstats`
  ADD CONSTRAINT `pollstats_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
