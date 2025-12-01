<?php
// pages/isbn_list.php (Daftar Riwayat Ajuan ISBN)
require_once '../conn.php';
require_once '../function.php'; 
check_role_access([1, 2, 3]); // Cek status login

// 1. Ambil Data dari Database
try {
    global $conn;
    $current_user_id = $_SESSION['id_user']; 
    $current_user_role = $_SESSION['id_role']; // 🚨 AMBIL ID ROLE

    // Tentukan apakah user adalah Member (Role 3) atau Admin/Operator (Role 1/2)
    $is_member = ($current_user_role == 3);
    
    // 🚨 KOREKSI LOGIKA RBAC
    $sql = "
        SELECT id_isbn, judul_buku, submitted_at, status_ajuan 
        FROM isbn_submissions 
    ";
    
    $where_clause = "";
    
    // Jika Role adalah Member (3), batasi hanya ajuannya sendiri
    if ($is_member) {
        $where_clause = " WHERE id_user = :user_id ";
    }
    // Jika Role 1 (Sysadmin) atau 2 (Operator), $where_clause kosong (LIHAT SEMUA)

    $sql .= $where_clause;
    $sql .= " ORDER BY submitted_at DESC";

    $stmt = $conn->prepare($sql);
    
    // Bind parameter hanya jika itu adalah Member
    if ($is_member) {
        $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $isbn_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("ISBN List DB Error: " . $e->getMessage());
    set_flashdata('error', 'Gagal memuat data ajuan ISBN dari database.');
    $isbn_submissions = [];
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h1 class="page-header"><span class="glyphicon glyphicon-tag"></span> Daftar Pengajuan ISBN</h1>
    <p class="mb-4">Riwayat pengajuan ISBN Anda beserta status terkini.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Tabel Riwayat Pengajuan</h6>
            <a href="isbn_add.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajukan ISBN Baru
            </a>
        </div>

        <hr class="featurette-divider">
        
        <div class="card-body">
            
            <?php display_flashdata(); // Tampilkan pesan dari proses edit/add ?>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Ajuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($isbn_submissions as $submission) {
                            $status_text = $submission['status_ajuan'];
                            $id_isbn = $submission['id_isbn'];
                            $current_user_role = $_SESSION['id_role'];

                            // 🚨 PERBAIKAN UTAMA: Mendefinisikan inline style
                            $status_style = ''; 

                            switch ($status_text) {
                                case 'Diajukan': 
                                    $status_style = 'style="background-color: #2393f5ff; color: white;"'; // Abu-abu Gelap (Secondary)
                                    break;
                                case 'Dalam Proses': 
                                    $status_style = 'style="background-color: #ffc62bff; color: black;"'; // Kuning Terang (Warning)
                                    break;
                                case 'Terbit': 
                                    $status_style = 'style="background-color: #005714ff; color: white;"'; // Hijau (Success)
                                    break;
                                case 'Ditolak': 
                                    $status_style = 'style="background-color: #d40015ff; color: white;"'; // Merah (Danger)
                                    break;
                                default: 
                                    $status_style = 'style="background-color: #383838ff; color: white;"'; // Biru Muda (Info)
                                    break;
                            }
                        ?>
                        <tr>
                            <td class="dt-control"></td> 
                            <td><?= htmlspecialchars($submission['judul_buku']); ?></td>
                            <td><?= date('d M Y', strtotime($submission['submitted_at'])); ?></td>
                            <td>
                                <span class="badge p-2" <?= $status_style; ?>><?= $status_text; ?></span>
                            </td>
                            <td>
                                <a href="isbn_view.php?id=<?= $submission['id_isbn']; ?>" class="btn btn-info btn-xs" title="Detail"><span class="glyphicon glyphicon-eye-open"></span></a>
                                
                                <?php 
                                // Tombol Edit: Muncul jika status 'Diajukan' (untuk Member) ATAU Role adalah Admin/Operator
                                if (($submission['status_ajuan'] == 'Diajukan') || ($current_user_role == 1 || $current_user_role == 2)) :
                                ?>
                                    <a href="isbn_edit.php?id=<?= $submission['id_isbn']; ?>" class="btn btn-warning btn-xs" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
                                <?php endif; ?>

                                <?php
                                // TOMBOL RESET: HANYA untuk Role 1/2 dan status "Dalam Proses"
                                if ($submission['status_ajuan'] == 'Dalam Proses' && ($current_user_role == 1 || $current_user_role == 2)) :
                                ?>
                                    <form method="POST" action="isbn_reset_status.php" style="display:inline;" onsubmit="return confirm('Anda yakin ingin mereset status ajuan ini kembali ke \'Diajukan\'? Ini akan memungkinkan perubahan data.');">
                                        <input type="hidden" name="id" value="<?= $submission['id_isbn']; ?>">
                                        <button type="submit" name="reset_status" class="btn btn-danger btn-xs" title="Reset Status"><span class="glyphicon glyphicon-repeat"></span></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } // End of foreach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <p class="well"><span class="label label-danger"><b>PERHATIAN...!</b></span> Bagi ISBN yang telah terbit, mohon untuk segera memasukan dalam katalog ISBN pada menu <b>"Katalog/Tambah Katalog"</b> agar dapat segera publish di halaman publik.</p>
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

        // 🚨 Memastikan Penomoran Otomatis dan DataTables berfungsi
        var table = $('#dataTable').DataTable({
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

