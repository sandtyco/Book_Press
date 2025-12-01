<?php
// member.php (Form Registrasi Member, di ROOT DIRECTORY /press)

session_start();
require_once 'function.php'; // Untuk flashdata
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Member ISBN</title>
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
        .register-panel { margin-top: 50px; margin-bottom: 50px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="register-panel panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fas fa-user-plus"></i> Registrasi Akun Member</h3>
                </div>
                <div class="panel-body">
                    
                    <?php display_flashdata(); ?>

                    <form role="form" method="post" action="register.php">
                        <p class="small text-danger">Field bertanda (*) wajib diisi.</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <input class="form-control" name="nama_lengkap" type="text" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nomor Telepon <span class="text-danger">*</span></label>
                                    <input class="form-control" name="no_telp" type="text" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea class="form-control" name="alamat" rows="2"></textarea>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input class="form-control" name="email" type="email" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input class="form-control" name="username" type="text" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input class="form-control" name="password" type="password" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Ulangi Password <span class="text-danger">*</span></label>
                            <input class="form-control" name="re_password" type="password" required>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group">
                            <label>Kode Verifikasi (Hitung) <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <img src="captcha_image.php" alt="CAPTCHA" style="border: 1px solid #ccc; padding: 5px; width: 100%;">
                                </div>
                                <div class="col-xs-6">
                                    <input class="form-control" placeholder="Masukkan Hasil" name="captcha_input" type="text" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-lg btn-primary btn-block">Daftar Sekarang</button>
                        
                        <hr>
                        
                        <p class="text-center small">Sudah punya akun? <a href="login.php">Login di sini</a> | Kembali ke <a href="index.php">Home</a></p>
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