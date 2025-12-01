<?php
// pages/dash_member.php

require_once '../conn.php'; 
require_once '../function.php'; 

// HANYA Role 3 (Member) yang diizinkan
check_role_access([3]);

$current_user_id = $_SESSION['id_user'];
$member_data = []; 

// --- AMBIL DATA PROFIL PENGGUNA DARI DATABASE ---
try {
    global $conn;
    // PENGUBAHAN QUERY DISINI: menggunakan no_telp, alamat, dan foto_profil
    $sql_user = "SELECT nama_lengkap, email, no_telp, alamat, foto_profil FROM users WHERE id_user = :id";
    
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bindParam(':id', $current_user_id, PDO::PARAM_INT);
    $stmt_user->execute();
    $member_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$member_data) {
        set_flashdata('error', 'Data profil pengguna tidak ditemukan.');
        header('Location: logout.php'); 
        exit;
    }
} catch (PDOException $e) {
    // Jika masih terjadi error, kemungkinan koneksi atau typo di query/kolom lain
    error_log("Failed to load member profile data: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat data profil. Cek log error database Anda.');
}


// --- 1. AMBIL DATA AJUAN & STATISTIK dari tabel isbn_submissions ---
$stats = [
    'total' => 0,
    'diproses' => 0,
    'terbit' => 0
];
$submissions = []; 

try {
    global $conn;
    // Statistik Ajuan (MENGGUNAKAN STRING ENUM)
    $sql_stats = "SELECT 
                    SUM(CASE WHEN id_user = :id THEN 1 ELSE 0 END) AS total,
                    SUM(CASE WHEN id_user = :id AND status_ajuan = 'Dalam Proses' THEN 1 ELSE 0 END) AS diproses,
                    SUM(CASE WHEN id_user = :id AND status_ajuan = 'Terbit' THEN 1 ELSE 0 END) AS terbit
                  FROM isbn_submissions 
                  WHERE id_user = :id"; 
    
    $stmt_stats = $conn->prepare($sql_stats);
    $stmt_stats->bindParam(':id', $current_user_id, PDO::PARAM_INT);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

    // Daftar Semua Ajuan
    $sql_list = "SELECT id_isbn, judul_buku, submitted_at, status_ajuan 
                 FROM isbn_submissions 
                 WHERE id_user = :id 
                 ORDER BY submitted_at DESC";
    
    $stmt_list = $conn->prepare($sql_list);
    $stmt_list->bindParam(':id', $current_user_id, PDO::PARAM_INT);
    $stmt_list->execute();
    $submissions = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Dashboard DB Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat statistik ajuan ISBN.');
}

// Fungsi helper untuk menampilkan status
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
    <h1 class="page-header"><span class="glyphicon glyphicon-home"></span> Dashboard Member</h1>

    <?php display_flashdata(); ?>

    <div class="row">
        <div class="col-md-7">
            <div class="alert alert-info" style="margin-top: 5px;">
                <h3>Halo, selamat datang penulis hebat kami:</h3>
                <h1>"<?= htmlspecialchars($member_data['nama_lengkap'] ?? 'Member'); ?>"</h1>
                <p>Gunakan dashboard member ini untuk memantau status pengajuan ISBN Anda.</p>
                <a href="isbn_add.php" class="btn btn-success btn-sm">
                    <span class="glyphicon glyphicon-send"></span> Ajukan ISBN Baru
                </a>
            </div>
        </div>

        <div class="col-md-5">
            <div class="panel panel-info" style="margin-top: 5px;">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-user"></span> Profil Anda</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-4 text-center">
                            <img src="../assets/img/user/<?= htmlspecialchars($member_data['foto_profil'] ?? 'default.jpg'); ?>" 
                                 class="img-polaroid" 
                                 style="width: 100px; height: 100px; object-fit: cover; margin-bottom: 10px;">
                        </div>
                        <div class="col-xs-8">
                            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($member_data['nama_lengkap'] ?? 'N/A'); ?></p>
                            <p><strong>Alamat:</strong> <?= htmlspecialchars($member_data['alamat'] ?? 'N/A'); ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($member_data['email'] ?? 'N/A'); ?></p>
                            <p><strong>Telp:</strong> <?= htmlspecialchars($member_data['no_telp'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <hr>
    
    <h3>Statistik Pengajuan ISBN</h3>
    <div class="row">
        
        <div class="col-md-4">
            <div class="panel panel-warning">
                <div class="panel-body text-center">
                    <h1 class="pull-left"><span class="glyphicon glyphicon-inbox"></span></h1>
                    <h2 class="text-right"><?= $stats['total'] ?? 0; ?></h2>
                    <h4 class="text-right">Total Pengajuan</h4>
                </div>
                <div class="panel-footer">
                    <a href="#table-submissions">Lihat Detail Ajuan &raquo;</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-body text-center">
                    <h1 class="pull-left"><span class="glyphicon glyphicon-time"></span></h1>
                    <h2 class="text-right"><?= $stats['diproses'] ?? 0; ?></h2>
                    <h4 class="text-right">Ajuan Diproses</h4>
                </div>
                <div class="panel-footer">
                    <a href="#table-submissions">Lihat Ajuan Diproses &raquo;</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-info">
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

    <h3 id="table-submissions">Daftar Pengajuan ISBN Anda</h3>
    
    <div class="table-responsive">
        <table id="ajuanTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pengajuan</th>
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
                            <td><?= date('d M Y, H:i', strtotime($submission['submitted_at'])); ?></td>
                            <td><?= get_status_badge($submission['status_ajuan']); ?></td>
                            <td>
                                <a href="isbn_view.php?id=<?= $submission['id_isbn']; ?>" class="btn btn-info btn-xs">
                                    <span class="glyphicon glyphicon-search"></span> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Anda belum memiliki pengajuan ISBN yang terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#ajuanTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "order": [[ 2, "desc" ]], // Urutkan berdasarkan Tanggal Pengajuan terbaru
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" 
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>