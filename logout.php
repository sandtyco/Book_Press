<?php
// logout.php

// Pastikan sesi sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua variabel sesi
$_SESSION = array();

// Jika menggunakan cookie sesi, hapus juga cookie tersebut
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Akhiri sesi
session_destroy();

// Arahkan pengguna kembali ke halaman login
header("Location: index.php?alert=logged_out");
exit;