-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 01, 2025 at 03:57 AM
-- Server version: 8.2.0
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `press_amikom`
--

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
CREATE TABLE IF NOT EXISTS `info` (
  `id_info` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `posted_by` int NOT NULL,
  `posted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_info`),
  KEY `posted_by` (`posted_by`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`id_info`, `title`, `image`, `content`, `posted_by`, `posted_at`) VALUES
(1, 'Informasi Launching Amikom Press', 'news.jpg', '<p><strong>Dummy Textual</strong></p><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.</p><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p>', 2, '2025-11-30 03:05:53'),
(2, 'Infromasi Tambahan', 'news.jpg', '<h2>Selamat Datang, sysadmin!</h2><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. Anda telah login sebagai **Administrator Sistem**. Gunakan menu **Manajemen Sistem** untuk mengelola Pengguna, Katalog, dan Ajuan ISBN.</p><p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><p>&nbsp;</p>', 2, '2025-11-30 03:06:53'),
(3, 'Penyesuaian Pengajuan ISBN Amikom Press', 'news.jpg', '<h3>Penyesuaian Pengajuan ISBN Amikom Press</h3><p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 2, '2025-11-30 03:47:34'),
(4, 'Dummy Text Sample', '1764548603_692cdffb45d3e.jpeg', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,</p>', 1, '2025-12-01 07:23:23'),
(5, 'Raja Jawa Menulis Biografi Palsu', '1764549957_692ce54523dce.jpg', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,</p><blockquote><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.&nbsp;</p></blockquote><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,</p>', 1, '2025-12-01 07:45:57'),
(6, 'Test Berita Utama', '1764559505_692d0a91a0044.jpg', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,</p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,</p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p><ul><li>&nbsp;Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus.&nbsp;</li><li>Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.&nbsp;</li></ul><p>Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,</p>', 1, '2025-12-01 10:25:05');

-- --------------------------------------------------------

--
-- Table structure for table `isbn_submissions`
--

DROP TABLE IF EXISTS `isbn_submissions`;
CREATE TABLE IF NOT EXISTS `isbn_submissions` (
  `id_isbn` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `judul_buku` varchar(255) NOT NULL,
  `penulis_lain` varchar(255) DEFAULT NULL,
  `isbn_number` varchar(20) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `jumlah_halaman` int NOT NULL,
  `edisi` varchar(50) DEFAULT NULL,
  `sinopsis` text NOT NULL,
  `naskah` varchar(255) NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `status_ajuan` enum('Diajukan','Dalam Proses','Terbit','Ditolak') NOT NULL DEFAULT 'Diajukan',
  `catatan_admin` text,
  `submitted_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_isbn`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `isbn_submissions`
--

INSERT INTO `isbn_submissions` (`id_isbn`, `id_user`, `judul_buku`, `penulis_lain`, `isbn_number`, `barcode`, `jumlah_halaman`, `edisi`, `sinopsis`, `naskah`, `cover`, `lampiran`, `status_ajuan`, `catatan_admin`, `submitted_at`, `updated_at`) VALUES
(1, 3, 'Pengantar Jaringan Komputer Modern', 'Budi Santoso', NULL, NULL, 350, '1', 'Buku ini membahas dasar-dasar jaringan komputer, mulai dari model OSI hingga konfigurasi router dasar.', 'naskah_jaringan_user1_20251125a.docx', 'default.jpg', NULL, 'Diajukan', NULL, '2025-11-25 08:53:25', '2025-11-30 01:10:05'),
(2, 4, 'Filosofi Coding dan Debugging', 'Cocot Markesot', '978-202-4356-01-1', 'barcode_4_20251130154721.png', 180, '1', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 'naskah_filosofi_user1_20251120.docx', 'default.jpg', NULL, 'Terbit', 'Sedang dalam tahap verifikasi metadata di Perpusnas. Perkiraan selesai 3 hari kerja.', '2025-11-20 10:00:00', '2025-12-01 08:09:34'),
(3, 5, 'Kiat Sukses Investasi Digital', 'Diana Purnama', '978-602-8800-01-1', 'barcode_5_20251129191639.jpg', 250, '1', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 'naskah_investasi_user1_20251115.docx', 'default.jpg', NULL, 'Terbit', 'ISBN berhasil diterbitkan. Silakan cek detail pengajuan untuk nomor ISBN resmi. Jangan lupa! ubah bagian Cover dengan menambahkan Nomor ISBN di Pojok Atas Kanan, dan Barcode di Cover belakang bagian Bawah Kiri', '2025-11-15 15:30:00', '2025-12-01 08:09:48'),
(4, 3, 'Dasar-Dasar Fotografi Smartphone', NULL, NULL, NULL, 120, NULL, 'Teknik-teknik dasar pengambilan gambar hanya dengan menggunakan smartphone.', 'naskah_fotografi_user1_20251110.docx', 'default.jpg', NULL, 'Ditolak', 'Ajuan ditolak. Mohon lampirkan Daftar Isi dan Sampul Preliminar untuk verifikasi kelengkapan.', '2025-11-10 09:00:00', NULL),
(5, 5, 'Membuat Aplikasi Web dengan Laravel 11', 'Agus Salim', '', NULL, 400, NULL, 'Buku panduan langkah demi langkah untuk membangun aplikasi kompleks menggunakan framework Laravel versi terbaru.', 'naskah_laravel_user1_20251124.docx', 'default.jpg', NULL, 'Dalam Proses', NULL, '2025-11-25 08:53:25', '2025-11-26 05:11:55'),
(6, 6, 'Jurnalistik Data di Era Big Data', NULL, NULL, NULL, 280, NULL, 'Cara mengolah data besar menjadi narasi jurnalistik yang informatif dan akurat.', 'naskah_jurnal_user1_20251118.docx', 'default.jpg', NULL, 'Dalam Proses', 'Naskah sudah dikirim ke tim editing untuk pengecekan format.', '2025-11-18 11:45:00', NULL),
(7, 5, 'Panduan Praktis Desain UX/UI', 'Siti Rahma', '978-602-8800-02-8', 'barcode_5_20251129191802.jpg', 310, '1', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 'naskah_uxui_user1_20251105.docx', 'default.jpg', NULL, 'Terbit', 'Proses selesai. ISBN telah dikeluarkan.', '2025-11-05 16:10:00', '2025-12-01 08:09:58'),
(8, 3, 'Eksplorasi Deep Learning dengan Python', '', NULL, NULL, 450, '1', 'Membahas konsep jaringan saraf tiruan yang kompleks dan implementasinya.', 'naskah_deep_user1_20251125b.docx', 'default.jpg', NULL, 'Diajukan', NULL, '2025-11-25 08:53:25', '2025-11-30 01:10:24'),
(9, 3, 'Manajemen Proyek Agile untuk Pemula', NULL, NULL, NULL, 200, NULL, 'Pengenalan framework Scrum dan Kanban dalam pengelolaan proyek IT.', 'naskah_agile_user1_20251112.docx', 'default.jpg', NULL, 'Dalam Proses', 'Menunggu konfirmasi final dari kepala penerbit.', '2025-11-12 08:30:00', NULL),
(10, 4, 'Pengembangan Game Indie dengan Unity', 'Rizky Putra', '', NULL, 380, NULL, 'Langkah-langkah pembuatan game 2D dan 3D sederhana menggunakan Unity.', 'naskah_game_user1_20251125c.docx', 'default.jpg', NULL, 'Dalam Proses', NULL, '2025-11-25 08:53:25', '2025-11-26 05:12:08'),
(11, 6, 'Kamasutra Banyumas', 'Bawuk Esti', '', NULL, 230, NULL, 'Kamasutra Banyumas adalah merupakan bagian warisan dari kakek sugiono diwilayah banyumas, berisikan tentang istilah gawuk yang menjadi label pada wanita yang dijadikan budak sexual oleh para kaum bangsawan jawa.', 'naskah_6_20251125185842.docx', 'default.jpg', NULL, 'Diajukan', NULL, '2025-11-26 01:58:42', '2025-11-26 05:23:27'),
(12, 4, 'Sengsara Membawa Nikmat', 'Tiko Pangabean, Sularsih', '', NULL, 300, 'Revisi 1', 'Sengsara membawa nikmat ini buku dongeng yang mengisahkan anak rantau di wilayah sumatera yang menjadi tokoh adat di sebuah desa dan memiliki pengaruh kuat untuk perubahan kultur budaya yang ada disana.', 'naskah_4_20251128001113.docx', 'cover_4_20251128001113.jpg', 'lampiran_4_20251128001113.pdf', 'Diajukan', 'Sedang dalam Proses Ajuan, Menunggu 14 Hari Kerja', '2025-11-28 07:11:13', '2025-11-28 07:29:14'),
(13, 5, 'Bermain Phyton Itu Menyenangkan', 'Andre Silalahi', '', NULL, 250, '12', 'Phyton adalah bahasa universal dalam bidang teknologi informasi. Banyak yang menggunakan phyton sebagai bentuk penyesuaian terhadap teknologi artificial intellegence. Silahkan Baca kalau mampu', 'naskah_5_20251128010453.docx', 'default.jpg', 'lampiran_5_20251128010453.pdf', 'Dalam Proses', 'Sedang dalam proses ajuan ke perpusnas Tanggal 30 November 2025', '2025-11-28 08:04:53', '2025-11-30 22:41:19'),
(14, 7, 'Narsistik: Fenomena Sosial dan Budaya', 'Irfan Santiko', NULL, NULL, 250, '1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sit amet blandit nulla. Cras eleifend quam vitae metus maximus congue. Integer in diam bibendum urna sagittis rutrum. Curabitur non congue ex. Duis et molestie ligula. Mauris sit amet urna vestibulum, volutpat est tristique, lacinia est. Fusce finibus vitae eros ullamcorper maximus. Ut eu eleifend nulla, ac convallis risus. Praesent sit amet tincidunt risus. Quisque suscipit vehicula lorem non tristique.\r\nVestibulum nec libero risus. Mauris posuere luctus quam, eget accumsan justo lobortis non. Vivamus sed feugiat lectus, a sodales orci. In eu mauris at tellus volutpat ultrices. Nunc et gravida sem. Nullam quam nibh, varius id dignissim sed, tempor mattis tortor. Donec mollis, arcu in rhoncus dapibus, lorem diam condimentum lectus, sit amet ullamcorper metus mauris a dui. Quisque posuere elit blandit congue luctus. Nullam posuere, tortor ac finibus dignissim, nunc purus malesuada elit, vestibulum tristique nisi dui tempus dui. Phasellus a congue leo. Donec in est dictum odio auctor tempus vel in metus.', 'naskah_7_20251130033302.docx', 'default.jpg', 'lampiran_7_20251130033302.pdf', 'Diajukan', NULL, '2025-11-30 10:33:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `katalog`
--

DROP TABLE IF EXISTS `katalog`;
CREATE TABLE IF NOT EXISTS `katalog` (
  `id_katalog` int NOT NULL AUTO_INCREMENT,
  `id_isbn` int NOT NULL,
  `judul_katalog` varchar(255) NOT NULL,
  `penulis_katalog` varchar(255) NOT NULL,
  `sinopsis_katalog` text,
  `cover_katalog` varchar(255) DEFAULT NULL,
  `isbn_number` varchar(20) NOT NULL,
  `created_by_user_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_katalog`),
  UNIQUE KEY `uk_id_isbn` (`id_isbn`),
  KEY `created_by_user_id` (`created_by_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `katalog`
--

INSERT INTO `katalog` (`id_katalog`, `id_isbn`, `judul_katalog`, `penulis_katalog`, `sinopsis_katalog`, `cover_katalog`, `isbn_number`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(6, 7, 'Panduan Praktis Desain UX/UI', 'Siti Rahma', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 'default.jpg', '978-602-8800-02-8', 1, '2025-11-30 22:40:13', '2025-12-01 08:10:24'),
(5, 3, 'Kiat Sukses Investasi Digital', 'Diana Purnama', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 'default.jpg', '978-602-8800-01-1', 1, '2025-11-30 22:32:47', '2025-12-01 08:10:29'),
(7, 2, 'Filosofi Coding dan Debugging', 'Cocot Markesot Bekukur', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 'default.jpg', '978-202-4356-01-1', 1, '2025-11-30 22:48:25', '2025-12-01 08:10:18');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_role_id` int NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message_content` text NOT NULL,
  `read_status` enum('read','unread') DEFAULT 'unread',
  `is_sent_by_role` enum('Admin','Operator','Member') NOT NULL,
  `sent_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `receiver_id` int DEFAULT NULL,
  PRIMARY KEY (`id_message`),
  KEY `sender_id` (`sender_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id_message`, `sender_id`, `receiver_role_id`, `subject`, `message_content`, `read_status`, `is_sent_by_role`, `sent_at`, `receiver_id`) VALUES
(1, 4, 2, 'Konfirmasi Terbitan', 'Berapa lama ya saya harus menunggu ISBN keluar?', 'read', 'Member', '2025-11-28 20:40:48', NULL),
(2, 4, 1, 'tes', 'tes pesan', 'read', 'Member', '2025-11-28 20:51:32', NULL),
(3, 4, 1, 'tes', 'tes pesan', 'read', 'Member', '2025-11-28 20:52:00', NULL),
(4, 1, 3, 'Re: tes', 'ok pesan di terima', 'read', '', '2025-11-29 06:01:03', NULL),
(5, 2, 3, 'Re: Konfirmasi Terbitan', 'Ok siap.. permohonan sudah di proses bro', 'read', 'Operator', '2025-11-29 06:03:35', NULL),
(6, 3, 1, 'tes ani sulastri', 'Hello ini pesan baru dari Ani Sulastri', 'read', 'Member', '2025-11-30 01:43:28', NULL),
(7, 3, 2, 'Proses ISBN Ani Sulastri', 'Halo bagaimana proses ajuan ISBN saya? sudah 14 hari lebih belum ada informasi apapun... mohon diperiksa ya', 'read', 'Member', '2025-11-30 01:48:13', NULL),
(8, 2, 3, 'Re: Proses ISBN Ani Sulastri', 'Ya saat ini masih dalam proses, kami akan bantu untuk notif ke Perpusnas, mohon ditunggu, setelah ini kami segera mengirimkan notifikasi pesan ke anda melalui pesan email', 'read', 'Operator', '2025-11-30 01:49:22', 3),
(9, 2, 3, 'Re: Proses ISBN Ani Sulastri', 'Halo Ani Proses ajuan ISBN Anda telah selesai di proses, silahkan dapat melihatnya di menu Daftar Ajuan ISBN Anda, terima kasih', 'read', 'Operator', '2025-11-30 01:50:56', 3),
(10, 5, 1, 'Klarifikasi Data Buku', 'Halo admin, bisakah saya mengubah data buku saya untuk mengganti penulis yang tertera sebagai kontributor? karena ada yang disklaim di sana... terima kasih', 'read', 'Member', '2025-11-30 08:58:54', NULL),
(11, 5, 2, 'Pergantian nama', 'Halo operator saya ingin merubah pola nama ajuan... bagaimana porsesnya?', 'unread', 'Member', '2025-11-30 09:00:28', NULL),
(12, 7, 1, 'Halo', 'Terima kasih telah mengajak saya bergabung menjadi penulis buku.', 'read', 'Member', '2025-11-30 10:31:03', NULL),
(13, 7, 2, 'Konfirmasi terbitan', 'Halo Admin, untuk terbitan saya kira-kira memakan waktu berapa lama ya?', 'unread', 'Member', '2025-11-30 20:34:13', NULL),
(14, 1, 3, 'Re: Halo', 'Halo Ria, Selamat datang di Amikom Press, silahkan jika anda memiliki naskah yang akan diajukan, jangan sungkan bertanya...', 'unread', '', '2025-11-30 23:13:48', 7);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Operator'),
(3, 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text,
  `no_telp` varchar(20) DEFAULT NULL,
  `id_role` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `foto_profil` varchar(255) DEFAULT 'default.png',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `id_role` (`id_role`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `email`, `alamat`, `no_telp`, `id_role`, `is_active`, `foto_profil`, `created_at`) VALUES
(1, 'sysadmin', 'd46c1df4a7a94474e5e9a82ded4ad2313ebf647aef6b7fc04d1d2437b58d391f', 'Irfan Santiko', 'admin@press.ac.id', 'Purwokerto', '08126652775', 1, 1, 'default.png', '2025-11-24 20:41:25'),
(2, 'operator1', 'd46c1df4a7a94474e5e9a82ded4ad2313ebf647aef6b7fc04d1d2437b58d391f', 'Ranggi Praharaningtyas Aji', 'ranggi@amikompurwokerto.ac.id', 'Tambaksogra, Kab. Banyumas', '08125536613', 2, 1, 'default.png', '2025-11-24 20:41:25'),
(3, 'niani', 'd46c1df4a7a94474e5e9a82ded4ad2313ebf647aef6b7fc04d1d2437b58d391f', 'Ani Sulastri', 'member@press.ac.id', 'Banjarnegara', '08542308186', 3, 1, 'default.png', '2025-11-24 20:41:25'),
(4, 'sutikno', 'd46c1df4a7a94474e5e9a82ded4ad2313ebf647aef6b7fc04d1d2437b58d391f', 'Tiko Pangabean', 'tyco.sandtyco@yahoo.co.id', 'Perkutut Timur No 9 Cilacap', '081678377262', 3, 1, 'default.png', '2025-11-25 00:19:01'),
(5, 'mamamia', 'd46c1df4a7a94474e5e9a82ded4ad2313ebf647aef6b7fc04d1d2437b58d391f', 'Jono Sujono', 'parjono@gmail.com', 'Lengkong Waru, Cilacap', '08182827366', 3, 1, 'default.png', '2025-11-25 00:55:57'),
(6, 'budbud', 'd46c1df4a7a94474e5e9a82ded4ad2313ebf647aef6b7fc04d1d2437b58d391f', 'Budi Anggoro', 'budbud@gmail.com', 'Nusakambangan Island', '081663728873', 3, 1, 'default.png', '2025-11-26 01:55:53'),
(7, 'aryantini', 'd46c1df4a7a94474e5e9a82ded4ad2313ebf647aef6b7fc04d1d2437b58d391f', 'Ria Aryantini', 'aryantini@gmail.com', 'Dusun Gewok Karanggintung Sumbang Banyumas', '081552418601', 3, 1, 'default.png', '2025-11-30 10:30:12');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
