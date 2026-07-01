-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 01, 2026 at 03:31 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u291997781_leavecard`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('0716d9708d321ffb6a00818614779e779925365c', 'i:1;', 1733725418),
('0716d9708d321ffb6a00818614779e779925365c:timer', 'i:1733725416;', 1733725417),
('0a57cb53ba59c46fc4b692527a38a87c78d84028', 'i:1;', 1733819765),
('0a57cb53ba59c46fc4b692527a38a87c78d84028:timer', 'i:1733819765;', 1733819765),
('1574bddb75c78a6fd2251d61e2993b5146201319', 'i:1;', 1733723823),
('1574bddb75c78a6fd2251d61e2993b5146201319:timer', 'i:1733723823;', 1733723823),
('4d134bc072212ace2df385dae143139da74ec0ef', 'i:1;', 1733753697),
('4d134bc072212ace2df385dae143139da74ec0ef:timer', 'i:1733753697;', 1733753697),
('91032ad7bbcb6cf72875e8e8207dcfba80173f7c', 'i:1;', 1733729230),
('91032ad7bbcb6cf72875e8e8207dcfba80173f7c:timer', 'i:1733729229;', 1733729229),
('9e6a55b6b4563e652a23be9d623ca5055c356940', 'i:1;', 1733726322),
('9e6a55b6b4563e652a23be9d623ca5055c356940:timer', 'i:1733726322;', 1733726322),
('agsdftafw@agjag|127.0.0.1', 'i:1;', 1731994631),
('agsdftafw@agjag|127.0.0.1:timer', 'i:1731994631;', 1731994631),
('agsfgas@gha|127.0.0.1', 'i:1;', 1731994565),
('agsfgas@gha|127.0.0.1:timer', 'i:1731994564;', 1731994564),
('asdasda@fjasf|127.0.0.1', 'i:1;', 1731996276),
('asdasda@fjasf|127.0.0.1:timer', 'i:1731996276;', 1731996276),
('b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f', 'i:1;', 1733728585),
('b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f:timer', 'i:1733728585;', 1733728585),
('d435a6cdd786300dff204ee7c2ef942d3e9034e2', 'i:2;', 1733732506),
('d435a6cdd786300dff204ee7c2ef942d3e9034e2:timer', 'i:1733732505;', 1733732506),
('dsadygasd@gjag|127.0.0.1', 'i:1;', 1731995103),
('dsadygasd@gjag|127.0.0.1:timer', 'i:1731995103;', 1731995103),
('f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59', 'i:1;', 1734008734),
('f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59:timer', 'i:1734008734;', 1734008734),
('gsafdgas@ghag|127.0.0.1', 'i:1;', 1731994533),
('gsafdgas@ghag|127.0.0.1:timer', 'i:1731994533;', 1731994533),
('hadbkas@jasfja|127.0.0.1', 'i:1;', 1731996163),
('hadbkas@jasfja|127.0.0.1:timer', 'i:1731996162;', 1731996163),
('hasdghas@fahs|127.0.0.1', 'i:1;', 1731994444),
('hasdghas@fahs|127.0.0.1:timer', 'i:1731994444;', 1731994444),
('hasdghsa@fggajg|127.0.0.1', 'i:1;', 1731994477),
('hasdghsa@fggajg|127.0.0.1:timer', 'i:1731994477;', 1731994477),
('hsagdasgdgd@g|127.0.0.1', 'i:1;', 1731994426),
('hsagdasgdgd@g|127.0.0.1:timer', 'i:1731994426;', 1731994426),
('sadashda@faf|127.0.0.1', 'i:1;', 1731995201),
('sadashda@faf|127.0.0.1:timer', 'i:1731995200;', 1731995200),
('sdadsa@jfajf|127.0.0.1', 'i:1;', 1731994773),
('sdadsa@jfajf|127.0.0.1:timer', 'i:1731994773;', 1731994773),
('testing@gmail.com|127.0.0.1', 'i:3;', 1732188098),
('testing@gmail.com|127.0.0.1:timer', 'i:1732188098;', 1732188098);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `card_info`
--

CREATE TABLE `card_info` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `emp_num` varchar(255) NOT NULL,
  `inclusive_period` varchar(255) DEFAULT NULL,
  `nature_of_activity` varchar(255) DEFAULT NULL,
  `no_of_days_credited` decimal(8,2) DEFAULT NULL,
  `dso_no_vsr` varchar(255) DEFAULT NULL,
  `inclusive_dates` varchar(255) DEFAULT NULL,
  `no_days_leave` decimal(8,2) DEFAULT NULL,
  `leave_without_pay` decimal(8,2) DEFAULT NULL,
  `service_cred_bal` decimal(8,2) DEFAULT NULL,
  `nature_of_leave` varchar(255) DEFAULT NULL,
  `dso_no_rol` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `card_info`
--

INSERT INTO `card_info` (`id`, `emp_num`, `inclusive_period`, `nature_of_activity`, `no_of_days_credited`, `dso_no_vsr`, `inclusive_dates`, `no_days_leave`, `leave_without_pay`, `service_cred_bal`, `nature_of_leave`, `dso_no_rol`, `remarks`, `created_at`, `updated_at`) VALUES
(258, '4637684', '1/5-3/1/14', 'NAT REVIEW', 6.00, '', '', 0.00, 0.00, 6.00, '', '', '', NULL, NULL),
(259, '4637684', 'APRIL-MAY 2014', 'SCH. PROPERTY CUSTODIAN', 15.00, '', '', 0.00, 0.00, 21.00, '', '', '', NULL, NULL),
(260, '4637684', '11/15,22/14', 'NAT\'L INVENTORY OF DEPED', 2.00, '', '', 0.00, 0.00, 23.00, '', '', '', NULL, NULL),
(261, '4637684', '', 'PUBLIC SCH. BLDG SY \'14-\'15', 0.00, '', '', 0.00, 0.00, 0.00, '', '', '', NULL, NULL),
(262, '4637684', 'CY 2015', '', 0.00, '', '', 0.00, 0.00, 0.00, '', '', '', NULL, NULL),
(263, '4637684', '1/24/15', 'EARLY REGISTRATION', 1.00, '', '', 0.00, 0.00, 24.00, '', '', '', NULL, NULL),
(264, '4637684', '5/18-23/15', 'BRIGADA ESKWELA', 3.00, '', '', 0.00, 0.00, 27.00, '', '', '', NULL, NULL),
(265, '4637684', '', '', 0.00, '', '7/23/15', 1.00, 0.00, 26.00, 'SL', '', '', NULL, NULL),
(266, '4637684', '', '', 0.00, '', '08-Oct', 1.00, 0.00, 25.00, 'SL', '', '', NULL, NULL),
(267, '4637684', '', '', 0.00, '', '10/19pm;26am/15', 0.00, 1.00, 0.00, 'UND/TAR', '', 'w/out pay', NULL, NULL),
(268, '4637684', '06-Dec', 'INDEPENDENCE DAY', 1.00, '64, s. 15', '', 0.00, 0.00, 26.00, '', '', '', NULL, NULL),
(269, '4637684', '8/15/15', '3-day TRAINING for GR. 10 TLE', 1.00, '', '', 0.00, 0.00, 27.00, '', '', '', NULL, NULL),
(270, '4637684', '', '', 0.00, '', '11-Apr', 1.00, 0.00, 26.00, '', '', '', NULL, NULL),
(271, '4637684', '8/18/15', 'JMR FORUM', 1.00, '109, s. 15', '', 0.00, 0.00, 27.00, '', '', '', NULL, NULL),
(272, '4637684', '9/18/15', 'MILITARY PARADE', 1.00, '', '', 0.00, 0.00, 28.00, '', '', '', NULL, NULL),
(273, '4637684', '', '', 0.00, '', '1/21/16', 0.00, 0.50, 0.00, 'PERSONAL', '', 'W/OUT PAY', NULL, NULL),
(274, '4637684', '', '', 0.00, '', '2/15/16', 1.00, 0.00, 27.00, 'SL', '', '', NULL, NULL),
(275, '4637684', '11/21/15', 'PALARONG PANLUNSOD', 1.00, '', '', 0.00, 0.00, 28.00, '', '', '', NULL, NULL),
(276, '4637684', '1/16/16', 'DALAGAN-LAKAW', 1.00, '', '', 0.00, 0.00, 29.00, '', '', '', NULL, NULL),
(277, '4637684', '1/30/16', 'Early Registration', 1.00, '241, s. 16', '', 0.00, 0.00, 30.00, '', '', '', NULL, NULL),
(278, '4637684', '2/26-28/16', '2ND DIVISION ARTS CAMP', 2.00, '247, s. 17', '', 0.00, 0.00, 32.00, '', '', '', NULL, NULL),
(279, '4637684', '4/28,29/16', 'Retooling on Test Construction', 2.00, '105, s. 16', '', 0.00, 0.00, 34.00, '', '', '', NULL, NULL),
(280, '4637684', '6/2,4/17', 'Brigada eskwela', 2.00, '244, s. 17', '', 0.00, 0.00, 36.00, '', '', '', NULL, NULL),
(281, '4637684', '06-Jul', 'Trng Wokshp on Integration', 1.00, '242, s. 17', '', 0.00, 0.00, 37.00, '', '', '', NULL, NULL),
(282, '4637684', '', 'of Computer Utility', 0.00, '', '', 0.00, 0.00, 37.00, '', '', '', NULL, NULL),
(283, '4637684', '06-Dec', 'Independence Day', 1.00, '245, s. 17', '', 0.00, 0.00, 38.00, '', '', '', NULL, NULL),
(284, '4637684', '7/16-17/16', 'NAGA CITY GRASS ROOTS ', 2.00, '216, s. 16', '', 0.00, 0.00, 40.00, '', '', '', NULL, NULL),
(285, '4637684', '', 'COACHES TRAINING', 0.00, '', '', 0.00, 0.00, 40.00, '', '', '', NULL, NULL),
(286, '4637684', '', '', 0.00, '', '7/25/16', 1.00, 0.00, 39.00, 'SL', '', '', NULL, NULL),
(287, '4637684', '7/22-24/16', 'VAL. FORM. THROUG FOOTBALL', 2.00, '274, s. 16', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(288, '4637684', '08-Jun', 'DIV. PLAN CONF ON EPP/TVL/TLE', 1.00, '283, s. 16', '', 0.00, 0.00, 42.00, '', '', '', NULL, NULL),
(289, '4637684', '', '', 0.00, '', '8/16/16', 1.00, 0.00, 41.00, 'SL', '', '', NULL, NULL),
(290, '4637684', '9/24/16*', 'RILINIG KI INA', 0.00, '246, s. 17', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(291, '4637684', '11/26/16*', 'NAPSSAA Meet', 0.00, '249, s. 17', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(292, '4637684', '11/30/16*', 'Div. Exhibit of Best Output', 0.00, '12, s. 17', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(293, '4637684', '', '', 0.00, '', '10/29,30,31am/19', 2.50, 0.00, 38.50, 'SL', '', '*exceeds limit', NULL, NULL),
(294, '4637684', 'CY 2017', '', 0.00, '', '2/24/17', 1.00, 0.00, 34.50, 'SL', '', '', NULL, NULL),
(295, '4637684', '1/29/17', '3rd Dalagan-Lakaw', 1.00, '252, s. 17', '', 0.00, 0.00, 35.50, '', '', '', NULL, NULL),
(296, '4637684', '02-Apr', 'PALARONG BICOL', 1.00, '158, s. 17', '', 0.00, 0.00, 36.50, '', '', '', NULL, NULL),
(297, '4637684', '03-Apr', 'Color Fun Run', 1.00, '161, s. 17', '', 0.00, 0.00, 37.50, '', '', '', NULL, NULL),
(298, '4637684', '', '', 0.00, '', '6/13/17', 1.00, 0.00, 36.50, 'SL', '', '', NULL, NULL),
(299, '4637684', '5/29-31/17', 'GULAYAN SA PAARALAN', 4.00, '203, s. 17', '', 0.00, 0.00, 40.50, '', '', '', NULL, NULL),
(300, '4637684', '', '', 0.00, '', '08-Jan', 1.00, 0.00, 39.50, 'SL', '', '', NULL, NULL),
(301, '4637684', '', '', 0.00, '', '9/12-13/17', 2.00, 0.00, 37.50, 'SL', '', '', NULL, NULL),
(302, '4637684', '', '', 0.00, '', '10/26/17', 1.00, 0.00, 36.50, 'SL', '', '', NULL, NULL),
(303, '4637684', '', '', 0.00, '', '11/24/17', 1.00, 0.00, 35.50, 'SL', '', '', NULL, NULL),
(304, '4637684', '5/26/17', 'EHRIS/EPDS', 1.00, '148, s. 18', '', 0.00, 0.00, 36.50, '', '', '', NULL, NULL),
(305, '4637684', '5/15-20/18', 'Brigada Eskwela 2017', 2.50, '155, s. 18', '', 0.00, 0.00, 39.00, '', '', '', NULL, NULL),
(306, '4637684', '1/4-6/17', 'Emergency Brigada', 0.50, '157, s. 18', '', 0.00, 0.00, 39.50, '', '', '', NULL, NULL),
(307, '4637684', 'CY 2018', '', 0.00, '', '2/20/18', 1.00, 0.00, 38.50, 'SL', '', '', NULL, NULL),
(308, '4637684', '5/22-23/2018', 'Gulayan sa Paaralan', 2.00, '98, s. 18', '', 0.00, 0.00, 40.50, '', '', '', NULL, NULL),
(309, '4637684', '1/06/18', '5th Div. ', 1.00, '', '', 0.00, 0.00, 41.50, '', '', '', NULL, NULL),
(310, '4637684', '4/26-30/18', 'Training in Inclusive Educ. For Teachers', 5.00, '140, s. 18', '', 0.00, 0.00, 46.50, '', '', '', NULL, NULL),
(311, '4540408', '1/5-3/1/14', 'NAT REVIEW', 6.00, '', '', 0.00, 0.00, 6.00, '', '', '', NULL, NULL),
(312, '4540408', 'APRIL-MAY 2014', 'SCH. PROPERTY CUSTODIAN', 15.00, '', '', 0.00, 0.00, 21.00, '', '', '', NULL, NULL),
(313, '4540408', '11/15,22/14', 'NAT\'L INVENTORY OF DEPED', 2.00, '', '', 0.00, 0.00, 23.00, '', '', '', NULL, NULL),
(314, '4540408', NULL, 'PUBLIC SCH. BLDG SY \'14-\'15', 0.00, NULL, NULL, 0.00, 0.00, 0.00, NULL, '', NULL, NULL, '2024-12-12 17:51:34'),
(315, '4540408', 'CY 2015', NULL, 0.00, NULL, NULL, 0.00, 0.00, 0.00, NULL, '', NULL, NULL, '2024-12-12 17:54:59'),
(316, '4540408', '1/24/15', 'EARLY REGISTRATION', 1.00, '', '', 0.00, 0.00, 24.00, '', '', '', NULL, NULL),
(317, '4540408', '5/18-23/15', 'BRIGADA ESKWELA', 3.00, '', '', 0.00, 0.00, 27.00, '', '', '', NULL, NULL),
(318, '4540408', NULL, NULL, 0.00, NULL, '7/23/15', 1.00, 0.00, 26.00, 'SL', '', '', NULL, '2024-12-12 03:09:42'),
(319, '4540408', NULL, NULL, 0.00, NULL, '08-Oct', 1.00, 0.00, 25.00, 'SL', '', NULL, NULL, '2024-12-12 17:50:40'),
(320, '4540408', NULL, NULL, 0.00, NULL, '10/19pm;26am/15', 0.00, 1.00, 0.00, 'UND/TAR', '', 'w/out pay', NULL, '2024-12-12 03:27:23'),
(321, '4540408', '06-Dec', 'INDEPENDENCE DAY', 1.00, '64, s. 15', '', 0.00, 0.00, 26.00, '', '', '', NULL, NULL),
(322, '4540408', '8/15/15', '3-day TRAINING for GR. 10 TLE', 1.00, '', '', 0.00, 0.00, 27.00, '', '', '', NULL, NULL),
(323, '4540408', NULL, NULL, 0.00, NULL, '11-Apr', 1.00, 0.00, 26.00, NULL, '', '', NULL, '2024-12-12 03:10:09'),
(324, '4540408', '8/18/15', 'JMR FORUM', 1.00, '109, s. 15', '', 0.00, 0.00, 27.00, '', '', '', NULL, NULL),
(325, '4540408', '9/18/15', 'MILITARY PARADE', 1.00, '', '', 0.00, 0.00, 28.00, '', '', '', NULL, NULL),
(326, '4540408', '', '', 0.00, '', '1/21/16', 0.00, 0.50, 0.00, 'PERSONAL', '', 'W/OUT PAY', NULL, NULL),
(327, '4540408', '', '', 0.00, '', '2/15/16', 1.00, 0.00, 27.00, 'SL', '', '', NULL, NULL),
(328, '4540408', '11/21/15', 'PALARONG PANLUNSOD', 1.00, '', '', 0.00, 0.00, 28.00, '', '', '', NULL, NULL),
(329, '4540408', '1/16/16', 'DALAGAN-LAKAW', 1.00, '', '', 0.00, 0.00, 29.00, '', '', '', NULL, NULL),
(330, '4540408', '1/30/16', 'Early Registration', 1.00, '241, s. 16', '', 0.00, 0.00, 30.00, '', '', '', NULL, NULL),
(331, '4540408', '2/26-28/16', '2ND DIVISION ARTS CAMP', 2.00, '247, s. 17', '', 0.00, 0.00, 32.00, '', '', '', NULL, NULL),
(332, '4540408', '4/28,29/16', 'Retooling on Test Construction', 2.00, '105, s. 16', '', 0.00, 0.00, 34.00, '', '', '', NULL, NULL),
(333, '4540408', '6/2,4/17', 'Brigada eskwela', 2.00, '244, s. 17', '', 0.00, 0.00, 36.00, '', '', '', NULL, NULL),
(334, '4540408', '06-Jul', 'Trng Wokshp on Integration', 1.00, '242, s. 17', '', 0.00, 0.00, 37.00, '', '', '', NULL, NULL),
(335, '4540408', '', 'of Computer Utility', 0.00, '', '', 0.00, 0.00, 37.00, '', '', '', NULL, NULL),
(336, '4540408', '06-Dec', 'Independence Day', 1.00, '245, s. 17', '', 0.00, 0.00, 38.00, '', '', '', NULL, NULL),
(337, '4540408', '7/16-17/16', 'NAGA CITY GRASS ROOTS ', 2.00, '216, s. 16', '', 0.00, 0.00, 40.00, '', '', '', NULL, NULL),
(338, '4540408', '', 'COACHES TRAINING', 0.00, '', '', 0.00, 0.00, 40.00, '', '', '', NULL, NULL),
(339, '4540408', '', '', 0.00, '', '7/25/16', 1.00, 0.00, 39.00, 'SL', '', '', NULL, NULL),
(340, '4540408', '7/22-24/16', 'VAL. FORM. THROUG FOOTBALL', 2.00, '274, s. 16', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(341, '4540408', '08-Jun', 'DIV. PLAN CONF ON EPP/TVL/TLE', 1.00, '283, s. 16', '', 0.00, 0.00, 42.00, '', '', '', NULL, NULL),
(342, '4540408', '', '', 0.00, '', '8/16/16', 1.00, 0.00, 41.00, 'SL', '', '', NULL, NULL),
(343, '4540408', '9/24/16*', 'RILINIG KI INA', 0.00, '246, s. 17', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(344, '4540408', '11/26/16*', 'NAPSSAA Meet', 0.00, '249, s. 17', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(345, '4540408', '11/30/16*', 'Div. Exhibit of Best Output', 0.00, '12, s. 17', '', 0.00, 0.00, 41.00, '', '', '', NULL, NULL),
(346, '4540408', '', '', 0.00, '', '10/29,30,31am/19', 2.50, 0.00, 38.50, 'SL', '', '*exceeds limit', NULL, NULL),
(347, '4540408', 'CY 2017', '', 0.00, '', '2/24/17', 1.00, 0.00, 34.50, 'SL', '', '', NULL, NULL),
(348, '4540408', '1/29/17', '3rd Dalagan-Lakaw', 1.00, '252, s. 17', '', 0.00, 0.00, 35.50, '', '', '', NULL, NULL),
(349, '4540408', '02-Apr', 'PALARONG BICOL', 1.00, '158, s. 17', '', 0.00, 0.00, 36.50, '', '', '', NULL, NULL),
(350, '4540408', '03-Apr', 'Color Fun Run', 1.00, '161, s. 17', '', 0.00, 0.00, 37.50, '', '', '', NULL, NULL),
(351, '4540408', '', '', 0.00, '', '6/13/17', 1.00, 0.00, 36.50, 'SL', '', '', NULL, NULL),
(352, '4540408', '5/29-31/17', 'GULAYAN SA PAARALAN', 4.00, '203, s. 17', '', 0.00, 0.00, 40.50, '', '', '', NULL, NULL),
(353, '4540408', '', '', 0.00, '', '08-Jan', 1.00, 0.00, 39.50, 'SL', '', '', NULL, NULL),
(354, '4540408', '', '', 0.00, '', '9/12-13/17', 2.00, 0.00, 37.50, 'SL', '', '', NULL, NULL),
(355, '4540408', '', '', 0.00, '', '10/26/17', 1.00, 0.00, 36.50, 'SL', '', '', NULL, NULL),
(356, '4540408', '', '', 0.00, '', '11/24/17', 1.00, 0.00, 35.50, 'SL', '', '', NULL, NULL),
(357, '4540408', '5/26/17', 'EHRIS/EPDS', 1.00, '148, s. 18', '', 0.00, 0.00, 36.50, '', '', '', NULL, NULL),
(358, '4540408', '5/15-20/18', 'Brigada Eskwela 2017', 2.50, '155, s. 18', NULL, 0.00, 0.00, 39.00, NULL, '', '', NULL, '2024-12-12 05:22:22'),
(359, '4540408', '1/4-6/17', 'Emergency Brigada', 0.50, '157, s. 18', NULL, 0.00, 0.00, 39.50, NULL, '', '', NULL, '2024-12-12 03:32:16'),
(360, '4540408', 'CY 2018', NULL, 0.00, NULL, '2/20/18', 1.00, 0.00, 38.50, 'SL', '', NULL, NULL, '2024-12-12 13:43:42'),
(361, '4540408', '5/22-23/2018', 'Gulayan sa Paaralan', 2.00, '98, s. 18', '', 0.00, 0.00, 40.50, '', '', '', NULL, NULL),
(362, '4540408', '01/06/2018', '5th Div. ', 1.00, '', '', 0.00, 0.00, 41.50, '', '', '', NULL, NULL),
(363, '4540408', '4/26-30/18', 'Training in Inclusive Educ. For Teachers', 5.00, '140, s. 18', '', 0.00, 0.00, 46.50, '', '', '', NULL, NULL),
(364, '4217542', '10/11-12/08', 'A&E National Test', 2.00, '', '', 0.00, 0.00, 2.00, '', '', '', NULL, NULL),
(365, '4217542', '05-Oct', 'COMELEC ELECTION', 5.00, '', '', 0.00, 0.00, 7.00, '', '', '', NULL, NULL),
(366, '4217542', '10/25/10', 'COMELEC ELECTION', 5.00, '', '', 0.00, 0.00, 12.00, '', '', '', NULL, NULL),
(367, '4217542', 'C/Y 2011', '', 0.00, '', '', 0.00, 0.00, 12.00, '', '', '', NULL, NULL),
(368, '4217542', '4/18-22/11', 'Property Custodian', 5.00, '', '', 0.00, 0.00, 17.00, '', '', '', NULL, NULL),
(369, '4217542', '5/2-27/11', 'Property Custodian', 10.00, '', '', 0.00, 0.00, 27.00, '', '', '', NULL, NULL),
(370, '4217542', 'C/Y 2012', '', 0.00, '', '2/22-3/16/12', 17.00, 0.00, 10.00, 'SL', '', 'W/ PAY', NULL, NULL),
(371, '4217542', '', 'Property Custodian', 15.00, '', '', 0.00, 0.00, 25.00, '', '', '', NULL, NULL),
(372, '4217542', 'C/Y 2013', '', 0.00, '', '1/28-2/1/13', 5.00, 0.00, 20.00, 'SL', '', 'W/ PAY', NULL, NULL),
(373, '4217542', '', 'Property Custodian', 15.00, '', '', 0.00, 0.00, 35.00, '', '', '', NULL, NULL),
(374, '4217542', '5/13/13', 'COMELEC ELECTION', 5.00, '', '', 0.00, 0.00, 40.00, '', '', '', NULL, NULL),
(375, '4217542', '10/28/13', 'COMELEC ELECTION', 5.00, '', '', 0.00, 0.00, 45.00, '', '', '', NULL, NULL),
(376, '4217542', 'C/Y 2014', '', 0.00, '', '', 0.00, 0.00, 45.00, '', '', '', NULL, NULL),
(377, '4217542', '', 'Mass Trng. of Gr. 3 & 9 Tchrs.', 5.00, '', '', 0.00, 0.00, 50.00, '', '', '', NULL, NULL),
(378, '4217542', 'C/Y 2015', '', 0.00, '', '', 0.00, 0.00, 50.00, '', '', '', NULL, NULL),
(379, '4217542', '2/28/15', 'Reg. Memo. No. 14, s. 2015', 1.00, '', '', 0.00, 0.00, 51.00, '', '', '', NULL, NULL),
(380, '4217542', '', 'Leadership Cong. For  Supreme', 0.00, '', '', 0.00, 0.00, 51.00, '', '', '', NULL, NULL),
(381, '4217542', '', 'Student\'s Gov\'t Officers & Leaders', 0.00, '', '', 0.00, 0.00, 51.00, '', '', '', NULL, NULL),
(382, '4217542', '5/3-8/15', 'Training for Grade 10 Teachers', 5.00, '', '', 0.00, 0.00, 56.00, '', '', '', NULL, NULL),
(383, '4217542', '5/18-20/15', 'Brigada Eskwela', 1.50, '', '', 0.00, 0.00, 57.50, '', '', '', NULL, NULL),
(384, '4217542', '5/31/15', 'Tayo Para sa Edukasyon', 1.00, '', '', 0.00, 0.00, 58.50, '', '', '', NULL, NULL),
(385, '4217542', '06-Dec', 'Independence Day', 1.00, '', '', 0.00, 0.00, 59.50, '', '', '', NULL, NULL),
(386, '4217542', '7/17,18/15', 'SPG/SSG Training Workshop', 2.00, '', '', 0.00, 0.00, 61.50, '', '', '', NULL, NULL),
(387, '4217542', '8/18/15', 'JMR Forum', 1.00, '', '', 0.00, 0.00, 62.50, '', '', '', NULL, NULL),
(388, '4217542', '8/29/15', '2nd Lakadan Pa Isarog', 1.00, '', '', 0.00, 0.00, 63.50, '', '', '', NULL, NULL),
(389, '4217542', '9/18/15', 'Military Parade', 1.00, '', '', 0.00, 0.00, 64.50, '', '', '', NULL, NULL),
(390, '4217542', '12/30/15', 'Rizal Day *', 0.50, '', '', 0.00, 0.00, 65.00, '', '', '', NULL, NULL),
(391, '4217542', 'CY 2016', '', 0.00, '', '', 0.00, 0.00, 65.00, '', '', '', NULL, NULL),
(392, '4217542', 'APRIL-MAY 2016', 'PROPERTY CUSTODIAN', 15.00, '127, s. 17', '', 0.00, 0.00, 80.00, '', '', '', NULL, NULL),
(393, '4217542', '05-Sep', 'COMELEC/ELECTION', 5.00, '127, s. 17', '', 0.00, 0.00, 85.00, '', '', '', NULL, NULL),
(394, '4217542', '06-Dec', 'INDEPENDENCE DAY', 1.00, '245, s. 17', '', 0.00, 0.00, 86.00, '', '', '', NULL, NULL),
(395, '4217542', '6/25/16', 'TRNG ON Flag & Heraldic Code ', 1.00, '127, s. 17', '', 0.00, 0.00, 87.00, '', '', '', NULL, NULL),
(396, '4217542', '', 'of the Philippines', 0.00, '', '', 0.00, 0.00, 87.00, '', '', '', NULL, NULL),
(397, '4217542', '7/23/16', 'Div. Leadership Trng & Election', 2.00, '127, s. 17', '', 0.00, 0.00, 89.00, '', '', '', NULL, NULL),
(398, '4217542', '', 'of SPG/SSG Federation Ofcrs.', 0.00, '', '', 0.00, 0.00, 89.00, '', '', '', NULL, NULL),
(399, '4217542', '1/28/17', 'Early Registration', 1.00, '251, s. 17', '', 0.00, 0.00, 90.00, '', '', '', NULL, NULL),
(400, '4217542', '02-Nov', 'Araling Panlipunan Summit', 1.00, '127, s. 17', '', 0.00, 0.00, 91.00, '', '', '', NULL, NULL),
(401, '4217542', '02/04,18/2017', 'Palarong Bicol 2017', 2.00, '158, s. 17', '', 0.00, 0.00, 93.00, '', '', '', NULL, NULL),
(402, '4217542', '', '', 0.00, '', '9/18/17', 1.00, 0.00, 92.00, 'SL', '', '', NULL, NULL),
(403, '4217542', '', '', 0.00, '', '1/5,6/18', 2.00, 0.00, 90.00, 'SL', '', '', NULL, NULL),
(404, '4217542', '4/17-20,24,26/17,5/8-12,15-19,25-26,29/17 ', 'Designated as Property Custodian', 15.00, '147, s. 18', '', 0.00, 0.00, 105.00, '', '', '', NULL, NULL),
(405, '4217542', '5/29-31/17', 'mtot', 3.00, '52, s. 18', '', 0.00, 0.00, 108.00, '', '', '', NULL, NULL),
(406, '4217542', '5/26/17', 'EHRIS/EPDS', 1.00, '148, s. 18', '', 0.00, 0.00, 109.00, '', '', '', NULL, NULL),
(407, '4217542', '12/23/17', 'Dalagan Para sa Marawi', 1.00, '149, s. 18', '', 0.00, 0.00, 110.00, '', '', '', NULL, NULL),
(408, '4217542', '8/18/17', 'Jesse Robredo Day', 1.00, '150, s. 18', '', 0.00, 0.00, 111.00, '', '', '', NULL, NULL),
(409, '4217542', '5/15-20/117', 'Brigada Eskwela 2017', 2.00, '155, s. 18', '', 0.00, 0.00, 113.00, '', '', '', NULL, NULL),
(410, '4217542', '11/25/17', 'Salingoy Bicol & AP Div. Patiribayan', 1.00, '150, s. 18', '', 0.00, 0.00, 114.00, 'exceeds limit', '', '', NULL, NULL),
(411, '4217542', '1/4-6/17', 'Emergency Brigada', 1.50, '157, s. 18', '', 0.00, 0.00, 115.50, '', '', '', NULL, NULL),
(412, '4217542', 'CY 2018', '', 0.00, '', '', 0.00, 0.00, 115.50, '', '', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_11_19_073210_add_new_field_to_users', 2),
(5, '2024_11_21_052049_add_status', 3),
(6, '2024_11_24_103608_create_card_info_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('admin@gmail.com', '$2y$12$Pnvltbpsy6kXGgMxQnKdHekYw1ILQXzV6nLK4LgNuwQMZdPeqFB3e', '2024-11-18 20:02:36');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0am048k5rBEYA4dDV0lKqZmKVzuH2CY9dyoIvSvK', NULL, '100.51.194.111', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0 ;Build/0220;', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRkJLODFzSlh3UEJrUlpGazhmR3N5TDZEWjJDM0FMMkRWWTF6b2pWTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782563746),
('1sCkOA2iG36VyAAr6pjIKquNoSLd6enlTp4NYxDP', NULL, '168.100.11.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMTVhckNLelJFdUFlUHgxQ29lZ0ZLV2RwWHdCYTZDZnZtcHBWcUNRRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782534719),
('2xOBHc2a4GYha7v2QbCfZ7FrAmshzkDoKC84RoIv', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoia25pVnVxS2tha0FpcWZpUzBkOWN5eFExZ1FxQW95UDZCUWNTS2dxQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782621981),
('4bgsbeRSRwgGyfgtIKzQu9G75ljMUSkApIlyJAEv', NULL, '2a02:4780:11:1234::d5', 'Mozilla/5.0 (Linux; Android 11; moto g power (2022)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36 Chrome-Lighthouse', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRVU0YkZ6NWJ4RlZFVm5rN2V3Smh5TldjUzFIOHREZjFGUFRmb3h6QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782876642),
('5hmAPOif47BP9LASiCMZ092GuOwg26yoEjGXW5P3', NULL, '154.28.229.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXBVcjdKblk2Mmt1cmg0SGJpS0d1MDJDUWdFN0h1eWd4V0NvaWMybCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782531837),
('6lefhZxonmcIie2fiwpmqSreHq2SYGjdIW14C7b7', NULL, '103.4.250.165', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiam1wZ25TTTBrRU1jQnBrSnU4S2N5S3NOWkdrTjl0Y1pwQUtxcklLMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782531130),
('6mcBxmLAQBXyzGf3Hsn2IlEd7ConvkkguswLe9rl', NULL, '148.113.206.127', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZXFKQmt3NU1oc0pJTVZCamMyak0xS28wbVQ4czVrYzFJVXNwZEJxViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782684513),
('7DILuoGhOr48timeAi1ycPLA0DwyFTwoGojnFA0M', NULL, '91.196.152.154', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDU1ZThsQXpPSEtqU1NFMURIM2FHMjNFVEtrelBpTGZ4azJ6emtDayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782533296),
('879cXffZLUAOJw2qDfzvdAH0c9x3tAsZ2sudXEy7', NULL, '56.125.23.202', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQjFwR0F6d0RUakV0cmY2UUlSd2ZBeDFJejI4NWFGdHZUVzlJWmMzSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782652506),
('B2EPhSdXp5LCvBlyd25dqshIDMeXGwOpNOIJnQLv', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoicFhlV1RMRzRyWm0yNnJQZUJlUEh6YTgwamNOY3pHN3FTaFkyOXRDViI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782713744),
('BArc7sIwXqCPADiGIEOZtTtVEj8NlWLTQWc0PTsf', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieDBQMFYzU3VLTHA0ZmtXWkltcWxVZDRlN0hsNmRSSnVTM1ZNb2ZiTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3JlZ2lzdGVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782545573),
('cDYemebzxUPeBSPf7cHDKxqhbqrG0SUfN8hzNYUW', NULL, '202.78.167.208', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVGFLNE1tWnBEUlF5Vmdmd0UwVFJtcVJBNDBLZ08xaVBaT3dJNzFvMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782531079),
('cZto7CxWlvCOIF56ijWrVRRbZx7nkIKkLIYToQRx', NULL, '148.113.206.127', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNnNwdW1wRlNTM3RBd3NSV0xaODVQdkpnNExOYkFRQ0ZmZ2ppRmxUeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782684514),
('d0xwWlvHVbq5QJcTzA0aZO9WEvDscyeLiVF0QPs2', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiYXBhZ3FvUVl5YjY3MmFpbVRSMVRMR2FINXUxeUFrTDFPN3BMNjJSTCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782535400),
('E3pvH1ge7rWQOIFnn0WQ45CuvIUQm4O9PUk1uzlY', NULL, '103.196.9.147', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWVxak4zTTNhdUd3SUdIQ0VrTUdGYnRtQ3FwYmdZNkRzNFFWcXQ0cyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782533275),
('EUIKwvxqnHsWzKK9mveZu1j0idC2jCKeMyat7DEc', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZTY0d1VtSHk5eHFoVUhLYnFjTDdSYld4eGh2OHZGZXZqZkxST214MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782547319),
('ffdl7wGVGmHH55wPZQLZuwRu8jL0UC3d7od2nfxk', NULL, '168.100.11.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRkdRcWZRU1RDT0phOVdyNHNEMmpBRnNOZDY3VkEwWlJBa0ZvZldKNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782534718),
('fJjFLQEIti6ehW1sBcDMYsXdyhfz8JLPcAohKCjo', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXlJMkhwZFlmc2dORW1kc2JLb3NkWlN6ZzhwTGFMVERwU2dTS1FsTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vcmVnaXN0ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782546749),
('FVuwYSYbK3mCFVpxNSaZ4AqlpwFf0yUH8lxgdZVr', NULL, '2a03:b0c0:2:d0::1727:c001', 'Mozilla/5.0 (l9scan/2.0.43a353538383a373631313a303a363339313a31313a303837343a323031623; +https://leakix.net)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlZrZDVzOHhuTTBENzg3Qk1HdjlqQmE0UWlmdGt4b1hIOGdMWFVHdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782531136),
('fxEX4okOOLlTivziCsBDuyg7mXx8CZbrAivxYyUo', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieDFMQ0ZnajdkVkdGbjRXUVV3UXd1dEFFaHNoaFcydEdyazVSbUpRaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vcmVnaXN0ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782541038),
('G0cMP21ZxrZjyJvFnqbmLojoTChWp6PeYnRaTdj1', NULL, '148.113.206.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiV1RBWnpscGdGNE9VSDg4QUsyWHV0TjhYWjFBMzZ6S29KcGhSQ1gzWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782684329),
('gI1aUvN1tdk03AGkvUeMfbdeTq2DqX6cFPzIkkJt', NULL, '2a02:4780:59:5be4::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRE9OZVBYUGE0S0tnN25uS1A3aFBaUXNqcXVONWdHNnZISnk4Wk1jbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782876613),
('GWXwKx9B9XdRTXWucum5T4QzZZaJogqDRYEtDOU9', NULL, '142.93.231.173', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFJKVjlhOXFmak5USEUwZVhVSkx1dTU0Tkc0blh3TWxpYWxWenBLTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782674564),
('HKzIHbQXWGM7blzgMQhTktxJ0dVGfx1BVN7Mhx4R', NULL, '103.196.9.114', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoielBqd0hHWnVDek9qMk56czEzb1BHU2oxRjhJY3pJdG1tYjBYSzNRaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782531866),
('hM4195ePFjCAdW5McKttLfO0pmTQN3F8Ksc7mFNb', NULL, '100.51.194.111', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0 ;Build/0220;', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV0lFb1JXejRBV21ZeW9DbzQ1UktxN0ZEMFpBSzUxbG1DeWZ6OEl0ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782563746),
('HmckkGSWw8a5BFKo2sBos32u1964bpT4XYp0ERjM', NULL, '103.4.250.165', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoickVlNjVXdnZsWHVUTFRVSG5TNHUwZGIyQ2VJaEJBVU9teDhkRDVkYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782531117),
('HMmvuvDDy97zcGKgEKzHVqaoENLYi4xBfktEiKFh', NULL, '91.196.152.64', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieTZmblR1T1U0cGNzMUVyYWZWTWJJMG80MzVmVUVTWlZtZlVzalladyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782533025),
('HtddJuJel6hdwlt3gqyKrcEK2JgfWynpPuFEYaRI', NULL, '2a0a:4cc0:2000:8528:c4e8:3ff:febb:3ee9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEtXNmUyZWxLWVJkcnN3NGZGQWgxZk5iUkxGc1hMTlhqbU04dGYwNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782531252),
('Hv5dLqcUfKm7dGHOTut56bHPUvqEgyIHf9ab214Q', NULL, '104.252.191.83', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU05VR1ZJaDY2d2p0cXZteWo0dW5UaXJPRUNGVUxwalRkSUNORjZ0UiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782534523),
('IGb3yiyoxTz7goiPoosd8NL8AC1KVQ7ZQvInLmDw', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiRkE1REhRMlV6Y29CVUxFVVJaN3dvWEhSeU5PZUpZSmVhb013aDhVcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782787405),
('IgpEWPB80N6YghyZaBXXkK1DRv7FoJqCxiXcZXkz', NULL, '91.231.89.123', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidWJGVEZpbmZjYTVJMm5SNHNjeElsNlJmcXlOeXQ0Y2w2dlhDQTBYaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782532889),
('IxKu8X0FDxmIohpQJtUTLo6w1J5qJpmh91X99UwY', NULL, '103.196.9.114', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRjIxeTc3TkZQRGZJakVRajljS2FmNGhIclFJZ3JQd05hV1FqQTRDNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782531838),
('IYslQIwCETs4xCClL0jgh9IrAYjnVb04v1VYW868', NULL, '148.113.206.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiR3REZDNKUTZQYUl2VDl1NnpEVzJzeVZUekNmVmtjVWpsVDlIMEl6bSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782684330),
('JEGIWVs64kAu0pIRNvLeI0BhePPeHvPUCBn2Mv1u', NULL, '104.164.126.28', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM0dVSkdCRVhmV09OSFBhbG1YN2tLYnhsT0RvTHZqdWNTWlBpSFpDZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782542529),
('jKKZR61xj94hiY1ZrY67jLvI6BEHaINB5aWNqDBU', NULL, '34.170.234.211', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicWVSanhKaGpJS2wxam9vOVlmMXZTcG1TUXBDdk55TlFyS0RmOUxBcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782646873),
('JvFbjODqjsJ06G2rzFKz91sHP46mJ4OFGKEHdyyW', NULL, '91.196.152.149', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibEhXakRGZTRnaWc1aFFxOFVPczNPQkd1V0E3ak1wd0paeVpqQWthZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782533077),
('kJQPE6vjxG4rT4EDlsenIkNACiWg5vm8QL1B2j2g', NULL, '34.63.145.100', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSzBRa3dJSFNLUXM1RXF6aVVEM1N3SWFxVjJHeFFrSnlFQjJKaWtGNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782645397),
('KkIkdUeLIbbmHzvgc4Q2mOJKbGDAVEEKiomvf7uM', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMnNERmtwMUdDTVcwVUVFOFJiZXpibU9pZnBJcFFuVVdzeVJGOUtueSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3JlZ2lzdGVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782540891),
('klucmoH9v6g1ctdbFLMRJ8M0MqikGMUhqxCH9DH2', NULL, '35.231.98.252', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjEwWnBVTGlrZjNKSE1rWVZIdGQ4TkE0bGpxOHcyajhFV1Y5SVpPWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782658524),
('KsaCG3XXLQ6YAmzfzFW14Ehn9NICeWUBBX1G0W6J', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmNtVWJpMzM4Mno4UUE3UkdCTzJ4c1NvVjk2VzY2ejVaMldmdGlTRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782553430),
('mLsYNJvnPLsIlT5vKZlNEaz3yvDt6Wbk20wQvdrK', NULL, '136.66.21.30', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiemxzNjR5WVpVSk91RUV4MHJyVEtkRDZUaTR0cDUxT3FpMDU2eXRuQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782655521),
('MWT8nrNjtFMYXFHwTgeRVTyxoAoXAXt0KVXt9mgx', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkYyT2R2aDdMVmF3N0d3VmNpUGRpb0lCMFRRRHZBQ2RwVkRGbk01WCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782545897),
('n24gMh1agOM0xDgGneQ1ustfpcMviEeeb4XplDwt', NULL, '108.130.88.89', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZHRUYXZaWGFXaWJPY2tqckRSVlJ3cVJiVmtjV0R2YWVPRk5xdGVGQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782531558),
('NgEyFhuSnzYCswRuFuiCfFP9xXtlbNENKhlfc4uL', NULL, '2604:a880:cad:d0::db5:a001', 'Mozilla/5.0 (l9scan/2.0.43a353538383a373631313a303a363339313a31313a303837343a323031623; +https://leakix.net)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWENGNWdKVGRMcE1VU3JuaHRtaTVJclZ1UGduem9OTzNISkt5QTEzUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782531137),
('nuhRLfhs2sHL4Sky3c1gFQsofUl6jHJGac6n8Gm1', NULL, '103.196.9.147', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibWhMZ3hsOWJZdzlKOUViVWozNEZ2QUM3RE5GaGl5NkN5STVVTEMyMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782533240),
('OcXAhJlRSUFngdpbb10IdH9JAIDRw174KYp5Ou77', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYWRDQ3VYcWpLTkJMM2hoQWlmdEFxcEFCUElaSDBKMjhqMVp4eU44ayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782540113),
('oq8ekVylD9FEjWR7Uta887icBKsR8ibNkGsmlA1G', NULL, '47.237.24.105', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieEVXZmtmbDZZd1VlcU5CT1ZwT0xZekprUkFRejVicEZPODM0R2o0dSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782690246),
('pc9exCVabHWwrIcghGip52uLbBo3uq5Lia5gmPkS', NULL, '108.130.88.89', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRVM3TW5oMGJ5TVZvelBma2ZIUGJLeDlJOGdpQTlwSnZhRTIwQUd0SCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782531558),
('pm60nTCXfgWzhvHwuZ1Dktgay4zWLcY2rQgiRakz', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVGNxODRxOFpCS0FiZnAyZVRkY2N2OHlSdkhJSzhTdjBYbzFHQmc3TyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782540737),
('pNp515IXbPYjAxlFHlLi8ShMaGARDP9zm0sywfUl', NULL, '34.63.145.100', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY1hETkpKazVQb05xY2FwSVQ3RWlBanMzSDh5MUMxd0VUVFUwUjNIUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782645397),
('PtHwYef9A2AdVrocvQJb8GvRFcwt5oELznlk5fGM', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUVN6aHh0UjBmS1REZTlJV0dBdGx3cWt2V0k5TzVlMExvMkZzRFhUayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782546835),
('qLbxwzXUY4F8VwoMEfhHoU3kvP0tNd5IC6mhwcfC', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieFVLeHN0aHJwblhSZ3dzb1lZS3BZMTJvYmVid0RxQUp6Sm1nM1V6NCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782541220),
('QVRcbdvToE3vDvOgqMoPT8fWdC1cpSutvDZtnR5s', NULL, '104.252.191.83', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaTVDcGs3aFMwUkdSbW9ZM25UazRNaUREa0xCNGNBd2paanFkZFphMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782534503),
('RB0Y9xXcOGA5LBZPridyZgTEfgqJgENxWkvfIfTz', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXp5MlhKT25JYzhzM285ZkhYZHQ5OGYwdmhRWFh6UW1QN3dKRWFBNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782535779),
('RDr0fhezICCTz8t8OcdTUSeeGjM1lXKNuGT4uVYC', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXF2UTFxRTlQZldNYkNRSmxwV212bGE0UkpsTld4ckJlbkw3bjRQeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782547214),
('RocxQ5q2gF80Qp4TDERPpo2M5UucTnb3oxyVG20C', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiek8zdEc0aFh3Z01lbzV2aHVsM3FDRkxrN3d5Tjd2dDVHeFlobGhBVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782621982),
('sJUbbIjT1A3ueO5f2UUosQ72YpZFNCCz7zyNg0EA', NULL, '202.78.167.208', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXpxWkRyYlY3enV6YUFteG5nZTkxQTh5bWRuZTZ5aHIyTmszM3BPUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782531079),
('TlSm2WjAFzLgOCQJcW9xc6N3bCtz24iHcew0QtC2', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiRFc3RXRXTklFUWRlYUZuYU1CVUxGMnhKMDZ3cGRTdzFFMkxvR1gzZiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782535400),
('tPHHmmvAT9GKVj6EKMApmBWuWTwALP0aoAewvYi6', NULL, '165.232.69.140', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidTBoTno3MW5hZ1ZoWE1heXZoU3RJUkxOa1FZOHhad3YzZE94ckREWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782847304),
('UOzxmfvuV2HpSAuyhHIOF29T0pc2PqcEx0NZPDdq', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEFmTklXTFRDU2VXa0lKc2VOcmxmd3JyTWFyd1FaOVRFRmhPSzJ0byI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782541161),
('UU9xGNtkpbtGGUqH3Gn97PK6z5EoDsdCbrVVRGcB', NULL, '104.164.173.154', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlRqbkdMM0JzYVB2MElyVFA4bU9DVTByYlV1VzRYcmdqbHBET3czQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782534504),
('VW1TGkVHy0SzlzwLYCxFxwPjQyTxWIOdRylqGgEc', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoibkVENjNLSjNtdWxkNkQ0RllJakNxbEpQUHkxNlAyeml4VE8xZWQxeCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782787405),
('w7d4bqNnpWN2jjjR3ULPKJHZ6t7MDTcpFlho2MCV', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1c0anUyMTFKUHh1QlBnSld0UWFMVnZQdTl1bUU2RTlLT1R3MHNoVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782535640),
('w9ZFfrtQtZw8agjM9wGMt9435eUkUcZxlJ8IpPAa', NULL, '146.190.142.148', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMzd1eVF4ZEVvUmhwNzBQY3JpbjFic1dxSFpVUFBkTDRPVlRwVjZJViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782587206),
('WMslXiNAjWVu5epI1qX2VuB5oubuDP96yFEooAb0', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1lPT2VvSXR5WTJSRVI4VXFuRlJGQUhEUzdIUkM1eHJJdFhiMVF2QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782551198),
('XBKbsQ16x6UlVCMBwHLmTTvdHcVccTuRL7BDQPLy', NULL, '91.231.89.123', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXJBdUVYeUIxQVNtb0hXM1daQVBzR2dyVkREM2luckZIVUJNWGl2TCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782532823),
('xl2jxwzW5tDyrGoGt7fmjklTTQ94Qm6HwAmAfJlU', NULL, '136.66.21.30', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1pRUWxNVDRPbHNXSzU4aGRQWWZxWEV5NTdWQzU0NlpQVE5peVFqUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782655520),
('XqtVxzTtHmGY9TdiYHoANzsQOoCc7Rj4o7K9buw2', NULL, '34.170.234.211', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSEpQTzFTa29ySDJGdjhNT3A5UFBPcTBwejlqZTVpSGJEekE4alJVaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782646873),
('y3jdBNyQ3u69TKtEwnFfXDEyP1wUSHrWSD24G0Jc', NULL, '216.73.216.104', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiajVQZ0xtY2ZQeHBrMThZVGR0M21LNWZEcU1IVWRaMDBLRTRKWGlmNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782553142),
('yprFU7wokN9AQCnLYJSi57UOypyBxdsyYAnQnKM8', NULL, '64.227.136.91', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVdZc2xIY0M1eGpENzI0dGprSGlocno0YllCaFpGSlFtaUtuUE12VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LmNhcmQuZWR1bGVhdmUuY29tL3dlbGNvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1782759866),
('yTeM3eyhc729Xk5wBx9bIKDYmU0gzvgVWNZCaVHN', NULL, '2a02:4780:11:c0de::e', 'Go-http-client/2.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiWHR6b3pPSlJNTmpnQzZwQWhwVEtJUlRhMUlkaThWN0tvQ3NzNGp3UCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1782713744),
('zJEKjNCVqVSPxyXPU4WdwJpjcyYKe3LObu5neTNE', NULL, '104.164.126.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNXFWSDVGdXVjYmxtSEJRajlzZDhBR1cyc2c0UTJCbG5kTEg4SlpkRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782531117),
('ZzAZrMhbekBcdqp8iq6LSyGyuyZIywfItEdcwj5m', NULL, '35.231.98.252', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQXN0ODdrZlJlS0V3NmJ1b2tlVkNTU241VG9XMmF0Q2RkcWR5S3BDYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vY2FyZC5lZHVsZWF2ZS5jb20vd2VsY29tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1782658525);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `usertype` varchar(255) NOT NULL DEFAULT 'user',
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `date_employed` date DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `employee_number` varchar(255) NOT NULL,
  `station` varchar(255) DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `usertype`, `phone`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `position`, `date_employed`, `sex`, `date_of_birth`, `place_of_birth`, `employee_number`, `station`, `civil_status`, `status`) VALUES
(2, 'admin', 'admin@gmail.com', 'admin', '09566422783', NULL, '$2y$12$WaaBlmU9cxNsp.xqm2wtC.pYM2N4J9klIE1by6Q0M1WNQFejK4Pp6', NULL, '2024-10-18 06:51:16', '2024-10-18 06:51:16', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, 'active'),
(4, 'MARCAIDA, RODJINNA RIZI F.', 'user@gmail.com', 'user', '1234567890', '2024-12-12 14:23:02', '$2y$12$tqGE1yBcSWTDXynTneVEC.bozdAEm5joW37U5XT7JpPx47Pw1pcTK', 'AFjmB2Oh3I1EYnaNpoaUozbAiqbYSddadWg78EqaWunODtMa1Qfa5nBODYd2', '2024-11-19 04:31:42', '2024-12-12 12:42:43', 'Teacher I', '0000-00-00', 'Female', '2024-11-19', 'Camaligan', '4540408', 'Leon Q. Mercado HS (508)', 'Married', 'active'),
(28, 'BENITO, ALDRIEN L.', 'user1@gmail.com', 'user', '1234567890', '2024-12-10 00:35:23', '$2y$12$0VGMt6tvXe43XgrMMjQ..OIT2k4qdn.uK/EFRIPJeiiQsy6oZixjK', NULL, '2024-12-10 00:34:53', '2024-12-10 00:35:59', 'Teacher I', '2024-12-10', 'Male', '1997-06-25', 'Naga City', '4637684', 'Leon Q. Mercado HS (508)', 'Married', 'active'),
(31, 'DALAODAO, EDWIN', 'user2@gmail.com', 'user', '1234567890', '2024-12-12 00:47:01', '$2y$12$aI9VCxYag87usxR6vyocCOoLWYjPS7Qirh8vedsSD3znuADcE6xW.', NULL, '2024-12-12 00:46:41', '2024-12-12 00:49:07', 'Teacher III', '2008-01-03', 'Male', '1981-02-08', 'Naga City', '4217542', 'Leon Q. Mercado HS (508)', 'Single', 'active'),
(37, 'ABAY, ROGER JR.', 'mr.niko0719@gmail.com', 'user', '09566422783', '2024-12-12 17:28:31', '$2y$12$yF0q4tEirW5sPOM7s84BNu0yMQHi0gC8qvL2KioUHDNHnoxmGLIfy', NULL, '2024-12-12 17:27:22', '2024-12-12 17:29:32', 'TECHNICAL ASSISTANT 1', '2024-12-13', 'Male', '2024-12-13', 'VILLAREY', '4563211', 'CENTRAL', 'SINGLE', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `card_info`
--
ALTER TABLE `card_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `card_info`
--
ALTER TABLE `card_info`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=416;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
