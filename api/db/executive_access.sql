-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 27, 2024 at 02:16 PM
-- Server version: 11.2.2-MariaDB
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `executive_access`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_group_module`
--

DROP TABLE IF EXISTS `account_group_module`;
CREATE TABLE IF NOT EXISTS `account_group_module` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `account_group_id` int(100) NOT NULL,
  `module_id` bigint(25) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'on',
  `assigned_by` bigint(25) NOT NULL,
  `date` text NOT NULL,
  `last_updated` text DEFAULT NULL,
  `last_updated_by` bigint(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=438 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_modules`
--

DROP TABLE IF EXISTS `app_modules`;
CREATE TABLE IF NOT EXISTS `app_modules` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `module_id` bigint(200) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `module_title` varchar(100) NOT NULL,
  `module_type` varchar(100) DEFAULT NULL,
  `module_status` varchar(100) DEFAULT 'deactivated',
  `item_title` varchar(50) DEFAULT NULL,
  `icon` varchar(25) DEFAULT NULL,
  `type` varchar(25) NOT NULL DEFAULT 'independent',
  `link` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_modules`
--

INSERT INTO `app_modules` (`id`, `module_id`, `plan_id`, `module_title`, `module_type`, `module_status`, `item_title`, `icon`, `type`, `link`) VALUES
(31, 100010, NULL, 'User Management', 'system', 'deactivated', 'User Type Management', 'users-cog', 'independent', 'user-type-management'),
(32, 200010, NULL, 'Staff Management', 'system', 'deactivated', 'Staff Management', 'user-hard-hat', 'independent', 'staff-account-management'),
(48, 10020231007220318, NULL, 'Dashboard', 'system', 'deactivated', 'Dashboard', 'chart-bar', 'independent', 'dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `company_staff`
--

DROP TABLE IF EXISTS `company_staff`;
CREATE TABLE IF NOT EXISTS `company_staff` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(25) NOT NULL,
  `user_id` bigint(25) NOT NULL,
  `work_shift` int(255) NOT NULL,
  `regular_account_id` bigint(25) DEFAULT NULL,
  `department` int(100) DEFAULT NULL,
  `user_role` int(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `created_by` bigint(200) NOT NULL,
  `last_updated` text NOT NULL,
  `block` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `module_function`
--

DROP TABLE IF EXISTS `module_function`;
CREATE TABLE IF NOT EXISTS `module_function` (
  `module_id` bigint(25) NOT NULL,
  `function_id` bigint(25) NOT NULL,
  `function_title` varchar(200) NOT NULL,
  PRIMARY KEY (`function_id`),
  UNIQUE KEY `function_id` (`function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_modules`
--

DROP TABLE IF EXISTS `role_modules`;
CREATE TABLE IF NOT EXISTS `role_modules` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `business_id` bigint(25) NOT NULL,
  `role_id` int(200) NOT NULL,
  `module_id` bigint(25) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'on',
  `assigned_by` bigint(25) NOT NULL,
  `date` text NOT NULL,
  `last_updated` text DEFAULT NULL,
  `last_updated_by` bigint(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=459 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_accounts`
--

DROP TABLE IF EXISTS `staff_accounts`;
CREATE TABLE IF NOT EXISTS `staff_accounts` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `staff_id` bigint(25) NOT NULL,
  `staff_personal_id` bigint(25) NOT NULL,
  `business_id` bigint(25) NOT NULL,
  `role_id` int(100) NOT NULL,
  `added_date` varchar(100) NOT NULL,
  `added_by` bigint(25) NOT NULL,
  `account_type` int(10) NOT NULL,
  `block` int(10) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_right`
--

DROP TABLE IF EXISTS `staff_right`;
CREATE TABLE IF NOT EXISTS `staff_right` (
  `business_id` bigint(200) NOT NULL,
  `role_id` int(200) NOT NULL,
  `module_id` bigint(25) NOT NULL,
  `right_id` bigint(25) NOT NULL AUTO_INCREMENT,
  `function_id` bigint(25) NOT NULL,
  `super_function` bigint(25) NOT NULL DEFAULT 0,
  `status` varchar(10) NOT NULL DEFAULT 'on',
  `added_by` bigint(200) NOT NULL,
  `date_added` text NOT NULL,
  `last_updated_by` bigint(25) DEFAULT NULL,
  `last_updated` text DEFAULT NULL,
  PRIMARY KEY (`right_id`),
  UNIQUE KEY `right_id` (`right_id`)
) ENGINE=InnoDB AUTO_INCREMENT=978 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_role`
--

