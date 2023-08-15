-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2023 at 07:10 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cricket_scorecard`
--

-- --------------------------------------------------------

--
-- Table structure for table `cricket_matches`
--

CREATE TABLE `cricket_matches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_a_id` int(11) NOT NULL,
  `team_b_id` int(11) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `format` varchar(255) NOT NULL,
  `over` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cricket_matches`
--

INSERT INTO `cricket_matches` (`id`, `team_a_id`, `team_b_id`, `venue`, `format`, `over`, `time`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Mirpur', 'T10', 10, '2023-07-16 10:30:00', 'finished', '2023-08-15 10:54:10', '2023-08-15 11:07:25'),
(2, 3, 4, 'Colombo', 'T10', 10, '2023-08-15 22:50:00', 'ongoing', '2023-08-15 10:54:46', '2023-08-15 10:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `innings`
--

CREATE TABLE `innings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `match_id` int(11) NOT NULL,
  `battingTeam_id` int(11) NOT NULL,
  `bowlingTeam_id` int(11) NOT NULL,
  `innings` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `innings`
--

INSERT INTO `innings` (`id`, `match_id`, `battingTeam_id`, `bowlingTeam_id`, `innings`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, '1', '2', '2023-08-15 10:58:13', '2023-08-15 11:04:03'),
(2, 1, 2, 1, '2', '2', '2023-08-15 10:58:13', '2023-08-15 11:07:25');

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2023_04_01_085308_create_teams_table', 1),
(4, '2023_04_01_085316_create_players_table', 1),
(5, '2023_04_01_095106_create_venues_table', 1),
(6, '2023_04_02_143603_create_cricket_matches_table', 1),
(7, '2023_04_03_053259_create_scores_table', 1),
(8, '2023_04_04_175014_create_squads_table', 1),
(9, '2023_04_05_070704_create_innings_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `team_id` int(11) NOT NULL,
  `role` enum('Batting AllRounder','Bowling AllRounder','WK Batsman','Batsman','Bowler') NOT NULL,
  `batting_style` enum('Right handed','Left handed') NOT NULL,
  `bowling_style` enum('Right arm pace','Left arm pace','Left arm spin','Right arm spin','N/A') NOT NULL,
  `born` datetime NOT NULL,
  `biography` text NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `name`, `image`, `team_id`, `role`, `batting_style`, `bowling_style`, `born`, `biography`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Litton Das', NULL, 1, 'WK Batsman', 'Right handed', 'N/A', '1990-02-28 00:00:00', 'player', 1, '2023-08-15 10:17:13', '2023-08-15 10:17:13'),
(2, 'Tamim Iqbal', NULL, 1, 'Batsman', 'Left handed', 'N/A', '2023-08-30 00:00:00', 'Tamim Iqbal', 1, '2023-08-15 10:17:50', '2023-08-15 10:17:50'),
(3, 'Rony Talukdar', NULL, 1, 'Batsman', 'Right handed', 'N/A', '2023-08-16 00:00:00', 'Rony Talukdar', 0, '2023-08-15 10:18:21', '2023-08-15 10:47:53'),
(4, 'Shakib Al Hasan', NULL, 1, 'Batting AllRounder', 'Left handed', 'Left arm spin', '2023-08-01 00:00:00', 'Shakib Al Hasan', 1, '2023-08-15 10:19:13', '2023-08-15 10:19:13'),
(5, 'Mushfiqur Rahim', NULL, 1, 'WK Batsman', 'Right handed', 'N/A', '2023-08-30 00:00:00', 'Mushfiqur Rahim', 1, '2023-08-15 10:20:25', '2023-08-15 10:20:25'),
(6, 'Mahmudullah', NULL, 1, 'Batting AllRounder', 'Right handed', 'Right arm spin', '2023-08-01 00:00:00', 'Mahmudullah', 0, '2023-08-15 10:21:07', '2023-08-15 10:51:31'),
(7, 'Mehidy Hasan Miraz', NULL, 1, 'Bowling AllRounder', 'Right handed', 'Right arm spin', '2023-08-01 00:00:00', 'Mehidy Hasan Miraz', 1, '2023-08-15 10:21:33', '2023-08-15 10:21:33'),
(8, 'Towhid Hridoy', NULL, 1, 'Batsman', 'Right handed', 'N/A', '2023-08-30 00:00:00', 'Towhid Hridoy', 1, '2023-08-15 10:22:04', '2023-08-15 10:22:04'),
(9, 'Ebadot Hossain', NULL, 1, 'Bowler', 'Right handed', 'Right arm pace', '2023-08-01 00:00:00', 'Ebadot Hossain', 1, '2023-08-15 10:23:04', '2023-08-15 10:23:04'),
(10, 'Nasum Ahmed', NULL, 1, 'Bowler', 'Left handed', 'Left arm spin', '2023-08-16 00:00:00', 'Nasum Ahmed', 1, '2023-08-15 10:23:36', '2023-08-15 10:23:36'),
(11, 'Taijul Islam', NULL, 1, 'Bowler', 'Left handed', 'Left arm spin', '2023-08-10 00:00:00', 'Taijul Islam', 1, '2023-08-15 10:26:08', '2023-08-15 10:26:08'),
(12, 'Taskin Ahmed', NULL, 1, 'Bowler', 'Left handed', 'Right arm pace', '2023-08-16 00:00:00', 'Taskin Ahmed', 1, '2023-08-15 10:26:41', '2023-08-15 10:26:41'),
(13, 'Shoriful Islam', NULL, 1, 'Bowler', 'Right handed', 'Right arm pace', '2023-08-11 00:00:00', 'Shoriful Islam', 1, '2023-08-15 10:27:14', '2023-08-15 10:27:14'),
(14, 'Mustafizur Rahman', NULL, 1, 'Bowler', 'Left handed', 'Left arm pace', '2023-08-11 00:00:00', 'Mustafizur Rahman', 1, '2023-08-15 10:27:51', '2023-08-15 10:27:51'),
(15, 'Mohammad Naim', NULL, 1, 'Batsman', 'Left handed', 'N/A', '2023-08-22 00:00:00', 'Mohammad Naim', 1, '2023-08-15 10:28:22', '2023-08-15 10:28:22'),
(16, 'Najmul Hossain Shanto', NULL, 1, 'Batsman', 'Left handed', 'N/A', '2023-08-16 00:00:00', 'Najmul Hossain Shanto', 1, '2023-08-15 10:28:48', '2023-08-15 10:28:48'),
(17, 'Yasir Ali', NULL, 1, 'Batsman', 'Right handed', 'N/A', '2023-08-16 00:00:00', 'Yasir Ali', 1, '2023-08-15 10:29:15', '2023-08-15 10:29:15'),
(18, 'Dhawan, S', NULL, 2, 'Batsman', 'Left handed', 'N/A', '2023-08-12 00:00:00', 'Dhawan, S', 1, '2023-08-15 10:30:28', '2023-08-15 10:30:28'),
(19, 'Sharma, RG', NULL, 2, 'Batsman', 'Right handed', 'N/A', '2023-08-16 00:00:00', 'Sharma, RG', 1, '2023-08-15 10:31:02', '2023-08-15 10:31:02'),
(20, 'Shubman Gill', NULL, 2, 'Batsman', 'Right handed', 'N/A', '2023-08-09 00:00:00', 'Shubman Gill', 1, '2023-08-15 10:31:34', '2023-08-15 10:31:34'),
(21, 'Rahul, KL', NULL, 2, 'WK Batsman', 'Right handed', 'N/A', '2023-08-18 00:00:00', 'Rahul, KL', 1, '2023-08-15 10:31:52', '2023-08-15 10:31:52'),
(22, 'Ishan Kishan', NULL, 2, 'WK Batsman', 'Right handed', 'N/A', '2023-08-17 00:00:00', 'Ishan Kishan', 1, '2023-08-15 10:32:16', '2023-08-15 10:32:16'),
(23, 'Gaikwad, RD', NULL, 2, 'Batsman', 'Right handed', 'N/A', '2023-08-16 00:00:00', 'Gaikwad, RD', 1, '2023-08-15 10:32:40', '2023-08-15 10:32:40'),
(24, 'Iyer, SS', NULL, 2, 'Batsman', 'Right handed', 'N/A', '2023-08-16 00:00:00', 'Iyer, SS', 1, '2023-08-15 10:33:04', '2023-08-15 10:33:04'),
(25, 'Kohli, V', NULL, 2, 'Batting AllRounder', 'Right handed', 'Right arm pace', '2023-08-17 00:00:00', 'Kohli, V', 1, '2023-08-15 10:33:23', '2023-08-15 10:33:23'),
(26, 'Pant, RR', NULL, 2, 'WK Batsman', 'Right handed', 'N/A', '2023-08-17 00:00:00', 'Pant, RR', 1, '2023-08-15 10:33:47', '2023-08-15 10:33:47'),
(27, 'Patel, AR', NULL, 2, 'Bowling AllRounder', 'Right handed', 'Right arm spin', '2023-08-18 00:00:00', 'Patel, AR', 1, '2023-08-15 10:34:10', '2023-08-15 10:34:10'),
(28, 'Samson, SV', NULL, 2, 'WK Batsman', 'Right handed', 'N/A', '2023-08-17 00:00:00', 'Samson, SV', 1, '2023-08-15 10:34:34', '2023-08-15 10:34:34'),
(29, 'Jadeja, RA', NULL, 2, 'Batting AllRounder', 'Left handed', 'Left arm spin', '2023-08-10 00:00:00', 'Jadeja, RA', 1, '2023-08-15 10:35:01', '2023-08-15 10:35:01'),
(30, 'Chahal, YS', NULL, 2, 'Bowler', 'Right handed', 'Right arm spin', '2023-08-17 00:00:00', 'Chahal, YS', 1, '2023-08-15 10:35:24', '2023-08-15 10:35:24'),
(31, 'Pandya, HH', NULL, 2, 'Bowling AllRounder', 'Right handed', 'Right arm pace', '2023-08-10 00:00:00', 'Pandya, HH', 1, '2023-08-15 10:35:48', '2023-08-15 10:35:48'),
(32, 'Umran Malik', NULL, 2, 'Bowler', 'Right handed', 'Right arm pace', '2023-08-10 00:00:00', 'Umran Malik', 1, '2023-08-15 10:36:14', '2023-08-15 10:36:14'),
(33, 'Babar Azam', NULL, 3, 'Batsman', 'Right handed', 'N/A', '2023-08-05 00:00:00', 'Babar Azam', 1, '2023-08-15 10:36:53', '2023-08-15 10:36:53'),
(34, 'Imam-ul-Haq', NULL, 3, 'WK Batsman', 'Right handed', 'N/A', '2023-08-09 00:00:00', 'Imam-ul-Haq', 1, '2023-08-15 10:37:22', '2023-08-15 10:37:22'),
(35, 'Mohammad Rizwan', NULL, 3, 'WK Batsman', 'Right handed', 'N/A', '2023-08-11 00:00:00', 'Mohammad Rizwan', 1, '2023-08-15 10:37:46', '2023-08-15 10:37:46'),
(36, 'Shadab Khan', NULL, 3, 'Batsman', 'Right handed', 'N/A', '2023-08-16 00:00:00', 'Shadab Khan', 1, '2023-08-15 10:38:06', '2023-08-15 10:38:06'),
(37, 'Fakhar Zaman', NULL, 3, 'Batting AllRounder', 'Right handed', 'Right arm pace', '2023-08-16 00:00:00', 'Fakhar Zaman', 1, '2023-08-15 10:38:28', '2023-08-15 10:38:28'),
(38, 'Haris Rauf', NULL, 3, 'Batting AllRounder', 'Right handed', 'Left arm pace', '2023-08-17 00:00:00', 'Haris Rauf', 1, '2023-08-15 10:38:51', '2023-08-15 10:38:51'),
(39, 'Mohammad Nawaz', NULL, 3, 'WK Batsman', 'Right handed', 'Left arm pace', '2023-08-17 00:00:00', 'Mohammad Nawaz', 1, '2023-08-15 10:39:08', '2023-08-15 10:39:08'),
(40, 'Mohammad Wasim', NULL, 3, 'Batting AllRounder', 'Right handed', 'Right arm pace', '2023-08-11 00:00:00', 'Mohammad Wasim', 1, '2023-08-15 10:39:29', '2023-08-15 10:39:29'),
(41, 'Usama Mir', NULL, 3, 'Bowling AllRounder', 'Right handed', 'Left arm pace', '2023-08-05 00:00:00', 'Usama Mir', 1, '2023-08-15 10:39:47', '2023-08-15 10:39:47'),
(42, 'Kamran Ghulam', NULL, 3, 'WK Batsman', 'Left handed', 'Right arm pace', '2023-08-11 00:00:00', 'Kamran Ghulam', 1, '2023-08-15 10:40:12', '2023-08-15 10:40:12'),
(43, 'Khushdil Shah', NULL, 3, 'Bowling AllRounder', 'Right handed', 'Right arm pace', '2023-08-16 00:00:00', 'Khushdil Shah', 1, '2023-08-15 10:40:26', '2023-08-15 10:40:26'),
(44, 'de Silva, PWH', NULL, 4, 'Batsman', 'Left handed', 'Left arm pace', '2023-08-30 00:00:00', 'de Silva, PWH', 1, '2023-08-15 10:40:54', '2023-08-15 10:40:54'),
(45, 'Fernando, MNK', NULL, 4, 'Bowling AllRounder', 'Left handed', 'Left arm pace', '2023-08-24 00:00:00', 'Fernando, MNK', 1, '2023-08-15 10:41:09', '2023-08-15 10:41:09'),
(46, 'Hematha, MADI', NULL, 4, 'Bowler', 'Left handed', 'Left arm pace', '2023-08-11 00:00:00', 'Hematha, MADI', 1, '2023-08-15 10:41:28', '2023-08-15 10:41:28'),
(47, 'Kumara, CBRLS', NULL, 4, 'Bowling AllRounder', 'Left handed', 'Left arm pace', '2023-08-12 00:00:00', 'Kumara, CBRLS', 1, '2023-08-15 10:41:47', '2023-08-15 10:41:47'),
(48, 'Madushanka, D', NULL, 4, 'Batting AllRounder', 'Right handed', 'Right arm pace', '2023-08-08 00:00:00', 'Madushanka, D', 1, '2023-08-15 10:42:02', '2023-08-15 10:42:02'),
(49, 'Asalanka, KIC', NULL, 4, 'Bowling AllRounder', 'Left handed', 'Left arm pace', '2023-08-24 00:00:00', 'Asalanka, KIC', 1, '2023-08-15 10:42:34', '2023-08-15 10:42:34'),
(50, 'Chameera, PVD', NULL, 4, 'Bowling AllRounder', 'Right handed', 'Left arm pace', '2023-09-06 00:00:00', 'Chameera, PVD', 1, '2023-08-15 10:42:49', '2023-08-15 10:42:49'),
(51, 'de Silva, DM', NULL, 4, 'WK Batsman', 'Right handed', 'Right arm pace', '2023-08-23 00:00:00', 'de Silva, DM', 1, '2023-08-15 10:43:15', '2023-08-15 10:43:15'),
(52, 'Fernando, WIA', NULL, 4, 'Bowling AllRounder', 'Right handed', 'Right arm pace', '2023-08-12 00:00:00', 'Fernando, WIA', 1, '2023-08-15 10:43:39', '2023-08-15 10:43:39'),
(53, 'Karunaratne, C', NULL, 4, 'Batting AllRounder', 'Left handed', 'Right arm pace', '2023-08-17 00:00:00', 'Karunaratne, C', 1, '2023-08-15 10:43:55', '2023-08-15 10:43:55'),
(54, 'Mendis, BKG', NULL, 4, 'Bowling AllRounder', 'Right handed', 'Right arm pace', '2023-08-23 00:00:00', 'Mendis, BKG', 1, '2023-08-15 10:44:14', '2023-08-15 10:44:14');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `match_id` int(11) NOT NULL,
  `score_line` varchar(255) NOT NULL,
  `run` int(11) NOT NULL,
  `ball` int(11) NOT NULL,
  `batsman_id` int(11) NOT NULL,
  `bowler_id` int(11) NOT NULL,
  `battingTeam_id` int(11) NOT NULL,
  `bowlingTeam_id` int(11) NOT NULL,
  `wicket` int(11) NOT NULL,
  `extra` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id`, `match_id`, `score_line`, `run`, `ball`, `batsman_id`, `bowler_id`, `battingTeam_id`, `bowlingTeam_id`, `wicket`, `extra`, `created_at`, `updated_at`) VALUES
(1, 1, '4', 4, 1, 1, 32, 1, 2, 0, '0', '2023-08-15 10:59:04', '2023-08-15 10:59:04'),
(2, 1, 'NB2', 0, 0, 1, 32, 1, 2, 0, 'NB2', '2023-08-15 10:59:20', '2023-08-15 10:59:20'),
(3, 1, 'WD1', 0, 0, 1, 32, 1, 2, 0, 'WD1', '2023-08-15 10:59:36', '2023-08-15 10:59:36'),
(4, 1, 'B2', 0, 1, 1, 32, 1, 2, 0, 'B2', '2023-08-15 10:59:43', '2023-08-15 10:59:43'),
(5, 1, 'LB1', 0, 1, 1, 32, 1, 2, 0, 'LB1', '2023-08-15 10:59:53', '2023-08-15 10:59:53'),
(6, 1, 'W', 0, 1, 1, 32, 1, 2, 1, '0', '2023-08-15 11:00:05', '2023-08-15 11:00:05'),
(7, 1, '3', 3, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:30', '2023-08-15 11:00:30'),
(8, 1, '3', 3, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:32', '2023-08-15 11:00:32'),
(9, 1, '2', 2, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:33', '2023-08-15 11:00:33'),
(10, 1, '4', 4, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:35', '2023-08-15 11:00:35'),
(11, 1, '5', 5, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:37', '2023-08-15 11:00:37'),
(12, 1, '6', 6, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:38', '2023-08-15 11:00:38'),
(13, 1, '5', 5, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:39', '2023-08-15 11:00:39'),
(14, 1, '6', 6, 1, 2, 32, 1, 2, 0, '0', '2023-08-15 11:00:40', '2023-08-15 11:00:40'),
(15, 1, '2', 2, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:00:47', '2023-08-15 11:00:47'),
(16, 1, '4', 4, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:00:49', '2023-08-15 11:00:49'),
(17, 1, '4', 4, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:00:50', '2023-08-15 11:00:50'),
(18, 1, '5', 5, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:00:52', '2023-08-15 11:00:52'),
(19, 1, '3', 3, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:00:53', '2023-08-15 11:00:53'),
(20, 1, '2', 2, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:00:54', '2023-08-15 11:00:54'),
(21, 1, '6', 6, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:00:56', '2023-08-15 11:00:56'),
(22, 1, 'NB2', 0, 0, 2, 31, 1, 2, 0, 'NB2', '2023-08-15 11:00:57', '2023-08-15 11:00:57'),
(23, 1, 'WD2', 0, 0, 2, 31, 1, 2, 0, 'WD2', '2023-08-15 11:00:59', '2023-08-15 11:00:59'),
(24, 1, 'B1', 0, 1, 2, 31, 1, 2, 0, 'B1', '2023-08-15 11:00:59', '2023-08-15 11:00:59'),
(25, 1, 'LB1', 0, 1, 2, 31, 1, 2, 0, 'LB1', '2023-08-15 11:01:01', '2023-08-15 11:01:01'),
(26, 1, '1', 1, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:01:03', '2023-08-15 11:01:03'),
(27, 1, '2', 2, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:01:04', '2023-08-15 11:01:04'),
(28, 1, '3', 3, 1, 2, 31, 1, 2, 0, '0', '2023-08-15 11:01:05', '2023-08-15 11:01:05'),
(29, 1, 'W', 0, 1, 2, 29, 1, 2, 1, '0', '2023-08-15 11:01:45', '2023-08-15 11:01:45'),
(30, 1, 'W', 0, 1, 4, 29, 1, 2, 1, '0', '2023-08-15 11:01:51', '2023-08-15 11:01:51'),
(31, 1, '4', 4, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:01:55', '2023-08-15 11:01:55'),
(32, 1, '5', 5, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:01:56', '2023-08-15 11:01:56'),
(33, 1, '4', 4, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:01:57', '2023-08-15 11:01:57'),
(34, 1, '3', 3, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:01:58', '2023-08-15 11:01:58'),
(35, 1, '4', 4, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:01:59', '2023-08-15 11:01:59'),
(36, 1, '5', 5, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:00', '2023-08-15 11:02:00'),
(37, 1, '3', 3, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:01', '2023-08-15 11:02:01'),
(38, 1, '0', 0, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:03', '2023-08-15 11:02:03'),
(39, 1, '1', 1, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:04', '2023-08-15 11:02:04'),
(40, 1, '1', 1, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:05', '2023-08-15 11:02:05'),
(41, 1, '2', 2, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:15', '2023-08-15 11:02:15'),
(42, 1, '2', 2, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:16', '2023-08-15 11:02:16'),
(43, 1, '2', 2, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:17', '2023-08-15 11:02:17'),
(44, 1, '2', 2, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:19', '2023-08-15 11:02:19'),
(45, 1, '1', 1, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:20', '2023-08-15 11:02:20'),
(46, 1, '4', 4, 1, 5, 29, 1, 2, 0, '0', '2023-08-15 11:02:22', '2023-08-15 11:02:22'),
(47, 1, '5', 5, 1, 5, 31, 1, 2, 0, '0', '2023-08-15 11:02:40', '2023-08-15 11:02:40'),
(48, 1, '2', 2, 1, 5, 31, 1, 2, 0, '0', '2023-08-15 11:02:41', '2023-08-15 11:02:41'),
(49, 1, '0', 0, 1, 5, 31, 1, 2, 0, '0', '2023-08-15 11:02:42', '2023-08-15 11:02:42'),
(50, 1, '3', 3, 1, 5, 31, 1, 2, 0, '0', '2023-08-15 11:02:45', '2023-08-15 11:02:45'),
(51, 1, '4', 4, 1, 5, 31, 1, 2, 0, '0', '2023-08-15 11:02:47', '2023-08-15 11:02:47'),
(52, 1, '5', 5, 1, 5, 31, 1, 2, 0, '0', '2023-08-15 11:02:48', '2023-08-15 11:02:48'),
(53, 1, '1', 1, 1, 5, 25, 1, 2, 0, '0', '2023-08-15 11:02:57', '2023-08-15 11:02:57'),
(54, 1, '3', 3, 1, 5, 25, 1, 2, 0, '0', '2023-08-15 11:03:00', '2023-08-15 11:03:00'),
(55, 1, '4', 4, 1, 5, 25, 1, 2, 0, '0', '2023-08-15 11:03:01', '2023-08-15 11:03:01'),
(56, 1, '5', 5, 1, 5, 25, 1, 2, 0, '0', '2023-08-15 11:03:02', '2023-08-15 11:03:02'),
(57, 1, '1', 1, 1, 5, 25, 1, 2, 0, '0', '2023-08-15 11:03:03', '2023-08-15 11:03:03'),
(58, 1, '6', 6, 1, 5, 25, 1, 2, 0, '0', '2023-08-15 11:03:05', '2023-08-15 11:03:05'),
(59, 1, 'W', 0, 1, 5, 32, 1, 2, 1, '0', '2023-08-15 11:03:35', '2023-08-15 11:03:35'),
(60, 1, '4', 4, 1, 7, 32, 1, 2, 0, '0', '2023-08-15 11:03:42', '2023-08-15 11:03:42'),
(61, 1, '6', 6, 1, 7, 32, 1, 2, 0, '0', '2023-08-15 11:03:43', '2023-08-15 11:03:43'),
(62, 1, '3', 3, 1, 7, 32, 1, 2, 0, '0', '2023-08-15 11:03:45', '2023-08-15 11:03:45'),
(63, 1, '2', 2, 1, 7, 32, 1, 2, 0, '0', '2023-08-15 11:03:46', '2023-08-15 11:03:46'),
(64, 1, '1', 1, 1, 7, 32, 1, 2, 0, '0', '2023-08-15 11:03:47', '2023-08-15 11:03:47'),
(65, 1, '3', 3, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:12', '2023-08-15 11:05:12'),
(66, 1, '3', 3, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:14', '2023-08-15 11:05:14'),
(67, 1, '4', 4, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:15', '2023-08-15 11:05:15'),
(68, 1, '3', 3, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:15', '2023-08-15 11:05:15'),
(69, 1, '2', 2, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:16', '2023-08-15 11:05:16'),
(70, 1, '3', 3, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:17', '2023-08-15 11:05:17'),
(71, 1, '4', 4, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:17', '2023-08-15 11:05:17'),
(72, 1, '5', 5, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:18', '2023-08-15 11:05:18'),
(73, 1, '2', 2, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:19', '2023-08-15 11:05:19'),
(74, 1, '3', 3, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:22', '2023-08-15 11:05:22'),
(75, 1, '2', 2, 1, 18, 14, 2, 1, 0, '0', '2023-08-15 11:05:23', '2023-08-15 11:05:23'),
(76, 1, 'W', 0, 1, 18, 14, 2, 1, 1, '0', '2023-08-15 11:05:25', '2023-08-15 11:05:25'),
(77, 1, '2', 2, 1, 19, 14, 2, 1, 0, '0', '2023-08-15 11:05:29', '2023-08-15 11:05:29'),
(78, 1, '4', 4, 1, 19, 14, 2, 1, 0, '0', '2023-08-15 11:05:31', '2023-08-15 11:05:31'),
(79, 1, '2', 2, 1, 19, 14, 2, 1, 0, '0', '2023-08-15 11:05:32', '2023-08-15 11:05:32'),
(80, 1, '3', 3, 1, 19, 14, 2, 1, 0, '0', '2023-08-15 11:05:34', '2023-08-15 11:05:34'),
(81, 1, '4', 4, 1, 19, 14, 2, 1, 0, '0', '2023-08-15 11:05:35', '2023-08-15 11:05:35'),
(82, 1, '0', 0, 1, 19, 14, 2, 1, 0, '0', '2023-08-15 11:05:36', '2023-08-15 11:05:36'),
(83, 1, '1', 1, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:41', '2023-08-15 11:05:41'),
(84, 1, '2', 2, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:42', '2023-08-15 11:05:42'),
(85, 1, '4', 4, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:43', '2023-08-15 11:05:43'),
(86, 1, '5', 5, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:45', '2023-08-15 11:05:45'),
(87, 1, '6', 6, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:46', '2023-08-15 11:05:46'),
(88, 1, '6', 6, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:47', '2023-08-15 11:05:47'),
(89, 1, '6', 6, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:48', '2023-08-15 11:05:48'),
(90, 1, '4', 4, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:49', '2023-08-15 11:05:49'),
(91, 1, '2', 2, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:51', '2023-08-15 11:05:51'),
(92, 1, '3', 3, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:52', '2023-08-15 11:05:52'),
(93, 1, '4', 4, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:54', '2023-08-15 11:05:54'),
(94, 1, '3', 3, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:55', '2023-08-15 11:05:55'),
(95, 1, '2', 2, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:58', '2023-08-15 11:05:58'),
(96, 1, '1', 1, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:05:59', '2023-08-15 11:05:59'),
(97, 1, '2', 2, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:06:03', '2023-08-15 11:06:03'),
(98, 1, '1', 1, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:06:04', '2023-08-15 11:06:04'),
(99, 1, '0', 0, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:06:05', '2023-08-15 11:06:05'),
(100, 1, '1', 1, 1, 19, 13, 2, 1, 0, '0', '2023-08-15 11:06:06', '2023-08-15 11:06:06'),
(101, 1, 'W', 0, 1, 19, 12, 2, 1, 1, '0', '2023-08-15 11:06:13', '2023-08-15 11:06:13'),
(102, 1, '1', 1, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:20', '2023-08-15 11:06:20'),
(103, 1, '3', 3, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:21', '2023-08-15 11:06:21'),
(104, 1, '2', 2, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:22', '2023-08-15 11:06:22'),
(105, 1, '1', 1, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:23', '2023-08-15 11:06:23'),
(106, 1, '2', 2, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:25', '2023-08-15 11:06:25'),
(107, 1, '3', 3, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:27', '2023-08-15 11:06:27'),
(108, 1, '2', 2, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:28', '2023-08-15 11:06:28'),
(109, 1, '1', 1, 1, 20, 12, 2, 1, 0, '0', '2023-08-15 11:06:28', '2023-08-15 11:06:28'),
(110, 1, 'W', 0, 1, 20, 12, 2, 1, 1, '0', '2023-08-15 11:06:30', '2023-08-15 11:06:30'),
(111, 1, '2', 2, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:40', '2023-08-15 11:06:40'),
(112, 1, '2', 2, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:43', '2023-08-15 11:06:43'),
(113, 1, '1', 1, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:45', '2023-08-15 11:06:45'),
(114, 1, '0', 0, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:46', '2023-08-15 11:06:46'),
(115, 1, '2', 2, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:47', '2023-08-15 11:06:47'),
(116, 1, '3', 3, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:48', '2023-08-15 11:06:48'),
(117, 1, '2', 2, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:49', '2023-08-15 11:06:49'),
(118, 1, '1', 1, 1, 21, 12, 2, 1, 0, '0', '2023-08-15 11:06:51', '2023-08-15 11:06:51'),
(119, 1, 'W', 0, 1, 21, 9, 2, 1, 1, '0', '2023-08-15 11:07:01', '2023-08-15 11:07:01'),
(120, 1, '3', 3, 1, 22, 9, 2, 1, 0, '0', '2023-08-15 11:07:05', '2023-08-15 11:07:05'),
(121, 1, '2', 2, 1, 22, 9, 2, 1, 0, '0', '2023-08-15 11:07:07', '2023-08-15 11:07:07'),
(122, 1, 'NB2', 0, 0, 22, 9, 2, 1, 0, 'NB2', '2023-08-15 11:07:09', '2023-08-15 11:07:09'),
(123, 1, '3', 3, 1, 22, 9, 2, 1, 0, '0', '2023-08-15 11:07:11', '2023-08-15 11:07:11'),
(124, 1, '2', 2, 1, 22, 9, 2, 1, 0, '0', '2023-08-15 11:07:13', '2023-08-15 11:07:13'),
(125, 1, '1', 1, 1, 22, 9, 2, 1, 0, '0', '2023-08-15 11:07:14', '2023-08-15 11:07:14');

-- --------------------------------------------------------

--
-- Table structure for table `squads`
--

CREATE TABLE `squads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `player_name` varchar(255) NOT NULL,
  `team_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `squads`
--

INSERT INTO `squads` (`id`, `match_id`, `player_id`, `player_name`, `team_id`, `team_name`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Litton Das', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(2, 1, 2, 'Tamim Iqbal', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(3, 1, 4, 'Shakib Al Hasan', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(4, 1, 5, 'Mushfiqur Rahim', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(5, 1, 7, 'Mehidy Hasan Miraz', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(6, 1, 8, 'Towhid Hridoy', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(7, 1, 9, 'Ebadot Hossain', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(8, 1, 10, 'Nasum Ahmed', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(9, 1, 12, 'Taskin Ahmed', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(10, 1, 13, 'Shoriful Islam', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(11, 1, 14, 'Mustafizur Rahman', 1, 'Bangladesh', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(12, 1, 18, 'Dhawan, S', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(13, 1, 19, 'Sharma, RG', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(14, 1, 20, 'Shubman Gill', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(15, 1, 21, 'Rahul, KL', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(16, 1, 22, 'Ishan Kishan', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(17, 1, 25, 'Kohli, V', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(18, 1, 26, 'Pant, RR', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(19, 1, 27, 'Patel, AR', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(20, 1, 29, 'Jadeja, RA', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(21, 1, 31, 'Pandya, HH', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13'),
(22, 1, 32, 'Umran Malik', 2, 'India', '2023-08-15 10:58:13', '2023-08-15 10:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `head_coach` varchar(255) DEFAULT NULL,
  `home_venue_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `head_coach`, `home_venue_id`, `created_at`, `updated_at`) VALUES
(1, 'Bangladesh', 'Chandika Hathurusingha', '1', '2023-08-15 10:12:34', '2023-08-15 10:12:34'),
(2, 'India', 'Rahul Dravid', '2', '2023-08-15 10:12:44', '2023-08-15 10:12:44'),
(3, 'Pakistan', 'Saqlain Mushtaq', '3', '2023-08-15 10:14:16', '2023-08-15 10:14:16'),
(4, 'Srilanka', 'Chris Silverwood', '4', '2023-08-15 10:14:40', '2023-08-15 10:14:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Kaushik Biswas', 'admin@gmail.com', '$2a$12$/s.K5A06oXRrppq7OJxZSe/G7eWH.OHzTU0a16gT3YbP8gObwlReC', 'admin', 'active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `location`, `capacity`, `created_at`, `updated_at`) VALUES
(1, 'Mirpur', 'Dhaka, Mirpur', 25000, '2023-08-15 10:11:45', '2023-08-15 10:11:45'),
(2, 'Mubmai', 'Mumbai. India', 60000, '2023-08-15 10:11:58', '2023-08-15 10:11:58'),
(3, 'Karachi', 'Karachi, Pakistan', 45000, '2023-08-15 10:13:03', '2023-08-15 10:13:03'),
(4, 'Colombo', 'Colombo, Srilanka', 30000, '2023-08-15 10:13:25', '2023-08-15 10:13:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cricket_matches`
--
ALTER TABLE `cricket_matches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `innings`
--
ALTER TABLE `innings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `squads`
--
ALTER TABLE `squads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cricket_matches`
--
ALTER TABLE `cricket_matches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `innings`
--
ALTER TABLE `innings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `squads`
--
ALTER TABLE `squads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
