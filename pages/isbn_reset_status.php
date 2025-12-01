<?php
// pages/isbn_reset_status.php

require_once '../conn.php';
require_once '../function.php';

// 1. Otorisasi Akses Halaman: Hanya Role 1 dan 2 yang Boleh Mengakses
$allowed_roles = [1, 2];
check_role_access($allowed_roles);

// 2. Validasi Metode dan Data POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reset_status']) || !isset($_POST['id']) || !is_numeric($_POST['id'])) {
    set_flashdata('error', 'Permintaan tidak valid atau Anda tidak diizinkan mengakses.');
    header('Location: isbn_list.php');
    exit;
}

$id_isbn = (int)$_POST['id'];
$new_status = 'Diajukan';

// 3. Proses Update Database
try {
    global $conn;

    // Cek dulu status ajuan saat ini untuk memastikan integritas (optional, tapi baik)
    $stmt_check = $conn->prepare("SELECT status_ajuan FROM isbn_submissions WHERE id_isbn = :id");
    $stmt_check->bindParam(':id', $id_isbn, PDO::PARAM_INT);
    $stmt_check->execute();
    $submission = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$submission) {
        set_flashdata('error', 'Ajuan tidak ditemukan.');
        header('Location: isbn_list.php');
        exit;
    }
    
    // Pastikan hanya status 'Dalam Proses' yang bisa direset
    if ($submission['status_ajuan'] !== 'Dalam Proses') {
        set_flashdata('warning', 'Ajuan #'.$id_isbn.' tidak dapat direset karena statusnya saat ini adalah: ' . $submission['status_ajuan']);
        header('Location: isbn_list.php');
        exit;
    }

    // Lakukan Update: Reset status ke 'Diajukan'
    $stmt_update = $conn->prepare("
        UPDATE isbn_submissions 
        SET status_ajuan = :new_status, updated_at = NOW() 
        WHERE id_isbn = :id AND status_ajuan = 'Dalam Proses'
    ");
    $stmt_update->bindParam(':new_status', $new_status);
    $stmt_update->bindParam(':id', $id_isbn, PDO::PARAM_INT);
    
    if ($stmt_update->execute()) {
        if ($stmt_update->rowCount() > 0) {
            set_flashdata('success', 'Status ajuan ID #' . $id_isbn . ' berhasil direset menjadi "' . $new_status . '".');
        } else {
            set_flashdata('warning', 'Status ajuan ID #' . $id_isbn . ' tidak diubah.');
        }
    } else {
        set_flashdata('error', 'Gagal mereset status ajuan (kesalahan database).');
    }

} catch (PDOException $e) {
    error_log("ISBN Reset DB Error: " . $e->getMessage());
    set_flashdata('error', 'Terjadi kesalahan sistem saat memproses reset.');
}

// 4. Redirect kembali ke daftar
header('Location: isbn_list.php');
exit;
?>