<?php
// pages/user_edit.php (FINAL CODE)
require_once '../conn.php';
require_once '../function.php';
check_auth(); // Cek status login

// HANYA ROLE ADMIN (ID ROLE = 1) YANG BOLEH MENGAKSES
check_role([1]); 

$page_title = "Edit Pengguna";
$error = [];

// 1. Ambil ID user yang akan diedit dari URL
$user_id_to_edit = (int)($_GET['id'] ?? 0);
if ($user_id_to_edit === 0) {
    set_flashdata('error', 'ID pengguna tidak valid.');
    header('Location: user_list.php');
    exit;
}

// Ambil daftar role untuk dropdown
try {
    $roles = $conn->query("SELECT id_role, role_name FROM roles ORDER BY id_role")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    set_flashdata('error', 'Gagal memuat daftar role.');
    $roles = [];
}

// --- Default Values (untuk sticky form) ---
$username = '';
$nama_lengkap = '';
$email = '';
$alamat = ''; 
$no_telp = ''; 
$id_role = '';
$user_data = []; // Untuk menampung data user dari DB


// --- PROSES UPDATE DATA (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Sanitasi Input
    $nama_lengkap = sanitize_input($_POST['nama_lengkap']);
    $email = sanitize_input($_POST['email']);
    $alamat = sanitize_input($_POST['alamat']); 
    $no_telp = sanitize_input($_POST['no_telp']); 
    $id_role = (int)$_POST['id_role'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Field yang akan diupdate
    $update_fields = [
        'nama_lengkap' => $nama_lengkap, 
        'email' => $email,
        'alamat' => $alamat, 
        'no_telp' => $no_telp, 
        'id_role' => $id_role
    ];
    
    // 2. Validasi
    if (empty($nama_lengkap) || empty($email) || empty($id_role)) {
        $error[] = "Nama Lengkap, Email, dan Role wajib diisi.";
    }

    // Validasi Password Baru (Opsional)
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $error[] = "Password baru minimal 6 karakter.";
        } else if ($new_password !== $confirm_password) {
            $error[] = "Konfirmasi Password tidak cocok.";
        } else {
            $update_fields['password'] = hash_password($new_password);
        }
    }
    
    // 3. Update Database jika tidak ada error
    if (empty($error)) {
        try {
            $set_clauses = [];
            foreach ($update_fields as $key => $value) {
                $set_clauses[] = "$key = :$key";
            }
            $sql = "UPDATE users SET " . implode(', ', $set_clauses) . " WHERE id_user = :id_user";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $user_id_to_edit);
            foreach ($update_fields as $key => &$value) {
                $stmt->bindParam(":$key", $value);
            }
            
            if ($stmt->execute()) {
                set_flashdata('success', 'Data pengguna berhasil diperbarui!');
                header('Location: user_list.php');
                exit;
            } else {
                $error[] = "Gagal memperbarui database.";
            }

        } catch (PDOException $e) {
            error_log("User Edit Error: " . $e->getMessage());
            $error[] = "Kesalahan Database: Gagal memperbarui pengguna.";
        }
    }
    
    // Jika POST gagal, data POST tetap digunakan untuk sticky form (sudah di set di awal POST)
}


// --- AMBIL DATA USER UNTUK FORM (GET / Setelah POST gagal) ---
// Bagian ini dijalankan setelah POST logic ATAU jika ini adalah request GET pertama
try {
    $stmt = $conn->prepare("SELECT username, nama_lengkap, email, alamat, no_telp, id_role FROM users WHERE id_user = :id");
    $stmt->bindParam(':id', $user_id_to_edit);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        set_flashdata('error', 'Data pengguna tidak ditemukan.');
        header('Location: user_list.php');
        exit;
    }
    
    // Jika request GET pertama (atau POST berhasil/gagal): Inisialisasi variabel FORM
    if ($_SERVER['REQUEST_METHOD'] === 'GET' || !empty($error)) {
        $username = $user_data['username'];
        
        // Cek apakah variabel sudah terisi saat POST. Jika belum (yaitu request GET), ambil dari DB.
        $nama_lengkap = $nama_lengkap ?: $user_data['nama_lengkap'];
        $email = $email ?: $user_data['email'];
        $alamat = $alamat ?: $user_data['alamat'];
        $no_telp = $no_telp ?: $user_data['no_telp'];
        $id_role = $id_role ?: $user_data['id_role'];
    }

} catch (PDOException $e) {
    error_log("User Edit Load Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat data pengguna.');
    header('Location: user_list.php');
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-pencil"></span> <?= $page_title ?>: <?= htmlspecialchars($username) ?></h1>

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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Edit Data</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="user_edit.php?id=<?= $user_id_to_edit ?>" class="form-horizontal">
                        
                        <div class="form-group">
                            <label for="username" class="col-sm-3 control-label">Username</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?= htmlspecialchars($username) ?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nama_lengkap" class="col-sm-3 control-label">Nama Lengkap</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                    value="<?= htmlspecialchars($nama_lengkap) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat" class="col-sm-3 control-label">Alamat</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($alamat ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="no_telp" class="col-sm-3 control-label">Nomor Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="no_telp" name="no_telp" 
                                    value="<?= htmlspecialchars($no_telp ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="id_role" class="col-sm-3 control-label">Role Akses</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="id_role" name="id_role" required>
                                    <option value="">-- Pilih Role --</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id_role'] ?>" <?= ($id_role == $role['id_role']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($role['role_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="panel panel-info">
                            <div class="panel-heading"><h3 class="panel-title">Ganti Password (Kosongkan jika tidak diubah)</h3></div>
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

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Simpan Perubahan</button>
                                <a href="user_list.php" class="btn btn-default">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>