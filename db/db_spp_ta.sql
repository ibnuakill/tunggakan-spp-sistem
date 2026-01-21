-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2026 at 12:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spp_ta`
--

-- --------------------------------------------------------

--
-- Table structure for table `tabel_admin`
--

CREATE TABLE `tabel_admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_admin`
--

INSERT INTO `tabel_admin` (`id_admin`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', 'admin', 'Jia');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_galeri`
--

CREATE TABLE `tabel_galeri` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tabel_galeri`
--

INSERT INTO `tabel_galeri` (`id`, `judul`, `deskripsi`, `file_path`, `uploaded_at`) VALUES
(5, 'ppppppp', 'pppp', 'assets/img/gallery/1763386254_Logo.jpg', '2025-11-17 20:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_jenis_biaya`
--

CREATE TABLE `tabel_jenis_biaya` (
  `id_biaya` int(11) NOT NULL,
  `nama_biaya` varchar(50) NOT NULL,
  `nominal` decimal(10,2) NOT NULL,
  `periode` enum('Bulanan','Tahunan','Sekali') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_jenis_biaya`
--

INSERT INTO `tabel_jenis_biaya` (`id_biaya`, `nama_biaya`, `nominal`, `periode`) VALUES
(1, 'SPP Bulanan', 60000.00, 'Bulanan');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_kelas`
--

CREATE TABLE `tabel_kelas` (
  `id_kelas` int(11) NOT NULL,
  `nama_kelas` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_kelas`
--

INSERT INTO `tabel_kelas` (`id_kelas`, `nama_kelas`) VALUES
(1, 'PG'),
(2, 'A'),
(3, 'B');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_pembayaran`
--

CREATE TABLE `tabel_pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `nis` varchar(15) NOT NULL,
  `id_biaya` int(11) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `bulan_bayar` varchar(20) NOT NULL,
  `tahun_bayar` year(4) NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `status_valid` tinyint(1) DEFAULT 0,
  `id_siswa_tahun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_pembayaran`
--

INSERT INTO `tabel_pembayaran` (`id_pembayaran`, `nis`, `id_biaya`, `tanggal_bayar`, `bulan_bayar`, `tahun_bayar`, `jumlah_bayar`, `id_admin`, `status_valid`, `id_siswa_tahun`) VALUES
(245, '3197235986', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(246, '3197235986', 1, '2024-08-10', 'Agustus', '2024', 60000.00, 1, 1, NULL),
(247, '3197235986', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(248, '3197235986', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(249, '3197235986', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(250, '3197235986', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(251, '3197235986', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(252, '3197235986', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(253, '3197235986', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(254, '3197235986', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(255, '3197235986', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(256, '3197235986', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(257, '3210915275', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(258, '3210915275', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(259, '3210915275', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(260, '3208638801', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(261, '3208638801', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(262, '3208638801', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(263, '3208638801', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(264, '3208638801', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(265, '3208638801', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(266, '3208638801', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(267, '3208638801', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(268, '3193641944', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(269, '3193641944', 1, '2024-08-10', 'Agustus', '2024', 60000.00, 1, 1, NULL),
(270, '3193641944', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(271, '3193641944', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(272, '3193641944', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(273, '3193641944', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(274, '3193641944', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(275, '3193641944', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(276, '3193641944', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(277, '3193641944', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(278, '3193641944', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(279, '3193641944', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(280, '3206593166', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(281, '3206593166', 1, '2024-08-10', 'Agustus', '2024', 60000.00, 1, 1, NULL),
(282, '3206593166', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(283, '3206593166', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(284, '3206593166', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(285, '3206593166', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(286, '3206593166', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(287, '3206593166', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(288, '3206593166', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(289, '3206593166', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(290, '3206593166', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(291, '3206593166', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(292, '3193125097', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(293, '3193125097', 1, '2024-08-10', 'Agustus', '2024', 60000.00, 1, 1, NULL),
(294, '3193125097', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(295, '3193125097', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(296, '3193125097', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(297, '3193125097', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(298, '3193125097', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(299, '3193125097', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(300, '3193125097', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(301, '3193125097', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(302, '3193125097', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(303, '3193125097', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(304, '3216301317', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(305, '3216301317', 1, '2024-08-10', 'Agustus', '2024', 60000.00, 1, 1, NULL),
(306, '3216301317', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(307, '3216301317', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(308, '3216301317', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(309, '3216301317', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(310, '3216301317', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(311, '3216301317', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(312, '3216301317', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(313, '3216301317', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(314, '3216301317', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(315, '3216301317', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(316, '3200454488', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(317, '3200454488', 1, '2024-08-10', 'Agustus', '2024', 60000.00, 1, 1, NULL),
(318, '3200454488', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(319, '3200454488', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(320, '3200454488', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(321, '3200454488', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(322, '3200454488', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(323, '3200454488', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(324, '3200454488', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(325, '3200454488', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(326, '3200454488', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(327, '3200454488', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(328, '319319101', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(329, '3221480774', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(330, '3221480774', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(331, '3221480774', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(332, '3221480774', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(333, '3221480774', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(334, '3221480774', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(335, '3221480774', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(336, '3221480774', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(337, '3221480774', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(338, '3221480774', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(339, '3191586014', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(340, '3204060994', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(341, '3204060994', 1, '2024-08-10', 'Agustus', '2024', 60000.00, 1, 1, NULL),
(342, '3204060994', 1, '2024-09-10', 'September', '2024', 60000.00, 1, 1, NULL),
(343, '3204060994', 1, '2024-10-10', 'Oktober', '2024', 60000.00, 1, 1, NULL),
(344, '3204060994', 1, '2024-11-10', 'November', '2024', 60000.00, 1, 1, NULL),
(345, '3204060994', 1, '2024-12-10', 'Desember', '2024', 60000.00, 1, 1, NULL),
(346, '3204060994', 1, '2025-01-10', 'Januari', '2025', 60000.00, 1, 1, NULL),
(347, '3204060994', 1, '2025-02-10', 'Februari', '2025', 60000.00, 1, 1, NULL),
(348, '3204060994', 1, '2025-03-10', 'Maret', '2025', 60000.00, 1, 1, NULL),
(349, '3204060994', 1, '2025-04-10', 'April', '2025', 60000.00, 1, 1, NULL),
(350, '3204060994', 1, '2025-05-10', 'Mei', '2025', 60000.00, 1, 1, NULL),
(351, '3204060994', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(352, '3200781751', 1, '2024-07-10', 'Juli', '2024', 60000.00, 1, 1, NULL),
(353, '3200781751', 1, '2025-06-10', 'Juni', '2025', 60000.00, 1, 1, NULL),
(354, '3197235986', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(355, '3197235986', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(356, '3197235986', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(357, '3197235986', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(358, '3210915275', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(359, '3210915275', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(360, '3210915275', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(361, '3210915275', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(362, '3205919570', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(363, '3205919570', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(364, '3205919570', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(365, '3205919570', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(366, '3200150936', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(367, '3200150936', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(368, '3200150936', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(369, '3200150936', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(370, '3208638801', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(371, '3208638801', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(372, '3208638801', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(373, '3208638801', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(374, '3217337627', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(375, '3217337627', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(376, '3217337627', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(377, '3217337627', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(378, '3207301919', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(379, '3207301919', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(380, '3216374301', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(381, '3216374301', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(382, '3216374301', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(383, '3216374301', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(384, '3206593166', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(385, '3206593166', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(386, '3206593166', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(387, '3193125097', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(388, '3193125097', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(389, '3193125097', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(390, '3200454488', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(391, '3200454488', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(392, '3200454488', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(393, '319319101', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(394, '319319101', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(395, '319319101', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(396, '319319101', 1, '2025-12-10', 'Desember', '2025', 60000.00, 1, 1, NULL),
(397, '3221480774', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(398, '3221480774', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(399, '3221480774', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(400, '3221480774', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(401, '3204060994', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(402, '3204060994', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(403, '3200781751', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(404, '3200781751', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(405, '3200781751', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(406, '3200781751', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(407, '3229092808', 1, '2025-07-10', 'Juli', '2025', 60000.00, 1, 1, NULL),
(408, '3229092808', 1, '2025-08-10', 'Agustus', '2025', 60000.00, 1, 1, NULL),
(409, '3229092808', 1, '2025-09-10', 'September', '2025', 60000.00, 1, 1, NULL),
(410, '3229092808', 1, '2025-10-10', 'Oktober', '2025', 60000.00, 1, 1, NULL),
(412, '3191586014', 1, '2026-01-20', 'Januari', '2026', 60000.00, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tabel_siswa`
--

CREATE TABLE `tabel_siswa` (
  `nis` varchar(15) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `kelamin` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_siswa`
--

INSERT INTO `tabel_siswa` (`nis`, `nama_siswa`, `id_kelas`, `kelamin`) VALUES
('0', 'AFKHAR FEZFAN WIRIADI', 1, 'L'),
('12345678', 'Cecep Asu', 1, 'L'),
('3191179806', 'SHARIFFA HABIBA PUTRI', 3, 'P'),
('3191586014', 'NAURA UMEIZA KHAYIRA', 3, 'P'),
('3193125097', 'MUHAMMAD FAHMI AHYAN', 3, 'L'),
('319319101', 'MUHAMMAD HAIDAR AL KHALIFI', 3, 'L'),
('3193641944', 'LUTFI PRATAMA SINGGIH', 3, 'L'),
('3197235986', 'ALEESHA TAZKIA', 3, 'P'),
('3200150936', 'ALLEEYA ZAIDA RAMADHAN', 3, 'P'),
('3200454488', 'MUHAMMAD KENAND HAIKAL', 3, 'L'),
('3200781751', 'SYAKILA NOURA ADZKIYA', 3, 'P'),
('3204060994', 'SYIFA AULIA RAHMAH', 1, 'P'),
('3205919570', 'ASSYAFA PUTRI QINANDHITA', 2, 'P'),
('3206593166', 'MUHAMMAD ROZAN SENTANA', 3, 'L'),
('3207301919', 'GHIBRAN ATHAR ZULKARNAEN', 3, 'L'),
('3208638801', 'GIOVANI KHAYRA ISKANDAR', 2, 'L'),
('3210915275', 'AQILA SHAZFA ROMESA', 1, 'P'),
('3216301317', 'LEYCA SAFIYA HANIFA', 2, 'P'),
('3216374301', 'LUVINNO PRANATA SATRIYO', 1, 'L'),
('3217337627', 'GIOVANO DAVIZ MARCELINO', 2, 'L'),
('3221480774', 'NADINE RUBY MAULIDA', 1, 'P'),
('3229092808', 'PANGERAN MUHAMMAD RAFASYA', 1, 'L');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_siswa_aktif`
--

CREATE TABLE `tabel_siswa_aktif` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabel_siswa_aktif`
--

INSERT INTO `tabel_siswa_aktif` (`id`, `nis`, `tahun_ajaran`) VALUES
(1, '3197235986', '2024-2025'),
(2, '3210915275', '2024-2025'),
(3, '3208638801', '2024-2025'),
(4, '3193641944', '2024-2025'),
(5, '3206593166', '2024-2025'),
(6, '3193125097', '2024-2025'),
(7, '3216301317', '2024-2025'),
(8, '3200454488', '2024-2025'),
(9, '319319101', '2024-2025'),
(10, '3221480774', '2024-2025'),
(11, '3191586014', '2024-2025'),
(12, '3204060994', '2024-2025'),
(13, '3200781751', '2024-2025'),
(14, '0', '2025-2026'),
(15, '3197235986', '2025-2026'),
(16, '3200150936', '2025-2026'),
(17, '3210915275', '2025-2026'),
(18, '3205919570', '2025-2026'),
(19, '3207301919', '2025-2026'),
(20, '3208638801', '2025-2026'),
(21, '3217337627', '2025-2026'),
(22, '3216301317', '2025-2026'),
(23, '3193641944', '2025-2026'),
(24, '3216374301', '2025-2026'),
(25, '3193125097', '2025-2026'),
(26, '319319101', '2025-2026'),
(27, '3200454488', '2025-2026'),
(28, '3206593166', '2025-2026'),
(29, '3221480774', '2025-2026'),
(30, '3191586014', '2025-2026'),
(31, '3229092808', '2025-2026'),
(32, '3191179806', '2025-2026'),
(33, '3200781751', '2025-2026'),
(34, '3204060994', '2025-2026'),
(38, '12345678', '2025-2026');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tabel_admin`
--
ALTER TABLE `tabel_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tabel_galeri`
--
ALTER TABLE `tabel_galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tabel_jenis_biaya`
--
ALTER TABLE `tabel_jenis_biaya`
  ADD PRIMARY KEY (`id_biaya`);

--
-- Indexes for table `tabel_kelas`
--
ALTER TABLE `tabel_kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indexes for table `tabel_pembayaran`
--
ALTER TABLE `tabel_pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `nis` (`nis`),
  ADD KEY `id_biaya` (`id_biaya`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `tabel_siswa`
--
ALTER TABLE `tabel_siswa`
  ADD PRIMARY KEY (`nis`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- Indexes for table `tabel_siswa_aktif`
--
ALTER TABLE `tabel_siswa_aktif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tahun_ajaran` (`tahun_ajaran`),
  ADD KEY `idx_nis` (`nis`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tabel_admin`
--
ALTER TABLE `tabel_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabel_galeri`
--
ALTER TABLE `tabel_galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tabel_jenis_biaya`
--
ALTER TABLE `tabel_jenis_biaya`
  MODIFY `id_biaya` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabel_kelas`
--
ALTER TABLE `tabel_kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tabel_pembayaran`
--
ALTER TABLE `tabel_pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=413;

--
-- AUTO_INCREMENT for table `tabel_siswa_aktif`
--
ALTER TABLE `tabel_siswa_aktif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tabel_pembayaran`
--
ALTER TABLE `tabel_pembayaran`
  ADD CONSTRAINT `tabel_pembayaran_ibfk_1` FOREIGN KEY (`nis`) REFERENCES `tabel_siswa` (`nis`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tabel_pembayaran_ibfk_2` FOREIGN KEY (`id_biaya`) REFERENCES `tabel_jenis_biaya` (`id_biaya`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tabel_pembayaran_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `tabel_admin` (`id_admin`) ON UPDATE CASCADE;

--
-- Constraints for table `tabel_siswa`
--
ALTER TABLE `tabel_siswa`
  ADD CONSTRAINT `tabel_siswa_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `tabel_kelas` (`id_kelas`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
