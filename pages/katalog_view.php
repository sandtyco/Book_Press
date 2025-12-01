<?php
// pages/katalog_view.php (dengan DataTables)

session_start();
require_once '../conn.php'; 
require_once '../function.php'; 

// Akses hanya untuk Role 1 (Admin) dan Role 2 (Operator)
check_role_access([1, 2]);

// --- Bagian Logika Hapus (Delete) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // ... (Logika Delete sama seperti sebelumnya) ...
    $id_katalog = $_GET['id'];
    try {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM katalog WHERE id_katalog = :id");
        $stmt->bindParam(':id', $id_katalog, PDO::PARAM_INT);
        if ($stmt->execute()) {
            set_flashdata('success', 'Data katalog berhasil dihapus.');
        } else {
            set_flashdata('error', 'Gagal menghapus data katalog.');
        }
    } catch (PDOException $e) {
        set_flashdata('error', 'Kesalahan Database: ' . $e->getMessage());
    }
    header('Location: katalog_view.php');
    exit;
}

// --- Bagian Pengambilan Data Katalog ---
try {
    global $conn;
    
    $sql = "SELECT k.*, u.username as nama_user_pengaju
            FROM katalog k
            JOIN isbn_submissions i ON k.id_isbn = i.id_isbn
            JOIN users u ON i.id_user = u.id_user 
            ORDER BY k.created_at DESC";
            
    $stmt = $conn->query($sql);
    $katalog_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Katalog View DB Error: " . $e->getMessage());
    $katalog_data = [];
    set_flashdata('error', 'Gagal memuat data katalog.');
}

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h1 class="page-header"><span class="glyphicon glyphicon-book"></span> Manajemen Katalog</h1>
    <p class="well"><span class="label label-primary"><b>PETUNJUK...!</b></span> Manajemen katalog ini merupakan daftar katalog khusus untuk ISBN yang telah terbit. Dengan menambahkan dalam tabel Manajemen Katalog ini, maka akan muncul pada halaman publik.</p>

    <?php display_flashdata(); ?>

    <p>
        <a href="katalog_add.php" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Tambah Katalog</a>
    </p>

    <div class="table-responsive">
        <table id="katalogTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cover</th>
                    <th>ISBN</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Pengaju Asli</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($katalog_data)): ?>
                    <tr><td colspan="7" class="text-center">Belum ada data katalog yang ditambahkan.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($katalog_data as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            
                            <td>
                                <?php if (!empty($row['cover_katalog'])): ?>
                                    <img src="../assets/uploads/cover/<?= htmlspecialchars($row['cover_katalog']); ?>" alt="Cover Buku" style="width: 60px; height: auto;">
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            
                            <td><strong><?= htmlspecialchars($row['isbn_number']); ?></strong></td>
                            <td><?= htmlspecialchars($row['judul_katalog']); ?></td>
                            <td><?= htmlspecialchars($row['penulis_katalog']); ?></td>
                            <td><?= htmlspecialchars($row['nama_user_pengaju']); ?></td>
                            <td>
                                <a href="katalog_edit.php?id=<?= $row['id_katalog']; ?>" class="btn btn-warning btn-xs" title="Edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                <a href="?action=delete&id=<?= $row['id_katalog']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Anda yakin ingin menghapus katalog ini?')" title="Hapus"><span class="glyphicon glyphicon-trash"></span> Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>