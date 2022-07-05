-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 11, 2022 at 10:34 PM
-- Server version: 10.2.43-MariaDB-log
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Database: `bp_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_bp`
--

CREATE TABLE `{prefix}_bp` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `weight` float NOT NULL,
  `height` float NOT NULL,
  `temperature` float NOT NULL,
  `waist` float NOT NULL,
  `tag` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `create_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_bp_items`
--

CREATE TABLE `{prefix}_bp_items` (
  `bp_id` int(11) NOT NULL,
  `index` tinyint(4) NOT NULL DEFAULT 1,
  `sys` int(11) DEFAULT NULL,
  `dia` int(11) DEFAULT NULL,
  `pulse` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_category`
--

CREATE TABLE `{prefix}_category` (
  `member_id` int(11) NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `topic` varchar(150) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_family`
--

CREATE TABLE `{prefix}_family` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `favorite` tinyint(1) NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sex` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `height` float NOT NULL,
  `id_card` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provinceID` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8_unicode_ci DEFAULT 'TH',
  `birthday` date DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `sys` double NOT NULL,
  `dia` double NOT NULL,
  `bmi` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_language`
--

CREATE TABLE `{prefix}_language` (
  `id` int(11) NOT NULL,
  `key` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `owner` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `js` tinyint(1) NOT NULL,
  `th` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `en` text COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_user`
--

CREATE TABLE `{prefix}_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `permission` text COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sex` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_card` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provinceID` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8_unicode_ci DEFAULT 'TH',
  `visited` int(11) DEFAULT 0,
  `lastvisited` int(11) DEFAULT 0,
  `session_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `social` tinyint(1) DEFAULT 0,
  `line_uid` varchar(33) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activatecode` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `{prefix}_bp`
--
ALTER TABLE `{prefix}_bp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `{prefix}_bp_items`
--
ALTER TABLE `{prefix}_bp_items`
  ADD PRIMARY KEY (`bp_id`,`index`) USING BTREE;

--
-- Indexes for table `{prefix}_category`
--
ALTER TABLE `{prefix}_category`
  ADD KEY `type` (`type`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `{prefix}_family`
--
ALTER TABLE `{prefix}_family`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `{prefix}_language`
--
ALTER TABLE `{prefix}_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_user`
--
ALTER TABLE `{prefix}_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE,
  ADD UNIQUE KEY `token` (`token`) USING BTREE,
  ADD UNIQUE KEY `id_card` (`id_card`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `activatecode` (`activatecode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `{prefix}_bp`
--
ALTER TABLE `{prefix}_bp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_family`
--
ALTER TABLE `{prefix}_family`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_language`
--
ALTER TABLE `{prefix}_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_user`
--
ALTER TABLE `{prefix}_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
