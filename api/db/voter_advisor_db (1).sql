-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 03, 2023 at 04:09 PM
-- Server version: 10.6.5-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voter_advisor_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_group_module`
--

DROP TABLE IF EXISTS `account_group_module`;
CREATE TABLE IF NOT EXISTS `account_group_module` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `account_group_id` int(100) NOT NULL,
  `module_id` int(200) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'on',
  `assigned_by` bigint(25) NOT NULL,
  `date` text NOT NULL,
  `last_updated` text DEFAULT NULL,
  `last_updated_by` bigint(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=433 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `account_group_module`
--

INSERT INTO `account_group_module` (`id`, `account_group_id`, `module_id`, `status`, `assigned_by`, `date`, `last_updated`, `last_updated_by`) VALUES
(424, 1, 100010, 'on', 100, '2023-02-21 17:34:37', '100', 0),
(425, 2, 200010, 'on', 90020230222121421, '2023-02-22 14:40:10', '90020230222121421', 0),
(426, 2, 5000100, 'on', 90020230222121421, '2023-02-22 14:40:10', '90020230222121421', 0),
(427, 2, 6000, 'on', 100, '2023-04-03 15:57:22', '100', NULL),
(428, 2, 5000, 'on', 100, '2023-04-03 15:57:22', '100', NULL),
(429, 7, 7000, 'on', 100, '2023-04-03 15:59:39', '100', NULL),
(430, 7, 4000100, 'on', 100, '2023-04-03 15:59:39', '100', NULL),
(431, 7, 3000100, 'on', 100, '2023-04-03 15:59:39', '100', NULL),
(432, 7, 200010, 'on', 100, '2023-04-03 15:59:39', '100', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `app_modules`
--

INSERT INTO `app_modules` (`id`, `module_id`, `plan_id`, `module_title`, `module_type`, `module_status`, `item_title`, `icon`, `type`, `link`) VALUES
(31, 100010, NULL, 'User Management', 'system', 'deactivated', 'User Type Management', 'users-cog', 'independent', 'user-type-management'),
(32, 200010, NULL, 'Staff Management', 'system', 'deactivated', 'Staff Management', 'user-hard-hat', 'independent', 'staff-account-management'),
(38, 3000100, NULL, 'Issues Module', 'system', 'deactivated', 'Issues Module', 'question-square', 'independent', 'issues-manager'),
(39, 4000100, NULL, 'Election Management Module', 'system', 'deactivated', 'Election Management Module', 'box-ballot', 'independent', 'elections-manager'),
(41, 5000, NULL, 'Candidate Module', 'system', 'deactivated', 'Candidate Module', 'users', 'independent', 'candidate-manager'),
(42, 6000, NULL, 'Political Party Priorities Module', 'system', 'deactivated', 'Political Priorities', 'poll-people', 'independent', 'political-priorities-manager'),
(43, 7000, NULL, 'County Module', 'system', 'deactivated', 'Counties Manager', 'map-marked-alt', 'independent', 'counties');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `election_type_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `image` text NOT NULL,
  `position` varchar(100) NOT NULL,
  `county` varchar(25) NOT NULL,
  `date_added` varchar(100) NOT NULL,
  `deleted` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_type_id`, `party_id`, `first_name`, `middle_name`, `last_name`, `full_name`, `image`, `position`, `county`, `date_added`, `deleted`) VALUES
(1, 1, 100, 'John', 'F.', 'Sommony', 'John F. Sommony', '/media/images/default_avatar.png', 'President', 'Grand Bassa County', '2023-03-25 20:16:41', 1),
(2, 1, 100, 'Contee', 'C. ', 'Glaydor', 'Contee C.  Glaydor', '/media/images/default_avatar.png', 'Senator', 'Grand Bassa', '2023-04-03 16:39:01', 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `county`
--

DROP TABLE IF EXISTS `county`;
CREATE TABLE IF NOT EXISTS `county` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `date` varchar(100) NOT NULL,
  `deleted` int(10) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `county`
--

INSERT INTO `county` (`id`, `title`, `date`, `deleted`) VALUES
(1, 'Updated County', '2023-04-02 17:28:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `county_district`
--

DROP TABLE IF EXISTS `county_district`;
CREATE TABLE IF NOT EXISTS `county_district` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `county_id` int(255) NOT NULL,
  `district_title` varchar(250) NOT NULL,
  `date` varchar(100) NOT NULL,
  `deleted` int(10) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `county_district`
--

INSERT INTO `county_district` (`id`, `county_id`, `district_title`, `date`, `deleted`) VALUES
(1, 1, 'Updated District', '2023-04-02 17:33:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `election_type`
--

DROP TABLE IF EXISTS `election_type`;
CREATE TABLE IF NOT EXISTS `election_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `date` varchar(100) NOT NULL,
  `added_by` bigint(25) NOT NULL,
  `deleted` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `election_type`
--

INSERT INTO `election_type` (`id`, `status`, `title`, `date`, `added_by`, `deleted`) VALUES
(1, 1, 'National Elections', '2023-03-25 20:23:13', 100, 0);

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

