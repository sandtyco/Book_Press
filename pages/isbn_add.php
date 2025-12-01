<?php
// pages/isbn_add.php (Formulir Pengajuan ISBN)

require_once '../conn.php';
require_once '../function.php';

// Memeriksa Role untuk mengakses halaman ini
check_role_access([1, 2, 3]);

$errors = [];
$edisi_val = '';
$judul_buku_val = '';
$penulis_lain_val = '';
$jumlah_halaman_val = '';
$sinopsis_val = '';

// --- FOLDER UPLOAD ---
$base_upload_path = '../assets/uploads/';
$upload_dir_naskah = $base_upload_path . 'naskah/';
$upload_dir_cover = $base_upload_path . 'cover/';
$upload_dir_lampiran = $base_upload_path . 'lampiran/';

// Definisi file default
$default_cover_file = 'default.jpg'; // Nama file cover default

// Pastikan direktori upload ada
if (!is_dir($upload_dir_naskah)) mkdir($upload_dir_naskah, 0777, true);
if (!is_dir($upload_dir_cover)) mkdir($upload_dir_cover, 0777, true);
if (!is_dir($upload_dir_lampiran)) mkdir($upload_dir_lampiran, 0777, true);

// 1. Proses Form
if (isset($_POST['submit_isbn'])) {
    
    // Ambil dan bersihkan data form
    $judul_buku     = sanitize_input($_POST['judul_buku']);
    $penulis_lain   = sanitize_input($_POST['penulis_lain'] ?? '');
    $edisi          = sanitize_input($_POST['edisi'] ?? '1');
    $jumlah_halaman = (int)$_POST['jumlah_halaman'];
    $sinopsis       = sanitize_input($_POST['sinopsis']);
    $user_id        = $_SESSION['id_user'];
    
    // Re-assign nilai form untuk pre-fill jika ada error
    $judul_buku_val = $judul_buku;
    $penulis_lain_val = $penulis_lain;
    $edisi_val = $edisi;
    $jumlah_halaman_val = $jumlah_halaman;
    $sinopsis_val = $sinopsis;

    // Validasi Dasar
    if (empty($judul_buku) || empty($sinopsis) || empty($edisi)) {
        $errors[] = "Judul, Edisi, dan Sinopsis wajib diisi.";
    }
    if ($jumlah_halaman <= 0) {
        $errors[] = "Jumlah halaman harus angka positif.";
    }

    // --- 2. Proses Upload File ---
    
    // Naskah (Wajib)
    $naskah_file_name = process_upload($_FILES['naskah'], $upload_dir_naskah, ['docx'], 50000000, $user_id, 'naskah');

    // Lampiran (Wajib)
    $lampiran_file_name = process_upload($_FILES['lampiran'], $upload_dir_lampiran, ['pdf'], 20000000, $user_id, 'lampiran');

    // 🚨 PERUBAHAN: Cover (Opsional)
    $cover_file_name = $default_cover_file; // Set default
    
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
        $result = process_upload($_FILES['cover'], $upload_dir_cover, ['jpg', 'jpeg', 'png'], 10000000, $user_id, 'cover');
        
        if (is_array($result)) { 
            // Jika ada error upload, catat error tersebut.
            $errors[] = "Cover: " . $result[0];
        } else {
            // Jika upload sukses, gunakan nama file yang baru.
            $cover_file_name = $result; 
        }
    }
    
    // Cek hasil upload file Wajib
    if (is_array($naskah_file_name)) {
        $errors[] = "Naskah: " . $naskah_file_name[0];
    }
    if (is_array($lampiran_file_name)) {
        $errors[] = "Lampiran: " . $lampiran_file_name[0];
    }
    
    // Cek apakah semua file wajib terupload (jika proses_upload tidak mengembalian string, berarti ada error)
    if (!is_string($naskah_file_name) || !is_string($lampiran_file_name) || is_array($cover_file_name)) {
        // Jika cover_file_name adalah array, berarti ada error upload spesifik di cover
        if (is_array($cover_file_name)) {
             // Error cover sudah ditambahkan di atas, tapi kita pastikan jika ada error fatal di file wajib.
             $errors[] = "Terdapat masalah pada file yang wajib diisi (Naskah/Lampiran)."; 
        }
    }

    // --- 3. Simpan ke Database ---
    if (empty($errors)) {
        try {
            global $conn;

            $sql = "INSERT INTO isbn_submissions (
                        id_user, judul_buku, penulis_lain, edisi, jumlah_halaman, sinopsis, 
                        naskah, cover, lampiran, status_ajuan, submitted_at
                    ) VALUES (
                        :user_id, :judul, :penulis, :edisi, :halaman, :sinopsis, 
                        :naskah, :cover, :lampiran, 'Diajukan', NOW()
                    )";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':judul', $judul_buku);
            $stmt->bindParam(':penulis', $penulis_lain);
            $stmt->bindParam(':edisi', $edisi);
            $stmt->bindParam(':halaman', $jumlah_halaman);
            $stmt->bindParam(':sinopsis', $sinopsis);
            $stmt->bindParam(':naskah', $naskah_file_name);
            $stmt->bindParam(':cover', $cover_file_name); // Menggunakan nama file hasil upload atau default.jpg
            $stmt->bindParam(':lampiran', $lampiran_file_name);

            if ($stmt->execute()) {
                set_flashdata('success', 'Pengajuan ISBN untuk buku "' . $judul_buku . '" berhasil dikirim!');
                header('Location: isbn_list.php');
                exit;
            } else {
                $errors[] = "Gagal menyimpan pengajuan ke database.";
            }

        } catch (PDOException $e) {
            error_log("ISBN Add DB Error: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan sistem saat menyimpan data.";
        }
    }
    
    // Jika ada error setelah proses form, tampilkan flashdata
    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
        set_flashdata('error', $error_message);
    }
}


// --- HTML FORM ---
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-plus"></span> Pengajuan ISBN Baru</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Isi Detail Naskah</h6>
        </div>
        <div class="card-body">
            
            <?php display_flashdata(); ?>
            
            <form method="POST" action="isbn_add.php" enctype="multipart/form-data">
                
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
                    <label for="naskah">Upload Naskah Lengkap (.docx) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control-file" id="naskah" name="naskah" accept=".docx" required>
                    <small class="form-text text-muted">Maks. 50 MB.</small>
                </div>

                <div class="form-group">
                    <label for="cover">Upload Desain Cover (.jpg/.png) (Opsional)</label>
                    <input type="file" class="form-control-file" id="cover" name="cover" accept=".jpg,.jpeg,.png">
                    <small class="form-text text-muted">Abaikan jika tidak ada. Jika kosong, akan menggunakan cover **default.jpg**. Maks. 10 MB.</small>
                </div>

                <div class="form-group">
                    <label for="lampiran">Upload Lampiran Dokumen (Surat Pernyataan, dll) (.pdf) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control-file" id="lampiran" name="lampiran" accept=".pdf" required>
                    <small class="form-text text-muted">Maks. 20 MB. Gabungkan semua lampiran ke dalam satu file PDF.</small>
                </div>
                
                <button type="submit" name="submit_isbn" class="btn btn-primary mt-3">
                    <i class="fas fa-paper-plane"></i> Ajukan ISBN
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>