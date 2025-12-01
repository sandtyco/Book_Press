<?php
// includes/sidebar.php (FINAL: Dropdown, Glyphicon, dan Styling Konsisten)

// Variabel sesi harus sudah diset di includes/header.php
// $current_id_role sudah tersedia dari header.php
$role = isset($current_id_role) ? (int)$current_id_role : 0; 

// Menentukan URL Dashboard yang benar
$dashboard_url = ($role == 1) ? 'dash_sys.php' : (($role == 2) ? 'dash_opt.php' : 'dash_member.php');

// Fungsi bantuan untuk menandai menu utama jika sub-menu aktif
function is_parent_active($page_names) {
    // $page_names adalah array berisi nama file sub-menu
    return in_array(basename($_SERVER['PHP_SELF']), $page_names) ? 'active' : '';
}
?>

<div class="col-sm-3 col-md-2 sidebar" style="margin-top: 50px;">
    <ul class="nav nav-sidebar">
        <li <?= (basename($_SERVER['PHP_SELF']) == 'dash_sys.php' || basename($_SERVER['PHP_SELF']) == 'dash_opt.php' || basename($_SERVER['PHP_SELF']) == 'dash_member.php') ? 'class="active"' : '' ?>>
            <a href="<?= $dashboard_url ?>" style="color: #337ab7;">
                <span class="glyphicon glyphicon-home"></span> DASHBOARD <span class="sr-only">(current)</span>
            </a>
        </li>
        <li <?= basename($_SERVER['PHP_SELF']) == 'profil.php' || basename($_SERVER['PHP_SELF']) == 'profil_edit.php' ? 'class="active"' : '' ?>>
            <a href="profil.php"><span class="glyphicon glyphicon-cog"></span> Profil</a>
        </li>
    </ul>

    <ul class="nav nav-sidebar">
        
        <?php if ($role == 1): ?>
            <li class="sidebar-header">SYSTEM</li>
            
            <li class="<?= is_parent_active(['user_list.php', 'user_add.php', 'user_edit.php']) ?>">
                <a href="#submenuUser" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <span class="glyphicon glyphicon-tower"></span> Manajemen Pengguna 
                    <span class="caret" style="float: right; margin-top: 8px;"></span>
                </a>
                
                <ul class="collapse list-unstyled" id="submenuUser">
                    <li class="<?= basename($_SERVER['PHP_SELF']) == 'user_list.php' ? 'active-submenu' : '' ?>">
                        <a href="user_list.php">&nbsp; &nbsp; &nbsp; <span class="glyphicon glyphicon-list-alt"></span> Daftar Pengguna</a>
                    </li>
                    <li class="<?= basename($_SERVER['PHP_SELF']) == 'user_add.php' ? 'active-submenu' : '' ?>">
                        <a href="user_add.php">&nbsp; &nbsp; &nbsp; <span class="glyphicon glyphicon-user"></span> Tambah Pengguna</a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if ($role == 1 || $role == 2): ?>
            <li class="sidebar-header">KONTEN</li>
            
            <li class="<?= is_parent_active(['katalog_view.php', 'katalog_add.php', 'katalog_edit.php']) ?>">
                <a href="#submenuKatalog" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <span class="glyphicon glyphicon-book"></span> Katalog
                    <span class="caret" style="float: right; margin-top: 8px;"></span>
                </a>
                <ul class="collapse list-unstyled" id="submenuKatalog">
                    <li class="<?= basename($_SERVER['PHP_SELF']) == 'katalog_list.php' ? 'active-submenu' : '' ?>">
                        <a href="katalog_view.php">&nbsp; &nbsp; &nbsp; <span class="glyphicon glyphicon-th-list"></span> Daftar Katalog</a>
                    </li>
                    <li class="<?= basename($_SERVER['PHP_SELF']) == 'katalog_add.php' ? 'active-submenu' : '' ?>">
                        <a href="katalog_add.php">&nbsp; &nbsp; &nbsp; <span class="glyphicon glyphicon-plus-sign"></span> Tambah Katalog</a>
                    </li>
                </ul>
            </li>

            <li class="<?= is_parent_active(['info_list.php', 'info_add.php', 'info_edit.php']) ?>">
                <a href="#submenuInfo" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <span class="glyphicon glyphicon-bullhorn"></span> Informasi
                    <span class="caret" style="float: right; margin-top: 8px;"></span>
                </a>
                <ul class="collapse list-unstyled" id="submenuInfo">
                    <li class="<?= basename($_SERVER['PHP_SELF']) == 'info_list.php' ? 'active-submenu' : '' ?>">
                        <a href="info_list.php">&nbsp; &nbsp; &nbsp; <span class="glyphicon glyphicon-comment"></span> List Informasi</a>
                    </li>
                    <li class="<?= basename($_SERVER['PHP_SELF']) == 'info_add.php' ? 'active-submenu' : '' ?>">
                        <a href="info_add.php">&nbsp; &nbsp; &nbsp; <span class="glyphicon glyphicon-send"></span> Tambah Informasi</a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        
        <li class="sidebar-header">MENU ISBN</li>
        <li <?= basename($_SERVER['PHP_SELF']) == 'isbn_list.php' ? 'class="active"' : '' ?>>
            <a href="isbn_list.php"><span class="glyphicon glyphicon-folder-open"></span> Daftar Ajuan ISBN</a>
        </li>
        
        <li class="sidebar-header">USER</li>
        <?php
        $unread_count = get_unread_message_count($_SESSION['id_role']);
        ?>
        <li>
            <a href="message.php">
                <span class="glyphicon glyphicon-envelope"></span> Pesan 
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?= $unread_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>
    
    <ul class="nav nav-sidebar sidebar-fixed-bottom" style="margin-top: 50px;">
         <li class="divider"></li>
        <li>
            <a href="../logout.php" style="color: #990000ff;"> <span class="glyphicon glyphicon-log-out"></span> Logout
            </a>
        </li>
    </ul>
</div>