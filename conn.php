<?php
// conn.php

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Ganti dengan user database Anda
define('DB_PASS', '');     // Ganti dengan password database Anda
define('DB_NAME', 'press_amikom'); // Ganti dengan nama database Anda

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set mode error PDO ke Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set mode fetch default ke Array Asosiatif
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // echo "Koneksi berhasil!"; // Hapus baris ini setelah pengujian

} catch (PDOException $e) {
    // Tampilkan pesan error dan hentikan skrip jika koneksi gagal
    die("Koneksi Database Gagal: " . $e->getMessage());
}