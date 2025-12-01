<?php
// pages/katalog_add.php

require_once '../conn.php'; 
require_once '../function.php'; 

check_role_access([1, 2]);

$current_user_id = $_SESSION['id_user'];

// 1. Ambil ISBN yang sudah 'Terbit' dan BELUM ada di tabel katalog
try {
    global $conn;
    $sql_available = "
        SELECT i.id_isbn, i.judul_buku, i.penulis_lain, i.sinopsis, i.isbn_number, i.cover, u.username as nama_pengaju 
        FROM isbn_submissions i
        LEFT JOIN katalog k ON i.id_isbn = k.id_isbn
        JOIN users u ON i.id_user = u.id_user
        WHERE i.status_ajuan = 'Terbit' AND k.id_katalog IS NULL
        ORDER BY i.judul_buku ASC
    ";
    $stmt_available = $conn->query($sql_available);
    $available_isbn = $stmt_available->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Pesan error ini muncul jika SQL gagal
    error_log("Katalog Add DB Error: " . $e->getMessage());
    $available_isbn = [];
    set_flashdata('error', 'Gagal memuat daftar ISBN yang tersedia.'); 
}

// 2. Proses Form Submit (jika ada POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_isbn_selected = $_POST['id_isbn'] ?? 0;
    
    // Cari data ISBN yang dipilih untuk dimasukkan
    $selected_data = array_filter($available_isbn, function($item) use ($id_isbn_selected) {
        return $item['id_isbn'] == $id_isbn_selected;
    });
    $selected_data = reset($selected_data); 

    if ($selected_data) {
        // Ambil data dari tabel submissions untuk di-insert ke katalog
        $judul = $selected_data['judul_buku'];
        $penulis = $selected_data['penulis_lain']; 
        $sinopsis = $selected_data['sinopsis'];
        $isbn = $selected_data['isbn_number'];
        $cover_path = $selected_data['cover']; // ✅ Tambahan ini

        try {
            // ✅ KOREKSI QUERY INSERT
            $sql_insert = "INSERT INTO katalog (id_isbn, judul_katalog, penulis_katalog, sinopsis_katalog, cover_katalog, isbn_number, created_by_user_id, created_at) 
                           VALUES (:id_isbn, :judul, :penulis, :sinopsis, :cover, :isbn, :user_id, NOW())";
            
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bindParam(':id_isbn', $id_isbn_selected, PDO::PARAM_INT);
            $stmt_insert->bindParam(':judul', $judul);
            $stmt_insert->bindParam(':penulis', $penulis);
            $stmt_insert->bindParam(':sinopsis', $sinopsis);
            $stmt_insert->bindParam(':cover', $cover_path); // ✅ Tambahan Binding
            $stmt_insert->bindParam(':isbn', $isbn);
            $stmt_insert->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
            
            if ($stmt_insert->execute()) {
                set_flashdata('success', 'Katalog berhasil ditambahkan.');
                header('Location: katalog_view.php');
                exit;
            } else {
                set_flashdata('error', 'Gagal menyimpan data katalog.');
            }
        } catch (PDOException $e) {
            error_log("Katalog Insert DB Error: " . $e->getMessage());
            set_flashdata('error', 'Kesalahan Database: Data ISBN sudah ada atau error lainnya.');
        }
    } else {
        set_flashdata('error', 'ISBN tidak valid atau sudah masuk katalog.');
    }
    header('Location: katalog_add.php');
    exit;
}

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h1 class="page-header"><span class="glyphicon glyphicon-plus"></span> Tambah Katalog Baru</h1>

    <?php display_flashdata(); ?>

    <form method="post" action="katalog_add.php">
        <div class="form-group">
            <label for="id_isbn">Pilih ISBN yang Sudah Terbit (Hanya yang Belum Masuk Katalog)</label>
            <select class="form-control" name="id_isbn" id="id_isbn" required onchange="updateDetails(this.value)">
                <option value="">-- Pilih ISBN --</option>
                <?php foreach ($available_isbn as $isbn_item): ?>
                    <option 
                        value="<?= $isbn_item['id_isbn']; ?>" 
                        data-judul="<?= htmlspecialchars($isbn_item['judul_buku']); ?>"
                        data-penulis="<?= htmlspecialchars($isbn_item['penulis_lain']); ?>"
                        data-sinopsis="<?= htmlspecialchars(strip_tags($isbn_item['sinopsis'])); ?>"
                        data-isbn="<?= htmlspecialchars($isbn_item['isbn_number']); ?>"
                    >
                        [<?= htmlspecialchars($isbn_item['isbn_number']); ?>] - <?= htmlspecialchars($isbn_item['judul_buku']); ?> (Pengaju: <?= htmlspecialchars($isbn_item['nama_pengaju']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($available_isbn)): ?>
                <p class="text-danger mt-2">Tidak ada ISBN berstatus 'Terbit' yang tersedia untuk ditambahkan ke katalog.</p>
            <?php endif; ?>
        </div>
        
        <hr>
        
        <h4>Preview Data yang Akan Ditambahkan:</h4>
        <div class="panel panel-info">
            <div class="panel-body">
                <p><strong>ISBN:</strong> <span id="preview_isbn">-</span></p>
                <p><strong>Judul:</strong> <span id="preview_judul">-</span></p>
                <p><strong>Penulis:</strong> <span id="preview_penulis">-</span></p>
                <p><strong>Sinopsis:</strong> <span id="preview_sinopsis">-</span></p>
            </div>
        </div>

        <a href="katalog_view.php" class="btn btn-default">Batal</a>
        <button type="submit" class="btn btn-success" <?= empty($available_isbn) ? 'disabled' : ''; ?>>Tambahkan ke Katalog</button>
    </form>
</div>

<script>
function updateDetails(id_isbn) {
    if (id_isbn) {
        var selectedOption = document.querySelector(`option[value="${id_isbn}"]`);
        document.getElementById('preview_isbn').textContent = selectedOption.getAttribute('data-isbn');
        document.getElementById('preview_judul').textContent = selectedOption.getAttribute('data-judul');
        document.getElementById('preview_penulis').textContent = selectedOption.getAttribute('data-penulis');
        document.getElementById('preview_sinopsis').textContent = selectedOption.getAttribute('data-sinopsis');
    } else {
        document.getElementById('preview_isbn').textContent = '-';
        document.getElementById('preview_judul').textContent = '-';
        document.getElementById('preview_penulis').textContent = '-';
        document.getElementById('preview_sinopsis').textContent = '-';
    }
}
</script>

<?php include '../includes/footer.php'; ?>