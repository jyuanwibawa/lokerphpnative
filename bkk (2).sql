-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Feb 2025 pada 03.28
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bkk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `lamaran`
--

CREATE TABLE `lamaran` (
  `id` int(11) NOT NULL,
  `lowongan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telpon` varchar(255) NOT NULL,
  `cv` varchar(255) NOT NULL,
  `slamaran` varchar(255) DEFAULT NULL,
  `slamaran2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lamaran`
--

INSERT INTO `lamaran` (`id`, `lowongan_id`, `user_id`, `nama`, `email`, `telpon`, `cv`, `slamaran`, `slamaran2`) VALUES
(32, 319, 54, 'alumni', 'alumni@gmail.com', '098246293017', 'alumni_Social_Media_Admin_PT_Treasure_Maker.pdf', 'Valid', NULL),
(33, 17, 51, 'Khurotul Nisa', 'khurotul.nisaa@gmail.com', '0895422481279', 'Khurotul_Nisa_Vocalis__SM_Entertaiment.pdf', 'Valid', NULL),
(34, 17, 55, 'Kim Mingyu', 'mingyu@gmail.com', '089392837102', 'Kim_Mingyu_Vocalis__SM_Entertaiment.pdf', 'Valid', NULL),
(35, 319, 54, 'nisa', 'alumni@gmail.com', '098246293017', 'nisa_Social_Media_Admin_PT_Treasure_Maker.pdf', 'Valid', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `lowongan`
--

CREATE TABLE `lowongan` (
  `id` int(11) NOT NULL,
  `perusahaan` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `tanggal` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lowongan`
--

INSERT INTO `lowongan` (`id`, `perusahaan`, `title`, `deskripsi`, `tanggal`) VALUES
(15, 'cig', 'Social Media Admin', 'Bekerja non-shift', '24-08-2024'),
(17, 'SM Entertaiment', 'Vocalis ', 'butuh penyanyi nie', '26-10-2024'),
(319, 'PT Treasure Maker', 'Social Media Admin', 'Social Media Admin bertanggung jawab dalam mengelola dan mengembangkan akun media sosial perusahaan dengan membuat konten menarik, menjaga interaksi dengan audiens, serta menganalisis performa untuk meningkatkan engagement. Kandidat minimal lulusan SMA/SMK, D3, atau S1, diutamakan memiliki pengalaman di bidang social media management. Harus menguasai platform media sosial, tools desain (Canva, Photoshop, CapCut), serta memahami analisis insight dan dasar digital marketing. Dibutuhkan keterampilan komunikasi yang baik, kreativitas, manajemen waktu, serta mampu bekerja secara tim maupun mandiri.', '24 Februari 2025');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nisn` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telpon` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Pending','Verified') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `nisn`, `nama`, `email`, `telpon`, `role`, `username`, `password`, `status`) VALUES
(45, 87654321, 'Santoso', 'sultan@gmail.com', '0897654573618', 'Alumni', 'santoso', '$2y$10$lbavB6Vb4H9PE/kWql3h5.oPZZz3k.xhvNrm.e756s2tyElEMyKH2', 'Verified'),
(46, 22222, 'nisaaaa', 'nisa@gmsil.com', '098765431', 'Admin', 'nisaaa', '$2y$10$zYHqC4xE0FphQsWAhVcISena1MO3f3n3t26w4NmQLVN0eK4aXOxwa', 'Verified'),
(47, 55555, 'nnn', 'zz@gmsil.om', '7890', 'Alumni', 'imin', '$2y$10$WJw9DgWSQIigBhp9VnrQ.uc8UOwBE16LZeWIi0ZAB7oNwEJIEFxDG', 'Verified'),
(48, 2222, 'nisaaa', 'nisz@gmail.com', '0812345679', 'Alumni', 'haechanlee', '$2y$10$KeiIztX0QWLXcBj9ErDv6uOFdxQmYwM26Gke6rr/g4TeQObT45CQ6', 'Verified'),
(49, 9752839, 'haechan', 'hc@gmail.com', '092493013456', 'Staff', 'hc', '$2y$10$pVlWc.yzQSCLc0.BbUACmuUfgZygcw/YLfcRmc/0K6SsLLQYEx366', 'Verified'),
(50, 85929, 'jeong wonwoo', 'wonu@gmail.com', '082739172937', 'Admin', 'wonu', '$2y$10$xSuE6yQvSdYhRs8ETXnpG.2MKqsst7N.uzVjXnxqScQ1fs48n7OD.', 'Verified'),
(51, 22273041, 'Khurotul Nisa', 'khurotul.nisaa@gmail.com', '0895422481279', 'Alumni', 'khurotulnisa', '$2y$10$FKOUu8U9pSIwr7RU5qv6IuDTkajbabzBMwziPLwnvVuM3XHUoyUo6', 'Verified'),
(52, 1111111, 'nisa\r\n', 'staff1@gmail.com', '089283610274', 'Staff', 'staff', '$2y$10$7fk3sO8kDDZYL79us7cKG.MnREeV6egu6AbJXiP.7FGX6tbBt9mvG', 'Verified'),
(53, 987365, 'nisa', 'adminprs@gmail.com', '0982530182764', 'Admin', 'admin', '$2y$10$TQr1dgpyH0cxegWv.aDlNODD4aNlT/OEDKJB.m/B1vzg7AcqSnmwq', 'Verified'),
(54, 26391, 'nisa', 'alumni@gmail.com', '098246293017', 'Alumni', 'alumni', '$2y$10$Esz5wS/9m/GlWl5GHxD29uHo40eAxoKqYw/AAcr8NAnjLNZX.KOty', 'Verified'),
(55, 0, 'Kim Mingyu', 'mingyu@gmail.com', '089392837102', 'Alumni', 'mingyuganteng', '$2y$10$VP4xvzcrn1bytzMbrqdR5OXe6PA3zIXjY56NjrUQkX.TmtJr/qOcq', 'Verified'),
(56, 0, 'Song Mingi', 'fixon@outlook.com', '083719371927', 'Alumni', 'mingi', '$2y$10$QcJpU3vdrB4gu7tBKsvXC.r9M.dXyJ6j8eHcysffnfUZ7paJdYREO', 'Verified'),
(57, 0, 'nadin', NULL, NULL, 'Alumni', 'nadin', '$2y$10$V/HITWRTCjFN649tjtlkW.5O2XPx.HQ0TaLIMvC4PtfDoDqF4No9u', 'Verified'),
(58, 0, 'alumni1', 'alumnis@gmail.com', '0892917291', 'Alumni', 'alumni1', '$2y$10$Oo5kqDR4iaZB3ODryKcnseZwp.z1JEg0bhmVQDNcRvWUVlxCjpJX6', 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lowongan_id` (`lowongan_id`);

--
-- Indeks untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  ADD CONSTRAINT `lamaran_ibfk_1` FOREIGN KEY (`lowongan_id`) REFERENCES `lowongan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
