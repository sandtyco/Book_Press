<?php
// profil.php (HALAMAN PROFIL INSTITUSI - DUMMY)

session_start(); 
require_once 'conn.php';
require_once 'function.php'; 

// Pengaturan Judul Halaman
$page_title = "Profil Amikom Press - Universitas Amikom Purwokerto";

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
        /* CSS yang sama dengan index.php untuk konsistensi */
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
        
        /* Gaya Tambahan */
        .feature-item { text-align: center; padding: 20px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 20px; }
        .feature-item h3 { margin-top: 0; }
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
                <li class="active"><a href="profil.php">Profil</a></li>
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

<div class="container">

    <div class="content-section">
        <ol class="breadcrumb" style="margin-bottom: 30px;">
            <li><a href="index.php">Home</a></li>
            <li class="active">Profil</li>
        </ol>
        
        <div class="page-header">
            <h1><span class="glyphicon glyphicon-info-sign"></span> Profil Penerbit Amikom Press</h1>
            <p class="lead">Mengenal lebih dekat visi, misi, dan komitmen kami dalam dunia literasi dan publikasi.</p>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Visi & Misi Kami</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4><span class="glyphicon glyphicon-eye-open"></span> Visi</h4>
                        <p>Menjadi **Penerbit Perguruan Tinggi terkemuka** di Indonesia yang berbasis teknologi informasi dan menghasilkan karya-karya ilmiah yang inovatif, relevan, serta memiliki daya saing global pada tahun 2030.</p>
                    </div>
                    <div class="col-md-6">
                        <h4><span class="glyphicon glyphicon-tasks"></span> Misi</h4>
                        <ul>
                            <li>Mendukung proses pendidikan dan penelitian dengan memfasilitasi publikasi buku ajar dan buku referensi berkualitas.</li>
                            <li>Menyediakan layanan penerbitan yang profesional, transparan, dan berorientasi pada kepuasan penulis.</li>
                            <li>Mendorong peningkatan jumlah karya ilmiah Dosen dan Mahasiswa Universitas Amikom Purwokerto.</li>
                            <li>Mengembangkan platform publikasi digital untuk menjangkau audiens yang lebih luas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>

        <h2>Sejarah Singkat dan Kedudukan</h2>
        <p>Amikom Press didirikan pada tahun **2018** sebagai unit kerja di bawah Rektorat Universitas Amikom Purwokerto dengan tujuan utama mendukung Tri Dharma Perguruan Tinggi, khususnya dalam bidang penelitian dan pengabdian masyarakat melalui jalur publikasi buku. Sejak pendiriannya, kami telah menerbitkan ratusan judul buku ajar, monograf, dan buku populer yang memberikan kontribusi signifikan bagi perkembangan ilmu pengetahuan di Indonesia.</p>

        <hr>

        <h2>Layanan Utama Kami</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="feature-item">
                    <span class="glyphicon glyphicon-barcode" style="font-size: 3em; color: #5bc0de;"></span>
                    <h3>Pengurusan ISBN</h3>
                    <p>Layanan pengurusan ISBN cepat dan terintegrasi dengan Perpusnas, memastikan legalitas karya Anda.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <span class="glyphicon glyphicon-pencil" style="font-size: 3em; color: #f0ad4e;"></span>
                    <h3>Editorial & Desain</h3>
                    <p>Tim editor profesional dan desainer layout yang memastikan kualitas tampilan dan isi buku Anda.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <span class="glyphicon glyphicon-cloud-upload" style="font-size: 3em; color: #5cb85c;"></span>
                    <h3>Penerbitan Digital</h3>
                    <p>Fasilitasi publikasi dalam format *e-book* (Digital) untuk menjangkau pasar yang lebih luas.</p>
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
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><a href="profil.php">Profil</a></li>
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
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>