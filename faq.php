<?php
// faq.php (HALAMAN FAQ - PERTANYAAN YANG SERING DIAJUKAN)

session_start(); 
require_once 'conn.php';
require_once 'function.php'; 

// Pengaturan Judul Halaman
$page_title = "FAQ (Pertanyaan Umum) - Amikom Press";

// Data dummy untuk Pertanyaan Umum (Accordion Group 1)
$faq_umum = [
    [
        'id' => 'collapseUmum1',
        'q' => 'Apa itu Amikom Press?',
        'a' => 'Amikom Press adalah penerbit resmi di bawah naungan Universitas Amikom Purwokerto yang berfokus pada publikasi karya ilmiah, buku ajar, dan literatur dari sivitas akademika maupun penulis umum.'
    ],
    [
        'id' => 'collapseUmum2',
        'q' => 'Jenis buku apa saja yang bisa diterbitkan di Amikom Press?',
        'a' => 'Kami menerima berbagai jenis naskah, termasuk buku ajar, buku referensi, monograf, hasil penelitian, dan buku populer ilmiah yang relevan dengan fokus keilmuan kami.'
    ],
    [
        'id' => 'collapseUmum3',
        'q' => 'Bagaimana mekanisme pengurusan ISBN?',
        'a' => 'Pengurusan ISBN dilakukan secara internal oleh tim Amikom Press dan diajukan ke Perpustakaan Nasional setelah naskah dinyatakan lolos *review* dan siap cetak. Penulis hanya perlu melengkapi dokumen persyaratan.'
    ],
    [
        'id' => 'collapseUmum4',
        'q' => 'Apakah Amikom Press melayani jasa cetak saja?',
        'a' => 'Fokus utama kami adalah penerbitan yang mencakup ISBN, *layout*, *cover*, dan legalitas. Kami tidak melayani jasa cetak murni tanpa proses penerbitan.'
    ],
];

// Data dummy untuk Pertanyaan Pengguna (Accordion Group 2)
$faq_pengguna = [
    [
        'id' => 'collapsePengguna1',
        'q' => 'Apa benefit bergabung menjadi penulis di Amikom Press?',
        'a' => 'Penulis akan mendapatkan ISBN resmi, pendampingan *review* dari pakar, desain profesional, hak cipta yang jelas, dan persentase royalti dari penjualan buku (jika dijual secara komersial).'
    ],
    [
        'id' => 'collapsePengguna2',
        'q' => 'Bagaimana proses awal untuk mengajukan naskah?',
        'a' => 'Anda harus mendaftar sebagai *member* melalui halaman "JOIN". Setelah login, Anda bisa mengakses dashboard untuk mengajukan proposal dan naskah awal.'
    ],
    [
        'id' => 'collapsePengguna3',
        'q' => 'Apakah ada biaya yang harus dibayar penulis?',
        'a' => 'Tergantung pada jenis penerbitan. Untuk sivitas akademika, kami menyediakan skema subsidi. Untuk umum, mungkin terdapat biaya *self-publishing* atau paket penerbitan tertentu. Detail biaya akan dijelaskan setelah naskah diterima.'
    ],
    [
        'id' => 'collapsePengguna4',
        'q' => 'Berapa lama proses penerbitan hingga terbit?',
        'a' => 'Proses penerbitan standar (mulai dari *review*, revisi, *layout*, hingga ISBN terbit) berkisar antara 1 hingga 3 bulan, tergantung kompleksitas naskah dan kecepatan revisi dari penulis.'
    ],
];

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
        /* CSS yang sama dengan template sebelumnya */
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
        
        /* Gaya Khusus Accordion/FAQ */
        .panel-title a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .panel-heading {
            padding: 0;
        }
        .panel-default > .panel-heading {
            background-color: #f5f5f5;
        }
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
                <li class="active"><a href="faq.php">FAQ</a></li> <li><a href="kontak.php">Kontak</a></li>
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
        <ol class="breadcrumb" style="margin-bottom: 30px;">
            <li><a href="index.php">Home</a></li>
            <li class="active">FAQ</li>
        </ol>

        <div class="page-header">
            <h1><span class="glyphicon glyphicon-question-sign"></span> Pertanyaan yang Sering Diajukan (FAQ)</h1>
            <p class="lead">Temukan jawaban atas pertanyaan umum seputar penerbitan, layanan, dan menjadi penulis di Amikom Press.</p>
        </div>

        <h2>1. Pertanyaan Seputar Mekanisme dan Layanan</h2>
        <div class="panel-group" id="accordionUmum" role="tablist" aria-multiselectable="true">
            <?php foreach ($faq_umum as $index => $faq): ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingUmum<?= $index + 1; ?>">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordionUmum" href="#<?= $faq['id']; ?>" aria-expanded="<?= ($index == 0) ? 'true' : 'false'; ?>" aria-controls="<?= $faq['id']; ?>">
                                <span class="glyphicon glyphicon-plus-sign" style="margin-right: 10px;"></span> <?= htmlspecialchars($faq['q']); ?>
                            </a>
                        </h4>
                    </div>
                    <div id="<?= $faq['id']; ?>" class="panel-collapse collapse <?= ($index == 0) ? 'in' : ''; ?>" role="tabpanel" aria-labelledby="headingUmum<?= $index + 1; ?>">
                        <div class="panel-body">
                            <?= htmlspecialchars($faq['a']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <br>
        
        <h2>2. Pertanyaan Seputar Penulis dan Keanggotaan</h2>
        <div class="panel-group" id="accordionPengguna" role="tablist" aria-multiselectable="true">
            <?php foreach ($faq_pengguna as $index => $faq): ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingPengguna<?= $index + 1; ?>">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordionPengguna" href="#<?= $faq['id']; ?>" aria-expanded="false" aria-controls="<?= $faq['id']; ?>">
                                <span class="glyphicon glyphicon-plus-sign" style="margin-right: 10px;"></span> <?= htmlspecialchars($faq['q']); ?>
                            </a>
                        </h4>
                    </div>
                    <div id="<?= $faq['id']; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingPengguna<?= $index + 1; ?>">
                        <div class="panel-body">
                            <?= htmlspecialchars($faq['a']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="alert alert-info text-center" style="margin-top: 40px;">
            Jika pertanyaan Anda belum terjawab, silakan hubungi kami melalui halaman <a href="kontak.php">Kontak</a> atau daftar <a href="member.php">JOIN</a> untuk memulai proses penerbitan!
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="info_list.php">Informasi</a></li>
                    <li><a href="katalog.php">Katalog</a></li>
                    <li class="active"><a href="faq.php">FAQ</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                </ul>
            </p>
        </div>
    </div>
</footer>

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
    // Script untuk mengganti ikon saat dropdown dibuka/ditutup (opsional, tapi mempercantik tampilan)
    $(document).on('show.bs.collapse', '.panel-collapse', function () {
        $(this).prev('.panel-heading').find('.glyphicon').removeClass('glyphicon-plus-sign').addClass('glyphicon-minus-sign');
    });
    $(document).on('hide.bs.collapse', '.panel-collapse', function () {
        $(this).prev('.panel-heading').find('.glyphicon').removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');
    });
</script>

</body>
</html>