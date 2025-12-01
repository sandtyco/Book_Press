    <?php
        // includes/footer.php (REVISI: Penutup Layout)
        ?>  
    </div> 
</div> 
<footer class="footer">
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <p class="text-left text-muted">
            &copy; <?php echo date('Y'); ?> Sistem Informasi Pengajuan ISBN | Amikom Press - Universitas Amikom Purwokerto.<br>
            Project By: <a href="#">Educollabs</a> | Amikom Press Versi 1.0
        </p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha384-NXgwF8Kv9SSAr+jemKKcbvQsz+teULH/a5UNJvZc6kP47hZgl62M1vGnw6gHQhb1" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" integrity="sha384-VCGDSwGwLWkVOK5vAWSaY38KZ4oKJ0whHjpJQhjqrMlWadpf2dUVKLgOLBdEaLvZ" crossorigin="anonymous"></script>

<script src="../assets/js/bootstrap.min.js"></script>

<script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js" integrity="sha384-VQb2IR8f6y3bNbMe6kK6H+edzCXdt7Z/3GtWA7zYzXcvfwYRR5rHGl46q28FbtsY" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.bootstrap.min.js" integrity="sha384-dWxQaWIW01kOo1Nq6GAXs3j8feQUr2oRJqX98pfW0MULdhw/Jc03disinzgnGUkh" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/searchpanes/2.3.5/js/dataTables.searchPanes.min.js" integrity="sha384-K/AjOK05+9/Frrtu19Xc8DX23LQAHpksb178AP+JheKIv6hx2FEGJdaEaugFwldM" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/searchpanes/2.3.5/js/searchPanes.bootstrap.min.js" integrity="sha384-EDO9LHKpDfCMplAHxWO4K/wBNYv5HG0xmXiChPwVlVIKPbcEuLXq/q1dbSh8QzV3" crossorigin="anonymous"></script>

<script src="../assets/js/custom-menu.js"></script>
<script src="../assets/js/modal-script.js"></script>

<script>
// Script ini harus dijalankan setelah DOM dimuat dan setelah file DataTables JS dimuat. Collapse Menu.
    $(document).ready(function() {
        // Skrip ini mencari sub-menu yang memiliki kelas 'active-submenu'
        var active_submenu = $('.active-submenu').closest('.collapse');
        
        // Jika ditemukan sub-menu yang aktif, tambahkan kelas 'in'
        // Kelas 'in' adalah yang membuat sub-menu terbuka di Bootstrap Collapse
        if (active_submenu.length) {
            active_submenu.addClass('in');
        }
    });
</script>

<script>
// Script ini harus dijalankan setelah DOM dimuat dan setelah file DataTables JS dimuat. DATATABLE PESAN
    $(document).ready(function() {
        // Inisialisasi DataTables pada tabel Pesan
        $('#messageTable').DataTable({
            "paging": true,         // Aktifkan Paginasi
            "searching": true,      // Aktifkan Pencarian
            "ordering": true,       // Aktifkan Pengurutan
            "info": true,           // Tampilkan informasi "Showing X to Y of Z entries"
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], // Pilihan jumlah baris
            "language": {
            // Gunakan HTTPS penuh agar selalu berhasil dimuat
            "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" 
            }
        });
    });
</script>

<script>
// DATATABLE Dashboard untuk tabel ajuan member.--------------------------------------------------
    $(document).ready(function() {
        $('#ajuanTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "order": [[ 3, "desc" ]], // Urutkan berdasarkan Tanggal Pengajuan terbaru (kolom ke-3)
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" 
            }
        });
    });
</script>

