<?php
// pages/user_add.php
require_once '../conn.php';
require_once '../function.php';
check_auth(); // Cek status login

// HANYA ROLE ADMIN (ID ROLE = 1) YANG BOLEH MENGAKSES
check_role([1]); 

$page_title = "Tambah Pengguna Baru";
$error = [];

// Default values for sticky form
$username = '';
$nama_lengkap = '';
$email = '';
$alamat = ''; // TAMBAH
$no_telp = ''; // TAMBAH
$id_role = '';

// Ambil daftar role untuk dropdown
try {
    $roles = $conn->query("SELECT id_role, role_name FROM roles ORDER BY id_role")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    set_flashdata('error', 'Gagal memuat daftar role.');
    $roles = [];
}

// --- PROSES SUBMIT FORM (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Sanitasi Input
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $nama_lengkap = sanitize_input($_POST['nama_lengkap']);
    $email = sanitize_input($_POST['email']);
    $alamat = sanitize_input($_POST['alamat']); // TAMBAH
    $no_telp = sanitize_input($_POST['no_telp']); // TAMBAH
    $id_role = (int)$_POST['id_role'];

    // 2. Validasi
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email) || empty($id_role)) {
        $error[] = "Semua field bertanda * wajib diisi.";
    }
    if (strlen($password) < 6) {
        $error[] = "Password minimal 6 karakter.";
    }
    // Cek duplikasi username
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt_check->execute([$username]);
    if ($stmt_check->fetchColumn() > 0) {
        $error[] = "Username sudah digunakan. Pilih username lain.";
    }

    // 3. Insert ke Database
    if (empty($error)) {
        $hashed_password = hash_password($password);

        try {
            $sql = "INSERT INTO users 
                    (username, password, nama_lengkap, email, alamat, no_telp, id_role) 
                    VALUES (:username, :password, :nama, :email, :alamat, :notelp, :role)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':nama', $nama_lengkap);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':alamat', $alamat); // TAMBAH
            $stmt->bindParam(':notelp', $no_telp); // TAMBAH
            $stmt->bindParam(':role', $id_role);
            
            if ($stmt->execute()) {
                set_flashdata('success', 'Pengguna **' . $nama_lengkap . '** berhasil ditambahkan!');
                header('Location: user_list.php');
                exit;
            } else {
                $error[] = "Gagal menyimpan data pengguna ke database.";
            }

        } catch (PDOException $e) {
            error_log("User Add Error: " . $e->getMessage());
            $error[] = "Kesalahan Database: Gagal menambahkan pengguna.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-plus"></span> <?= $page_title ?></h1>

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
                    <h3 class="panel-title">Data Pengguna</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="user_add.php" class="form-horizontal">
                        
                        <div class="form-group">
                            <label for="username" class="col-sm-3 control-label">Username <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="username" name="username" 
                                    value="<?= htmlspecialchars($username) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-sm-3 control-label">Password <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <p class="help-block">Minimal 6 karakter.</p>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="nama_lengkap" class="col-sm-3 control-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                    value="<?= htmlspecialchars($nama_lengkap) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat" class="col-sm-3 control-label">Alamat</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($alamat) ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="no_telp" class="col-sm-3 control-label">Nomor Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="no_telp" name="no_telp" 
                                    value="<?= htmlspecialchars($no_telp) ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="id_role" class="col-sm-3 control-label">Role Akses <span class="text-danger">*</span></label>
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

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-plus-sign"></span> Tambah Pengguna</button>
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