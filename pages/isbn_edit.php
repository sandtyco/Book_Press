<?php
// pages/isbn_edit.php (Formulir Pengeditan Ajuan ISBN)
require_once '../conn.php';
require_once '../function.php'; 

check_role_access([1, 2, 3]);

$id_isbn = $_GET['id'] ?? 0;
$current_user_id = $_SESSION['id_user'];
$current_user_role = $_SESSION['id_role'];
$data = null;
$error_redirect = 'Location: isbn_list.php';

// --- FOLDER UPLOAD ---
$base_upload_path = '../assets/uploads/';
$upload_dir_naskah = $base_upload_path . 'naskah/';
$upload_dir_cover = $base_upload_path . 'cover/';
$upload_dir_lampiran = $base_upload_path . 'lampiran/';


// 1. Ambil Data yang Akan Diedit (Otorisasi Data Dinamis)
try {
    global $conn;
    
    // Pastikan semua kolom baru dan lama (termasuk catatan_admin) ditarik
    $sql_select = "SELECT * FROM isbn_submissions WHERE id_isbn = :id";
    
    if ($current_user_role == 3) {
        $sql_select .= " AND id_user = :user_id";
    }

    $stmt = $conn->prepare($sql_select);
    $stmt->bindParam(':id', $id_isbn, PDO::PARAM_INT);
    
    if ($current_user_role == 3) {
        $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        set_flashdata('error', 'Data ajuan tidak ditemukan atau Anda tidak memiliki akses.');
        header($error_redirect);
        exit;
    }

    // Cek Status: HANYA untuk Member (Role 3) yang terbatasi
    if ($current_user_role == 3 && $data['status_ajuan'] !== 'Diajukan') {
        set_flashdata('warning', 'Ajuan tidak dapat diubah karena status sudah: ' . $data['status_ajuan']);
        header($error_redirect);
        exit;
    }
    
    // Tentukan nilai awal form
    $judul_buku_val = $data['judul_buku'];
    $penulis_lain_val = $data['penulis_lain'];
    $edisi_val = $data['edisi'];
    $jumlah_halaman_val = $data['jumlah_halaman'];
    $sinopsis_val = $data['sinopsis'];
    
    // Nilai awal untuk Catatan Admin
    $catatan_admin_val = $data['catatan_admin'] ?? ''; 

} catch (PDOException $e) {
    error_log("ISBN Edit Load DB Error: " . $e->getMessage());
    set_flashdata('error', 'Terjadi kesalahan sistem saat memuat data.');
    header($error_redirect);
    exit;
}

