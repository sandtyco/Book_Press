<?php
// index.php (VERSI FINAL TERBARU DENGAN 3 KOLOM BERITA)

// 1. START SESSION (WAJIB)
session_start(); 
require_once 'conn.php';
require_once 'function.php'; // Memuat fungsi-fungsi penting

// Cek apakah user sudah login. Jika iya, redirect ke dashboard.
if (is_logged_in()) {
    // Diasumsikan pages/dash_sys.php, dash_opt.php, dan dash_member.php ada
    $role = $_SESSION['id_role'];
    if ($role == 1) {
        header("Location: pages/dash_sys.php"); // Administrator
    } elseif ($role == 2) {
        header("Location: pages/dash_opt.php"); // Operator
    } elseif ($role == 3) {
        header("Location: pages/dash_member.php"); // Member/Penulis
    } else {
        header("Location: pages/dash_sys.php"); // Default
    }
    exit;
}

// Proses Login dari Modal
if (isset($_POST['login'])) {
    
    $username = sanitize_input($_POST['username']); 
    $password = $_POST['password']; 

    if (empty($username) || empty($password)) {
        set_flashdata('error', "Username dan Password harus diisi.");
    } else {
        try {
            global $conn;
            $stmt = $conn->prepare("SELECT id_user, username, password, id_role, is_active, nama_lengkap FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['is_active'] == 1) {
                    if (verify_password($password, $user['password'])) { 
                        // Login Berhasil! Set Session
                        $_SESSION['id_user'] = $user['id_user'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['id_role'] = $user['id_role'];
                        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

                        // Redirect ke dashboard berdasarkan id_role
                        switch ($user['id_role']) {
                            case 1: header("Location: pages/dash_sys.php"); break; 
                            case 2: header("Location: pages/dash_opt.php"); break;
                            case 3: header("Location: pages/dash_member.php"); break;
                            default:
                                set_flashdata('error', "Role tidak dikenal. Sesi dihancurkan.");
                                session_destroy();
                                break;
                        }
                        exit;
                    } else {
                        set_flashdata('error', "Username atau Password salah.");
                    }
                } else {
                    set_flashdata('error', "Akun Anda dinonaktifkan. Silakan hubungi Administrator.");
                }
            } else {
                set_flashdata('error', "Username atau Password salah.");
            }

        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            set_flashdata('error', "Terjadi kesalahan sistem. Silakan coba lagi.");
        }
    }
    
    // Redirect ke index.php untuk menampilkan flashdata setelah proses POST gagal
    header('Location: index.php');
    exit;
}

// -----------------------------------------------------------------
// LOGIKA PENGAMBILAN DATA 3 INFORMASI TERBARU
// -----------------------------------------------------------------

try {
    global $conn;
    
    // Ambil hanya 3 data terbaru
    $sql_info = "SELECT i.id_info, i.title as judul, i.image, 
                        i.content, 
                        i.posted_at as tanggal_terbit,
                        u.username as nama_posted_by
                 FROM info i
                 JOIN users u ON i.posted_by = u.id_user 
                 ORDER BY i.posted_at DESC 
                 LIMIT 3"; // Batasi hanya 3 record
    
    $stmt_info = $conn->prepare($sql_info);
    $stmt_info->execute();
    $info_data = $stmt_info->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Info DB Error: " . $e->getMessage());
    $info_data = [];
}

// -----------------------------------------------------------------

