<?php
// pages/profil.php
require_once '../conn.php';
require_once '../function.php';

// Cek Login: Pastikan sudah login
check_auth(); // Cek status login

// Variabel Tampilan
$id_user = $_SESSION['id_user'];
$page_title = "Profil Saya";

// 1. Ambil data pengguna dari database (TERMASUK ALAMAT & NO_TELP)
try {
    $stmt = $conn->prepare("
        SELECT 
            u.username, u.nama_lengkap, u.email, u.alamat, u.no_telp, u.foto_profil, r.role_name 
        FROM users u
        JOIN roles r ON u.id_role = r.id_role
        WHERE u.id_user = :id_user
    ");
    $stmt->bindParam(':id_user', $id_user);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        set_flashdata('error', 'Data pengguna tidak ditemukan.');
        header('Location: dash_sys.php');
        exit;
    }

} catch (PDOException $e) {
    set_flashdata('error', 'Gagal mengambil data profil.');
    header('Location: dash_sys.php');
    exit;
}

$role_name = $user['role_name'];
$username_display = $user['username'];
// Definisikan path foto profil
$foto_path = empty($user['foto_profil']) ? 
    '../assets/img/user/default.png' : 
    '../assets/img/user/' . htmlspecialchars($user['foto_profil']);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-cog"></span> <?= $page_title ?></h1>

    <?php display_flashdata(); // Tampilkan notifikasi ?>

    <div class="row">
        <div class="col-md-3 text-center">
            <img src="<?= $foto_path ?>" class="img-circle" alt="Foto Profil" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ccc;">
            <h3 style="margin-top: 10px;"><?= htmlspecialchars($user['nama_lengkap']) ?></h3>
            <p class="text-muted"><?= htmlspecialchars($role_name) ?></p>
            <a href="profil_edit.php" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-pencil"></span> Edit Profil</a>
        </div>

        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Detail Akun</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th style="width: 200px;">Nama Lengkap</th>
                            <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td><?= nl2br(htmlspecialchars($user['alamat'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Nomor Telepon</th>
                            <td><?= htmlspecialchars($user['no_telp'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Level Akses</th>
                            <td><?= htmlspecialchars($role_name) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>