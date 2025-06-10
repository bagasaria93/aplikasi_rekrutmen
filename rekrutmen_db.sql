-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 12:26 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rekrutmen_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_art`
--

CREATE TABLE `tb_art` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_ktp` varchar(20) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat_ktp` text NOT NULL,
  `alamat_domisili` text NOT NULL,
  `agama` varchar(50) NOT NULL,
  `pendidikan` varchar(100) NOT NULL,
  `status_perkawinan` enum('Belum Menikah','Menikah','Janda','Duda') NOT NULL,
  `nama_kontak_darurat` varchar(100) NOT NULL,
  `telp_kontak_darurat` varchar(20) NOT NULL,
  `hubungan_kontak_darurat` varchar(50) NOT NULL,
  `alamat_kontak_darurat` text NOT NULL,
  `jumlah_anak` int(2) DEFAULT 0,
  `status_id` int(11) DEFAULT 6,
  `trainer_id` int(11) DEFAULT NULL,
  `level` enum('Lead','Admin','Supervisor','Operator','Lainnya') DEFAULT 'Operator',
  `level_lainnya` varchar(100) DEFAULT NULL,
  `upload_cv` varchar(255) DEFAULT NULL,
  `upload_ktp` varchar(255) DEFAULT NULL,
  `upload_kk` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_kelulusan` enum('Proses','Lulus','Tidak Lulus') DEFAULT 'Proses',
  `nilai` decimal(5,2) DEFAULT NULL,
  `catatan_trainer` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_art`
--

INSERT INTO `tb_art` (`id`, `nama`, `no_ktp`, `no_telepon`, `alamat`, `email`, `tempat_lahir`, `tanggal_lahir`, `alamat_ktp`, `alamat_domisili`, `agama`, `pendidikan`, `status_perkawinan`, `nama_kontak_darurat`, `telp_kontak_darurat`, `hubungan_kontak_darurat`, `alamat_kontak_darurat`, `jumlah_anak`, `status_id`, `trainer_id`, `level`, `level_lainnya`, `upload_cv`, `upload_ktp`, `upload_kk`, `tanggal_mulai`, `tanggal_selesai`, `status_kelulusan`, `nilai`, `catatan_trainer`, `created_at`, `updated_at`) VALUES
(1, 'Maya Indah', '3175054503960001', '081987654321', 'Jl. Kenanga No. 321, Jakarta', 'maya@email.com', 'Jakarta', '1996-03-05', 'Jl. Kenanga No. 321, Jakarta', 'Jl. Kenanga No. 321, Jakarta', 'Islam', 'SMA', 'Belum Menikah', 'Siti Maya', '081234567890', 'Ibu', 'Jl. Kenanga No. 320, Jakarta', 0, 6, 1, 'Operator', NULL, NULL, NULL, NULL, '2025-06-01', '2025-06-15', 'Proses', NULL, NULL, '2025-06-10 09:56:51', '2025-06-10 09:56:51');

--
-- Triggers `tb_art`
--
DELIMITER $$
CREATE TRIGGER `tr_art_status_log` AFTER UPDATE ON `tb_art` FOR EACH ROW BEGIN
    IF OLD.status_id != NEW.status_id THEN
        INSERT INTO tb_log_status (art_id, status_lama_id, status_baru_id, catatan)
        VALUES (NEW.id, OLD.status_id, NEW.status_id, 'Status training diubah');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_departemen`
--