$page_title = "Selamat Datang di Penerbit Amikom Press";

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="icon" href="assets/img/favicon.png">
    <style>
        /* Gaya Top Bar disesuaikan sesuai input terakhir */
        body {
            background-image: url('assets/img/bg.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment:fixed;
        }
        .top-bar { background-image: url('assets/img/heading.jpg'); padding: 10px 0; border-bottom: 1px solid #eee; padding-top: 20px; padding-bottom: 20px; border-bottom-width: 0px;}
        .logo-text { font-size: 24px; font-weight: bold; color: #333; margin: 0; }
        .main-nav .navbar-nav > li > a { font-weight: 500; font-size: 16px; }
        .carousel-item-dummy { height: 400px; background-color: #777; color: white; text-align: center; padding-top: 150px; }
        .content-section { padding: 60px 0; }
        footer { background-color: #333; color: white; padding: 20px 0; }
        
        /* Gaya tambahan untuk panel berita */
        .panel-body img { object-fit: cover; } /* Gaya untuk memastikan gambar mengisi ruang panel dengan baik */
        .panel-heading h4 { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <a href="#"><img src="assets/img/logo_web.png" width="350px"></a>
            </div>
            <div class="col-xs-6 text-right">
                <h4><b>Segera Terbitkan Buku Anda!</b></h4>
                <p>Amikom Press - Universitas Amikom Purwokerto, Telp (0281) 623321
                <br>press_amikompwt <img src="assets/img/ig.png" width="16px" alt="instagram"> press_amikompwt <img src="assets/img/fb.png" width="16px" alt="facebook"> press_amikompwt <img src="assets/img/tt.png" width="16px" alt="tik-tok"> </p>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="main-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="info_list.php">Informasi</a></li>
                <li><a href="katalog.php">Katalog</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" data-toggle="modal" data-target="#loginModal"><span class="glyphicon glyphicon-log-in"></span> LOGIN</a></li>
                <li><a href="member.php"><span class="glyphicon glyphicon-user"></span> JOIN</a></li>
            </ul>
        </div>
    </div>
</nav>

<header id="myCarousel" class="carousel slide" style="bottom: 20px;">
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

    <div class="carousel-inner">
        <div class="item active">
            <div class="carousel-item-dummy" style="background-image: url('assets/img/bk1.png');">
                <h1>Pusat Publikasi Ilmiah</h1>
                <p>Amikom Press, Wadah Resmi Karya Berkualitas.</p>
            </div>
        </div>
        <div class="item">
            <div class="carousel-item-dummy" style="background-image: url('assets/img/bk2.png');">
                <h1>Layanan ISBN Cepat</h1>
                <p>Ajukan dan pantau status ISBN Anda secara online.</p>
            </div>
        </div>
        <div class="item">
            <div class="carousel-item-dummy" style="background-image: url('assets/img/bk3.png');">
                <h1>Bergabunglah Sekarang</h1>
                <p>Jadilah bagian dari komunitas penulis Amikom Press.</p>
            </div>
        </div>
    </div>

    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
    </a>
</header>


<div class="container">

    <div style="margin-top: 20px;">
        <?php display_flashdata(); ?>
    </div>
    
    <div class="content-section" id="home" style="padding-top: 20px;">
        <h2 class="text-center">Selamat Datang di Amikom Press</h2>
        <p class="lead text-center">
            Amikom Press adalah pilar penerbit yang berdedikasi tinggi di bawah naungan Universitas Amikom Purwokerto. Kami berkomitmen untuk menjadi wadah utama bagi sivitas akademika dan penulis independen dalam mempublikasikan karya-karya ilmiah, buku ajar, dan literatur berkualitas. Melalui layanan ISBN dan proses editorial yang profesional, Amikom Press siap mendampingi setiap langkah Anda dalam menyebarluaskan ilmu pengetahuan.
        </p>
        <hr class="featurette-divider">
        <div class="row text-center">
            
            <div class="col-md-4">
                <span class="glyphicon glyphicon-eye-open" style="font-size: 3em; color: #5bc0de;"></span>
                <h3>Visi & Misi</h3>
                <p>Kami memiliki visi untuk mendorong akselerasi ilmu pengetahuan dengan misi memfasilitasi publikasi ilmiah yang relevan dan berkontribusi nyata pada kemajuan literasi bangsa.</p>
            </div>
            
            <div class="col-md-4">
                <span class="glyphicon glyphicon-wrench" style="font-size: 3em; color: #f0ad4e;"></span>
                <h3>Layanan Unggulan</h3>
                <p>Layanan kami mencakup pengurusan ISBN yang cepat, proses *peer-review* yang terstruktur, desain *layout* profesional, hingga bantuan pemasaran dan distribusi buku.</p>
            </div>
            
            <div class="col-md-4">
                <span class="glyphicon glyphicon-star" style="font-size: 3em; color: #5cb85c;"></span>
                <h3>Keunggulan Kami</h3>
                <p>Amikom Press menjamin kualitas editorial yang ketat, transparansi proses penerbitan, serta dukungan penuh dari tim ahli di bidang akademik dan teknis publikasi.</p>
            </div>
            
        </div>
    </div>
    
    <div class="content-section" id="salam-literasi" style="background-color: #f9f9f9;border-top: 1px solid #eee;padding-top: 0px; padding-bottom: 0px;">
        <h2><span class="glyphicon glyphicon-book"></span> Salam Literasi</h2>
        <div class="row">
            <div class="col-md-2 text-center">
                <img src="assets/img/user/default.png" class="img-responsive" alt="Responsive image">
                <p style="margin-top: 5px;">Dr. Irfan Santiko, M.Kom.<br><small>Pimpinan Penerbit</small></p>
            </div>
            <div class="col-md-10">
                <blockquote style="border-left: 5px solid #5bc0de; padding-left: 15px;">
                    <p>
                        "Literasi adalah jembatan menuju peradaban yang maju. Di Amikom Press, kami percaya bahwa setiap penelitian dan gagasan berharga layak untuk disebarluaskan. Saya mengajak seluruh penulis, baik dosen, mahasiswa, maupun praktisi, untuk bergabung dan menjadikan platform ini sebagai rumah bagi karya-karya terbaik Anda. Mari kita tingkatkan kontribusi literasi kita bersama."
                    </p>
                    <footer>Redaksi Amikom Press</footer>
                </blockquote>
            </div>
        </div>
    </div>
    <hr class="featurette-divider">
    
    <div class="content-section" id="informasi" style="padding-bottom: 0px; padding-top: 20px;">
        <h2><span class="glyphicon glyphicon-info-sign"></span> Informasi Terbaru</h2>
        <p class="lead">Kami senantiasa memberikan informasi terbaru baik berita maupun pengumuman bagi para penulis dan umum.</p>
        
        <div class="row">
            <?php if (empty($info_data)): ?>
                <div class="col-xs-12">
                    <div class="alert alert-info text-center" role="alert">
                        <strong>Informasi Kosong!</strong> Belum ada informasi atau pengumuman terbaru yang dipublikasikan.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($info_data as $info): ?>
                    <?php 
                        // 1. Hapus semua tag HTML dari konten
                        $clean_content = strip_tags($info['content']);
                        // 2. Potong konten yang sudah bersih menjadi ringkasan (misalnya 100 karakter)
                        $ringkasan = substr($clean_content, 0, 100);
                        // Tambahkan elipsis jika konten lebih panjang
                        if (strlen($clean_content) > 100) {
                            $ringkasan .= '...';
                        }
                        // Tentukan path gambar yang benar
                        // Cek apakah ada gambar, jika tidak gunakan default. Asumsi path di DB adalah 'assets/uploads/info/...'
                        $image_path = !empty($info['image']) ? $info['image'] : 'assets/img/placeholder_info.png'; 
                    ?>
                    
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body text-center" style="padding: 0;">
                                <a href="info_detail.php?id=<?= $info['id_info']; ?>">
                                    <img src="assets/img/info/<?= htmlspecialchars($image_path); ?>" alt="<?= htmlspecialchars($info['judul']); ?>" 
                                         class="img-responsive" style="width: 100%; height: 200px; object-fit: cover; border-top-left-radius: 3px; border-top-right-radius: 3px;">
                                </a>
                            </div>
                            <div class="panel-heading">
                                <h4 class="text-center" style="margin-top: 0;">
                                    <?= htmlspecialchars($info['judul']); ?>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <p><?= htmlspecialchars($ringkasan); ?></p>
                                <hr style="margin: 10px 0;">
                                
                                <small class="text-muted">
                                    <span class="glyphicon glyphicon-time"></span> <?= date('d M Y', strtotime($info['tanggal_terbit'])); ?> |
                                    <span class="glyphicon glyphicon-user"></span> Oleh: <?= htmlspecialchars($info['nama_posted_by']); ?>
                                </small>
                            </div>
                            <div class="panel-footer text-center">
                                 <a href="info_detail.php?id=<?= $info['id_info']; ?>" class="btn btn-info btn-block btn-sm">
                                     <span class="glyphicon glyphicon-share-alt"></span> Baca Selengkapnya
                                 </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center" style="margin-top: 20px; padding-bottom: 20px;">
            <a href="info_list.php" class="btn btn-primary btn-lg">Lihat Semua Informasi & Pengumuman <span class="glyphicon glyphicon-arrow-right"></span></a>
        </div>
    </div>
    <hr class="featurette-divider">

    <div class="content-section" id="kontak" style="background-color: #f9f9f9;border-top: 1px solid #eee;padding-top: 0px;">
        <div class="page-header">
            <h2><span class="glyphicon glyphicon-map-marker"></span> Hubungi Kami</h2>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Alamat Penerbit</h3>
                    </div>
                    <div class="panel-body">
                        <address>
                            <strong>Amikom Press</strong><br>
                            Gedung Unit V Lantai 2, Universitas Amikom Purwokerto<br>
                            Jalan Letjend Pol. Soemarto No.126, Watumas, Purwanegara, Purwokerto Utara,<br>
                            Banyumas, Jawa Tengah 53127<br><br>
                            <abbr title="Phone"><i class="glyphicon glyphicon-phone-alt" aria-hidden="true"></i> (0281) 623321</abbr><br>
                            <abbr title="Email"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i> penerbit@amikompurwokerto.ac.id</abbr>
                        </address>
                        <p>Jam Kerja: Senin - Jumat (08.00 - 16.00 WIB)</p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Lokasi Penerbit</h3>
                    </div>
                    <div class="panel-body">
                        <p><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.5810804818652!2d109.22884757500108!3d-7.400745992609247!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e655f072387fab9%3A0x269c1733d358d2b7!2sUniversitas%20Amikom%20Purwokerto%20-%20Gedung%20Utama!5e0!3m2!1sid!2sid!4v1762994683751!5m2!1sid!2sid" width="510" height="177" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<footer class="footer">
    <div class="container">
        <div class="col-sm-7">
            <p class="text-left" style="margin-top: 20px;">
                © <?php echo date('Y'); ?> Sistem Informasi Pengajuan ISBN | Amikom Press - Universitas Amikom Purwokerto.<br>
                Project By: <a href="#">Educollabs</a> | Amikom Press Versi 1.0
            </p>
        </div>
        <div class="col-sm-5">
            <p class="text-right">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="info_list.php">Informasi</a></li>
                    <li><a href="katalog.php">Katalog</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                </ul>
            </p>
        </div>
    </div>
</footer>

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="loginModalLabel"><i class="glyphicon glyphicon-lock"></i> Login Sistem</h4>
            </div>
            <form method="POST" action="index.php"> 
                <input type="hidden" name="login" value="1"> 
                <div class="modal-body">
                    <div class="form-group">
                        <label for="login-username">Username</label>
                        <input type="text" class="form-control" id="login-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" class="form-control" id="login-password" name="password" required>
                    </div>
                    
                    <p class="text-center">Belum punya akun? 
                        <a href="member.php">Daftar Sekarang (JOIN)</a>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
    // Aktifkan Carousel secara otomatis
    $('.carousel').carousel({
        interval: 5000
    });
    
    // Tampilkan Modal kembali jika ada pesan error
    <?php if (has_flashdata('error')): ?>
        $(document).ready(function(){
            $('#loginModal').modal('show');
        });
    <?php endif; ?>
</script>

</body>
</html>