-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Des 2025 pada 06.49
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthycare_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `aktivitas`
--

CREATE TABLE `aktivitas` (
  `id` int(11) NOT NULL COMMENT 'ID Aktivitas',
  `user_id` int(11) NOT NULL COMMENT 'ID Pengguna yang memiliki jadwal ini',
  `nama_kegiatan` varchar(255) NOT NULL COMMENT 'Nama Kegiatan',
  `waktu_tanggal` datetime NOT NULL COMMENT 'Waktu dan Tanggal Kegiatan',
  `keterangan` text DEFAULT NULL COMMENT 'Keterangan tambahan (opsional)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `aktivitas`
--

INSERT INTO `aktivitas` (`id`, `user_id`, `nama_kegiatan`, `waktu_tanggal`, `keterangan`) VALUES
(2, 1, 'makan', '2025-12-16 17:35:00', 'sdadsad'),
(3, 1, 'makan', '2025-12-17 17:13:00', 'majag'),
(5, 1, 'mole', '2025-12-18 08:54:00', 'main'),
(6, 1, 'minum', '2025-12-18 09:00:00', 'abcd');

-- --------------------------------------------------------

--
-- Struktur dari tabel `checkup`
--

CREATE TABLE `checkup` (
  `id` int(11) NOT NULL COMMENT 'ID Jadwal Checkup',
  `user_id` int(11) NOT NULL COMMENT 'ID Pengguna yang memiliki jadwal ini',
  `nama_dokter` varchar(255) NOT NULL COMMENT 'Nama Dokter',
  `waktu_tanggal` datetime NOT NULL COMMENT 'Waktu dan Tanggal Checkup',
  `keterangan` text DEFAULT NULL COMMENT 'Keterangan tambahan (opsional)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `checkup`
--

INSERT INTO `checkup` (`id`, `user_id`, `nama_dokter`, `waktu_tanggal`, `keterangan`) VALUES
(1, 1, 'gg', '2025-12-16 17:38:00', 'asdasd'),
(2, 1, 'rebum', '2025-12-18 12:56:00', 'solat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `koneksi_pengawas`
--

CREATE TABLE `koneksi_pengawas` (
  `id_koneksi` int(11) NOT NULL,
  `id_pengawas` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `status` enum('connected','pending') DEFAULT 'connected',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `koneksi_pengawas`
--

INSERT INTO `koneksi_pengawas` (`id_koneksi`, `id_pengawas`, `id_user`, `status`, `created_at`) VALUES
(1, 2, 1, 'connected', '2025-12-17 06:59:43'),
(2, 2, 7, 'connected', '2025-12-17 09:37:51'),
(3, 8, 1, 'connected', '2025-12-18 01:57:32'),
(4, 2, 9, 'connected', '2025-12-29 13:31:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notif` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_pengawas` int(11) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id_notif`, `id_user`, `id_pengawas`, `pesan`, `waktu`, `is_read`) VALUES
(1, 1, 2, 'Peringatan: Jadwal \'adawd\' belum diselesaikan!', '2025-12-17 07:50:18', 1),
(2, 1, 2, 'Peringatan: Jadwal \'adawd\' belum diselesaikan!', '2025-12-17 09:19:35', 1),
(3, 1, 2, 'Peringatan Sistem: Jadwal Obat (adawd) milik ajan sudah melewati waktunya!', '2025-12-17 10:24:14', 1),
(4, 1, 2, 'Peringatan Sistem: Jadwal Aktivitas (makan) milik ajan sudah melewati waktunya!', '2025-12-17 10:24:14', 1),
(5, 1, 2, 'Peringatan Sistem: Jadwal Checkup (gg) milik ajan sudah melewati waktunya!', '2025-12-17 10:24:14', 1),
(6, 1, 2, 'Peringatan Sistem: Jadwal Aktivitas (adawd) milik ajan sudah melewati waktunya!', '2025-12-17 10:29:02', 1),
(7, 1, 2, 'Peringatan: Jadwal Obat (adawd) milik ajan sudah waktunya!', '2025-12-17 10:38:28', 1),
(8, 1, 2, 'Peringatan: Jadwal Aktivitas (makan) milik ajan sudah waktunya!', '2025-12-17 10:38:28', 1),
(9, 1, 2, 'Peringatan: Jadwal Aktivitas (adawd) milik ajan sudah waktunya!', '2025-12-17 10:38:28', 1),
(10, 1, 2, 'Peringatan: Jadwal Checkup (gg) milik ajan sudah waktunya!', '2025-12-17 10:38:28', 1),
(11, 1, 2, 'Peringatan: Jadwal Obat (adawd) milik ajan sudah terlewat!', '2025-12-17 10:48:19', 1),
(12, 1, 2, 'Peringatan: Jadwal Aktivitas (makan) milik ajan sudah terlewat!', '2025-12-17 10:48:19', 1),
(13, 1, 2, 'Peringatan: Jadwal Aktivitas (adawd) milik ajan sudah terlewat!', '2025-12-17 10:48:19', 1),
(14, 1, 2, 'Peringatan: Jadwal Checkup (gg) milik ajan sudah terlewat!', '2025-12-17 10:48:19', 1),
(15, 1, 2, 'Peringatan: Jadwal Checkup (gg) milik ajan sudah terlewat!', '2025-12-17 10:48:36', 1),
(16, 1, 2, 'Peringatan: Jadwal \'adawd\' belum diselesaikan!', '2025-12-17 10:58:52', 1),
(17, 1, 2, 'Peringatan: Jadwal \'adawd\' belum diselesaikan!', '2025-12-17 11:02:00', 1),
(18, 1, 8, 'Peringatan: Jadwal \'mole\' belum diselesaikan!', '2025-12-18 01:57:55', 1),
(19, 9, 2, 'Peringatan: Jadwal \'obat batuk\' belum diselesaikan!', '2025-12-29 13:31:21', 0),
(20, 9, 2, 'Peringatan: Jadwal \'obat batuk\' belum diselesaikan!', '2025-12-29 13:32:18', 1),
(21, 9, 2, 'Peringatan: Jadwal \'makan\' belum diselesaikan!', '2025-12-29 13:32:49', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `obat`
--

CREATE TABLE `obat` (
  `id` int(11) NOT NULL COMMENT 'ID Jadwal Obat',
  `user_id` int(11) NOT NULL COMMENT 'ID Pengguna yang memiliki jadwal ini',
  `nama_obat` varchar(255) NOT NULL COMMENT 'Nama Obat',
  `waktu_minum` time NOT NULL COMMENT 'Waktu Minum Obat (hanya jam)',
  `keterangan` text DEFAULT NULL COMMENT 'Keterangan tambahan (opsional)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `obat`
--

INSERT INTO `obat` (`id`, `user_id`, `nama_obat`, `waktu_minum`, `keterangan`) VALUES
(1, 1, 'adawd', '17:04:00', 'dadawd'),
(2, 9, 'obat batuk', '20:33:00', 'minum obat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengawas`
--

CREATE TABLE `pengawas` (
  `id_pengawas` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'ID Pengguna',
  `kode_unik` varchar(10) DEFAULT NULL,
  `username` varchar(255) NOT NULL COMMENT 'Nama pengguna',
  `email` varchar(255) NOT NULL COMMENT 'Email untuk login',
  `password` varchar(255) NOT NULL COMMENT 'Hash password',
  `role` enum('pengawas','pengguna') DEFAULT 'pengguna',
  `nama` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `ket` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `kode_unik`, `username`, `email`, `password`, `role`, `nama`, `gender`, `birthday`, `phone`, `ket`) VALUES
(1, 'HC-FSWY9O', 'ajan', 'ajan@gmail.com', '$2y$10$3C3.NCgPhQBlUuereDKq4.gMbXZH6yBLA5DIeY.SQwte6e8T4kSLu', 'pengguna', 'Amoy', 'Laki Laki', '2017-01-16', '1343141341', 'suka bakso'),
(2, NULL, 'amskoy', '1234@gmail.com', '$2y$10$PkXIzFF8oWf/GL8rI4bk/uEwuef3tZdABFadW9QlEyZLaMH4MP0W2', 'pengawas', NULL, NULL, NULL, NULL, NULL),
(3, NULL, 'lomo', 'amoy@gmail.com', '$2y$10$xIZYYS7XsbluWiswWWsAtullj.2rpFaMavUg1YHdWE3ss0nYlZcnm', 'pengawas', NULL, NULL, NULL, NULL, NULL),
(4, NULL, '123@gmail.com', '123@gmail.com', '$2y$10$srqwCHXuQSiowuZdX3ig8.F8Ft39iGfrd/ckstQzMmlWN3hSr.0Da', 'pengawas', NULL, NULL, NULL, NULL, NULL),
(5, NULL, 'tes', 'tes@gmail.com', '$2y$10$XCOD7GRjG5H8VXMbfbcDIuxEuU7GLqKTySw0wxPmOKsOus3k0A2G6', 'pengawas', NULL, NULL, NULL, NULL, NULL),
(6, NULL, 'adakun', 'ada@gmail.com', '$2y$10$r7pyjOJe3Zf5IgWoax5GXecn4YomtwRSH4CQ.ymEJUd812Vl8ErfO', 'pengguna', NULL, NULL, NULL, NULL, NULL),
(7, 'HC-DS91MK', 'Rebom', 'rebom@gmail.com', '$2y$10$4HYbFg.uW0mtWBeq8rO8zei64Qy8Oj7yebbn44Iknr8wNzv3fI8YO', 'pengguna', 'rebom', 'Perempuan', '2000-05-17', '12334445689', 'makan mie ayam'),
(8, NULL, 'ipek', 'ipek@gmail.com', '$2y$10$1xCiTmfhTPhw6XqkaIDjzewtfRxTbivbfS3NIfGlO4vsf8c5Ygunq', 'pengawas', NULL, NULL, NULL, NULL, NULL),
(9, 'HC-URQSY8', 'adit', 'adit@gmail.com', '$2y$10$r74Ng5mh3cU6PfK2s8t.reZ0YjpSjYrvzpfmHpXLaIJwhGMw6C8bu', 'pengguna', NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `checkup`
--
ALTER TABLE `checkup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `koneksi_pengawas`
--
ALTER TABLE `koneksi_pengawas`
  ADD PRIMARY KEY (`id_koneksi`),
  ADD KEY `id_pengawas` (`id_pengawas`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notif`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pengawas`
--
ALTER TABLE `pengawas`
  ADD PRIMARY KEY (`id_pengawas`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `kode_unik` (`kode_unik`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID Aktivitas', AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `checkup`
--
ALTER TABLE `checkup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID Jadwal Checkup', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `koneksi_pengawas`
--
ALTER TABLE `koneksi_pengawas`
  MODIFY `id_koneksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `obat`
--
ALTER TABLE `obat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID Jadwal Obat', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengawas`
--
ALTER TABLE `pengawas`
  MODIFY `id_pengawas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID Pengguna', AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD CONSTRAINT `aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `checkup`
--
ALTER TABLE `checkup`
  ADD CONSTRAINT `checkup_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `koneksi_pengawas`
--
ALTER TABLE `koneksi_pengawas`
  ADD CONSTRAINT `koneksi_pengawas_ibfk_1` FOREIGN KEY (`id_pengawas`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `koneksi_pengawas_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `obat`
--
ALTER TABLE `obat`
  ADD CONSTRAINT `obat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
