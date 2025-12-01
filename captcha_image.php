<?php
// captcha_image.php (DI ROOT DIRECTORY /press)

// Pastikan ekstensi GD diaktifkan di php.ini Anda
if (!extension_loaded('gd')) {
    // Sebagai fallback atau pesan error jika GD tidak ada
    header('Content-type: image/png');
    $image = imagecreate(200, 40);
    $bg_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 255, 0, 0);
    imagestring($image, 5, 5, 10, 'GD Extension Missing!', $text_color);
    imagepng($image);
    imagedestroy($image);
    exit;
}

session_start();

// 1. Hasilkan Soal Matematika Sederhana
$num1 = rand(1, 9);
$num2 = rand(1, 9);
$result = $num1 + $num2;

// Simpan hasil penjumlahan ke dalam session agar bisa diverifikasi
$_SESSION['captcha_result'] = $result;

// Buat string teks CAPTCHA yang akan ditampilkan
$captcha_text = "$num1 + $num2 = ?";

// 2. Pengaturan dan Pembuatan Gambar
$img_width = 150;
$img_height = 40;
$image = imagecreate($img_width, $img_height);

// 3. Definisi Warna
$bg_color = imagecolorallocate($image, 255, 255, 255); // Putih (Latar Belakang)
$text_color = imagecolorallocate($image, 50, 50, 50);   // Abu-abu gelap (Teks)
$line_color = imagecolorallocate($image, 200, 200, 200); // Abu-abu terang (Noise)

// Isi latar belakang dengan warna putih
imagefilledrectangle($image, 0, 0, $img_width, $img_height, $bg_color);

// 4. Tambahkan Garis Noise (Opsional, untuk mempersulit bot OCR)
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $img_width), rand(0, $img_height), rand(0, $img_width), rand(0, $img_height), $line_color);
}

// 5. Tulis Teks CAPTCHA
// Font path bisa berbeda-beda. Kita gunakan font bawaan (imagestring)
$font_size = 5; 
$x = 10;
$y = 15;
imagestring($image, $font_size, $x, $y, $captcha_text, $text_color);

// 6. Output Gambar dan Hapus Sumber Daya
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);

// Penting: pastikan tidak ada output HTML/teks lain di file ini
?>