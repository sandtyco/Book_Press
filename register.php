<?php
// register.php (Action Handler Registrasi, di ROOT DIRECTORY /press)

session_start();

require_once 'conn.php'; 
require_once 'function.php'; 

// Pastikan hanya bisa diakses via POST request dari form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: member.php');
    exit;
}

// Ambil data dari form
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$re_password = $_POST['re_password'] ?? '';
$no_telp = trim($_POST['no_telp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$captcha_input = $_POST['captcha_input'] ?? '';

// --- Validasi PHP (Lebih Detail) ---
if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password) || empty($no_telp)) {
    set_flashdata('error', 'Semua field dengan tanda (*) wajib diisi.');
    header('Location: member.php');
    exit;
}

if ($password !== $re_password) {
    set_flashdata('error', 'Konfirmasi password tidak cocok.');
    header('Location: member.php');
    exit;
}

// --- 0. Verifikasi CAPTCHA ---
if (!isset($_SESSION['captcha_result']) || $_SESSION['captcha_result'] != $captcha_input) {
    unset($_SESSION['captcha_result']); 
    set_flashdata('error', 'Kode verifikasi (CAPTCHA) salah!');
    header('Location: member.php');
    exit;
}
unset($_SESSION['captcha_result']); // Hapus CAPTCHA setelah berhasil

try {
    global $conn;

    // 1. Cek duplikasi username dan email
    $sql_check = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':username', $username);
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();
    
    if ($stmt_check->fetchColumn() > 0) {
        set_flashdata('error', 'Username atau Email sudah terdaftar.');
        header('Location: member.php');
        exit;
    }

    // 2. HASH Password menggunakan SHA-256
    $hashed_password = hash_password($password);

    // 3. Simpan data user baru
    $sql_insert = "INSERT INTO users 
                   (id_role, username, password, nama_lengkap, email, no_telp, alamat, is_active, created_at) 
                   VALUES (3, :username, :password, :nama, :email, :telp, :alamat, 1, NOW())"; // id_role=3 (Member), is_active=1

    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bindParam(':username', $username);
    $stmt_insert->bindParam(':password', $hashed_password);
    $stmt_insert->bindParam(':nama', $nama_lengkap);
    $stmt_insert->bindParam(':email', $email);
    $stmt_insert->bindParam(':telp', $no_telp);
    $stmt_insert->bindParam(':alamat', $alamat);
    
    if ($stmt_insert->execute()) {
        set_flashdata('success', 'Registrasi berhasil! Silakan login.');
        header('Location: login.php');
        exit;
    } else {
        set_flashdata('error', 'Registrasi gagal. Coba lagi.');
    }

} catch (PDOException $e) {
    error_log("Register DB Error: " . $e->getMessage());
    set_flashdata('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
}

header('Location: member.php'); // Kembali ke form jika ada kegagalan selain error_log
exit;
?>