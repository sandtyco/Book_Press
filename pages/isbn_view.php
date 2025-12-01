<?php
// pages/isbn_view.php (Halaman Detail Pengajuan ISBN)

require_once '../conn.php'; 
require_once '../function.php'; 

check_role_access([1, 2, 3]);

$id_isbn = $_GET['id'] ?? 0;
$submission = null;

// --- FOLDER UPLOAD ---
$base_upload_path = '../assets/uploads/';
$download_dir_naskah = $base_upload_path . 'naskah/';
$download_dir_cover = $base_upload_path . 'cover/'; // NEW
$download_dir_lampiran = $base_upload_path . 'lampiran/'; // NEW
$download_dir_barcode = $base_upload_path . 'barcode/'; // NEW

$current_user_id = $_SESSION['id_user'];
$current_user_role = $_SESSION['id_role'];

// 1. Ambil Data Detail Ajuan dari Database (Otorisasi Data Dinamis)
try {
    global $conn;
    
    // 🚨 PERUBAHAN UTAMA: Tambahkan kolom baru ke SELECT
    $sql_select = "
        SELECT 
            id_isbn, id_user, judul_buku, penulis_lain, isbn_number, barcode, edisi, jumlah_halaman, sinopsis, 
            naskah, cover, lampiran, status_ajuan, catatan_admin, submitted_at, updated_at
        FROM isbn_submissions 
        WHERE id_isbn = :id
    ";

    if ($current_user_role == 3) {
        $sql_select .= " AND id_user = :user_id";
    }
    
    $stmt = $conn->prepare($sql_select);
    $stmt->bindParam(':id', $id_isbn, PDO::PARAM_INT);
    
    if ($current_user_role == 3) {
        $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$submission) {
        set_flashdata('error', 'Detail ajuan tidak ditemukan atau Anda tidak memiliki akses.');
        header('Location: isbn_list.php');
        exit;
    }
    
} catch (PDOException $e) {
    error_log("ISBN View DB Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat detail ajuan dari database.');
    header('Location: isbn_list.php');
    exit;
}

// 2. Persiapan Tampilan Status
$status_text = $submission['status_ajuan'];
$badge_class = '';
switch ($status_text) {
    case 'Diajukan': $badge_class = 'label label-info'; break; 
    case 'Dalam Proses': $badge_class = 'label label-warning'; break; 
    case 'Terbit': $badge_class = 'label label-success'; break; 
    case 'Ditolak': $badge_class = 'label label-danger'; break; 
    default: $badge_class = 'label label-default'; break; // Menggunakan label-default untuk abu-abu
}

// Persiapan Link Download
$naskah_file = htmlspecialchars($submission['naskah']);
$cover_file = htmlspecialchars($submission['cover']); // NEW
$lampiran_file = htmlspecialchars($submission['lampiran']); // NEW
$barcode_file = htmlspecialchars($submission['barcode']); // NEW

$link_naskah = $download_dir_naskah . $naskah_file;
$link_cover = $download_dir_cover . $cover_file; // NEW
$link_lampiran = $download_dir_lampiran . $lampiran_file; // NEW
$link_barcode = $download_dir_barcode . $barcode_file; // NEW

?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h1 class="page-header"><span class="glyphicon glyphicon-eye-open"></span> Detail Pengajuan ISBN</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Buku: <?= htmlspecialchars($submission['judul_buku']); ?></h6>
            <h2><span class="label <?= $badge_class; ?> p-2"><?= $status_text; ?></span></h2>
        </div>
        <div class="card-body">
            
            <?php display_flashdata(); ?>

            <div class="row">
                <div class="col-lg-7">
                    
                    <h5 class="text-primary mb-3">Data Naskah</h5>
                    <table class="table table-sm table-striped">
                        <tr>
                            <th style="width: 35%;">ID Ajuan</th>
                            <td>#<?= $submission['id_isbn']; ?></td>
                        </tr>
                        <tr>
                            <th>Judul Buku</th>
                            <td><?= htmlspecialchars($submission['judul_buku']); ?></td>
                        </tr>
                        <tr>
                            <th>Penulis Lain</th>
                            <td><?= !empty($submission['penulis_lain']) ? htmlspecialchars($submission['penulis_lain']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th>Edisi</th>
                            <td><?= htmlspecialchars($submission['edisi']); ?></td> </tr>
                        <tr>
                            <th>Jumlah Halaman</th>
                            <td><?= number_format($submission['jumlah_halaman']); ?> halaman</td>
                        </tr>
                        <tr>
                            <th>ISBN (Jika Terbit)</th>
                            <td>
                                <?php if ($status_text == 'Terbit' && !empty($submission['isbn_number'])): ?>
                                    <strong class="text-success"><?= htmlspecialchars($submission['isbn_number']); ?></strong>
                                    <?php if (!empty($submission['barcode'])): ?>
                                        <a href="<?= $link_barcode; ?>" class="btn btn-xs btn-success ml-2" download>
                                            <i class="fas fa-download"></i> Barcode
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-warning">Menunggu terbit...</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Ajuan</th>
                            <td><?= date('d M Y H:i:s', strtotime($submission['submitted_at'])); ?></td>
                        </tr>
                        <?php if ($submission['updated_at']): ?>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td><?= date('d M Y H:i:s', strtotime($submission['updated_at'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <h5 class="text-primary mt-4 mb-3">Sinopsis</h5>
                    <p class="border p-3 bg-light rounded"><?= nl2br(htmlspecialchars($submission['sinopsis'])); ?></p>
                </div>
                
                <div class="col-lg-5">
                    <h5 class="text-primary mb-3">Dokumen Pendukung</h5>
                    
                    <div class="p-3 mb-2 border rounded d-flex justify-content-between align-items-center">
                        <div><strong>Naskah (.docx):</strong></div>
                        <a href="<?= $link_naskah; ?>" class="btn btn-sm btn-info" download>
                            <i class="fas fa-download"></i> Unduh File
                        </a>
                    </div>
                    
                    <div class="p-3 mb-2 border rounded d-flex justify-content-between align-items-center">
                        <div><strong>Desain Cover (.jpg/.png):</strong></div>
                        <a href="<?= $link_cover; ?>" class="btn btn-sm btn-info" download>
                            <i class="fas fa-download"></i> Unduh File
                        </a>
                    </div>
                    
                    <div class="p-3 mb-3 border rounded d-flex justify-content-between align-items-center">
                        <div><strong>Lampiran (.pdf):</strong></div>
                        <a href="<?= $link_lampiran; ?>" class="btn btn-sm btn-info" download>
                            <i class="fas fa-download"></i> Unduh File
                        </a>
                    </div>
                    
                    <br>

                    <div class="alert alert-info">
                        <strong>Catatan Admin:</strong>
                        <p class="mb-0 mt-2">
                            <?= !empty($submission['catatan_admin']) ? nl2br(htmlspecialchars($submission['catatan_admin'])) : 'Belum ada catatan atau feedback dari administrator.'; ?>
                        </p>
                    </div>
                    
                    <hr>
                    
                    <h5 class="text-primary mb-3">Tindakan Cepat</h5>
                    
                    <?php 
                        $can_edit = ($current_user_role == 1 || $current_user_role == 2);
                        if ($current_user_role == 3 && $status_text == 'Diajukan') {
                            $can_edit = true;
                        }
                    ?>

                    <?php if ($can_edit): ?>
                        <a href="isbn_edit.php?id=<?= $submission['id_isbn']; ?>" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Ubah Detail Ajuan
                        </a>
                        <?php if ($current_user_role == 3): ?>
                           <p class="text-muted small mt-2 text-center">Anda dapat mengedit data selama status masih "Diajukan".</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="isbn_list.php" class="btn btn-primary btn-block">
                            <i class="fas fa-list"></i> Kembali ke Daftar Ajuan
                        </a>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
        
        <?php if ($current_user_role == 1 || $current_user_role == 2): ?>
        <div class="card-footer text-muted small">
            ID Pengaju: #<?= $submission['id_user']; ?> 
        </div>
        <?php endif; ?>
    
    </div>
</div>

<?php include '../includes/footer.php'; ?>