// 2. Proses Form Update
if (isset($_POST['submit_isbn'])) {
    
    $judul_buku     = sanitize_input($_POST['judul_buku']);
    $penulis_lain   = sanitize_input($_POST['penulis_lain'] ?? '');
    $edisi          = sanitize_input($_POST['edisi'] ?? $data['edisi']);
    $jumlah_halaman = (int)$_POST['jumlah_halaman'];
    $sinopsis       = sanitize_input($_POST['sinopsis']);
    $errors = [];
    
    // Default: pertahankan nama file lama
    $naskah_file_name = $data['naskah'];
    $cover_file_name = $data['cover'];
    $lampiran_file_name = $data['lampiran'];
    
    // Ambil data status, ISBN, dan Catatan Admin jika Admin/Operator
    $status_ajuan_new = $data['status_ajuan'];
    $isbn_number_new = $data['isbn_number'];
    $barcode_file_name = $data['barcode'];
    $catatan_admin_new = $data['catatan_admin']; // Default: pertahankan yang lama

    if ($current_user_role == 1 || $current_user_role == 2) {
        $status_ajuan_new = sanitize_input($_POST['status_ajuan'] ?? $data['status_ajuan']);
        $isbn_number_new = sanitize_input($_POST['isbn_number'] ?? $data['isbn_number']);
        $catatan_admin_new = sanitize_input($_POST['catatan_admin'] ?? ''); // 🚨 PERUBAHAN: Ambil nilai catatan
    }

    // Validasi Dasar (Diulang agar pre-fill tidak hilang jika error)
    if (empty($judul_buku) || empty($sinopsis) || empty($edisi)) {
        $errors[] = "Judul, Edisi, dan Sinopsis wajib diisi.";
    }
    if ($jumlah_halaman <= 0) {
        $errors[] = "Jumlah halaman harus angka positif.";
    }
    
    // Re-assign nilai jika ada error agar form tidak kosong
    if (!empty($errors)) {
        $judul_buku_val = $judul_buku;
        $penulis_lain_val = $penulis_lain;
        $edisi_val = $edisi;
        $jumlah_halaman_val = $jumlah_halaman;
        $sinopsis_val = $sinopsis;
        // Re-assign catatan admin jika ada error agar tidak hilang
        if ($current_user_role == 1 || $current_user_role == 2) {
            $catatan_admin_val = $catatan_admin_new;
        }
    }


    // --- 3. Proses File Upload (Opsional saat Edit) ---
    // (Logika upload file tetap sama, menggunakan process_upload)
    
    // A. Upload Ulang Naskah (.docx)
    if (isset($_FILES['naskah']) && $_FILES['naskah']['error'] === UPLOAD_ERR_OK) {
        $result = process_upload($_FILES['naskah'], $upload_dir_naskah, ['docx'], 50000000, $data['id_user'], 'naskah', $data['naskah']);
        if (is_array($result)) { $errors[] = "Naskah: " . $result[0]; } else { $naskah_file_name = $result; }
    }
    
    // B. Upload Ulang Cover (.jpg/.png)
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $result = process_upload($_FILES['cover'], $upload_dir_cover, ['jpg', 'jpeg', 'png'], 10000000, $data['id_user'], 'cover', $data['cover']);
        if (is_array($result)) { $errors[] = "Cover: " . $result[0]; } else { $cover_file_name = $result; }
    }
    
    // C. Upload Ulang Lampiran (.pdf)
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
        $result = process_upload($_FILES['lampiran'], $upload_dir_lampiran, ['pdf'], 20000000, $data['id_user'], 'lampiran', $data['lampiran']);
        if (is_array($result)) { $errors[] = "Lampiran: " . $result[0]; } else { $lampiran_file_name = $result; }
    }

    // D. Upload Barcode (Hanya untuk Admin/Operator)
    if (($current_user_role == 1 || $current_user_role == 2) && isset($_FILES['barcode']) && $_FILES['barcode']['error'] === UPLOAD_ERR_OK) {
        $upload_dir_barcode = $base_upload_path . 'barcode/';
        if (!is_dir($upload_dir_barcode)) mkdir($upload_dir_barcode, 0777, true);

        $result = process_upload($_FILES['barcode'], $upload_dir_barcode, ['jpg', 'png'], 500000, $data['id_user'], 'barcode', $data['barcode']);
        if (is_array($result)) { $errors[] = "Barcode: " . $result[0]; } else { $barcode_file_name = $result; }
    }


    // --- 4. Update ke Database jika tidak ada error ---
    if (empty($errors)) {
        try {
            global $conn;
            
            // Kolom yang selalu diupdate (oleh semua role)
            $sql_fields = "judul_buku = :judul, penulis_lain = :penulis, edisi = :edisi, jumlah_halaman = :halaman, 
                           sinopsis = :sinopsis, naskah = :naskah, cover = :cover, lampiran = :lampiran, updated_at = NOW()";
            
            // Tambahkan kolom Admin HANYA jika Role 1/2
            if ($current_user_role == 1 || $current_user_role == 2) {
                // 🚨 PERUBAHAN: Tambahkan catatan_admin
                $sql_fields .= ", status_ajuan = :status_new, isbn_number = :isbn_new, barcode = :barcode_new, catatan_admin = :catatan_admin_new"; 
            }
            
            $sql_where = " WHERE id_isbn = :id";
            if ($current_user_role == 3) {
                $sql_where .= " AND id_user = :user_id";
            }

            $sql = "UPDATE isbn_submissions SET " . $sql_fields . $sql_where;
            
            $stmt = $conn->prepare($sql);
            
            // Bind parameter data utama
            $stmt->bindParam(':judul', $judul_buku);
            $stmt->bindParam(':penulis', $penulis_lain);
            $stmt->bindParam(':edisi', $edisi);
            $stmt->bindParam(':halaman', $jumlah_halaman);
            $stmt->bindParam(':sinopsis', $sinopsis);
            $stmt->bindParam(':naskah', $naskah_file_name);
            $stmt->bindParam(':cover', $cover_file_name);
            $stmt->bindParam(':lampiran', $lampiran_file_name);
            $stmt->bindParam(':id', $id_isbn);
            
            // Bind parameter Admin/Operator
            if ($current_user_role == 1 || $current_user_role == 2) {
                $stmt->bindParam(':status_new', $status_ajuan_new);
                $stmt->bindParam(':isbn_new', $isbn_number_new);
                $stmt->bindParam(':barcode_new', $barcode_file_name);
                $stmt->bindParam(':catatan_admin_new', $catatan_admin_new); // 🚨 PERUBAHAN: Binding catatan
            }
            // Bind parameter Member
            if ($current_user_role == 3) {
                $stmt->bindParam(':user_id', $current_user_id);
            }

            if ($stmt->execute()) {
                set_flashdata('success', 'Pengajuan ISBN berhasil diperbarui!');
                header($error_redirect);
                exit;
            } else {
                $errors[] = "Gagal menyimpan perubahan ke database.";
            }

        } catch (PDOException $e) {
            error_log("ISBN Edit Update DB Error: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan sistem saat memperbarui data.";
        }
    }
    
    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
        set_flashdata('error', $error_message);
        header('Location: isbn_edit.php?id=' . $id_isbn);
        exit;
    }
}

