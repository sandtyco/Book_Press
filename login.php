<?php
// login.php (DI ROOT DIRECTORY /press)

session_start();

// Periksa jika user sudah login, arahkan ke dashboard yang sesuai
if (isset($_SESSION['id_role'])) {
    // Redirect ke file di dalam folder pages/
    if ($_SESSION['id_role'] == 1) {
        header('Location: pages/dash_sys.php');
    } elseif ($_SESSION['id_role'] == 2) {
        header('Location: pages/dash_opt.php');
    } elseif ($_SESSION['id_role'] == 3) {
        header('Location: pages/dash_member.php');
    }
    exit;
}

// Sertakan file koneksi dan fungsi (karena sejajar, tidak perlu ../)
require_once 'conn.php'; 
require_once 'function.php'; // Termasuk hash_password() dan flashdata

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha_input = $_POST['captcha_input'] ?? ''; 

    if (empty($username) || empty($password)) {
        set_flashdata('error', 'Username dan password wajib diisi!');
        header('Location: login.php');
        exit;
    }
    
    // --- 0. Verifikasi CAPTCHA ---
    if (!isset($_SESSION['captcha_result']) || $_SESSION['captcha_result'] != $captcha_input) {
        unset($_SESSION['captcha_result']); 
        set_flashdata('error', 'Kode verifikasi (CAPTCHA) salah!');
        header('Location: login.php');
        exit;
    }
    unset($_SESSION['captcha_result']); // Hapus CAPTCHA setelah berhasil

    try {
        global $conn;

        $sql = "SELECT id_user, username, password, id_role, is_active, nama_lengkap, email, alamat, no_telp, foto_profil 
                FROM users 
                WHERE username = :username";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            
            // 1. Verifikasi Password (SHA-256)
            $input_hash = hash_password($password); 

            if ($input_hash === $user['password']) {
            
                if ($user['is_active'] == 1) {
                    
                    // KODE LOGIN SUKSES
                    $_SESSION['id_user'] = $user['id_user'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['id_role'] = $user['id_role'];
                    $_SESSION['user_data'] = [
                        'nama_lengkap' => $user['nama_lengkap'],
                        'email' => $user['email'],
                        'address' => $user['alamat'],
                        'phone' => $user['no_telp'],
                        'photo' => $user['foto_profil']
                    ];

                    // Pengalihan ke folder pages/
                    if ($user['id_role'] == 1) {
                        header('Location: pages/dash_sys.php');
                    } elseif ($user['id_role'] == 2) {
                        header('Location: pages/dash_opt.php');
                    } else {
                        header('Location: pages/dash_member.php');
                    }
                    exit;
                    
                } else {
                    set_flashdata('error', 'Akun Anda belum aktif atau diblokir. Hubungi Administrator.');
                }
            } else {
                set_flashdata('error', 'Password salah.');
            }
        } else {
            set_flashdata('error', 'Username tidak ditemukan.');
        }

    } catch (PDOException $e) {
        error_log("Login DB Error: " . $e->getMessage());
        set_flashdata('error', 'Terjadi kesalahan sistem saat mencoba login.');
    }

    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Sistem ISBN</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="assets/img/favicon.png">
    <style>
        body {
			background-image: url('assets/img/bg.jpg');
			background-repeat: no-repeat;
			background-size: cover;
			background-attachment:fixed;
			}
        .login-panel { margin-top: 100px; }
        .panel-heading { text-align: center; }
        .panel-title { font-size: 24px; }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fas fa-sign-in-alt"></i> Login Sistem ISBN</h3>
                </div>
                <div class="panel-body">
                    <?php display_flashdata(); ?>
                    <form role="form" method="post" action="login.php">
                        <fieldset>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input class="form-control" placeholder="Masukkan Username" name="username" type="text" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input class="form-control" placeholder="Masukkan Password" name="password" type="password" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Kode Verifikasi (Hitung)</label>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <img src="captcha_image.php" alt="CAPTCHA" style="border: 1px solid #ccc; padding: 5px; width: 100%;">
                                    </div>
                                    <div class="col-xs-6">
                                        <input class="form-control" placeholder="Hasil Hitung" name="captcha_input" type="text" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-lg btn-primary btn-block">Login</button>
                            <hr>
                            <p class="text-center small">Belum punya akun? <a href="member.php">Daftar di sini</a> | Kembali ke <a href="index.php">Home</a></p>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>