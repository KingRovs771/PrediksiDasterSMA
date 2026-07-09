-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Waktu pembuatan: 21 Apr 2026 pada 15.02
-- Versi server: 8.0.45
-- Versi PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `prediksi_daster_collection`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL UNIQUE,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Daster', 'Kategori produk daster standar', '2026-04-15 01:14:00', '2026-04-15 01:14:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_produk`
--

CREATE TABLE `data_produk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(50) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori_id` int NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_produk` (`kode_produk`),
  KEY `idx_kode` (`kode_produk`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `data_produk`
--

INSERT INTO `data_produk` (`id`, `kode_produk`, `nama_produk`, `kategori_id`, `harga`, `stok`, `created_at`, `updated_at`) VALUES
(1, 'DST-001', 'Daster Motif Bunga', 1, 20000.00, 207, '2026-04-15 01:14:38', '2026-04-15 02:55:16'),
(2, 'DST-002', 'Daster Motif Dinosaurus', 1, 10000.00, 700, '2026-04-15 02:52:39', '2026-04-15 02:52:39'),
(4, 'DST-003', 'Daster Motif Bunga Kamboja', 1, 8000.00, 400, '2026-04-15 02:53:47', '2026-04-15 02:55:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_penjualan`
--

CREATE TABLE `data_penjualan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produk_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_terjual` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `produk_id` (`produk_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_tanggal` (`tanggal`),
  CONSTRAINT `data_penjualan_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `data_produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `data_penjualan_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `data_penjualan`
--

INSERT INTO `data_penjualan` (`id`, `produk_id`, `tanggal`, `jumlah_terjual`, `user_id`, `created_at`) VALUES
(8, 1, '2026-04-01', 20, 1, '2026-04-15 02:23:58'),
(9, 1, '2026-04-02', 40, 1, '2026-04-15 02:24:08'),
(10, 1, '2026-04-03', 78, 1, '2026-04-15 02:24:16'),
(11, 1, '2026-04-06', 45, 1, '2026-04-15 02:24:25'),
(12, 1, '2026-04-08', 56, 1, '2026-04-15 02:24:33'),
(13, 1, '2026-04-09', 89, 1, '2026-04-15 02:24:42'),
(14, 1, '2026-02-02', 23, 1, '2026-04-15 02:25:04'),
(15, 1, '2026-02-03', 13, 1, '2026-04-15 02:25:12'),
(16, 1, '2026-02-12', 42, 1, '2026-04-15 02:25:23'),
(17, 1, '2026-02-20', 78, 1, '2026-04-15 02:25:33'),
(18, 1, '2026-03-05', 63, 1, '2026-04-15 02:25:46'),
(19, 1, '2026-03-13', 50, 1, '2026-04-15 02:25:59'),
(20, 1, '2026-03-16', 102, 1, '2026-04-15 02:26:21'),
(21, 1, '2026-01-08', 34, 1, '2026-04-15 02:26:42'),
(22, 1, '2026-01-19', 67, 1, '2026-04-15 02:26:52'),
(23, 1, '2026-01-30', 23, 1, '2026-04-15 02:27:00'),
(24, 1, '2026-01-23', 12, 1, '2026-04-15 02:27:09'),
(25, 1, '2026-01-24', 80, 1, '2026-04-15 02:27:20'),
(26, 1, '2026-01-01', 24, 1, '2026-04-15 02:27:33'),
(27, 4, '2026-01-13', 26, 1, '2026-04-15 02:54:03'),
(28, 4, '2026-01-16', 69, 1, '2026-04-15 02:54:18'),
(29, 4, '2026-01-28', 73, 1, '2026-04-15 02:54:28'),
(30, 4, '2026-02-11', 23, 1, '2026-04-15 02:54:37'),
(31, 4, '2026-02-19', 56, 1, '2026-04-15 02:54:48'),
(32, 4, '2026-03-10', 21, 1, '2026-04-15 02:54:56'),
(33, 4, '2026-04-01', 100, 1, '2026-04-15 02:55:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prediksi_log`
--

CREATE TABLE `prediksi_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kategori` varchar(100) NOT NULL,
  `bulan_awal` varchar(7) NOT NULL,
  `bulan_akhir` varchar(7) NOT NULL,
  `periode_n` int NOT NULL,
  `prediksi_bulan` varchar(7) NOT NULL,
  `nilai_prediksi` decimal(10,2) NOT NULL,
  `mape` decimal(5,2) NOT NULL,
  `smape` decimal(5,2) NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `prediksi_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin_tivayo', 'admin@tivayo.com', '$2y$10$cdJYLKsNkLqBa38qpfhonufvCXPtl0hetZWJj.3/LUHsByZulz.SW', 'admin', 1, '2026-04-14 01:19:29', '2026-04-15 01:14:08');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
