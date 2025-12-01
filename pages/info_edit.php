<?php
// pages/info_edit.php

require_once '../conn.php'; 
require_once '../function.php'; 

// HANYA Role 1 (Administrator) dan Role 2 (Operator) yang diizinkan
check_role_access([1, 2]);

$current_user_id = $_SESSION['id_user'];
$upload_dir = '../assets/img/info/'; 
$id_info = (int)$_GET['id'] ?? 0;
$info_data = null;

// --- 1. AMBIL DATA LAMA ---
try {
    global $conn;
    $sql = "SELECT * FROM info WHERE id_info = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_info, PDO::PARAM_INT);
    $stmt->execute();
    $info_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$info_data) {
        set_flashdata('error', 'Informasi tidak ditemukan.');
        header('Location: info_list.php');
        exit;
    }
} catch (PDOException $e) {
    set_flashdata('error', 'Gagal memuat data lama: ' . $e->getMessage());
    header('Location: info_list.php');
    exit;
}


// --- 2. LOGIKA UPDATE INFORMASI ---
if (isset($_POST['update_info'])) {
    
    $title = sanitize_input($_POST['title']);
    $content = $_POST['content']; 
    $old_image = $info_data['image'];
    $new_image_filename = $old_image; // Default: pertahankan gambar lama
    $errors = [];

    if (empty($title) || empty($content)) {
        $errors[] = "Judul dan Konten berita wajib diisi.";
    }

    // A. Logika Upload Gambar Baru
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Tipe file baru tidak didukung.";
        } elseif ($file['size'] > $max_size) {
            $errors[] = "Ukuran file baru terlalu besar (Maksimal 5MB).";
        } else {
            // Generate nama unik untuk gambar baru
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_image_filename = time() . '_' . uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $new_image_filename;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $errors[] = "Gagal memindahkan file yang diunggah.";
                $new_image_filename = $old_image; // Kembali ke nama file lama
            } else {
                // Jika upload berhasil, hapus gambar lama (HANYA jika bukan default 'news.jpg')
                if ($old_image && $old_image != 'news.jpg' && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
            }
        }
    }
    
    // B. Handle jika gambar lama adalah 'news.jpg' dan user mengupload gambar baru
    // Logika di atas sudah menangani: jika upload berhasil, $new_image_filename akan diupdate.

    if (empty($errors)) {
        try {
            $sql = "UPDATE info SET title = :title, image = :image, content = :content 
                    WHERE id_info = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':image', $new_image_filename); 
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':id', $id_info, PDO::PARAM_INT);

            if ($stmt->execute()) {
                set_flashdata('success', 'Informasi berhasil diperbarui!');
            } else {
                set_flashdata('error', 'Gagal memperbarui informasi ke database.');
            }
        } catch (PDOException $e) {
            error_log("Info Update DB Error: " . $e->getMessage());
            set_flashdata('error', 'Terjadi kesalahan sistem saat memperbarui.');
        }
        header('Location: info_list.php');
        exit;
    } else {
        set_flashdata('error', implode('<br>', $errors));
        header('Location: info_edit.php?id=' . $id_info);
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-edit"></span> Edit Informasi Publik</h1>
    
    <?php display_flashdata(); ?>

    <form method="POST" action="info_edit.php?id=<?= $id_info; ?>" enctype="multipart/form-data"> 
        <div class="form-group">
            <label for="title">Judul Informasi</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($info_data['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Gambar Saat Ini</label>
            <p>
                <?php $current_image_path = $upload_dir . $info_data['image']; ?>
                <?php if ($info_data['image'] && file_exists($current_image_path)): ?>
                    <img src="<?= $current_image_path; ?>" alt="Gambar Saat Ini" style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                <?php else: ?>
                    <span class="text-danger">Gambar tidak ditemukan di server.</span>
                <?php endif; ?>
            </p>
        </div>
        
        <div class="form-group">
            <label for="image">Ganti Gambar Utama (Opsional)</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif">
            <p class="help-block">Biarkan kosong jika tidak ingin mengganti gambar.</p>
        </div>

        <div class="form-group">
            <label for="editor">Konten Berita</label>
            <textarea id="editor" name="content" class="form-control" rows="10"><?= htmlspecialchars($info_data['content']); ?></textarea>
        </div>
        
        <a href="info_list.php" class="btn btn-default">Batal</a>
        <button type="submit" name="update_info" class="btn btn-success">
            <span class="glyphicon glyphicon-save"></span> Simpan Perubahan
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>