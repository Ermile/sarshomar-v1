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
-- Table structure for table `polldetails`
--

CREATE TABLE `polldetails` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` bigint(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `opt` int(11) UNSIGNED DEFAULT NULL,
  `type` enum('select','notify','text','upload','star','number','media_image','media_video','media_audio','order') DEFAULT NULL,
  `txt` text,
  `profile` mediumtext,
  `insertdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visitor_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `polldetails`
--
ALTER TABLE `polldetails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_opt` (`post_id`,`user_id`,`opt`) USING BTREE,
  ADD KEY `polldetails_ibfk_2` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `polldetails`
--
ALTER TABLE `polldetails`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `polldetails`
--
ALTER TABLE `polldetails`
  ADD CONSTRAINT `polldetails_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `polldetails_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
