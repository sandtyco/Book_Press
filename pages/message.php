<?php
// pages/message.php (Personal Communication Version - FINAL)

require_once '../conn.php'; 
require_once '../function.php'; 

// Semua peran (Role 1, 2, 3) diizinkan
check_role_access([1, 2, 3]);

$current_user_id = $_SESSION['id_user'];
$current_user_role = $_SESSION['id_role'];
$current_user_role_name = get_role_name($current_user_role); 

$active_tab = $_GET['tab'] ?? 'inbox'; 

$target_roles = [
    1 => 'Administrator',
    2 => 'Operator',
    3 => 'Member'
];

// --- LOGIKA PENGIRIMAN PESAN BARU ---
if (isset($_POST['send_message'])) {
    
    $subject = sanitize_input($_POST['subject']);
    $message_content = sanitize_input($_POST['message_content']);
    $errors = [];
    
    // Default values for message structure
    $receiver_role_id = null;
    $specific_receiver_id = null; // Kolom receiver_id

    if ($current_user_role == 3) {
        // Member mengirim pesan baru (selalu ke ROLE)
        $receiver_role_id = (int)$_POST['receiver_role_id'];
        if ($receiver_role_id == 3) {
             $errors[] = "Member hanya dapat mengirim pesan ke Administrator atau Operator.";
        }
    } elseif ($current_user_role == 1 || $current_user_role == 2) {
        // Admin/Operator membalas (selalu ke ID SPESIFIK Member)
        $specific_receiver_id = (int)$_POST['specific_receiver_id'];
        $receiver_role_id = 3; // Tetapkan role tujuan ke Member (3)
        if (empty($specific_receiver_id)) {
            $errors[] = "ID Member tujuan balasan tidak ditemukan.";
        }
    }

    if (empty($receiver_role_id) && empty($specific_receiver_id)) {
        $errors[] = "Tujuan pesan tidak valid.";
    }

    if (empty($errors)) {
        try {
            global $conn;
            // Menyimpan nilai receiver_id (bisa NULL)
            $sql = "INSERT INTO messages (sender_id, receiver_role_id, receiver_id, subject, message_content, is_sent_by_role) 
                    VALUES (:sender_id, :receiver_role_id, :specific_receiver_id, :subject, :content, :sender_role_name)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':sender_id', $current_user_id, PDO::PARAM_INT);
            $stmt->bindParam(':receiver_role_id', $receiver_role_id, PDO::PARAM_INT);
            $stmt->bindParam(':specific_receiver_id', $specific_receiver_id, PDO::PARAM_INT); 
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':content', $message_content);
            $stmt->bindParam(':sender_role_name', $current_user_role_name);

            if ($stmt->execute()) {
                $target_info = $specific_receiver_id ? "ID User " . $specific_receiver_id : "Role " . $target_roles[$receiver_role_id];
                set_flashdata('success', 'Pesan berhasil dikirim ke ' . $target_info . '!');
            } else {
                set_flashdata('error', 'Gagal mengirim pesan ke database.');
            }
        } catch (PDOException $e) {
            error_log("Message Send DB Error: " . $e->getMessage());
            set_flashdata('error', 'Terjadi kesalahan sistem saat mengirim pesan.');
        }
    } else {
        set_flashdata('error', implode('<br>', $errors));
    }
    header('Location: message.php?tab=sent');
    exit;
}