// ************************************************
// Mulai Tampilan HTML
// ************************************************
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-tag"></span> Edit Ajuan ISBN A/n <?= htmlspecialchars($data['id_isbn']) ?></h1>
    <?php if ($current_user_role == 1 || $current_user_role == 2): ?>
        <p class="text-info">Anda mengedit ajuan ID #<?= htmlspecialchars($data['id_isbn']) ?> dari Pengguna ID #<?= htmlspecialchars($data['id_user']) ?>.</p>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Detail Naskah</h6>
        </div>
        <div class="card-body">
            
            <?php display_flashdata(); ?>
            
            <form method="POST" action="isbn_edit.php?id=<?= $id_isbn ?>" enctype="multipart/form-data">
                
                <?php if ($current_user_role == 1 || $current_user_role == 2): ?>
                <hr>
                <h5 class="text-danger mb-3">🛠️ Panel Admin/Operator</h5>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="status_ajuan">Status Ajuan</label>
                        <select class="form-control" id="status_ajuan" name="status_ajuan">
                            <?php $statuses = ['Diajukan', 'Dalam Proses', 'Terbit', 'Ditolak']; ?>
                            <?php foreach ($statuses as $s): ?>
                                <option value="<?= $s ?>" <?= ($data['status_ajuan'] == $s) ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="isbn_number">Nomor ISBN</label>
                        <input type="text" class="form-control" id="isbn_number" name="isbn_number" value="<?= htmlspecialchars($data['isbn_number']) ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="barcode">Upload Barcode (.jpg/.png)</label>
                        <input type="file" class="form-control-file" id="barcode" name="barcode" accept=".jpg,.png">
                        <small class="form-text text-muted">File tersimpan: **<?= htmlspecialchars($data['barcode']) ?>**</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="catatan_admin">Catatan/Feedback Admin untuk Pengaju</label>
                    <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3"><?= htmlspecialchars($catatan_admin_val) ?></textarea>
                    <small class="form-text text-muted">Catatan ini akan terlihat oleh pengaju di halaman detail ajuan.</small>
                </div>
                
                <hr>
                <h5 class="text-primary mt-4 mb-3">Detail Naskah</h5>
                <?php endif; ?>
                <div class="form-group">
                    <label for="judul_buku">Judul Buku <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="judul_buku" name="judul_buku" value="<?= htmlspecialchars($judul_buku_val) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="penulis_lain">Penulis Lain (Opsional)</label>
                    <input type="text" class="form-control" id="penulis_lain" name="penulis_lain" value="<?= htmlspecialchars($penulis_lain_val) ?>">
                    <small class="form-text text-muted">Pisahkan dengan koma jika lebih dari satu.</small>
                </div>
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="jumlah_halaman">Jumlah Halaman Estimasi <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah_halaman" name="jumlah_halaman" min="1" value="<?= htmlspecialchars($jumlah_halaman_val) ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="edisi">Edisi ke- <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edisi" name="edisi" value="<?= htmlspecialchars($edisi_val) ?>" placeholder="Contoh: 1, Revisi-1" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="sinopsis">Sinopsis Buku <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="sinopsis" name="sinopsis" rows="5" required><?= htmlspecialchars($sinopsis_val) ?></textarea>
                </div>
                
                <hr>
                <h5 class="text-primary mt-4 mb-3">Dokumen Pendukung</h5>
                
                <div class="form-group">
                    <label for="naskah">Upload Ulang File Naskah (.docx) (Opsional)</label>
                    <input type="file" class="form-control-file" id="naskah" name="naskah" accept=".docx">
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengubah. File saat ini: **<?= htmlspecialchars($data['naskah']) ?>**</small>
                </div>
                
                <div class="form-group">
                    <label for="cover">Upload Ulang Desain Cover (.jpg/.png) (Opsional)</label>
                    <input type="file" class="form-control-file" id="cover" name="cover" accept=".jpg,.jpeg,.png">
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengubah. File saat ini: **<?= htmlspecialchars($data['cover']) ?>**</small>
                </div>
                
                <div class="form-group">
                    <label for="lampiran">Upload Ulang Lampiran Dokumen (.pdf) (Opsional)</label>
                    <input type="file" class="form-control-file" id="lampiran" name="lampiran" accept=".pdf">
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengubah. File saat ini: **<?= htmlspecialchars($data['lampiran']) ?>**</small>
                </div>

                <button type="submit" name="submit_isbn" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="isbn_list.php" class="btn btn-secondary mt-3">
                    Batal
                </a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>