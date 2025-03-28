<?php
require_once("database.php"); // Sesuaikan dengan lokasi file database Anda
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit();
}

// Ambil informasi pengguna dari sesi dengan nilai default jika tidak ada
$nama_user = isset($_SESSION["nama"]) ? $_SESSION["nama"] : '';
$email_user = isset($_SESSION["email"]) ? $_SESSION["email"] : '';
$telpon_user = isset($_SESSION["telpon"]) ? $_SESSION["telpon"] : '';
$role_user = isset($_SESSION["role"]) ? $_SESSION["role"] : '';

// Tangani permintaan AJAX untuk detail lowongan
if (isset($_GET['action']) && $_GET['action'] == 'get_details' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $statement = $db->prepare("SELECT * FROM lowongan WHERE id = :id");
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    echo json_encode($statement->fetch(PDO::FETCH_ASSOC));
    exit();
}

// Tangani form submission untuk tambah lowongan

// Tangani form submission untuk tambah lowongan
if (isset($_POST['add_lowongan'])) {
    if (!empty($_POST['perusahaan']) && !empty($_POST['title']) && !empty($_POST['deskripsi'])) {
        $perusahaan = $_POST['perusahaan'];
        $title = $_POST['title'];
        $deskripsi = $_POST['deskripsi'];
        $tanggal = date('d-m-Y');  // Mendapatkan waktu saat ini dalam format d-m-Y

        try {
            // Menambahkan data lowongan ke database
            $statement = $db->prepare("INSERT INTO lowongan (perusahaan, title, deskripsi, tanggal) 
                                       VALUES (:perusahaan, :title, :deskripsi, :tanggal)");
            $statement->execute([
                ':perusahaan' => $perusahaan,
                ':title' => $title,
                ':deskripsi' => $deskripsi,
                ':tanggal' => $tanggal
            ]);

            // Redirect ke halaman utama setelah berhasil menambah lowongan
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Semua field harus diisi.";
    }
}



// Tangani permintaan POST untuk edit lowongan
if (isset($_POST['edit_lowongan'])) {
    $id = $_POST['id'];
    $perusahaan = $_POST['perusahaan'];
    $title = $_POST['title'];
    $deskripsi = $_POST['deskripsi'];
    $statement = $db->prepare("UPDATE lowongan SET perusahaan = :perusahaan, title = :title, deskripsi = :deskripsi WHERE id = :id");
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->bindParam(':perusahaan', $perusahaan);
    $statement->bindParam(':title', $title);
    $statement->bindParam(':deskripsi', $deskripsi);
    $statement->execute();
    header('Location: index.php');
    exit();
}

// Tangani permintaan POST untuk hapus lowongan
if (isset($_POST['delete_lowongan'])) {
    $id = $_POST['id'];
    $statement = $db->prepare("DELETE FROM lowongan WHERE id = :id");
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    header('Location: index.php');
    exit();
}

// Ambil semua data lowongan
$statement = $db->query("SELECT * FROM lowongan ORDER BY id DESC");
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
// Ambil semua nama pengguna dengan role 'Perusahaan' dari tabel user untuk dropdown
$statement = $db->query("SELECT id, nama FROM user WHERE role = 'Perusahaan' ORDER BY nama ASC");
$user_list = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/logo_dinamika.png">
    <title>Jobs - Sistem Informasi BKK</title>
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
        <a class="navbar-brand" href="index">SISTEM INFORMASI BURSA KERJA KHUSUS <small>(SMK DINAMIKA KOTA
                TEGAL)</small></a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
            data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
            aria-label="Toggle navigation">
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
                            <span class="user"
                                style="font-family: monospace;"><?php echo $role_user; ?>&nbsp;<?php echo $nama_user; ?></span>
                        </p>
                    </div>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard"
                    style="background-color: #0054a8;">
                    <a class="nav-link" href="index">
                        <i class="fa fa-fw fa-dashboard"></i>
                        <span class="nav-link-text">Kelola Lowongan</span>
                    </a>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
                    <a class="nav-link" href="tables">
                        <i class="fa fa-fw fa-table"></i>
                        <span class="nav-link-text">Verifikasi Berkas</span>
                    </a>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export">
                    <a class="nav-link" href="export">
                        <i class="fa fa-fw fa-print"></i>
                        <span class="nav-link-text">Cetak</span>
                    </a>
                </li>

                <?php if ($role_user == 'Admin'): ?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export">
                    <a class="nav-link" href="user">
                        <i class="fa fa-fw fa-user"></i>
                        <span class="nav-link-text">Kelola User</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Version">
                    <a class="nav-link" href="#VersionModal" data-toggle="modal" data-target="#VersionModal">
                        <i class="fa fa-fw fa-code"></i>
                        <span class="nav-link-text">v-6.0</span>
                    </a>
                </li> -->
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
                    <a href="#">Kelola Lowongan</a>
                </li>
            </ol>
            <!-- Example DataTables Card-->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Lowogan Pekerjaan
                    <button class="btn btn-primary btn-sm float-right" data-toggle="modal"
                        data-target="#addModal">Tambah Lowogan</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>Job Title</th>
                                    <th>Tanggal</th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled"></th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled"
                                        style="text-align:right">Aksi</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $key): ?>
                                <tr id="row_<?php echo $key['id']; ?>">
                                    <td><?php echo $key['perusahaan']; ?></td>
                                    <td><?php echo $key['title']; ?></td>
                                    <td><?php echo $key['tanggal']; ?></td>
                                    <td class="td-no-border">
                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#detailModal"
                                            data-id="<?php echo $key['id']; ?>">Detail</button>
                                    </td>
                                    <td class="td-no-border">
                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#editModal" data-id="<?php echo $key['id']; ?>">Edit</button>
                                    </td>
                                    <td class="td-no-border">
                                        <button class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#deleteModal"
                                            data-id="<?php echo $key['id']; ?>">Hapus</button>
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
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
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
                        <button class="btn btn-close card-shadow-2 btn-sm" type="button"
                            data-dismiss="modal">Batal</button>
                        <a class="btn btn-primary btn-sm card-shadow-2" href="logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Lowongan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="detail_perusahaan">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="detail_perusahaan" readonly>
                        </div>
                        <div class="form-group">
                            <label for="detail_title">Job Title</label>
                            <input type="text" class="form-control" id="detail_title" readonly>
                        </div>
                        <div class="form-group">
                            <label for="detail_deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="detail_deskripsi" rows="4" readonly></textarea>
                        </div>
                        <div class="form-group">
                            <label for="detail_tanggal">Tanggal</label>
                            <textarea class="form-control" id="detail_deskripsi" rows="4" readonly></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Add -->
        <!-- Modal Add -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Lowongan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="POST" action="index.php">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="add_perusahaan">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="add_perusahaan" name="perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label for="add_title">Job Title</label>
                                <input type="text" class="form-control" id="add_title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="add_deskripsi">Deskripsi</label>
                                <textarea class="form-control" id="add_deskripsi" name="deskripsi" rows="4"
                                    required></textarea>
                            </div>
                            <!-- Field Tanggal -->
                            <input type="hidden" id="add_tanggal" name="tanggal" value="<?php echo date('d-m-Y'); ?>">
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit" name="add_lowongan">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Lowongan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="POST" action="index.php">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_perusahaan">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="edit_perusahaan" name="perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_title">Job Title</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_deskripsi">Deskripsi</label>
                                <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="4"
                                    required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit" name="edit_lowongan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal Delete -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Hapus Lowongan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="POST" action="index.php">
                        <input type="hidden" id="delete_id" name="id">
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin menghapus lowongan ini?</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-danger" type="submit" name="delete_lowongan">Hapus</button>
                        </div>
                    </form>
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

    <script>
    $(document).ready(function() {
        // Mengisi modal detail dengan data dari server
        $('#detailModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            $.ajax({
                url: 'index.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'get_details',
                    id: id
                },
                success: function(data) {
                    if (data) {
                        $('#detail_perusahaan').val(data.perusahaan);
                        $('#detail_title').val(data.title);
                        $('#detail_deskripsi').val(data.deskripsi);
                    }
                }
            });
        });

        // Mengisi modal edit dengan data dari server
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            $.ajax({
                url: 'index.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'get_details',
                    id: id
                },
                success: function(data) {
                    if (data) {
                        $('#edit_id').val(data.id);
                        $('#edit_perusahaan').val(data.perusahaan);
                        $('#edit_title').val(data.title);
                        $('#edit_deskripsi').val(data.deskripsi);
                    }
                }
            });
        });

        // Mengisi modal delete dengan id
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            $('#delete_id').val(id);
        });
    });
    </script>
</body>

</html>