// --- LOGIKA MENAMPILKAN PESAN ---
$messages = [];
try {
    global $conn;
    $sql = "";
    
    if ($active_tab === 'inbox') {
        if ($current_user_role == 3) {
            // 🚨 MEMBER: Hanya lihat pesan yang ditujukan ke ID mereka. 
            // Menggunakan alias 'sender_name' untuk fix error column not found. 🚨
            $sql = "SELECT m.*, u.`nama_lengkap` AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id_user 
                    WHERE m.receiver_id = :user_id ORDER BY m.sent_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
            
            // Tandai pesan Member sebagai 'read' (berdasarkan receiver_id)
            $conn->prepare("UPDATE messages SET read_status = 'read' WHERE receiver_id = :user_id AND read_status = 'unread'")->execute([':user_id' => $current_user_id]);
        
        } else {
            // ADMIN/OPERATOR: Lihat semua pesan yang ditujukan ke role mereka (dari Member)
            $sql = "SELECT m.*, u.`nama_lengkap` FROM messages m JOIN users u ON m.sender_id = u.id_user 
                    WHERE m.receiver_role_id = :role_id AND m.receiver_id IS NULL ORDER BY m.sent_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':role_id', $current_user_role, PDO::PARAM_INT);
            
            // Tandai pesan Admin/Op sebagai 'read'
            $conn->prepare("UPDATE messages SET read_status = 'read' WHERE receiver_role_id = :role_id AND receiver_id IS NULL AND read_status = 'unread'")->execute([':role_id' => $current_user_role]);
        }

    } elseif ($active_tab === 'sent') {
        // Pesan yang dikirim oleh pengguna saat ini
        $sql = "SELECT m.* FROM messages m 
                WHERE m.sender_id = :user_id ORDER BY m.sent_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    }
    
    if (isset($stmt)) {
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    error_log("Message Fetch DB Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat pesan dari database. (Kode Error: ' . $e->getMessage() . ')');
}

if (!function_exists('get_role_name')) {
    function get_role_name($id) {
        $roles = [1 => 'Administrator', 2 => 'Operator', 3 => 'Member'];
        return $roles[$id] ?? 'Unknown';
    }
}

?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-envelope"></span> Kotak Pesan Internal</h1>

    <?php display_flashdata(); ?>

    <div class="row mb-3">
        <div class="col-md-12">
            <?php if ($current_user_role == 3): ?>
            <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#composeModal">
                <span class="glyphicon glyphicon-send"></span> Pesan Baru
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?= ($active_tab == 'inbox' ? 'active' : '') ?>">
            <a href="message.php?tab=inbox">Kotak Masuk (<?= ($current_user_role == 3 ? 'Personal' : 'Untuk Role: ' . $current_user_role_name); ?>)</a>
        </li>
        <li role="presentation" class="<?= ($active_tab == 'sent' ? 'active' : '') ?>">
            <a href="message.php?tab=sent">Pesan Terkirim (Oleh Anda)</a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active pt-3">
            
            <?php if (empty($messages)): ?>
                <div class="alert alert-info text-center mt-4">
                    Tidak ada pesan di folder ini.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="messageTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 20%;"><?= ($active_tab == 'inbox' ? 'Pengirim' : 'Tujuan Role'); ?></th>
                                <th style="width: 50%;">Subjek</th>
                                <th style="width: 20%;">Waktu Kirim</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td>
                                    <?php if ($active_tab == 'inbox'): ?>
                                        <span class="text-primary">
                                            <?php 
                                            // 🚨 Menggunakan alias 'sender_name' untuk Member (Role 3) 🚨
                                            if ($current_user_role == 3 && isset($msg['sender_name'])) {
                                                echo htmlspecialchars($msg['sender_name']); 
                                            } else {
                                                echo htmlspecialchars($msg['nama_lengkap']); 
                                            }
                                            ?>
                                        </span>
                                        <small class="text-muted d-block"> (Role: <?= htmlspecialchars($msg['is_sent_by_role']); ?>)</small>
                                    <?php else: ?>
                                        <strong class="text-success">
                                            <?php 
                                            if ($current_user_role != 3 && !empty($msg['receiver_id'])) {
                                                echo 'Member Spesifik'; 
                                            } else {
                                                echo htmlspecialchars($target_roles[$msg['receiver_role_id']]);
                                            }
                                            ?>
                                        </strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($msg['subject']); ?></strong>
                                </td>
                                <td><?= date('d M Y, H:i', strtotime($msg['sent_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#viewModal"
                                        data-id="<?= $msg['id_message']; ?>"
                                        data-subject="<?= htmlspecialchars($msg['subject']); ?>"
                                        data-content="<?= htmlspecialchars($msg['message_content']); ?>"
                                        
                                        data-sender="<?php 
                                            if ($active_tab == 'inbox') {
                                                $name = ($current_user_role == 3 && isset($msg['sender_name'])) ? $msg['sender_name'] : $msg['nama_lengkap'];
                                                echo htmlspecialchars($name . ' (' . $msg['is_sent_by_role'] . ')');
                                            } else {
                                                echo htmlspecialchars($target_roles[$msg['receiver_role_id']]);
                                            }
                                        ?>"
                                        
                                        data-sent-at="<?= date('d M Y, H:i', strtotime($msg['sent_at'])); ?>"
                                        
                                        data-sender-name="<?php 
                                            if ($active_tab == 'inbox') {
                                                echo ($current_user_role == 3 && isset($msg['sender_name'])) ? htmlspecialchars($msg['sender_name']) : htmlspecialchars($msg['nama_lengkap']);
                                            }
                                        ?>"
                                        
                                        data-original-sender-id="<?= ($active_tab == 'inbox' ? $msg['sender_id'] : 0); ?>"> 
                                        Lihat
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="composeModal" tabindex="-1" role="dialog" aria-labelledby="composeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="message.php">
                
                <input type="hidden" name="receiver_role_id" id="hidden_receiver_role_id" value=""> 

                <input type="hidden" name="specific_receiver_id" id="specific_receiver_id" value=""> 

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="composeModalLabel">Kirim Pesan Baru</h4>
                </div>
                <div class="modal-body">
                    
                    <?php if ($current_user_role == 3): ?>
                        <div class="form-group" id="compose-target-role-group">
                            <label for="select_receiver_role_id">Tujuan Pesan (Role)</label>
                            <select class="form-control" id="select_receiver_role_id" name="receiver_role_id" required>
                                <option value="">Pilih Tujuan...</option>
                                <?php foreach ($target_roles as $id => $name): ?>
                                    <?php
                                        // Member (3) hanya bisa kirim ke Admin (1) atau Operator (2)
                                        if ($id == 1 || $id == 2) {
                                            echo '<option value="' . $id . '">' . $name . '</option>';
                                        }
                                    ?>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-muted small mt-1">Pesan akan diterima oleh semua pengguna dengan peran yang dituju.</p>
                        </div>
                    <?php else: ?>
                        <div class="form-group" id="compose-target-name-group" style="display: none;">
                            <label>Membalas Pesan Kepada:</label>
                            <p class="form-control-static">
                                <strong id="reply-target-name"></strong> 
                                <span class="text-muted">(Member)</span>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="subject">Subjek</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message_content">Isi Pesan</label>
                        <textarea class="form-control" id="message_content" name="message_content" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" name="send_message" class="btn btn-primary">
                        <span class="glyphicon glyphicon-send"></span> Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="viewModalLabel">Detail Pesan</h4>
            </div>
            <div class="modal-body">
                <p><strong>Dari/Kepada:</strong> <span id="modal-sender"></span></p>
                <p><strong>Subjek:</strong> <span id="modal-subject"></span></p>
                <p><strong>Waktu:</strong> <span id="modal-sent-at"></span></p>
                <hr>
                <p id="modal-content"></p>
            </div>
            <div class="modal-footer">
                <?php if ($active_tab == 'inbox' && ($current_user_role == 1 || $current_user_role == 2)): ?>
                    <button type="button" class="btn btn-warning" id="reply-btn">
                        <span class="glyphicon glyphicon-share-alt"></span> Balas Pesan
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>