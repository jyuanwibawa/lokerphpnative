<?php
require_once("database.php");
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

// Ambil informasi pengguna dari sesi
$nama_user = isset($_SESSION["nama"]) ? $_SESSION["nama"] : '';
$role_user = isset($_SESSION["role"]) ? $_SESSION["role"] : '';

$message = "";

if (isset($_GET['verify_id'])) {
    $user_id = $_GET['verify_id'];

    // Query untuk memverifikasi pengguna
    $sql = "UPDATE user SET status = 'Verified' WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id);

    if ($stmt->execute()) {
        $message = "Pengguna berhasil diverifikasi.";
    } else {
        $message = "Terjadi kesalahan. Silakan coba lagi.";
    }
}

if (isset($_GET['delete_id'])) {
    $user_id = $_GET['delete_id'];

    // Query untuk menghapus pengguna
    $sql = "DELETE FROM user WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id);

    if ($stmt->execute()) {
        $message = "Pengguna berhasil dihapus.";
    } else {
        $message = "Terjadi kesalahan. Silakan coba lagi.";
    }
}

// Query untuk mendapatkan pengguna yang belum diverifikasi
$sql = "SELECT id, username, nama, email, telpon, role FROM user WHERE status = 'Pending'";
$stmt = $db->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/d.png">
    <title>Dashboard - Sistem Informasi BKK</title>
    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/admin.css" rel="stylesheet">
  
</head>
<body class="fixed-nav sticky-footer" id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <a class="navbar-brand" href="regis">SISTEM INFORMASI BURSA KERJA KHUSUS <small>(SMK DINAMIKA KOTA TEGAL)</small></a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav sidebar-menu" id="exampleAccordion">

                <li class="sidebar-profile nav-item" data-toggle="tooltip" data-placement="right" title="Admin">
                    <div class="profile-main">
                        <p class="image">
                            <img alt="image" src="images/logo_dinamika.png" width="100">
                        </p>
                        <p>
                            <span class="user" style="font-family: monospace;"><?php echo htmlspecialchars($role_user); ?>&nbsp;<?php echo htmlspecialchars($nama_user); ?></span>
                        </p>
                    </div>
                </li>

                <?php if ($role_user == 'Admin'): ?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Verifikasi Pendaftaran" style="background-color: #0054a8;">
                    <a class="nav-link" href="regis">
                        <i class="fa fa-fw fa-check"></i>
                        <span class="nav-link-text">Verifikasi Pendaftaran</span>
                    </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Kelola User">
                    <a class="nav-link" href="user">
                        <i class="fa fa-fw fa-user"></i>
                        <span class="nav-link-text">Kelola User</span>
                    </a>
                </li>
                <?php endif; ?>

            </ul>

            <ul class="navbar-nav sidenav-toggler">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                        <i class="fa fa-fw fa-angle-left"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-fw fa-sign-out"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="container-fluid">

            <!-- Breadcrumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Verifikasi Pengguna</a>
                </li>
            </ol>

            <!-- Example DataTables Card-->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Daftar Pengguna yang Belum Diverifikasi
                </div>
                <div class="card-body">
                    <p class="text-center text-success mt-3"><small><?php echo htmlspecialchars($message); ?></small></p>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Role</th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled" style="text-align:right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['telpon']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td class="td-no-border">
                                        <a href="?verify_id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm">Verifikasi</a>
                                        <a href="?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted"></div>
            </div>
        </div>
        <!-- /.container-fluid-->

        <!-- /.content-wrapper-->
        <footer class="sticky-footer">
            <div class="container">
                <div class="text-center">
                    <small>Copyright © SMK DINAMIKA KOTA TEGAL 2024</small>
                </div>
            </div>
        </footer>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fa fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Yakin Ingin Keluar?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Pilih "Logout" jika anda ingin mengakhiri sesi.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <a class="btn btn-primary" href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <!-- Page level plugin JavaScript-->
        <script src="vendor/datatables/jquery.dataTables.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
        <!-- Custom scripts for all pages-->
        <script src="js/admin.js"></script>
        <!-- Custom scripts for this page-->
        <script src="js/admin-datatables.js"></script>
    </div>

</body>

</html>
