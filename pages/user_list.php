<?php
// pages/user_list.php
require_once '../conn.php';
require_once '../function.php';
check_auth(); // Cek status login

// HANYA ROLE ADMIN (ID ROLE = 1) YANG BOLEH MENGAKSES
check_role([1]); 

$page_title = "Manajemen Pengguna";

// Query untuk mengambil semua data user (TERMASUK ALAMAT & NO_TELP)
try {
    $stmt = $conn->prepare("
        SELECT 
            u.id_user, u.username, u.nama_lengkap, u.email, u.alamat, u.no_telp, r.role_name 
        FROM users u
        JOIN roles r ON u.id_role = r.id_role
        ORDER BY u.id_user DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    set_flashdata('error', 'Gagal memuat data pengguna dari database.');
    $users = [];
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header"><span class="glyphicon glyphicon-user"></span> <?= $page_title ?></h1>

    <?php display_flashdata(); // Tampilkan notifikasi ?>

    <div class="row">
        <div class="col-md-12">
            <p>
                <a href="user_add.php" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Tambah Pengguna Baru</a>
            </p>

            <div class="table-responsive">
                <table id="userTable" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>No. Telp</th>
                            <th>Role</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($users as $user): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars(substr($user['alamat'] ?? '-', 0, 30)) ?><?= (strlen($user['alamat'] ?? '') > 30) ? '...' : '' ?></td>
                            <td><?= htmlspecialchars($user['no_telp'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($user['role_name']) ?></td>
                            <td>
                                <a href="user_edit.php?id=<?= $user['id_user'] ?>" class="btn btn-warning btn-xs" title="Edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                
                                <?php if ($user['id_user'] != $_SESSION['id_user']): // Tidak bisa menghapus diri sendiri ?>
                                <a href="user_delete.php?id=<?= $user['id_user'] ?>" class="btn btn-danger btn-xs" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna <?= htmlspecialchars($user['username']) ?>? Tindakan ini tidak dapat dibatalkan.')" 
                                   title="Hapus">
                                   <span class="glyphicon glyphicon-trash"></span> Hapus
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        var indonesianLanguage = {
            "sProcessing": "Sedang memproses...",
            "sLengthMenu": "Tampilkan _MENU_ entri",
            "sZeroRecords": "Tidak ditemukan data yang relevan",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ total entri)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        };

        // Memastikan Penomoran Otomatis dan DataTables berfungsi
        var table = $('#userTable').DataTable({
            "language": indonesianLanguage, 
            "order": [[ 2, "desc" ]], 
            "columnDefs": [
                {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0,
                }
            ],
            "responsive": true
        });

        table.on( 'order.dt search.dt', function () {
            let i = 1;
            table.cells(null, 0, { search: 'applied', order: 'applied' }).every( function () {
                this.data(i++);
            } );
        } ).draw();
        
        setTimeout(function() {
            table.columns.adjust().draw();
        }, 100); 
    });
</script>