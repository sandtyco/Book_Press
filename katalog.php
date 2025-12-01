<?php
// katalog.php (DAFTAR ISBN TERBIT MENGGUNAKAN DATATABLES)

session_start(); 
require_once 'conn.php';
require_once 'function.php'; 

$katalog_data = [];

try {
    global $conn;
    
    // Ambil data katalog. Join ke tabel users jika ingin menampilkan created_by
    $sql_katalog = "SELECT k.id_katalog, k.judul_katalog, k.penulis_katalog, k.isbn_number, k.created_at, 
                           u.username as nama_creator
                    FROM katalog k
                    JOIN users u ON k.created_by_user_id = u.id_user 
                    ORDER BY k.created_at DESC";
    
    $stmt_katalog = $conn->prepare($sql_katalog);
    $stmt_katalog->execute();
    $katalog_data = $stmt_katalog->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Katalog DB Error: " . $e->getMessage());
    $katalog_data = [];
}

$page_title = "Katalog ISBN Terbit - Amikom Press";

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css">
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
        
        /* Penyesuaian agar DataTables terlihat rapi */
        #katalogTable_wrapper { margin-top: 20px; }
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
        <div class="page-header">
            <h1><span class="glyphicon glyphicon-book"></span> Katalog ISBN Terbit Amikom Press</h1>
            <p class="lead">Daftar lengkap ISBN yang telah diterbitkan oleh Amikom Press. Gunakan fitur pencarian untuk menemukan buku spesifik.</p>
        </div>
        
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="katalogTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th>Nomor ISBN</th>
                                <th style="width: 15%;">Tanggal Terbit</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($katalog_data)): ?>
                                <?php $no = 1; foreach ($katalog_data as $katalog): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($katalog['judul_katalog']); ?></td>
                                    <td><?= htmlspecialchars($katalog['penulis_katalog']); ?></td>
                                    <td style="color: #1703ccff;"><?= htmlspecialchars($katalog['isbn_number']); ?></td>
                                    <td><?= date('d M Y', strtotime($katalog['created_at'])); ?></td>
                                    <td class="text-center">
                                        <a href="katalog_view.php?id=<?= $katalog['id_katalog']; ?>" class="btn btn-primary btn-xs">
                                            <span class="glyphicon glyphicon-eye-open"></span> Preview
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data ISBN yang terbit.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // INISIALISASI DATATABLES
        $('#katalogTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json" // Menggunakan bahasa Indonesia
            },
            "pagingType": "full_numbers"
        });
    });
</script>

</body>
</html>