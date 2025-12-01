<?php
// includes/header.php

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Data pengguna yang sedang login
// Variabel ini akan berisi nama_lengkap dari sesi (atau 'Tamu' jika sesi belum diset/login)
$current_nama_lengkap = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Tamu';
$current_id_role = isset($_SESSION['id_role']) ? $_SESSION['id_role'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Sistem Press Amikom Purwokerto</title>
    <link rel="icon" href="../assets/img/favicon.png">
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet"> 

    <link href="https://cdn.datatables.net/2.3.5/css/dataTables.bootstrap.min.css" rel="stylesheet" integrity="sha384-7itFZPytguFHyr6N46MNRN/dlvPjma7RLUqv06zk1JhOmyJbtRVWmvZ4nJm4QqgJ" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/searchpanes/2.3.5/css/searchPanes.bootstrap.min.css" rel="stylesheet" integrity="sha384-JiN2Hnt+gdHZT8EHluYOZbwmBsZ3Zlgn/vsILhf/U6TxuX7FpGT/6ASGbfT0FJGc" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid" style="margin-bottom: 20px;">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="../assets/img/logo_admin.png" width="250px"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse" style="margin-top: 18px;">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">Halo, <b><?= htmlspecialchars($current_nama_lengkap) ?></b></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-user"></span> Akun <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="profil.php">Profil Saya</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="../logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid" style="margin-top: 50px;">
    <div class="row">

        