DROP TABLE IF EXISTS `issues`;
CREATE TABLE IF NOT EXISTS `issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_title` varchar(255) NOT NULL,
  `base_value` int(255) DEFAULT 0,
  `description` varchar(255) NOT NULL,
  `date` varchar(100) NOT NULL,
  `added_by` bigint(25) NOT NULL,
  `deleted` int(10) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `issue_title`, `base_value`, `description`, `date`, `added_by`, `deleted`) VALUES
(1, 'Education Update', 20, 'We all need good roads', '2023-0', 100, 1),
(2, 'Good Road Network Test', 5, 'We all need good roads', '2023-03-25 20:09:52', 100, 0),
(3, 'Good Road Network Test', 10, 'We all need good roads', '2023-04-01 19:52:57', 100, 0);

-- --------------------------------------------------------

--
-- Table structure for table `module_function`
--

DROP TABLE IF EXISTS `module_function`;
CREATE TABLE IF NOT EXISTS `module_function` (
  `module_id` int(200) NOT NULL,
  `function_id` int(100) NOT NULL,
  `function_title` varchar(200) NOT NULL,
  PRIMARY KEY (`function_id`),
  UNIQUE KEY `function_id` (`function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `module_function`
--

INSERT INTO `module_function` (`module_id`, `function_id`, `function_title`) VALUES
(5000, 5001, 'View Candidate'),
(5000, 5002, 'Add Candidate'),
(5000, 5003, 'Edit/Update Candidate'),
(5000, 5004, 'Remove Candidate'),
(6000, 6001, 'View Party Priorities'),
(6000, 6002, 'Set Part Issue Priorities'),
(7000, 7001, 'View  Countires and districts'),
(7000, 7002, 'Add/Create new county and districts'),
(7000, 7003, 'Edit County and district'),
(7000, 7004, 'Remove County and districts'),
(100010, 100012, 'View User Account Group'),
(100010, 100014, 'View User Account Group Modules'),
(100010, 100017, 'Assign modules to user account groups'),
(200010, 200021, 'Add Staff Role'),
(200020, 200022, 'View Staff Role'),
(200010, 200024, 'Assign Module to Role'),
(200010, 200025, 'Create new Staffs'),
(3000100, 3000101, 'View Issues'),
(3000100, 3000102, 'Add/Create new issues'),
(3000100, 3000103, 'Edit Issues'),
(3000100, 3000104, 'Delete/Remove Package'),
(4000100, 4000101, 'View Elections'),
(4000100, 4000102, 'Add/create new elections'),
(4000100, 4000103, 'Edit Election'),
(4000100, 4000104, 'Delete/Remove Election');

-- --------------------------------------------------------

--
-- Table structure for table `party`
--

DROP TABLE IF EXISTS `party`;
CREATE TABLE IF NOT EXISTS `party` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `date` datetime(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `party_issue_priority`
--

DROP TABLE IF EXISTS `party_issue_priority`;
CREATE TABLE IF NOT EXISTS `party_issue_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `party_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `point_allocated` int(11) NOT NULL,
  `date` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `party_issue_priority`
--

INSERT INTO `party_issue_priority` (`id`, `party_id`, `issue_id`, `point_allocated`, `date`) VALUES
(1, 100, 1, 200, '2023-0'),
(2, 100, 2, 400, '2023-0'),
(3, 100, 3, 600, '2023-0');

-- --------------------------------------------------------

--
-- Table structure for table `role_modules`
--

DROP TABLE IF EXISTS `role_modules`;
CREATE TABLE IF NOT EXISTS `role_modules` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `business_id` bigint(25) NOT NULL,
  `role_id` int(200) NOT NULL,
  `module_id` int(200) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'on',
  `assigned_by` bigint(25) NOT NULL,
  `date` text NOT NULL,
  `last_updated` text DEFAULT NULL,
  `last_updated_by` bigint(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=444 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role_modules`
--

INSERT INTO `role_modules` (`id`, `business_id`, `role_id`, `module_id`, `status`, `assigned_by`, `date`, `last_updated`, `last_updated_by`) VALUES
(441, 100, 504, 5000, 'on', 100, '2023-04-01 20:01:09', '100', NULL),
(442, 100, 504, 4000100, 'on', 100, '2023-04-01 20:01:09', '100', NULL),
(443, 100, 504, 3000100, 'on', 100, '2023-04-01 20:01:09', '100', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff_accounts`
--

INSERT INTO `staff_accounts` (`id`, `staff_id`, `staff_personal_id`, `business_id`, `role_id`, `added_date`, `added_by`, `account_type`, `block`) VALUES
(12, 70020230403154631, 90020230403154631, 100, 504, '2023-04-03 :15:31:46', 100, 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `staff_right`
--

DROP TABLE IF EXISTS `staff_right`;
CREATE TABLE IF NOT EXISTS `staff_right` (
  `business_id` bigint(200) NOT NULL,
  `role_id` int(200) NOT NULL,
  `module_id` int(200) NOT NULL,
  `right_id` int(200) NOT NULL AUTO_INCREMENT,
  `function_id` int(200) NOT NULL,
  `super_function` int(100) NOT NULL DEFAULT 0,
  `status` varchar(10) NOT NULL DEFAULT 'on',
  `added_by` bigint(200) NOT NULL,
  `date_added` text NOT NULL,
  `last_updated_by` bigint(25) DEFAULT NULL,
  `last_updated` text DEFAULT NULL,
  PRIMARY KEY (`right_id`),
  UNIQUE KEY `right_id` (`right_id`)
) ENGINE=InnoDB AUTO_INCREMENT=945 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff_right`
--

INSERT INTO `staff_right` (`business_id`, `role_id`, `module_id`, `right_id`, `function_id`, `super_function`, `status`, `added_by`, `date_added`, `last_updated_by`, `last_updated`) VALUES
(100, 504, 5000, 933, 5001, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 5000, 934, 5002, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 5000, 935, 5003, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 5000, 936, 5004, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 4000100, 937, 4000101, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 4000100, 938, 4000102, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 4000100, 939, 4000103, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 4000100, 940, 4000104, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 3000100, 941, 3000101, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 3000100, 942, 3000102, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 3000100, 943, 3000103, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL),
(100, 504, 3000100, 944, 3000104, 0, 'on', 100, '2023-04-01 20:01:09', NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=505 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff_role`
--

INSERT INTO `staff_role` (`company_id`, `role_id`, `role_title`, `added_by`, `date_added`) VALUES
(100, 504, 'Test Staff Role', 100, '2023-04-01 19:53:55');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_security`
--

INSERT INTO `users_security` (`user_id`, `number`, `country`, `username`, `password`, `user_type`, `blocked`, `last_login`, `last_location`, `qr_code`, `last_updated`) VALUES
(100, 770558804, 'Liberia', 'root_ninja', '$2y$10$2mz1r0qKfX6nCCUYh7L8S.Cu3nvafgaCvnh2yX6sMt5Yw5WDd/hqG', '1', 0, 0, NULL, NULL, NULL),
(90020230403154631, 775901684, 'Liberia', 'Teelu', '$2y$10$gVGvrtVsdmTgoeoJfjtdOeWU2GahZ6AHkfsSa7WNDgBHgJQqrsnim', '5', 0, 0, NULL, NULL, '2023-04-03 15:46:31');

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
  `city_providence` varchar(100) NOT NULL,
  `email` varchar(25) DEFAULT NULL,
  `country` text NOT NULL,
  `image` varchar(250) NOT NULL DEFAULT 'photo',
  `last_updated` varchar(100) DEFAULT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  `user_token` varchar(100) DEFAULT NULL,
  `latitude` text DEFAULT NULL,
  `longitude` text DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_accounts`
--

INSERT INTO `user_accounts` (`user_id`, `user_type`, `first_name`, `middle_name`, `last_name`, `full_name`, `date_of_birth`, `gender`, `address`, `city_providence`, `email`, `country`, `image`, `last_updated`, `qrcode`, `user_token`, `latitude`, `longitude`) VALUES
(100, 1, 'Ghost', NULL, 'Ninja', 'Ghost Ninja', 'March 3, 1998', 'ghost', 'Oldest Congo Town, Monrovia Liberia', 'Monrovia, Libera', 'conteeglaydor@gmail.com', 'Liberia', '/media/images/ninja_profile.png', NULL, NULL, NULL, NULL, NULL),
(90020230403154631, 5, NULL, NULL, NULL, 'Enoch Jallah', NULL, NULL, 'Duport Road', 'Montserrado', 'enochcjallah@gmail.com', 'Liberia', '/media/images/user-image-placeholder.png', NULL, NULL, NULL, NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_account_type`
--

INSERT INTO `user_account_type` (`id`, `account_type`, `title`, `icon`, `color`, `status`, `date_created`, `created_by`) VALUES
(1, 'ninja', 'Ninja', 'user-ninja', '#000000', 1, '', 100),
(2, 'business', 'Political Party', 'fa-user', '#5c2fe4', 0, 'February 18, 2023 6:20 pm', 100),
(5, 'staff', 'Business Staff Account', 'users', '#000000', 0, '2023-02-22 09:08:57', 100),
(7, 'business', 'Tamma Corporation', 'building', '#00fefcc', 0, 'February 18, 2023 6:20 pm', 100);

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
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

DROP TABLE IF EXISTS `vote`;
CREATE TABLE IF NOT EXISTS `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voter-info-id` int(11) NOT NULL,
  `party-id` int(11) NOT NULL,
  `date` datetime(6) NOT NULL,
  `country` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `voter-info`
--

DROP TABLE IF EXISTS `voter-info`;
CREATE TABLE IF NOT EXISTS `voter-info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone-number` varchar(100) NOT NULL,
  `date-of-brith` varchar(100) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `gender` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `voter-issue-priority`
--

DROP TABLE IF EXISTS `voter-issue-priority`;
CREATE TABLE IF NOT EXISTS `voter-issue-priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(100) NOT NULL,
  `point-allocated` int(11) NOT NULL,
  `date` datetime(6) NOT NULL,
  `voter-id` int(11) NOT NULL,
  `issue-id` int(11) NOT NULL,
  `purged` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