CREATE TABLE `tb_departemen` (
  `id` int(11) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_departemen`
--

INSERT INTO `tb_departemen` (`id`, `nama_departemen`, `created_at`) VALUES
(1, 'Telemarketing', '2025-06-10 09:56:51'),
(2, 'Kreatif', '2025-06-10 09:56:51'),
(3, 'Warehouse', '2025-06-10 09:56:51'),
(4, 'IT', '2025-06-10 09:56:51'),
(5, 'CCTV', '2025-06-10 09:56:51'),
(6, 'Operasional', '2025-06-10 09:56:51'),
(7, 'Finance & Accounting', '2025-06-10 09:56:51'),
(8, 'General Affair', '2025-06-10 09:56:51'),
(9, 'HR', '2025-06-10 09:56:51'),
(10, 'Aftercare', '2025-06-10 09:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `tb_log_status`
--

CREATE TABLE `tb_log_status` (
  `id` int(11) NOT NULL,
  `pelamar_id` int(11) DEFAULT NULL,
  `art_id` int(11) DEFAULT NULL,
  `status_lama_id` int(11) DEFAULT NULL,
  `status_baru_id` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pelamar`
--

CREATE TABLE `tb_pelamar` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `tanggal_phone_screening` date DEFAULT NULL,
  `status_id` int(11) DEFAULT 1,
  `departemen_id` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pelamar`
--

INSERT INTO `tb_pelamar` (`id`, `nama`, `no_telepon`, `alamat`, `email`, `tempat_lahir`, `tanggal_lahir`, `tanggal_phone_screening`, `status_id`, `departemen_id`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Siti Nurhaliza', '081234567890', 'Jl. Mangga No. 123, Jakarta Selatan', 'siti@email.com', 'Jakarta', '1995-03-15', '2025-06-01', 1, NULL, NULL, '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(2, 'Dewi Sartika', '082345678901', 'Jl. Melati No. 456, Bandung', 'dewi@email.com', 'Bandung', '1993-07-22', '2025-06-02', 5, NULL, NULL, '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(3, 'Rina Permata', '083456789012', 'Jl. Mawar No. 789, Surabaya', 'rina@email.com', 'Surabaya', '1996-11-08', '2025-06-03', 6, NULL, NULL, '2025-06-10 09:56:51', '2025-06-10 09:56:51');

--
-- Triggers `tb_pelamar`
--
DELIMITER $$
CREATE TRIGGER `tr_pelamar_status_log` AFTER UPDATE ON `tb_pelamar` FOR EACH ROW BEGIN
    IF OLD.status_id != NEW.status_id THEN
        INSERT INTO tb_log_status (pelamar_id, status_lama_id, status_baru_id, catatan)
        VALUES (NEW.id, OLD.status_id, NEW.status_id, 'Status diubah');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_status`
--

CREATE TABLE `tb_status` (
  `id` int(11) NOT NULL,
  `nama_status` varchar(50) NOT NULL,
  `warna` varchar(10) DEFAULT '#007bff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_status`
--

INSERT INTO `tb_status` (`id`, `nama_status`, `warna`, `created_at`, `updated_at`) VALUES
(1, 'KONFIRMASI', '#17a2b8', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(2, 'RESIGN', '#dc3545', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(3, 'FAILED', '#dc3545', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(4, 'NO FEEDBACK', '#6c757d', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(5, 'INTERVIEW', '#ffc107', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(6, 'TRAINING BM', '#28a745', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(7, 'TRAINING SULAM', '#28a745', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(8, 'TRAINING HO', '#28a745', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(9, 'HEAD OFFICE', '#007bff', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(10, 'STORE', '#6f42c1', '2025-06-10 09:56:51', '2025-06-10 09:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `tb_trainer`
--

CREATE TABLE `tb_trainer` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `level` enum('Lead','Admin','Supervisor','Operator','Lainnya') NOT NULL,
  `level_lainnya` varchar(100) DEFAULT NULL,
  `status_aktif` enum('Aktif','Non-aktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_trainer`
--

INSERT INTO `tb_trainer` (`id`, `nama`, `jabatan`, `level`, `level_lainnya`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Sari Dewi', 'Trainer Beauty Makeup', 'Lead', NULL, 'Aktif', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(2, 'Nina Kartika', 'Trainer Sulam Alis', 'Admin', NULL, 'Aktif', '2025-06-10 09:56:51', '2025-06-10 09:56:51'),
(3, 'Maya Sari', 'Head Office Trainer', 'Supervisor', NULL, 'Aktif', '2025-06-10 09:56:51', '2025-06-10 09:56:51');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_dashboard_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_dashboard_summary` (
`total_pelamar` bigint(21)
,`total_art` bigint(21)
,`total_trainer` bigint(21)
,`konfirmasi` bigint(21)
,`resign` bigint(21)
,`failed` bigint(21)
,`no_feedback` bigint(21)
,`interview` bigint(21)
,`training_bm` bigint(21)
,`training_sulam` bigint(21)
,`training_ho` bigint(21)
,`head_office` bigint(21)
,`store` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_head_office_departemen`
-- (See below for the actual view)
--
CREATE TABLE `v_head_office_departemen` (
`nama_departemen` varchar(100)
,`jumlah` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `v_dashboard_summary`
--
DROP TABLE IF EXISTS `v_dashboard_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_dashboard_summary`  AS SELECT (select count(0) from `tb_pelamar`) AS `total_pelamar`, (select count(0) from `tb_art`) AS `total_art`, (select count(0) from `tb_trainer` where `tb_trainer`.`status_aktif` = 'Aktif') AS `total_trainer`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 1) AS `konfirmasi`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 2) AS `resign`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 3) AS `failed`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 4) AS `no_feedback`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 5) AS `interview`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 6) AS `training_bm`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 7) AS `training_sulam`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 8) AS `training_ho`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 9) AS `head_office`, (select count(0) from `tb_pelamar` where `tb_pelamar`.`status_id` = 10) AS `store``store`  ;

-- --------------------------------------------------------

--
-- Structure for view `v_head_office_departemen`
--
DROP TABLE IF EXISTS `v_head_office_departemen`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_head_office_departemen`  AS SELECT `d`.`nama_departemen` AS `nama_departemen`, count(`p`.`id`) AS `jumlah` FROM (`tb_departemen` `d` left join `tb_pelamar` `p` on(`p`.`departemen_id` = `d`.`id` and `p`.`status_id` = 9)) GROUP BY `d`.`id`, `d`.`nama_departemen``nama_departemen`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_art`
--
ALTER TABLE `tb_art`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ktp` (`no_ktp`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `tb_departemen`
--
ALTER TABLE `tb_departemen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_log_status`
--
ALTER TABLE `tb_log_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelamar_id` (`pelamar_id`),
  ADD KEY `art_id` (`art_id`),
  ADD KEY `status_lama_id` (`status_lama_id`),
  ADD KEY `status_baru_id` (`status_baru_id`);

--
-- Indexes for table `tb_pelamar`
--
ALTER TABLE `tb_pelamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `departemen_id` (`departemen_id`);

--
-- Indexes for table `tb_status`
--
ALTER TABLE `tb_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_trainer`
--
ALTER TABLE `tb_trainer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_art`
--
ALTER TABLE `tb_art`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_departemen`
--
ALTER TABLE `tb_departemen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_log_status`
--
ALTER TABLE `tb_log_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_pelamar`
--
ALTER TABLE `tb_pelamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_status`
--
ALTER TABLE `tb_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_trainer`
--
ALTER TABLE `tb_trainer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_art`
--
ALTER TABLE `tb_art`
  ADD CONSTRAINT `tb_art_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `tb_status` (`id`),
  ADD CONSTRAINT `tb_art_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `tb_trainer` (`id`);

--
-- Constraints for table `tb_log_status`
--
ALTER TABLE `tb_log_status`
  ADD CONSTRAINT `tb_log_status_ibfk_1` FOREIGN KEY (`pelamar_id`) REFERENCES `tb_pelamar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_log_status_ibfk_2` FOREIGN KEY (`art_id`) REFERENCES `tb_art` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_log_status_ibfk_3` FOREIGN KEY (`status_lama_id`) REFERENCES `tb_status` (`id`),
  ADD CONSTRAINT `tb_log_status_ibfk_4` FOREIGN KEY (`status_baru_id`) REFERENCES `tb_status` (`id`);

--
-- Constraints for table `tb_pelamar`
--
ALTER TABLE `tb_pelamar`
  ADD CONSTRAINT `tb_pelamar_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `tb_status` (`id`),
  ADD CONSTRAINT `tb_pelamar_ibfk_2` FOREIGN KEY (`departemen_id`) REFERENCES `tb_departemen` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
