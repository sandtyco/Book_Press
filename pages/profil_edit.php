<?php
// pages/profil_edit.php
require_once '../conn.php';
require_once '../function.php';

// Cek Login: Semua role harus login
check_auth(); // Cek status login

$id_user = $_SESSION['id_user'];
$page_title = "Edit Profil Saya";
$error = [];
$upload_dir = '../assets/img/user/'; // Path penyimpanan

// --- Default Values (untuk sticky form) ---
$current_nama_lengkap = '';
$current_email = '';
$current_alamat = ''; // Tambah
$current_no_telp = ''; // Tambah
$user_data = [];


// --- AMBIL DATA UNTUK FORM (GET) ---
try {
    $stmt = $conn->prepare("SELECT username, nama_lengkap, email, alamat, no_telp, foto_profil FROM users WHERE id_user = :id_user");
    $stmt->bindParam(':id_user', $id_user);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set default value dari database
    $current_nama_lengkap = $user_data['nama_lengkap'];
    $current_email = $user_data['email'];
    $current_alamat = $user_data['alamat']; // Tambah
    $current_no_telp = $user_data['no_telp']; // Tambah

} catch (PDOException $e) {
    set_flashdata('error', 'Gagal memuat data form edit.');
    header('Location: profil.php');
    exit;
}


// --- PROSES UPDATE DATA (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = sanitize_input($_POST['nama_lengkap']);
    $email = sanitize_input($_POST['email']);
    $alamat = sanitize_input($_POST['alamat']); // Tambah
    $no_telp = sanitize_input($_POST['no_telp']); // Tambah
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Field yang akan diupdate
    $update_fields = [
        'nama_lengkap' => $nama_lengkap, 
        'email' => $email,
        'alamat' => $alamat, // Tambah
        'no_telp' => $no_telp // Tambah
    ];
    $upload_success = true;

    // Gunakan input POST untuk sticky form jika ada error
    $current_nama_lengkap = $nama_lengkap;
    $current_email = $email;
    $current_alamat = $alamat;
    $current_no_telp = $no_telp;

    // 1. Validasi Input Dasar
    if (empty($nama_lengkap) || empty($email)) {
        $error[] = "Nama Lengkap dan Email wajib diisi.";
    }

    // 2. Validasi Password Baru
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $error[] = "Password minimal 6 karakter.";
        } else if ($new_password !== $confirm_password) {
            $error[] = "Konfirmasi Password tidak cocok.";
        } else {
            $update_fields['password'] = hash_password($new_password);
        }
    }
    
    // 3. Handle File Upload (Foto Profil)
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $file = $_FILES['foto_profil'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2 MB

        if (!in_array($file['type'], $allowed_types)) {
            $error[] = "Format file foto tidak didukung. Gunakan JPG atau PNG.";
            $upload_success = false;
        } elseif ($file['size'] > $max_size) {
            $error[] = "Ukuran file foto maksimal 2MB.";
            $upload_success = false;
        }

        if ($upload_success && empty($error)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'user_' . $id_user . '_' . time() . '.' . $ext;
            $destination = $upload_dir . $new_filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                
                $old_photo = $user_data['foto_profil'];

                if (!empty($old_photo) && $old_photo !== 'default.png' && file_exists($upload_dir . $old_photo)) {
                    unlink($upload_dir . $old_photo);
                }

                $update_fields['foto_profil'] = $new_filename;
                
            } else {
                $error[] = "Gagal memindahkan file foto yang diunggah.";
                $upload_success = false;
            }
        }
    }
    
    // 4. Update Database jika tidak ada error
    if (empty($error)) {
        try {
            $set_clauses = [];
            foreach ($update_fields as $key => $value) {
                $set_clauses[] = "$key = :$key";
            }
            $sql = "UPDATE users SET " . implode(', ', $set_clauses) . " WHERE id_user = :id_user";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user);
            foreach ($update_fields as $key => &$value) {
                $stmt->bindParam(":$key", $value);
            }
            
            if ($stmt->execute()) {
                $_SESSION['nama_lengkap'] = $nama_lengkap; 
                set_flashdata('success', 'Profil berhasil diperbarui!');
                header('Location: profil.php');
                exit;
            } else {
                $error[] = "Gagal memperbarui database.";
            }

        } catch (PDOException $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            $error[] = "Kesalahan Database: Gagal memperbarui profil.";
        }
    }
}

// Tentukan path foto saat ini untuk tampilan form
$foto_profil_filename = $user_data['foto_profil'] ?? '';
if (isset($update_fields['foto_profil'])) {
    $foto_profil_filename = $update_fields['foto_profil'];
}

$foto_path = empty($foto_profil_filename) || $foto_profil_filename == 'default.png' ? 
    '../assets/img/user/default.png' : 
    '../assets/img/user/' . htmlspecialchars($foto_profil_filename);

?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-pencil"></span> <?= $page_title ?></h1>

    <?php 
    if (!empty($error)) {
        echo '<div class="alert alert-danger" role="alert"><ul>';
        foreach ($error as $msg) {
            echo '<li>' . $msg . '</li>';
        }
        echo '</ul></div>';
    }
    ?>

    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="profil_edit.php" enctype="multipart/form-data" class="form-horizontal">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Data Diri</h3>
                    </div>
                    <div class="panel-body">
                        
                        <div class="form-group">
                            <label for="username" class="col-sm-3 control-label">Username</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?= htmlspecialchars($user_data['username'] ?? 'N/A') ?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nama_lengkap" class="col-sm-3 control-label">Nama Lengkap</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                    value="<?= htmlspecialchars($current_nama_lengkap) ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?= htmlspecialchars($current_email) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat" class="col-sm-3 control-label">Alamat</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($current_alamat ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="no_telp" class="col-sm-3 control-label">Nomor Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="no_telp" name="no_telp" 
                                    value="<?= htmlspecialchars($current_no_telp ?? '') ?>">
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Ganti Password (Kosongkan jika tidak ingin diubah)</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="new_password" class="col-sm-3 control-label">Password Baru</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="col-sm-3 control-label">Konfirmasi Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Foto Profil</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="foto_profil" class="col-sm-3 control-label">Pilih Foto Baru</label>
                            <div class="col-sm-9">
                                <input type="file" id="foto_profil" name="foto_profil" accept="image/jpeg, image/png, image/jpg">
                                <p class="help-block">Maks 2MB. Format: JPG, PNG. Foto lama akan otomatis dihapus.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Foto Saat Ini</label>
                            <div class="col-sm-9">
                                <img src="<?= $foto_path ?>" alt="Foto Profil Saat Ini" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ccc;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Simpan Perubahan</button>
                        <a href="profil.php" class="btn btn-default">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>