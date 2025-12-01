<?php
// function.php (Kode Final Gabungan)

// Pastikan session sudah dimulai sebelum menggunakan $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------------------------------------------------
// 1. Fungsi Sanitasi Input
// ----------------------------------------------------------------------
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ----------------------------------------------------------------------
// 2. Fungsi Cek Status Login
// ----------------------------------------------------------------------
function check_login() {
    if (!isset($_SESSION['id_user']) || empty($_SESSION['id_user'])) {
        header("Location: ../index.php"); 
        exit;
    }
}

function check_auth() { 
    if (!is_logged_in()) {
        // Redirection yang benar dari dalam folder 'pages'
        set_flashdata('error', 'Anda harus login untuk mengakses halaman ini.');
        header("Location: ../index.php"); 
        exit;
    }
}

// ----------------------------------------------------------------------
// 3. Fungsi Cek Hak Akses (Role-Based Access Control)
// ----------------------------------------------------------------------
function check_role($allowed_roles) {
    // $allowed_roles adalah array dari id_role yang diizinkan, e.g., [1, 2]

    if (!isset($_SESSION['id_role']) || !in_array($_SESSION['id_role'], $allowed_roles)) {
        // Jika peran tidak diizinkan, redirect ke halaman error atau dashboard (misal: halaman utama)
        header("Location: index.php"); 
        exit;
    }
}

// Tambahkan setelah check_auth() atau is_logged_in()

/**
 * Memastikan pengguna sudah login DAN memiliki salah satu dari peran yang diizinkan.
 * @param array $allowed_roles Array berisi ID role yang diizinkan (misal: [1, 2])
 */
function check_role_access($allowed_roles = []) {
    
    // --- DEBUG BLOCK START ---
    $current_role = isset($_SESSION['id_role']) ? $_SESSION['id_role'] : 0;
    
    // Hapus/Komentari 3 baris di bawah ini setelah debugging selesai
    // echo "Current Role ID: " . $current_role . "<br>";
    // echo "Allowed Roles: " . implode(", ", $allowed_roles) . "<br>";
    // var_dump(in_array($current_role, $allowed_roles)); 
    // die(); // Hentikan eksekusi untuk melihat output
    // --- DEBUG BLOCK END ---

    // 1. Cek Autentikasi (Status Login)
    if (!is_logged_in()) {
        set_flashdata('error', 'Anda harus login untuk mengakses halaman ini.');
        header("Location: ../index.php"); 
        exit;
    }

    // 2. Cek Otorisasi (Hak Role)
    // $current_role sudah didefinisikan di atas.
    
    // Pastikan $allowed_roles adalah array
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    // Perhatikan: Perbandingan harus menggunakan string/integer yang tepat.
    // Jika ID Role Anda di DB adalah string, pastikan role ID yang Anda cek juga string (e.g. '1', '2')
    if (!empty($allowed_roles) && !in_array($current_role, $allowed_roles)) {
        
        set_flashdata('error', 'Akses ditolak. Anda tidak memiliki hak untuk melihat halaman ini.');
        
        // Redirect ke dashboard default user (agar user tidak kembali ke index.php jika sudah login)
        $dashboard_path = "dash_member.php"; // Default Member
        if ($current_role == 1) $dashboard_path = "dash_sys.php";
        if ($current_role == 2) $dashboard_path = "dash_opt.php";

        header("Location: " . $dashboard_path);
        exit;
    }
}

// ----------------------------------------------------------------------
// 4. Fungsi Hashing Password (Digunakan saat register/tambah user)
// ----------------------------------------------------------------------
// Fungsi untuk HASH (dipakai saat REGISTER/UPDATE)
function hash_password($password) {
    // Pastikan ini adalah metode hashing yang Anda gunakan sejak awal
    return hash('sha256', $password);
}

// ----------------------------------------------------------------------
// 5. FUNGSI BARU UNTUK NOTIFIKASI (FLASH DATA)
// ----------------------------------------------------------------------
/**
 * Fungsi untuk mengatur pesan notifikasi (Flash Data)
 * @param string $type Tipe pesan (success, error, warning, info)
 * @param string $message Isi pesan
 */
function set_flashdata($type, $message) {
    // Membuat array flashdata jika belum ada
    if (!isset($_SESSION['flashdata'])) {
        $_SESSION['flashdata'] = [];
    }
    $_SESSION['flashdata'] = ['type' => $type, 'message' => $message];
}

/**
 * Fungsi untuk menampilkan pesan notifikasi dan menghapusnya dari sesi
 */
function display_flashdata() {
    if (isset($_SESSION['flashdata']) && !empty($_SESSION['flashdata'])) {
        $type = $_SESSION['flashdata']['type'];
        $message = $_SESSION['flashdata']['message'];
        
        $alert_class = 'alert-info'; // Default

        // >>> KOREKSI: Mengganti 'match' dengan 'switch' untuk kompatibilitas PHP lama <<<
        switch ($type) {
            case 'success':
                $alert_class = 'alert-success';
                break;
            case 'error':
                $alert_class = 'alert-danger';
                break;
            case 'warning':
                $alert_class = 'alert-warning';
                break;
            default:
                $alert_class = 'alert-info';
                break;
        }

        echo '<div class="alert ' . $alert_class . ' alert-dismissible" role="alert">';
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo htmlspecialchars($message);
        echo '</div>';
        
        // Hapus pesan dari sesi agar hanya muncul sekali
        unset($_SESSION['flashdata']);
    }
}

/**
 * Memeriksa apakah pengguna saat ini sudah login.
 * @return bool True jika sudah login, False jika belum.
 */
