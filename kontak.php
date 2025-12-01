<?php
// kontak.php (HALAMAN KONTAK - DENGAN FUNGSI PHPMailer)

session_start(); 
require_once 'conn.php';
require_once 'function.php'; 

// --- 1. IMPOR PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Sesuaikan path ini jika perlu!
require 'PHPMailer/src/Exception.php'; 
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
// -------------------------

$page_title = "Hubungi Kami - Amikom Press";

// -----------------------------------------------------------------
// LOGIKA PENGIRIMAN EMAIL MENGGUNAKAN PHPMailer
// -----------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    
    // 1. Ambil dan sanitasi input
    $nama = sanitize_input($_POST['nama']);
    $email_pengguna = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subjek_user = sanitize_input($_POST['subjek']);
    $pesan_user = sanitize_input($_POST['pesan']);
    
    // Email Penerima Pesan (Tujuan)
    $email_penerima = "penerbit@amikompurwokerto.ac.id"; 
    
    // Email Pengirim Otentikasi (Akun Pribadi Anda)
    $auth_email = "sandtyco@gmail.com"; 

    // 2. Validasi dasar
    if (empty($nama) || empty($email_pengguna) || empty($subjek_user) || empty($pesan_user) || !filter_var($email_pengguna, FILTER_VALIDATE_EMAIL)) {
        set_flashdata('error', "Mohon lengkapi semua kolom dengan format yang benar.");
    } else {
        
        $mail = new PHPMailer(true);
        
        try {
            // =========================================================
            // KREDENSIAL SMTP (MENGGUNAKAN AKUN sandtyco@gmail.com)
            // =========================================================
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';             
            $mail->SMTPAuth   = true;
            $mail->Username   = $auth_email;                     // sandtyco@gmail.com
            $mail->Password   = 'qgpa vrfg iwxr ousl';    // !!! GANTI INI DENGAN PASSWORD APLIKASI ASLI !!!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587; 
            $mail->CharSet    = 'UTF-8';
            // =========================================================

            // Recipients
            $mail->setFrom($auth_email, 'Amikom Press (Website)'); // Pengirim Otentikasi
            $mail->addAddress($email_penerima, 'Amikom Press');                   // Email Tujuan Penerbit
            
            // Reply-To (PENTING! Agar Admin bisa membalas ke email pengguna)
            $mail->addReplyTo($email_pengguna, $nama); 

            // Content (Isi Email)
            $mail->isHTML(false); // Kirim sebagai Plain Text
            $mail->Subject = "[PRESS INFO] " . $subjek_user;
            
            $email_body = "Anda menerima pesan baru dari formulir kontak website Amikom Press.\n\n";
            $email_body .= "Nama Pengirim: " . $nama . "\n";
            $email_body .= "Email Pengirim: " . $email_pengguna . "\n";
            $email_body .= "Subjek: " . $subjek_user . "\n\n";
            $email_body .= "Isi Pesan:\n" . $pesan_user . "\n";
            $email_body .= "\n----------------------------------------\n";
            $email_body .= "Pesan dikirim pada: " . date('Y-m-d H:i:s');
            
            $mail->Body = $email_body;

            $mail->send();
            set_flashdata('success', "Pesan Anda berhasil dikirim! Kami akan segera meresponsnya.");
            
        } catch (Exception $e) {
            set_flashdata('error', "Maaf, pesan gagal terkirim. Mailer Error: {$mail->ErrorInfo}");
        }
    }
    
    header('Location: kontak.php');
    exit;
}

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
        /* CSS Konsisten Template */
        body {
            background-image: url('assets/img/bg.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment:fixed;
        }
        .top-bar { background-image: url('assets/img/heading.jpg'); padding: 10px 0; border-bottom: 1px solid #eee; padding-top: 20px; padding-bottom: 20px; border-bottom-width: 0px;}
        .logo-text { font-size: 24px; font-weight: bold; color: #333; margin: 0; }
        .main-nav .navbar-nav > li > a { font-weight: 500; font-size: 16px; }
        .content-section { padding: 40px 0; }
        footer { background-color: #333; color: white; padding: 20px 0; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <a href="index.php"><img src="assets/img/logo_web.png" width="350px"></a>
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
                <li><a href="index.php">Home</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="info_list.php">Informasi</a></li>
                <li><a href="katalog.php">Katalog</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li class="active"><a href="kontak.php">Kontak</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" data-toggle="modal" data-target="#loginModal"><span class="glyphicon glyphicon-log-in"></span> LOGIN</a></li>
                <li><a href="member.php"><span class="glyphicon glyphicon-user"></span> JOIN</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">

    <div class="content-section">
        <ol class="breadcrumb" style="margin-bottom: 20px;">
            <li><a href="index.php">Home</a></li>
            <li class="active">Kontak</li>
        </ol>
        
        <?php display_flashdata(); ?>

        <div class="page-header">
            <h1><span class="glyphicon glyphicon-phone"></span> Hubungi Kami</h1>
            <p class="lead">Kami siap melayani segala pertanyaan terkait penerbitan dan ISBN. Silakan hubungi kami melalui jalur yang tersedia atau kunjungi lokasi kami.</p>
        </div>

        <div class="row">
            
            <div class="col-md-6">
                <h2>Informasi Kontak Resmi</h2>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <address>
                            <strong>Amikom Press</strong><br>
                            Gedung Unit V Lantai 2, Universitas Amikom Purwokerto<br>
                            Jalan Letjend Pol. Soemarto No.126, Watumas, Purwanegara, Purwokerto Utara,<br>
                            Banyumas, Jawa Tengah 53127
                        </address>
                        <hr>
                        <p>
                            <span class="glyphicon glyphicon-phone-alt"></span> **Telepon:** (0281) 623321<br>
                            <span class="glyphicon glyphicon-envelope"></span> **Email:** <a href="mailto:penerbit@amikompurwokerto.ac.id">penerbit@amikompurwokerto.ac.id</a><br>
                            <span class="glyphicon glyphicon-globe"></span> **Website:** www.amikompurwokerto.ac.id
                        </p>
                        <hr>
                        <p>
                            **Jam Operasional:** Senin - Jumat (08.00 - 16.00 WIB)
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <h2>Lokasi Kami</h2>
                <div class="panel panel-default">
                    <div class="panel-body" style="padding: 0;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.5810804818652!2d109.22884757500108!3d-7.400745992609247!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e655f072387fab9%3A0x269c1733d358d2b7!2sUniversitas%20Amikom%20Purwokerto%20-%20Gedung%20Utama!5e0!3m2!1sid!2sid!4v1762994683751!5m2!1sid!2sid" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>

        <h2>Kirim Pesan</h2>
        <p>Gunakan formulir ini untuk mengirimkan pertanyaan non-urgensi.</p>
        
        <form method="POST" action="kontak.php">
            <input type="hidden" name="send_message" value="1">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="subjek">Subjek</label>
                <input type="text" class="form-control" id="subjek" name="subjek" required>
            </div>
            <div class="form-group">
                <label for="pesan">Pesan</label>
                <textarea class="form-control" id="pesan" name="pesan" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-send"></span> Kirim Pesan</button>
        </form>

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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="info_list.php">Informasi</a></li>
                    <li><a href="katalog.php">Katalog</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li class="active"><a href="kontak.php">Kontak</a></li>
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
</body>
</html>