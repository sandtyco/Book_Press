<?php
// info_list.php (DAFTAR SEMUA INFORMASI DENGAN PAGINATION)

session_start(); 
require_once 'conn.php';
require_once 'function.php'; 

// -----------------------------------------------------------------
// LOGIKA PENGAMBILAN DATA INFORMASI DAN PAGINATION
// -----------------------------------------------------------------

$limit = 4; // Batasan 5 berita per halaman
// Ambil nomor halaman dari URL, default ke 1
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
// Hitung posisi awal data
$start = ($page > 0) ? (($page * $limit) - $limit) : 0;

try {
    global $conn;
    
    // a. Hitung Total Data
    $total_data_stmt = $conn->query("SELECT COUNT(id_info) FROM info");
    $total_data = $total_data_stmt->fetchColumn();
    $total_pages = ceil($total_data / $limit);

    // b. Ambil Data Berita
    $sql_info = "SELECT i.id_info, i.title as judul, i.image, 
                        i.content, 
                        i.posted_at as tanggal_terbit,
                        u.username as nama_posted_by
                 FROM info i
                 JOIN users u ON i.posted_by = u.id_user 
                 ORDER BY i.posted_at DESC 
                 LIMIT :start, :limit";
    
    $stmt_info = $conn->prepare($sql_info);
    $stmt_info->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt_info->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt_info->execute();
    $info_data = $stmt_info->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Info List DB Error: " . $e->getMessage());
    $info_data = [];
    $total_pages = 0;
}

$page_title = "Semua Informasi - Amikom Press";

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
            background-image: url('../assets/img/bg.jpg'); /* Perhatikan path jika diletakkan di subfolder */
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
        .list-group-item .row img { max-width: 100%; height: auto; }
        
        /* Tambahan CSS untuk Tampilan List */
        .info-list-item h4 { margin-top: 0; }
        .info-list-item .meta { font-size: 12px; color: #777; margin-bottom: 10px; display: block; }
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
                <li class="active"><a href="info_list.php">Informasi</a></li>
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
        <div class="page-header">
            <h1><span class="glyphicon glyphicon-th-list"></span> Daftar Semua Informasi & Pengumuman</h1>
        </div>
        
        <div class="list-group">
            <?php if (empty($info_data)): ?>
                <div class="alert alert-warning text-center">
                    Tidak ada informasi yang ditemukan.
                </div>
            <?php else: ?>
                <?php foreach ($info_data as $info): ?>
                    <?php 
                        $clean_content = strip_tags($info['content']);
                        $ringkasan = substr($clean_content, 0, 200);
                        if (strlen($clean_content) > 200) { $ringkasan .= '...'; }
                        
                        $image_path = !empty($info['image']) ? $info['image'] : 'assets/img/placeholder_info_list.png';
                    ?>
                    
                    <a href="info_detail.php?id=<?= $info['id_info']; ?>" class="list-group-item info-list-item">
                        <div class="row">
                            <div class="col-sm-2 col-xs-4">
                                <img src="assets/img/info/<?= htmlspecialchars($image_path); ?>" alt="<?= htmlspecialchars($info['judul']); ?>" 
                                     class="img-responsive" style="height: 100px; object-fit: cover;">
                            </div>
                            
                            <div class="col-sm-10 col-xs-8">
                                <h4 class="list-group-item-heading">
                                    <?= htmlspecialchars($info['judul']); ?>
                                </h4>
                                <span class="meta">
                                    <span class="glyphicon glyphicon-time"></span> <?= date('d F Y', strtotime($info['tanggal_terbit'])); ?> | 
                                    <span class="glyphicon glyphicon-user"></span> Oleh: <?= htmlspecialchars($info['nama_posted_by']); ?>
                                </span>
                                <p class="list-group-item-text">
                                    <?= htmlspecialchars($ringkasan); ?>
                                </p>
                                <p class="text-right" style="margin-top: 5px;">
                                    <span class="btn btn-xs btn-default">Baca Selengkapnya »</span>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <nav class="text-center">
                <ul class="pagination">
                    <li class="<?= ($page <= 1) ? 'disabled' : ''; ?>">
                        <a href="?halaman=<?= $page - 1; ?>">«</a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="<?= ($page == $i) ? 'active' : ''; ?>">
                            <a href="?halaman=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="<?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a href="?halaman=<?= $page + 1; ?>">»</a>
                    </li>
                </ul>
            </nav>
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
                    <li class="active"><a href="info_list.php">Informasi</a></li>
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