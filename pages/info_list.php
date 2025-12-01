<?php
// pages/info_list.php

require_once '../conn.php'; 
require_once '../function.php'; 

// HANYA Role 1 (Administrator) dan Role 2 (Operator) yang diizinkan
check_role_access([1, 2]);

$infos = [];
try {
    global $conn;
    // Mengambil nama_lengkap dari user yang memposting
    $sql = "SELECT i.*, u.nama_lengkap FROM info i JOIN users u ON i.posted_by = u.id_user ORDER BY i.posted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $infos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Info List DB Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat daftar informasi.');
}

// --- LOGIKA HAPUS (termasuk penghapusan file gambar) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_info = (int)$_GET['id'];
    $image_to_delete = null;
    $upload_dir = '../assets/img/info/'; 

    // 1. Ambil nama file gambar sebelum dihapus
    try {
        $stmt = $conn->prepare("SELECT image FROM info WHERE id_info = :id");
        $stmt->bindParam(':id', $id_info, PDO::PARAM_INT);
        $stmt->execute();
        $image_to_delete = $stmt->fetchColumn();
    } catch (PDOException $e) {
        // Log error, tapi lanjutkan penghapusan baris
    }

    // 2. Hapus baris di DB
    try {
        $sql = "DELETE FROM info WHERE id_info = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_info, PDO::PARAM_INT);
        if ($stmt->execute()) {
            
            // 3. Hapus file gambar dari server, HANYA jika bukan default 'news.jpg'
            if ($image_to_delete && $image_to_delete != 'news.jpg' && file_exists($upload_dir . $image_to_delete)) {
                unlink($upload_dir . $image_to_delete);
            }
            
            set_flashdata('success', 'Informasi berhasil dihapus.');
        } else {
            set_flashdata('error', 'Gagal menghapus informasi.');
        }
    } catch (PDOException $e) {
        set_flashdata('error', 'Terjadi kesalahan sistem saat menghapus.');
    }
    header('Location: info_list.php');
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-list-alt"></span> Daftar Informasi Publik</h1>

    <?php display_flashdata(); ?>
    
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="info_add.php" class="btn btn-primary pull-left">
                <span class="glyphicon glyphicon-plus"></span> Tambah Informasi
            </a>
        </div>
    </div>
    <br>
    <div class="table-responsive">
        <table id="infoTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 10%;">Gambar</th>
                    <th style="width: 35%;">Judul</th>
                    <th style="width: 15%;">Diposting Oleh</th>
                    <th style="width: 15%;">Waktu Posting</th>
                    <th style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($infos as $info): ?>
                <tr>
                    <td><?= $info['id_info']; ?></td>
                    <td>
                        <?php 
                            // Path gambar menggunakan setting user: /assets/img/info/
                            $image_src = '../assets/img/info/' . htmlspecialchars($info['image']);
                        ?>
                        <?php if ($info['image'] && file_exists($image_src)): ?>
                            <img src="<?= $image_src; ?>" 
                                 alt="Thumbnail" 
                                 style="width: 50px; height: auto; border-radius: 3px;">
                        <?php else: ?>
                            <span class="text-danger small">Broken</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($info['title']); ?></td>
                    <td><?= htmlspecialchars($info['nama_lengkap']); ?></td>
                    <td><?= date('d M Y, H:i', strtotime($info['posted_at'])); ?></td>
                    <td>
                        <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#viewModal"
                            data-title="<?= htmlspecialchars($info['title']); ?>"
                            data-content="<?= htmlspecialchars($info['content']); ?>"
                            data-image="<?= htmlspecialchars($info['image']); ?>">
                            Lihat
                        </button>
                        
                        <a href="info_edit.php?id=<?= $info['id_info']; ?>" class="btn btn-warning btn-xs">Edit</a>
                        
                        <a href="info_list.php?action=delete&id=<?= $info['id_info']; ?>" 
                           class="btn btn-danger btn-xs" 
                           onclick="return confirm('Apakah Anda yakin ingin menghapus informasi ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="viewModalLabel">Detail Berita: <span id="modal-title"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="text-left mb-3">
                        <img id="modal-image" src="" alt="Gambar Utama" style="max-width: 50%; height: auto; border-radius: 5px; margin-bottom: 15px; border: 1px solid #eee;">
                    </div>
                    
                    <div id="modal-content-body">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>