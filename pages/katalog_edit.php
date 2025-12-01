<?php
// pages/katalog_edit.php

require_once '../conn.php'; 
require_once '../function.php'; 

check_role_access([1, 2]);

$id_katalog = $_GET['id'] ?? 0;
$katalog = null;

// 1. Ambil data katalog yang akan diedit
try {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM katalog WHERE id_katalog = :id");
    $stmt->bindParam(':id', $id_katalog, PDO::PARAM_INT);
    $stmt->execute();
    $katalog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$katalog) {
        set_flashdata('error', 'Data katalog tidak ditemukan.');
        header('Location: katalog_view.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Katalog Edit DB Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat data katalog.');
    header('Location: katalog_view.php');
    exit;
}

// 2. Proses Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul_baru = trim($_POST['judul_katalog'] ?? '');
    $penulis_baru = trim($_POST['penulis_katalog'] ?? '');
    $sinopsis_baru = trim($_POST['sinopsis_katalog'] ?? '');

    if (empty($judul_baru) || empty($penulis_baru)) {
        set_flashdata('error', 'Judul dan Penulis wajib diisi.');
        header('Location: katalog_edit.php?id=' . $id_katalog);
        exit;
    }

    try {
        $sql_update = "UPDATE katalog SET 
                       judul_katalog = :judul, 
                       penulis_katalog = :penulis, 
                       sinopsis_katalog = :sinopsis, 
                       updated_at = NOW() 
                       WHERE id_katalog = :id";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bindParam(':judul', $judul_baru);
        $stmt_update->bindParam(':penulis', $penulis_baru);
        $stmt_update->bindParam(':sinopsis', $sinopsis_baru);
        $stmt_update->bindParam(':id', $id_katalog, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            set_flashdata('success', 'Data katalog berhasil diperbarui.');
            header('Location: katalog_view.php');
            exit;
        } else {
            set_flashdata('error', 'Gagal memperbarui data katalog.');
        }
    } catch (PDOException $e) {
        error_log("Katalog Update DB Error: " . $e->getMessage());
        set_flashdata('error', 'Kesalahan Database saat update.');
    }
    header('Location: katalog_edit.php?id=' . $id_katalog);
    exit;
}

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h1 class="page-header"><span class="glyphicon glyphicon-pencil"></span> Edit Katalog: <?= htmlspecialchars($katalog['judul_katalog']); ?></h1>

    <?php display_flashdata(); ?>

    <form method="post" action="katalog_edit.php?id=<?= $id_katalog; ?>">
        
        <div class="form-group">
            <label>ISBN</label>
            <p class="form-control-static"><strong><?= htmlspecialchars($katalog['isbn_number']); ?></strong></p>
        </div>

        <div class="form-group">
            <label for="judul_katalog">Judul Buku</label>
            <input type="text" class="form-control" id="judul_katalog" name="judul_katalog" value="<?= htmlspecialchars($katalog['judul_katalog']); ?>" required>
        </div>

        <div class="form-group">
            <label for="penulis_katalog">Nama Penulis</label>
            <input type="text" class="form-control" id="penulis_katalog" name="penulis_katalog" value="<?= htmlspecialchars($katalog['penulis_katalog']); ?>" required>
        </div>

        <div class="form-group">
            <label for="sinopsis_katalog">Sinopsis</label>
            <textarea class="form-control" id="sinopsis_katalog" name="sinopsis_katalog" rows="5"><?= htmlspecialchars($katalog['sinopsis_katalog']); ?></textarea>
        </div>

        <a href="katalog_view.php" class="btn btn-default">Batal</a>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>