-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2026 at 09:07 AM
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
-- Database: `shoprental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `username`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Primary Admin', 'k14058.piyumika@gmail.com', 'k14058.piyumika@gmail.com', '$2y$12$UCrZe4G7wRl.IDXu1IEYTO/6gSXRrUykkUa1aW6dHyETXzOSaV5lC', NULL, '2026-02-04 00:46:34', '2026-02-04 00:46:34');

-- --------------------------------------------------------

--
-- Table structure for table `bus_stands`
--

CREATE TABLE `bus_stands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bus_stands`
--

INSERT INTO `bus_stands` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Padiyapelella', '2026-01-03 21:46:59', '2026-01-03 21:46:59'),
(2, 'Rikillagaskada', NULL, NULL),
(3, 'Delthota', NULL, NULL),
(4, 'Weligalla', NULL, NULL),
(5, 'Haragama', NULL, NULL),
(6, 'Kadugannawa', NULL, NULL),
(7, 'Waththegama', NULL, NULL),
(8, 'Hettipola', NULL, NULL),
(9, 'Naula', NULL, NULL),
(10, 'Raagala', NULL, NULL),
(11, 'Aaaaaaaaa', NULL, NULL),
(12, 'Pusallawa', NULL, NULL),
(13, 'Bbbbbbbb', NULL, NULL),
(14, 'Polgolla', NULL, NULL),
(15, 'Bogambara', NULL, NULL);

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
(4, '2026_01_03_000000_add_phone_and_position_to_users_table', 2),
(5, '2026_01_04_000000_create_shops_table', 3),
(6, '2026_01_04_000001_add_shop_name_to_shops_table', 3),
(7, '2026_01_04_000003_create_bus_stands_table', 3),
(8, '2026_01_04_000004_add_agreement_dates_to_shops_table', 4),
(9, '2026_01_04_010000_add_management_fields_to_shops_table', 4),
(10, '2026_01_04_020000_add_bus_stand_id_to_shops_table', 5),
(11, '2026_01_04_030000_set_shop_name_default', 6),
(12, '2026_01_09_000000_add_rate_to_shops_table', 7),
(13, '2026_01_24_000001_create_payments_table', 8),
(14, '2026_01_27_000000_create_payment_slips_table', 8),
(15, '2026_01_27_010000_add_shop_monthly_columns_and_media', 8),
(16, '2026_01_27_020000_add_owner_contact_and_addresses_to_shops', 8),
(17, '2026_02_04_000000_create_admins_table', 8),
(18, '2026_02_04_100000_recreate_admins_table', 9),
(19, '2026_02_08_000000_add_shop_owner_nic_to_shops', 10);

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `interest` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `shop_id`, `amount`, `interest`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 4, 12000.00, 1200.00, '2026-02-01 18:30:00', '2026-02-01 22:59:05', '2026-02-01 22:59:05'),
(2, 4, 5000.00, 0.00, '2026-02-01 18:30:00', '2026-02-01 23:00:58', '2026-02-01 23:00:58');

-- --------------------------------------------------------

--
-- Table structure for table `payment_slips`
--

CREATE TABLE `payment_slips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
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
('foghfDEIcx7BSFLWO2X5jftHTGjfHzJjaVkgtVLH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM2dxdElwNjFyWUJPbXhHbnJic3gyaDhCeDJXNWQ4OGpZYW1tY2doeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wYXltZW50cyI7czo1OiJyb3V0ZSI7czoxNDoicGF5bWVudHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1770537943);

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` int(10) UNSIGNED NOT NULL,
  `shop_number` varchar(20) NOT NULL,
  `shop_name` varchar(150) NOT NULL DEFAULT '',
  `shop_owner_name` varchar(150) NOT NULL,
  `shop_owner_nic` varchar(50) DEFAULT NULL,
  `shop_owner_phone` varchar(40) DEFAULT NULL,
  `shop_owner_address` varchar(255) DEFAULT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `advance_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `opening_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saving` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_payment` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_interest` decimal(12,2) NOT NULL DEFAULT 0.00,
  `year_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `year_interest` decimal(12,2) NOT NULL DEFAULT 0.00,
  `year_total_taken` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deduct_payment` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deduct_interest` decimal(12,2) NOT NULL DEFAULT 0.00,
  `agreement_duration` varchar(50) DEFAULT NULL,
  `bus_stand_id` int(10) UNSIGNED DEFAULT NULL,
  `total_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `shop_address` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `record_year` year(4) NOT NULL,
  `rent_january` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `agreement_start` date DEFAULT NULL,
  `agreement_end` date DEFAULT NULL,
  `rent_february` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_march` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_april` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_may` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_june` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_july` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_august` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_september` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_october` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_november` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rent_december` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`id`, `shop_number`, `shop_name`, `shop_owner_name`, `shop_owner_nic`, `shop_owner_phone`, `shop_owner_address`, `monthly_rent`, `rate`, `advance_payment`, `opening_balance`, `saving`, `due_payment`, `due_interest`, `year_charge`, `year_interest`, `year_total_taken`, `deduct_payment`, `deduct_interest`, `agreement_duration`, `bus_stand_id`, `total_paid`, `total_due`, `balance_amount`, `remarks`, `location`, `shop_address`, `image_path`, `record_year`, `rent_january`, `created_at`, `updated_at`, `agreement_start`, `agreement_end`, `rent_february`, `rent_march`, `rent_april`, `rent_may`, `rent_june`, `rent_july`, `rent_august`, `rent_september`, `rent_october`, `rent_november`, `rent_december`) VALUES
