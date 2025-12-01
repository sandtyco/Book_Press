<?php
// pages/info_add.php

require_once '../conn.php'; 
require_once '../function.php'; 

// HANYA Role 1 (Administrator) dan Role 2 (Operator) yang diizinkan
check_role_access([1, 2]);

$current_user_id = $_SESSION['id_user'];

// --- LOGIKA MENAMBAH INFORMASI DAN UPLOAD GAMBAR ---
if (isset($_POST['submit_info'])) {
    
    $title = sanitize_input($_POST['title']);
    $content = $_POST['content']; 
    $image_filename = null;
    $errors = [];

    if (empty($title) || empty($content)) {
        $errors[] = "Judul dan Konten berita wajib diisi.";
    }

    // 1. Logika Upload Gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        // Menggunakan path yang dikonfirmasi user: /assets/img/info/
        $upload_dir = '../assets/img/info/'; 
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Tipe file tidak didukung. Hanya JPEG, PNG, dan GIF yang diizinkan.";
        } elseif ($file['size'] > $max_size) {
            $errors[] = "Ukuran file terlalu besar (Maksimal 5MB).";
        } else {
            // Generate nama unik
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $image_filename = time() . '_' . uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $image_filename;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $errors[] = "Gagal memindahkan file yang diunggah. Cek izin folder 'info'.";
                $image_filename = null; 
            }
        }
    }

    if (empty($errors)) {
        
        // --- IMPLEMENTASI DEFAULT IMAGE ---
        // Jika tidak ada nama file unik yang berhasil di-upload, gunakan 'news.jpg'
        if (empty($image_filename)) {
            $image_filename = 'news.jpg';
        }
        
        try {
            global $conn;
            $sql = "INSERT INTO info (title, image, content, posted_by) VALUES (:title, :image, :content, :posted_by)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':image', $image_filename); 
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':posted_by', $current_user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                set_flashdata('success', 'Informasi baru berhasil dipublikasikan!');
            } else {
                set_flashdata('error', 'Gagal mempublikasikan informasi ke database.');
            }
        } catch (PDOException $e) {
            error_log("Info Add DB Error: " . $e->getMessage());
            set_flashdata('error', 'Terjadi kesalahan sistem saat mempublikasikan.');
        }
        header('Location: info_list.php');
        exit;
    } else {
        set_flashdata('error', implode('<br>', $errors));
        header('Location: info_add.php');
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-bullhorn"></span> Tambah Informasi Publik</h1>
    
    <?php display_flashdata(); ?>

    <form method="POST" action="info_add.php" enctype="multipart/form-data"> 
        <div class="form-group">
            <label for="title">Judul Informasi</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="image">Gambar Utama (Opsional)</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif">
            <p class="help-block">Maks 5MB (JPG, PNG, GIF). Jika kosong, menggunakan `news.jpg`.</p>
        </div>

        <div class="form-group">
            <label for="editor">Konten Berita</label>
            <textarea id="editor" name="content" class="form-control" rows="10"></textarea>
        </div>
        
        <a href="info_list.php" class="btn btn-default">Kembali</a>
        <button type="submit" name="submit_info" class="btn btn-primary">
            <span class="glyphicon glyphicon-ok-sign"></span> Publikasikan
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>