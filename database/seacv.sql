-- ========================================================
-- Database Export for SEACV
-- Suitable for Shared Hosting / InfinityFree phpMyAdmin
-- Note: Do NOT include CREATE DATABASE or USE statements
-- Generated on: 2026-09-05 07:29:37
-- ========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for `admins`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admins`
INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
('1', 'Fahrul', '$2y$10$DFr/jV6dDkxvxCgx59RNu.tUpPZRo.ujNCvLyvxkzc3n6okE526OO', '2026-09-05 07:28:21');

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `categories`
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
('1', 'CV Kreatif', '2026-09-05 12:19:35'),
('2', 'CV ATS', '2026-09-05 12:19:35'),
('3', 'Surat Lamaran Kerja', '2026-09-05 12:19:35');

-- --------------------------------------------------------
-- Table structure for `products`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `products`
INSERT INTO `products` (`id`, `name`, `price`, `category`, `image`, `created_at`) VALUES
('9', 'CV ATS Friendly Professional #684', '30000.00', 'CV ATS', 'uploads/684f4a03703c2_ats07.webp', '2026-09-04 15:47:30'),
('10', 'Surat Lamaran Kerja PUEBI #684', '15000.00', 'Surat Lamaran Kerja', 'uploads/684f5971833a0_slk04.webp', '2026-09-04 15:47:30'),
('11', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a08bd4a6fc_cvkreatif01.webp', '2026-09-04 15:47:30'),
('12', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a0a749fb53_cvkreatif02.webp', '2026-09-04 15:47:30'),
('13', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a0b1784112_cvkreatif03.webp', '2026-09-04 15:47:30'),
('14', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a1170923a0_cvkreatif04.webp', '2026-09-04 15:47:30'),
('15', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a1ae50dc99_cvkreatif05.webp', '2026-09-04 15:47:30'),
('16', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a1af1cc60c_cvkreatif06.webp', '2026-09-04 15:47:30'),
('17', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a488dbbf43_cvkreatif07.webp', '2026-09-04 15:47:30'),
('18', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a4e1e96171_cvkreatif08.webp', '2026-09-04 15:47:30'),
('19', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685a5279f2724_cvkreatif09.webp', '2026-09-04 15:47:30'),
('20', 'CV Kreatif Modern Style #685', '25000.00', 'CV Kreatif', 'uploads/685be8b639263_cvkreatif10.webp', '2026-09-04 15:47:30'),
('21', 'Surat Lamaran Kerja PUEBI #685', '15000.00', 'Surat Lamaran Kerja', 'uploads/685bec7e0c301_Surat Lamaran Kerja 01.webp', '2026-09-04 15:47:30'),
('22', 'Surat Lamaran Kerja PUEBI #686', '15000.00', 'Surat Lamaran Kerja', 'uploads/686b12ea2c0b1_Surat Lamaran 02.webp', '2026-09-04 15:47:30'),
('23', 'Surat Lamaran Kerja PUEBI #686', '15000.00', 'Surat Lamaran Kerja', 'uploads/686b1501f0223_Surat Lamaran 03.webp', '2026-09-04 15:47:30'),
('24', 'CV Kreatif Modern Style #686', '25000.00', 'CV Kreatif', 'uploads/686b227b4fa44_cvkreatif11.webp', '2026-09-04 15:47:30'),
('25', 'CV Kreatif Modern Style #686', '25000.00', 'CV Kreatif', 'uploads/686b228727121_cvkreatif12.webp', '2026-09-04 15:47:30'),
('26', 'CV Kreatif Modern Style #686', '25000.00', 'CV Kreatif', 'uploads/686b257fcb1ba_cvkreatif13.webp', '2026-09-04 15:47:30'),
('27', 'CV Kreatif Modern Style #68722006', '25000.00', 'CV Kreatif', 'uploads/68722006b3982_cvkreatif14.webp', '2026-09-04 15:47:30'),
('28', 'CV Kreatif Modern Style #688363', '25000.00', 'CV Kreatif', 'uploads/688363d4af930_cvkreatif15.webp', '2026-09-04 15:47:30'),
('29', 'CV Kreatif Modern Style #688365', '25000.00', 'CV Kreatif', 'uploads/688365a5654af_cvkreatif16.webp', '2026-09-04 15:47:30'),
('30', 'CV Kreatif Modern Style #6883673', '25000.00', 'CV Kreatif', 'uploads/6883673bd1d04_cvkreatif17.webp', '2026-09-04 15:47:30'),
('31', 'CV Kreatif Modern Style #6883696241515', '25000.00', 'CV Kreatif', 'uploads/6883696241515_cvkreatif18.webp', '2026-09-04 15:47:30'),
('32', 'CV Kreatif Modern Style #68836', '25000.00', 'CV Kreatif', 'uploads/68836d7c99dbf_cvkreatif19.webp', '2026-09-04 15:47:30'),
('33', 'CV Kreatif Modern Style #6883763', '25000.00', 'CV Kreatif', 'uploads/6883763c64ff7_cvkreatif20.webp', '2026-09-04 15:47:30'),
('34', 'Surat Lamaran Kerja PUEBI #68', '15000.00', 'Surat Lamaran Kerja', 'uploads/68c4a256ca39c_slk05.webp', '2026-09-04 15:47:30'),
('35', 'Surat Lamaran Kerja PUEBI #68', '15000.00', 'Surat Lamaran Kerja', 'uploads/68c4a77b478dc_SLK06.webp', '2026-09-04 15:47:30'),
('36', 'CV ATS Friendly Professional #68', '30000.00', 'CV ATS', 'uploads/68c4abe699499_ATS01.webp', '2026-09-04 15:47:30'),
('37', 'CV ATS Friendly Professional #68', '30000.00', 'CV ATS', 'uploads/68c950266d875_ATS02.webp', '2026-09-04 15:47:30'),
('38', 'CV Kreatif Modern Style #68', '25000.00', 'CV Kreatif', 'uploads/68c9504a3e9ab_CVKreatif21.webp', '2026-09-04 15:47:30'),
('39', 'CV Kreatif Modern Style #68', '25000.00', 'CV Kreatif', 'uploads/68cca4c6a7131_CVKreatif22.webp', '2026-09-04 15:47:30'),
('40', 'CV Kreatif Modern Style #68', '25000.00', 'CV Kreatif', 'uploads/68e869ec6d357_kreatif23.webp', '2026-09-04 15:47:30'),
('41', 'CV Kreatif Modern Style #68', '25000.00', 'CV Kreatif', 'uploads/68e8f4d8e9a70_kreatif24.webp', '2026-09-04 15:47:30'),
('42', 'CV Kreatif Modern Style #68', '25000.00', 'CV Kreatif', 'uploads/68e8faf8bfd66_kreatif25.webp', '2026-09-04 15:47:30');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