(1, '1', '', 'ssssssssss', NULL, NULL, NULL, 300.00, 0.00, 0.00, 0.00, 5222.00, 10.00, 1.00, 44.00, 4.00, 0.00, 0.00, 0.00, NULL, 3, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026', 0.00, '2026-01-04 03:16:48', '2026-01-04 03:16:48', NULL, '2026-01-04', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(4, '2', '', 'oshan', NULL, NULL, NULL, 300.00, 0.00, 0.00, 0.00, 150.00, 0.00, 0.00, 3600.00, 180.00, 4130.00, 0.00, 0.00, NULL, 6, 18200.00, 0.00, 0.00, NULL, NULL, NULL, NULL, '2026', 0.00, '2026-01-05 21:41:07', '2026-02-01 23:00:58', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, 'NL-001', 'Naula', 'amila perera', '200068602140', '+94766720869', 'andawala\r\nMadawala', 15000.00, 5.00, 500000.00, 50000.00, 5000.00, 10000.00, 200.00, 180000.00, 9000.00, 189000.00, 0.00, 0.00, NULL, 9, 0.00, 0.00, 0.00, NULL, 'Naula Town', 'Jaffna to kandy Road- Naula Town central bus stand', NULL, '2026', 0.00, '2026-02-08 00:05:58', '2026-02-08 00:12:56', '2026-02-01', '2027-12-30', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `position`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'piyumika dharmakeerthi', 'piumikad678@gmail.com', '0766720869', 'Manager', NULL, '$2y$12$khaRFaoGPtT4QLAwqDCtyey4wDasN/S35DAyeA4J7B4sI3pu4FBRu', NULL, '2026-01-03 10:02:02', '2026-01-03 10:02:02'),
(6, 'Rizna swati', 'dulanjalidarmakirthi@gmail.com', '+94766873456', 'Staff', NULL, '$2y$12$5rtQETs6M1f7mfB5kS2mAeCfNLX5smAXxD40D7u86t3KlhOUUqXXu', NULL, '2026-02-03 03:47:22', '2026-02-03 03:47:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_username_unique` (`username`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `bus_stands`
--
ALTER TABLE `bus_stands`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_slips`
--
ALTER TABLE `payment_slips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_slips_payment_id_foreign` (`payment_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shops_bus_stand_id_index` (`bus_stand_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bus_stands`
--
ALTER TABLE `bus_stands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_slips`
--
ALTER TABLE `payment_slips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payment_slips`
--
ALTER TABLE `payment_slips`
  ADD CONSTRAINT `payment_slips_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
