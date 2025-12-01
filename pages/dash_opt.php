<?php
// pages/dash_opt.php

require_once '../conn.php'; 
require_once '../function.php'; 

// HANYA Role 2 (Operator) yang diizinkan
check_role_access([2]);

$current_user_id = $_SESSION['id_user'];
$operator_data = []; // Data Operator

// --- AMBIL DATA PROFIL PENGGUNA (Operator) DARI DATABASE ---
try {
    global $conn;
    // Mengambil data Operator yang sedang login
    $sql_user = "SELECT nama_lengkap, email, no_telp, alamat, foto_profil FROM users WHERE id_user = :id";
    
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bindParam(':id', $current_user_id, PDO::PARAM_INT);
    $stmt_user->execute();
    $operator_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$operator_data) {
        set_flashdata('error', 'Data profil pengguna tidak ditemukan.');
        header('Location: logout.php'); 
        exit;
    }
} catch (PDOException $e) {
    error_log("Failed to load operator profile data: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat data profil.');
}


// --- 1. AMBIL DATA AJUAN & STATISTIK SISTEM ---
$stats = [
    'total' => 0,
    'diproses' => 0,
    'terbit' => 0
];
$submissions = []; 

try {
    // A. Statistik Ajuan SELURUH SISTEM (TANPA WHERE id_user)
    $sql_stats = "SELECT 
                    SUM(CASE WHEN status_ajuan IS NOT NULL THEN 1 ELSE 0 END) AS total,
                    SUM(CASE WHEN status_ajuan = 'Dalam Proses' THEN 1 ELSE 0 END) AS diproses,
                    SUM(CASE WHEN status_ajuan = 'Terbit' THEN 1 ELSE 0 END) AS terbit
                  FROM isbn_submissions"; 
    
    $stmt_stats = $conn->prepare($sql_stats);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

    // B. Daftar Semua Ajuan dari Semua User (JOIN users)
    $sql_list = "SELECT 
                    i.id_isbn, 
                    i.judul_buku, 
                    i.submitted_at, 
                    i.status_ajuan, 
                    u.nama_lengkap 
                 FROM isbn_submissions i 
                 JOIN users u ON i.id_user = u.id_user 
                 ORDER BY i.submitted_at DESC";
    
    $stmt_list = $conn->prepare($sql_list);
    $stmt_list->execute();
    $submissions = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Dashboard DB Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat statistik ajuan ISBN sistem.');
}

// Fungsi helper untuk menampilkan status (sama seperti dash_member)
function get_status_badge($status) {
    switch ($status) {
        case 'Diajukan':
            return '<span class="label label-info">Diajukan</span>';
        case 'Dalam Proses':
            return '<span class="label label-warning">Dalam Proses</span>';
        case 'Terbit':
            return '<span class="label label-success">Terbit</span>';
        case 'Ditolak':
            return '<span class="label label-danger">Ditolak</span>';
        default:
            return '<span class="label label-default">Tidak Diketahui</span>';
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-dashboard"></span> Dashboard Operator</h1>

    <?php display_flashdata(); ?>

    <div class="row">
        <div class="col-md-7">
            <div class="alert alert-info" style="margin-top: 5px;">
                <h2>👋 Selamat Datang, **<?= htmlspecialchars($operator_data['nama_lengkap'] ?? 'Operator'); ?>**!</h2>
                <p>Anda memiliki akses penuh untuk memproses dan memantau semua pengajuan ISBN yang masuk.</p>
            </div>
        </div>
        <div class="col-md-5" style="margin-top: 5px;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-user"></span> Profil Operator</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-4 text-center">
                            <img src="../assets/img/user/<?= htmlspecialchars($operator_data['foto_profil'] ?? 'default.jpg'); ?>" 
                                 class="img-circle" 
                                 style="width: 80px; height: 80px; object-fit: cover; margin-bottom: 10px;">
                        </div>
                        <div class="col-xs-8">
                            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($operator_data['nama_lengkap'] ?? 'N/A'); ?></p>
                            <p><strong>Alamat:</strong> <?= htmlspecialchars($operator_data['alamat'] ?? 'N/A'); ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($operator_data['email'] ?? 'N/A'); ?></p>
                            <p><strong>Telp:</strong> <?= htmlspecialchars($operator_data['no_telp'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <hr>
    
    <h3>Statistik Pengajuan ISBN (Seluruh Sistem)</h3>
    <div class="row">
        
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-body text-center">
                    <h1 class="pull-left"><span class="glyphicon glyphicon-inbox"></span></h1>
                    <h2 class="text-right"><?= $stats['total'] ?? 0; ?></h2>
                    <h4 class="text-right">Total Pengajuan</h4>
                </div>
                <div class="panel-footer">
                    <a href="#table-submissions">Lihat Detail Semua Ajuan &raquo;</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-warning">
                <div class="panel-body text-center">
                    <h1 class="pull-left"><span class="glyphicon glyphicon-time"></span></h1>
                    <h2 class="text-right"><?= $stats['diproses'] ?? 0; ?></h2>
                    <h4 class="text-right">Ajuan Dalam Proses</h4>
                </div>
                <div class="panel-footer">
                    <a href="#table-submissions">Lihat Ajuan Dalam Proses &raquo;</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-body text-center">
                    <h1 class="pull-left"><span class="glyphicon glyphicon-certificate"></span></h1>
                    <h2 class="text-right"><?= $stats['terbit'] ?? 0; ?></h2>
                    <h4 class="text-right">ISBN Terbit</h4>
                </div>
                <div class="panel-footer">
                    <a href="#table-submissions">Lihat ISBN Terbit &raquo;</a>
                </div>
            </div>
        </div>
        
    </div>
    
    <hr>

    <h3 id="table-submissions">Semua Pengajuan ISBN Masuk</h3>
    
    <div class="table-responsive">
        <table id="ajuanTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Buku</th>
                    <th>Pengaju</th> <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($submissions)): ?>
                    <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><?= $submission['id_isbn']; ?></td>
                            <td><?= htmlspecialchars($submission['judul_buku']); ?></td>
                            <td><?= htmlspecialchars($submission['nama_lengkap']); ?></td> <td><?= date('d M Y, H:i', strtotime($submission['submitted_at'])); ?></td>
                            <td><?= get_status_badge($submission['status_ajuan']); ?></td>
                            <td>
                                <a href="isbn_view.php?id=<?= $submission['id_isbn']; ?>" class="btn btn-info btn-xs">
                                    <span class="glyphicon glyphicon-search"></span> Lihat
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada pengajuan ISBN yang masuk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>