function is_logged_in() {
    // Cek apakah variabel sesi id_user sudah diset dan bukan kosong
    if (isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) {
        return true;
    }
    return false;
}

/**
 * Memverifikasi password yang dimasukkan pengguna dengan hash yang tersimpan.
 *
 * @param string $password_input Password yang dimasukkan oleh pengguna saat login.
 * @param string $stored_hash Hash password yang tersimpan di database.
 * @return bool True jika password cocok, False jika tidak.
 */
function verify_password($password_input, $stored_hash) {
    // Karena kita menggunakan SHA-256 buatan, kita tidak bisa menggunakan password_verify bawaan.
    // Kita harus membandingkan hash buatan kita secara langsung.
    // Hash yang benar: SHA256(INPUT) == STORED_HASH
    
    // Asumsi: Kita menggunakan hash sederhana yang kita buat di hash_password().
    return hash('sha256', $password_input) === $stored_hash;

    // CATATAN: Jika Anda menggunakan password_hash() asli PHP, Anda harus menggunakan:
    // return password_verify($password_input, $stored_hash);
}

// ----------------------------------------------------------------------
// 6. Fungsi penambahan atau pengajuan ISBN baru
// ----------------------------------------------------------------------

/**
 * Memproses upload file, memvalidasi ukuran dan ekstensi, serta menghasilkan nama unik.
 * @param array $file_data Array $_FILES['nama_field']
 * @param string $upload_dir Direktori target penyimpanan
 * @param array $allowed_extensions Daftar ekstensi yang diizinkan (misal: ['docx', 'pdf'])
 * @param int $max_size Ukuran maksimum file dalam byte
 * @param int $user_id ID pengguna untuk penamaan file
 * @param string $file_type_slug Kata kunci untuk penamaan file (misal: 'naskah', 'cover')
 * @param string|null $old_file_name Nama file lama yang akan dihapus jika upload baru berhasil (Opsional, untuk Edit)
 * @return string|array Mengembalikan nama file baru (string) jika sukses, atau array error jika gagal.
 */
function process_upload($file_data, $upload_dir, $allowed_extensions, $max_size, $user_id, $file_type_slug, $old_file_name = null) {
    
    // 1. Cek jika tidak ada file yang dipilih
    if ($file_data['error'] === UPLOAD_ERR_NO_FILE) {
        // Jika ini adalah proses EDIT dan ada file lama, kembalikan nama file lama.
        if ($old_file_name !== null) {
            return $old_file_name;
        }
        // Jika ini adalah proses ADD dan file wajib diupload, kembalikan error.
        return ["File wajib diupload."];
    }
    
    // 2. Cek error upload selain 'file tidak dipilih'
    if ($file_data['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "Ukuran file melebihi batas upload server.",
            UPLOAD_ERR_FORM_SIZE => "Ukuran file melebihi batas form.",
            UPLOAD_ERR_PARTIAL => "File hanya terupload sebagian.",
            UPLOAD_ERR_NO_TMP_DIR => "Folder temporary hilang.",
            UPLOAD_ERR_CANT_WRITE => "Gagal menyimpan file ke disk.",
            UPLOAD_ERR_EXTENSION => "Ekstensi PHP menghentikan upload.",
        ];
        return [$error_messages[$file_data['error']] ?? "Terjadi kesalahan upload yang tidak diketahui."];
    }

    // 3. Validasi Ekstensi
    $file_info = pathinfo($file_data['name']);
    $file_ext = strtolower($file_info['extension']);
    
    if (!in_array($file_ext, $allowed_extensions)) {
        return ["Format file tidak didukung. Mohon upload dalam format: " . implode(', ', $allowed_extensions) . "."];
    }
    
    // 4. Validasi Ukuran
    if ($file_data['size'] > $max_size) {
        $max_size_mb = round($max_size / 1000000);
        return ["Ukuran file terlalu besar (Maks. {$max_size_mb} MB)."];
    }

    // 5. Hapus file lama jika ada (untuk proses edit)
    if ($old_file_name && file_exists($upload_dir . $old_file_name)) {
        // Matikan error reporting jika file tidak bisa dihapus, tapi log saja.
        @unlink($upload_dir . $old_file_name); 
    }
    
    // 6. Generate nama file baru unik
    $timestamp = date('YmdHis');
    // Format: tipefile_iduser_timestamp.ext
    $new_file_name = "{$file_type_slug}_{$user_id}_{$timestamp}.{$file_ext}";
    $target_file = $upload_dir . $new_file_name;

    // 7. Pindahkan file
    if (!move_uploaded_file($file_data['tmp_name'], $target_file)) {
        return ["Gagal memindahkan file yang diupload. Cek permissions folder: " . $upload_dir];
    }

    // 8. Sukses, kembalikan nama file baru
    return $new_file_name;
}

// ----------------------------------------------------------------------
// 7. Fungsi mengatur Pesan ke semua ID_ROLE
// ----------------------------------------------------------------------

/**
 * Mengubah ID Role menjadi Nama Role yang mudah dibaca.
 * @param int $id ID Role (1, 2, 3)
 * @return string Nama Role
 */
function get_role_name($id) {
    $roles = [
        1 => 'Administrator',
        2 => 'Operator',
        3 => 'Member'
    ];
    return $roles[$id] ?? 'Unknown Role';
}
/**
 * Menghitung jumlah pesan yang belum terbaca berdasarkan Role ID pengguna yang sedang login.
 * Pesan ditujukan ke Role, bukan ke ID pengguna tertentu.
 */
function get_unread_message_count($role_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_role_id = :role_id AND read_status = 'unread'");
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("DB Error counting unread messages: " . $e->getMessage());
        return 0;
    }
}

?>