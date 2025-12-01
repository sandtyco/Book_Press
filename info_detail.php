<?php
// info_detail.php (DETAIL INFORMASI)

session_start(); 
require_once 'conn.php';
require_once 'function.php'; 

$info_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($info_id == 0) {
    // Jika ID tidak valid, arahkan kembali ke daftar
    header('Location: info_list.php');
    exit;
}

$info_detail = null;
try {
    global $conn;
    
    // Ambil detail informasi berdasarkan ID
    $sql_detail = "SELECT i.id_info, i.title as judul, i.image, 
                           i.content, 
                           i.posted_at as tanggal_terbit,
                           u.username as nama_posted_by
                    FROM info i
                    JOIN users u ON i.posted_by = u.id_user 
                    WHERE i.id_info = :id_info";
    
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->bindParam(':id_info', $info_id, PDO::PARAM_INT);
    $stmt_detail->execute();
    $info_detail = $stmt_detail->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Info Detail DB Error: " . $e->getMessage());
    $info_detail = null;
}

if (!$info_detail) {
    // Jika data tidak ditemukan
    $page_title = "Informasi Tidak Ditemukan - Amikom Press";
} else {
    $page_title = htmlspecialchars($info_detail['judul']) . " - Amikom Press";
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
        /* CSS yang sama dengan index.php */
        body {
            background-image: url('../assets/img/bg.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment:fixed;
        }
        .top-bar { background-image: url('assets/img/heading.jpg'); padding: 10px 0; border-bottom: 1px solid #eee; padding-top: 20px; padding-bottom: 20px; border-bottom-width: 0px;}
        .top-bar .container { max-width: 100%; }
        .logo-text { font-size: 24px; font-weight: bold; color: #333; margin: 0; }
        .main-nav .navbar-nav > li > a { font-weight: 500; font-size: 16px; }
        .content-section { padding: 40px 0; }
        footer { background-color: #333; color: white; padding: 20px 0; }
        
        /* Gaya Khusus Detail */
        .info-header { margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px;}
        .info-content img { max-width: 100%; height: auto; margin: 15px 0; border-radius: 5px; }
        .info-meta { font-size: 14px; color: #777; margin-top: 10px;}
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

<div class="container">

    <div class="content-section">
        <ol class="breadcrumb" style="margin-bottom: 30px;">
            <li><a href="index.php">Home</a></li>
            <li><a href="info_list.php">Informasi</a></li>
            <li class="active">Detail</li>
        </ol>
        
        <?php if (!$info_detail): ?>
            <div class="alert alert-danger text-center">
                <h2><span class="glyphicon glyphicon-warning-sign"></span> Informasi Tidak Ditemukan!</h2>
                <p>Data yang Anda cari mungkin telah dihapus atau ID yang dimasukkan salah.</p>
                <a href="info_list.php" class="btn btn-warning" style="margin-top: 10px;">Kembali ke Daftar Informasi</a>
            </div>
        <?php else: ?>
            <article>
                <div class="info-header">
                    <h1><?= htmlspecialchars($info_detail['judul']); ?></h1>
                    <div class="info-meta">
                        Diposting pada: **<?= date('d F Y H:i', strtotime($info_detail['tanggal_terbit'])); ?>** | Oleh: **<?= htmlspecialchars($info_detail['nama_posted_by']); ?>**
                    </div>
                </div>

                <?php if (!empty($info_detail['image'])): ?>
                    <div class="text-center" style="margin-bottom: 25px;">
                        <img src="assets/img/info/<?= htmlspecialchars($info_detail['image']); ?>" alt="Gambar Utama Berita" class="img-thumbnail" style="max-height: 400px; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <div class="info-content">
                    <?= $info_detail['content']; ?>
                </div>

                <hr style="margin-top: 50px;">
                <a href="info_list.php" class="btn btn-info"><span class="glyphicon glyphicon-chevron-left"></span> Kembali ke Daftar Informasi</a>
            </article>
        <?php endif; ?>

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
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>