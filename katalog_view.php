<?php
// katalog_view.php (DETAIL KATALOG PUBLIK)

session_start(); 
require_once 'conn.php';
require_once 'function.php'; 

$katalog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($katalog_id == 0) {
    // Jika ID tidak valid, arahkan kembali ke daftar
    header('Location: katalog.php');
    exit;
}

$katalog_detail = null;
try {
    global $conn;
    
    // Ambil semua detail katalog berdasarkan ID
    $sql_detail = "SELECT k.*, u.username as nama_creator
                   FROM katalog k
                   JOIN users u ON k.created_by_user_id = u.id_user 
                   WHERE k.id_katalog = :id_katalog";
    
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->bindParam(':id_katalog', $katalog_id, PDO::PARAM_INT);
    $stmt_detail->execute();
    $katalog_detail = $stmt_detail->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Katalog Detail DB Error: " . $e->getMessage());
    $katalog_detail = null;
}

if (!$katalog_detail) {
    $page_title = "Katalog Tidak Ditemukan - Amikom Press";
} else {
    $page_title = htmlspecialchars($katalog_detail['judul_katalog']) . " - Amikom Press";
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
        
        /* Gaya Khusus Detail Katalog */
        .cover-img { max-width: 300px; height: auto; border: 1px solid #ccc; padding: 5px; border-radius: 5px; }
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
                <li class="active"><a href="katalog.php">Katalog</a></li>
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
            <li><a href="katalog.php">Katalog</a></li>
            <li class="active">Detail</li>
        </ol>
        
        <?php if (!$katalog_detail): ?>
            <div class="alert alert-danger text-center">
                <h2><span class="glyphicon glyphicon-warning-sign"></span> Katalog Tidak Ditemukan!</h2>
                <p>Data buku yang Anda cari mungkin tidak ada dalam katalog.</p>
                <a href="katalog.php" class="btn btn-warning" style="margin-top: 10px;">Kembali ke Daftar Katalog</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-4 text-center">
                    <?php 
                        $cover_path = !empty($katalog_detail['cover_katalog']) ? $katalog_detail['cover_katalog'] : 'assets/img/default_cover.png';
                    ?>
                    <img src="assets/uploads/cover/<?= htmlspecialchars($cover_path); ?>" alt="Cover Buku" class="cover-img img-responsive center-block"><br>
                    <p class="text-muted">Nomor ISBN:</p>
                    <h3 style="margin-top: 20px; color: #1703ccff;"><?= htmlspecialchars($katalog_detail['isbn_number']); ?></h3>
                </div>
                
                <div class="col-md-8">
                    <h1 style="margin-top: 0;"><?= htmlspecialchars($katalog_detail['judul_katalog']); ?></h1>
                    
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Penulis:</th>
                            <td><?= htmlspecialchars($katalog_detail['penulis_katalog']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Terbit:</th>
                            <td><?= date('d F Y', strtotime($katalog_detail['created_at'])); ?></td>
                        </tr>
                        <tr>
                            <th>Upload oleh:</th>
                            <td><?= htmlspecialchars($katalog_detail['nama_creator']); ?></td>
                        </tr>
                    </table>
                    
                    <h3>Sinopsis Buku:</h3>
                    <div class="well">
                        <?= nl2br(htmlspecialchars($katalog_detail['sinopsis_katalog'])); ?>
                    </div>
                    
                    <a href="katalog.php" class="btn btn-info"><span class="glyphicon glyphicon-chevron-left"></span> Kembali ke Daftar Katalog</a>
                </div>
            </div>
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="info_list.php">Informasi</a></li>
                    <li class="active"><a href="katalog.php">Katalog</a></li>
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