<script>
    // --- KHUSUS MEMBER (Role 3) MODAL PESAN ---
    <?php if ($current_user_role == 3): ?>
    // Member: Ambil nilai dari dropdown dan masukkan ke hidden field yang digunakan di POST.
    $('#select_receiver_role_id').on('change', function() {
        $('#hidden_receiver_role_id').val($(this).val());
    });
    // Saat modal compose dibuka, pastikan hidden field terisi dari dropdown
    $('#composeModal').on('show.bs.modal', function() {
        if ($('#select_receiver_role_id').is(':visible')) {
            $('#hidden_receiver_role_id').val($('#select_receiver_role_id').val());
        }
    });
    <?php endif; ?>

    // Handler saat modal detail pesan (viewModal) dibuka
    $('#viewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var subject = button.data('subject');
        var content = button.data('content');
        var sender = button.data('sender');
        var sentAt = button.data('sent-at');
        
        var senderName = button.data('sender-name'); 
        var originalSenderId = button.data('original-sender-id');

        var modal = $(this);
        modal.find('#modal-sender').text(sender);
        modal.find('#modal-subject').text(subject);
        modal.find('#modal-sent-at').text(sentAt);
        modal.find('#modal-content').html(content.replace(/\n/g, "<br>")); 
        
        // --- LOGIKA REPLY UNTUK ADMIN/OPERATOR (Role 1 & 2) ---
        var currentUserRole = <?= $current_user_role; ?>;
        
        if (currentUserRole === 1 || currentUserRole === 2) {
            $('#reply-btn').off('click'); 
            
            // Ketika tombol "Balas Pesan" diklik
            $('#reply-btn').on('click', function() {
                
                // Set ID Member spesifik yang akan menerima balasan
                $('#specific_receiver_id').val(originalSenderId);

                // Tampilan dan Data di Modal Compose
                $('#reply-target-name').text(senderName);
                $('#compose-target-name-group').show(); 
                
                // Isi subjek balasan
                var replySubject = subject.startsWith("Re: ") ? subject : "Re: " + subject; 
                $('#composeModal input[name="subject"]').val(replySubject);
                
                // Bersihkan konten pesan
                $('#composeModal textarea[name="message_content"]').val('');
                
                // Update Judul Modal
                $('#composeModalLabel').text('Balas Pesan (Personal)');

                // Tutup modal view dan buka modal compose
                $('#viewModal').modal('hide');
                $('#composeModal').modal('show');
            });
        }
    });
    
    // Handler saat modal compose ditutup, reset form
    $('#composeModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        
        $('#composeModalLabel').text('Kirim Pesan Baru');
        
        // Reset field balasan Admin/Operator
        <?php if ($current_user_role != 3): ?>
        $('#reply-target-name').text('');
        $('#compose-target-name-group').hide();
        $('#specific_receiver_id').val(''); 
        <?php endif; ?>

        // Reset field untuk Member
        <?php if ($current_user_role == 3): ?>
        $('#select_receiver_role_id').val('');
        $('#hidden_receiver_role_id').val('');
        <?php endif; ?>
    });
</script>

<script>
    // --- KHUSUS VIEW DATATABLE DAN MODAL PADA HALAMAN INFORMASI DAN BERITA OLEH ADMIN DAN OPERATOR ---
    $(document).ready(function() {
        // 1. Inisialisasi DataTables
        $('#infoTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "order": [[ 4, "desc" ]], 
            "columnDefs": [
                { "orderable": false, "targets": [1, 5] } // Non-aktifkan sorting pada kolom Gambar dan Aksi
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" 
            }
        });
        
        // 2. Logika Modal View
        $('#viewModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var title = button.data('title');
            var content = button.data('content'); 
            var image = button.data('image'); // <--- Ambil nama file gambar
            
            var modal = $(this);
            modal.find('#modal-title').text(title);
            
            // Buat path gambar
            var imagePath = '../assets/img/info/' + image;
            
            // Set sumber gambar dan tampilkan
            modal.find('#modal-image').attr('src', imagePath);
            
            // Tampilkan konten HTML dari CKEditor
            modal.find('#modal-content-body').html(content); 
        });
    });
</script>

<script>
// --- KHUSUS EDITOR CKFINDER PADA HALAMAN INFORMASI DAN BERITA OLEH ADMIN DAN OPERATOR ---
    $(document).ready(function() {
        ClassicEditor
            .create( document.querySelector( '#editor' ), {
                // Hapus properti removePlugins untuk menghindari konflik dependensi.
                // Konfigurasi hanya melalui item toolbar yang ingin ditampilkan.
                toolbar: {
                    items: [
                        'heading', '|', 'bold', 'italic', 'underline', // Tambahkan underline untuk fungsionalitas umum
                        '|', 'bulletedList', 'numberedList', 'blockQuote', '|',
                        'insertTable', '|', 'undo', 'redo'
                    ]
                }
            } )
            .catch( error => {
                console.error( 'CKEditor failed to initialize:', error );
            } );
    });
</script>

<script>
// --- DATATABLE UNTUK HALAMAN KATALOG_VIEW ---
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#katalogTable').DataTable({
            "paging": true,      // Aktifkan pagination
            "searching": true,   // Aktifkan search box
            "ordering": true,    // Aktifkan sorting
            "info": true,        // Tampilkan informasi
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" // Menggunakan bahasa Indonesia
            }
        });
    });
</script>

</body>
</html>