DROP TABLE IF EXISTS `staff_role`;
CREATE TABLE IF NOT EXISTS `staff_role` (
  `company_id` bigint(200) NOT NULL,
  `role_id` int(100) NOT NULL AUTO_INCREMENT,
  `role_title` varchar(100) NOT NULL,
  `added_by` bigint(200) NOT NULL,
  `date_added` text NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=513 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_security`
--

DROP TABLE IF EXISTS `users_security`;
CREATE TABLE IF NOT EXISTS `users_security` (
  `user_id` bigint(25) NOT NULL,
  `number` int(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `user_type` varchar(24) NOT NULL,
  `blocked` int(10) DEFAULT 0,
  `last_login` int(2) DEFAULT 0,
  `last_location` varchar(200) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `last_updated` text DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_security`
--

INSERT INTO `users_security` (`user_id`, `number`, `country`, `username`, `password`, `user_type`, `blocked`, `last_login`, `last_location`, `qr_code`, `last_updated`) VALUES
(100, 770558804, 'Liberia', 'root_ninja', '$2y$10$2mz1r0qKfX6nCCUYh7L8S.Cu3nvafgaCvnh2yX6sMt5Yw5WDd/hqG', '1', 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

DROP TABLE IF EXISTS `user_accounts`;
CREATE TABLE IF NOT EXISTS `user_accounts` (
  `user_id` bigint(200) NOT NULL,
  `user_type` int(250) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(25) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `date_of_birth` varchar(25) DEFAULT NULL,
  `gender` varchar(80) DEFAULT NULL,
  `address` varchar(100) NOT NULL,
  `city_providence` varchar(100) DEFAULT NULL,
  `email` varchar(25) DEFAULT NULL,
  `country` text NOT NULL,
  `image` varchar(250) NOT NULL DEFAULT 'photo',
  `last_updated` varchar(100) DEFAULT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  `user_token` varchar(100) DEFAULT NULL,
  `latitude` text DEFAULT NULL,
  `longitude` text DEFAULT NULL,
  `approval_status` varchar(100) DEFAULT 'approved',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_accounts`
--

INSERT INTO `user_accounts` (`user_id`, `user_type`, `first_name`, `middle_name`, `last_name`, `full_name`, `date_of_birth`, `gender`, `address`, `city_providence`, `email`, `country`, `image`, `last_updated`, `qrcode`, `user_token`, `latitude`, `longitude`, `approval_status`) VALUES
(100, 1, 'Ghost', NULL, 'Ninja', 'Ghost Ninja', 'March 3, 1998', 'ghost', 'Oldest Congo Town, Monrovia Liberia', 'Monrovia, Libera', 'conteeglaydor@gmail.com', 'Liberia', '/media/images/ninja_profile.png', NULL, NULL, NULL, NULL, NULL, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `user_account_type`
--

DROP TABLE IF EXISTS `user_account_type`;
CREATE TABLE IF NOT EXISTS `user_account_type` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `account_type` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `color` varchar(100) NOT NULL,
  `status` int(10) DEFAULT NULL,
  `date_created` varchar(100) NOT NULL,
  `created_by` bigint(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account_type`
--

INSERT INTO `user_account_type` (`id`, `account_type`, `title`, `icon`, `color`, `status`, `date_created`, `created_by`) VALUES
(1, 'ninja', 'Ninja', 'user-ninja', '#000000', 1, '', 100);

-- --------------------------------------------------------

--
-- Table structure for table `user_verification_code`
--

DROP TABLE IF EXISTS `user_verification_code`;
CREATE TABLE IF NOT EXISTS `user_verification_code` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `user_type` varchar(25) NOT NULL,
  `code` varchar(100) NOT NULL,
  `number` varchar(25) NOT NULL,
  `country` varchar(100) NOT NULL,
  `email` text DEFAULT NULL,
  `date` text NOT NULL,
  `status` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=323 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_accounts`
--
ALTER TABLE `user_accounts` ADD FULLTEXT KEY `full_name` (`full_name`,`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
