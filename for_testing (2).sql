-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 21, 2025 at 09:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `for_testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `astrologers`
--

CREATE TABLE `astrologers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`languages`)),
  `specialization` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specialization`)),
  `experience` int(11) DEFAULT NULL,
  `per_minute_rate` decimal(8,2) DEFAULT NULL,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bio` text DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`availability`)),
  `weekly_availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`weekly_availability`)),
  `today_availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`today_availability`)),
  `is_available_now` tinyint(1) NOT NULL DEFAULT 0,
  `timezone` varchar(255) NOT NULL DEFAULT 'Asia/Kolkata',
  `is_online` tinyint(1) NOT NULL DEFAULT 1,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','rejected','inactive') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `astrologers`
--

INSERT INTO `astrologers` (`id`, `user_id`, `languages`, `specialization`, `experience`, `per_minute_rate`, `wallet_balance`, `bio`, `education`, `certifications`, `availability`, `weekly_availability`, `today_availability`, `is_available_now`, `timezone`, `is_online`, `last_seen_at`, `status`, `approved_at`, `approved_by`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 3, '[\"hindi\",\"english\"]', '[\"vedic\",\"western\",\"numerology\",\"tarot\"]', 3, 10.00, 0.00, NULL, 'Education', 'Certifications', NULL, NULL, NULL, 0, 'Asia/Kolkata', 1, NULL, 'approved', '2025-08-21 11:51:24', 2, NULL, '2025-08-21 11:50:23', '2025-08-21 11:51:24');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `astrologer_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','active','completed','cancelled','insufficient_balance') NOT NULL DEFAULT 'pending',
  `ended_by` enum('user','astrologer') DEFAULT NULL,
  `per_minute_rate` decimal(8,2) NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `last_deduction_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `astrologer_id`, `status`, `ended_by`, `per_minute_rate`, `started_at`, `ended_at`, `duration_minutes`, `total_amount`, `last_deduction_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, 3, 'completed', 'user', 10.00, '2025-08-21 12:51:01', '2025-08-21 12:52:15', 1, 22.42, NULL, 'dfdfdf', '2025-08-21 12:50:33', '2025-08-21 12:52:15'),
(2, 4, 3, 'completed', 'user', 10.00, '2025-08-21 13:08:50', '2025-08-21 13:09:36', 0, 17.70, NULL, 'dfdfd', '2025-08-21 13:08:43', '2025-08-21 13:09:36'),
(3, 4, 3, 'completed', 'user', 10.00, '2025-08-21 13:16:20', '2025-08-21 13:17:17', 0, 19.57, NULL, 'dfdf', '2025-08-21 13:16:05', '2025-08-21 13:17:17'),
(4, 4, 3, 'completed', 'astrologer', 10.00, '2025-08-21 13:29:59', '2025-08-21 13:30:37', 0, 16.43, NULL, 'dfdfd', '2025-08-21 13:29:44', '2025-08-21 13:30:37'),
(5, 4, 3, 'active', NULL, 10.00, '2025-08-21 13:46:31', NULL, 0, 10.00, NULL, 'jbjbjb', '2025-08-21 13:46:24', '2025-08-21 13:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `booking_id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 4, 'Hii', '2025-08-21 12:51:20', 1, '2025-08-21 12:51:20', '2025-08-21 12:51:56'),
(2, 1, 4, 3, 'Hii', '2025-08-21 12:51:37', 1, '2025-08-21 12:51:37', '2025-08-21 12:51:48'),
(3, 1, 4, 3, 'Hii', '2025-08-21 12:52:01', 1, '2025-08-21 12:52:01', '2025-08-21 12:52:09'),
(4, 2, 4, 3, 'Hii', '2025-08-21 13:09:11', 1, '2025-08-21 13:09:11', '2025-08-21 13:09:30'),
(5, 3, 4, 3, 'Hiii', '2025-08-21 13:16:44', 0, '2025-08-21 13:16:44', '2025-08-21 13:16:44'),
(6, 3, 3, 4, 'Hiiii', '2025-08-21 13:16:59', 0, '2025-08-21 13:16:59', '2025-08-21 13:16:59'),
(7, 4, 3, 4, 'Hiii', '2025-08-21 13:30:18', 0, '2025-08-21 13:30:18', '2025-08-21 13:30:18'),
(8, 4, 4, 3, 'Hiii', '2025-08-21 13:30:28', 1, '2025-08-21 13:30:28', '2025-08-21 13:30:34'),
(9, 5, 3, 4, 'hiii', '2025-08-21 13:46:47', 1, '2025-08-21 13:46:47', '2025-08-21 13:46:56'),
(10, 5, 4, 3, 'hiii', '2025-08-21 13:47:00', 0, '2025-08-21 13:47:00', '2025-08-21 13:47:00'),
(11, 5, 3, 4, 'Hiii', '2025-08-21 13:47:17', 1, '2025-08-21 13:47:17', '2025-08-21 13:47:50'),
(12, 5, 4, 3, 'sfsfs', '2025-08-21 13:47:35', 0, '2025-08-21 13:47:35', '2025-08-21 13:47:35'),
(13, 5, 3, 4, 'sfsfsfsf', '2025-08-21 13:47:40', 1, '2025-08-21 13:47:40', '2025-08-21 13:47:50');

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

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(8, 'default', '{\"uuid\":\"bb4212e1-a491-4aa9-8366-429b8f59da59\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:2:{i:0;s:6:\\\"sender\\\";i:1;s:8:\\\"receiver\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755802818,\"delay\":null}', 0, NULL, 1755802818, 1755802818),
(9, 'default', '{\"uuid\":\"3651f3a4-1ba1-47fd-9080-157503ba1e31\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:8;s:9:\\\"relations\\\";a:2:{i:0;s:6:\\\"sender\\\";i:1;s:8:\\\"receiver\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755802828,\"delay\":null}', 0, NULL, 1755802828, 1755802828),
(10, 'default', '{\"uuid\":\"2f9fd11c-8e01-46bd-ac4f-5cd1cecf4a5e\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:2:{i:0;s:6:\\\"sender\\\";i:1;s:8:\\\"receiver\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755803807,\"delay\":null}', 0, NULL, 1755803807, 1755803807),
(11, 'default', '{\"uuid\":\"e2384cf7-f7a3-4851-870a-b912f32d3129\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:2:{i:0;s:6:\\\"sender\\\";i:1;s:8:\\\"receiver\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755803820,\"delay\":null}', 0, NULL, 1755803820, 1755803820),
(12, 'default', '{\"uuid\":\"2a64b7ff-c929-4a24-ac13-77ceedd70ec3\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:2:{i:0;s:6:\\\"sender\\\";i:1;s:8:\\\"receiver\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755803837,\"delay\":null}', 0, NULL, 1755803837, 1755803837),
(13, 'default', '{\"uuid\":\"dd9fe60c-e49f-494c-a9a2-dcdacdc45fe3\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:12;s:9:\\\"relations\\\";a:2:{i:0;s:6:\\\"sender\\\";i:1;s:8:\\\"receiver\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755803856,\"delay\":null}', 0, NULL, 1755803856, 1755803856),
(14, 'default', '{\"uuid\":\"2144b704-86fb-47e9-9f86-1b58e6810849\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":14:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\ChatMessage\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:2:{i:0;s:6:\\\"sender\\\";i:1;s:8:\\\"receiver\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755803860,\"delay\":null}', 0, NULL, 1755803860, 1755803860);

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
(4, '2025_08_19_065835_create_personal_access_tokens_table', 1),
(5, '2025_08_19_065842_add_additional_fields_to_users_table', 1),
(6, '2025_08_19_073219_add_role_to_users_table', 1),
(7, '2025_08_19_073317_create_astrologers_table', 1),
(8, '2025_08_19_101556_create_bookings_table', 1),
(9, '2025_08_19_101738_add_wallet_balance_to_users_table', 1),
(10, '2025_08_19_102122_create_wallet_transactions_table', 1),
(11, '2025_08_19_122210_create_chat_messages_table', 1),
(12, '2025_08_19_173910_create_reviews_table', 1),
(13, '2025_08_19_173936_add_profile_fields_to_astrologers_table', 1),
(14, '2025_08_19_173956_add_phone_to_users_table', 1),
(15, '2025_08_20_102137_add_approval_status_to_astrologers_table', 1),
(16, '2025_08_21_085810_add_ended_by_to_bookings_table', 1),
(17, '2025_08_21_190250_add_detailed_availability_to_astrologers_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `astrologer_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('42w3Haadot3KvIjyBqBpgXALAbz42OmNkefF4jo0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUTRrb1ZqbFFvWlN2RUJxQnB1THU1TGJlME40ZEF3N2E3Z3NEMVJJTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755795631),
('MGLzxGJYtGyS4VVkE2H95LAyKcwq38cQKK2FAoHx', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMmVCbkM5NnZpczYyQVZ1Y1E5bm9ZYkljb2ZVUzVkSVY2ZG9SWHV1QyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGF0LzUvbWVzc2FnZXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1755803870),
('MSmEGJRuHPRiKG1yB2VEcG3kHa4MVHCTN0FhMw2b', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMlU5UUJURFE4a0puZG90QkZxQnRwbTJxb0s3UnZFNHpTOEVvV1NHaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGF0LzUvbWVzc2FnZXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1755803860),
('nNuZ52WNZ4g1U2uQ4yJ7VBm4eISzvJngv5u73TvK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFF3NkxDbHo1bkJIZUxUcGVFTEQxcGV0WklQMFVpd0s3TVkydzk5aCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1755795686),
('qwmtQqKfed4ntERdH4K9aDeYGs55bfa951vyLXcx', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZEdDbWpMZ1pTMFpGSjNpWkt0a050S3QxcW5FbENqazQ0bDhzMXpJdyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9hc3Ryb2xvZ2VycyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1755803184);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','astrologer','admin') NOT NULL DEFAULT 'user',
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `role`, `wallet_balance`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', NULL, '2025-08-21 08:54:22', '$2y$12$hBC4w.nBxj3MJToQL/6v0OvAMPSgTRYJ2TEYtqrNAaCvTPh7MJyfC', 'user', 0.00, 'mfncJskiG7', '2025-08-21 08:54:22', '2025-08-21 08:54:22'),
(2, 'Admin User', 'admin@test.com', NULL, NULL, '$2y$12$icUKpsE3iM1qJV0bLJFf7uB7KBK.6r8.pnvDKZzL/wYCXfvGYqJZa', 'admin', 0.00, NULL, '2025-08-21 08:54:23', '2025-08-21 08:54:23'),
(3, 'rahul saha', 'rahulastro@gmail.com', NULL, NULL, '$2y$12$tLCQHKY9JSGLM4k4mYpwheSFOJ/STGi0Rskbapbf9vq8nRz4gXF/i', 'astrologer', 0.00, NULL, '2025-08-21 11:50:23', '2025-08-21 11:50:23'),
(4, 'SK Nadim', 'sknadim6297@gmail.com', NULL, NULL, '$2y$12$.X7CFTIg2y0H3vuP3miWEOjYamwpmIZm741.SncQUTQaODCiylPxS', 'user', 13.88, NULL, '2025-08-21 12:49:58', '2025-08-21 13:46:24');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `transaction_type` enum('top_up','booking_deduction','refund','admin_adjustment') NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `user_id`, `type`, `amount`, `balance_after`, `description`, `transaction_type`, `booking_id`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 4, 'credit', 100.00, 100.00, 'Wallet top-up', 'top_up', NULL, NULL, '2025-08-21 12:50:22', '2025-08-21 12:50:22'),
(2, 4, 'debit', 10.00, 90.00, 'Booking fee for session with rahul saha', 'booking_deduction', 1, NULL, '2025-08-21 12:50:33', '2025-08-21 12:50:33'),
(3, 4, 'debit', 12.42, 77.58, 'Session charges (1.2415695666667 min) with Unknown Astrologer', 'booking_deduction', 1, NULL, '2025-08-21 12:52:15', '2025-08-21 12:52:15'),
(4, 4, 'debit', 10.00, 67.58, 'Booking fee for session with rahul saha', 'booking_deduction', 2, NULL, '2025-08-21 13:08:43', '2025-08-21 13:08:43'),
(5, 4, 'debit', 7.70, 59.88, 'Session charges (0.76983621666667 min) with Unknown Astrologer', 'booking_deduction', 2, NULL, '2025-08-21 13:09:36', '2025-08-21 13:09:36'),
(6, 4, 'debit', 10.00, 49.88, 'Booking fee for session with rahul saha', 'booking_deduction', 3, NULL, '2025-08-21 13:16:05', '2025-08-21 13:16:05'),
(7, 4, 'debit', 9.57, 40.31, 'Session charges (0.95684856666667 min) with Unknown Astrologer', 'booking_deduction', 3, NULL, '2025-08-21 13:17:17', '2025-08-21 13:17:17'),
(8, 4, 'debit', 10.00, 30.31, 'Booking fee for session with rahul saha', 'booking_deduction', 4, NULL, '2025-08-21 13:29:44', '2025-08-21 13:29:44'),
(9, 4, 'debit', 6.43, 23.88, 'Session charges (0.6433237 min) with Unknown Astrologer', 'booking_deduction', 4, NULL, '2025-08-21 13:30:37', '2025-08-21 13:30:37'),
(10, 4, 'debit', 10.00, 13.88, 'Booking fee for session with rahul saha', 'booking_deduction', 5, NULL, '2025-08-21 13:46:24', '2025-08-21 13:46:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `astrologers`
--
ALTER TABLE `astrologers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `astrologers_user_id_foreign` (`user_id`),
  ADD KEY `astrologers_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_status_started_at_index` (`status`,`started_at`),
  ADD KEY `bookings_user_id_status_index` (`user_id`,`status`),
  ADD KEY `bookings_astrologer_id_status_index` (`astrologer_id`,`status`);

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
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_receiver_id_foreign` (`receiver_id`),
  ADD KEY `chat_messages_booking_id_sent_at_index` (`booking_id`,`sent_at`),
  ADD KEY `chat_messages_sender_id_receiver_id_index` (`sender_id`,`receiver_id`);

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
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_booking_id_foreign` (`booking_id`),
  ADD KEY `reviews_astrologer_id_rating_index` (`astrologer_id`,`rating`),
  ADD KEY `reviews_user_id_created_at_index` (`user_id`,`created_at`);

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
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_transactions_booking_id_foreign` (`booking_id`),
  ADD KEY `wallet_transactions_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `wallet_transactions_type_created_at_index` (`type`,`created_at`),
  ADD KEY `wallet_transactions_transaction_type_index` (`transaction_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `astrologers`
--
ALTER TABLE `astrologers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `astrologers`
--
ALTER TABLE `astrologers`
  ADD CONSTRAINT `astrologers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `astrologers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_astrologer_id_foreign` FOREIGN KEY (`astrologer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_astrologer_id_foreign` FOREIGN KEY (`astrologer_id`) REFERENCES `astrologers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `wallet_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
