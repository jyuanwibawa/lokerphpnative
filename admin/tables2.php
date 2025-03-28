<?php
require_once("database.php"); // Koneksi DB

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

// Proses update status lamaran jika ada permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lamaran_id = $_POST['lamaran_id'];
    $status_lamaran = $_POST['status_lamaran'];

    $stmt = $db->prepare("UPDATE lamaran SET slamaran2 = ? WHERE id = ?");
    $stmt->execute([$status_lamaran, $lamaran_id]);

    if ($stmt->rowCount()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit(); // Akhiri script untuk mencegah rendering HTML
}

$statement = $db->prepare("
    SELECT lamaran.*, lowongan.title, lowongan.perusahaan 
    FROM lamaran 
    JOIN lowongan ON lamaran.lowongan_id = lowongan.id 
    WHERE lamaran.slamaran = 'Valid' 
    AND (lamaran.slamaran2 IS NULL OR lamaran.slamaran2 != 'Valid') 
    AND lowongan.perusahaan = ?
    ORDER BY lamaran.id DESC
");
$statement->execute([$nama_user]);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

$total_laporan_masuk = 0;
$total_laporan_menunggu = 0;
$total_laporan_ditanggapi = 0;

// Query untuk total laporan masuk
$stmt_masuk = $db->prepare("
    SELECT COUNT(*) AS total 
    FROM lamaran 
    JOIN lowongan ON lamaran.lowongan_id = lowongan.id 
    WHERE lowongan.perusahaan = ?
");
$stmt_masuk->execute([$nama_user]);
$row_masuk = $stmt_masuk->fetch(PDO::FETCH_ASSOC);
$total_laporan_masuk = $row_masuk['total'];

// Query untuk total laporan yang sudah ditanggapi (sudah memiliki nilai pada slamaran)
$stmt_ditanggapi = $db->prepare("
    SELECT COUNT(*) AS total 
    FROM lamaran 
    JOIN lowongan ON lamaran.lowongan_id = lowongan.id 
    WHERE slamaran IS NOT NULL 
    AND lowongan.perusahaan = ?
");
$stmt_ditanggapi->execute([$nama_user]);
$row_ditanggapi = $stmt_ditanggapi->fetch(PDO::FETCH_ASSOC);
$total_laporan_ditanggapi = $row_ditanggapi['total'];

// Query untuk total laporan yang belum memiliki nilai pada field slamaran
$stmt_menunggu = $db->prepare("
    SELECT COUNT(*) AS total 
    FROM lamaran 
    JOIN lowongan ON lamaran.lowongan_id = lowongan.id 
    WHERE slamaran IS NULL 
    AND lowongan.perusahaan = ?
");
$stmt_menunggu->execute([$nama_user]);
$row_menunggu = $stmt_menunggu->fetch(PDO::FETCH_ASSOC);
$total_laporan_menunggu = $row_menunggu['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/logo.png">
    <title>Verify - Sistem Informasi BKK</title>
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
        <a class="navbar-brand" href="index">SISTEM INFORMASI BURSA KERJA KHUSUS <small>(SMK DINAMIKA KOTA TEGAL)</small></a>
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
                            <span class="user" style="font-family: monospace;"><?php echo $role_user; ?>&nbsp;<?php echo $nama_user; ?></span>
                        </p>
                    </div>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables" >
                    <a class="nav-link" href="tables2">
                        <i class="fa fa-fw fa-table"></i>
                        <span class="nav-link-text">Verifikasi Pelamar</span>
                    </a>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export">
                    <a class="nav-link" href="export2">
                        <i class="fa fa-fw fa-print"></i>
                        <span class="nav-link-text">Cetak</span>
                    </a>
                </li>
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
                    <a href="#">Verifikasi Berkas</a>
                </li>
            </ol>

            <!-- Icon Cards-->
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-primary o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fa fa-fw fa-comments-o"></i>
                            </div>
                            <div class="mr-5"><?php echo $total_laporan_masuk; ?> Laporan Masuk</div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1" href="tables">
                            <span class="float-left">Total Laporan Masuk</span>
                            <span class="float-right">
                                <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-warning o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fa fa-fw fa-hourglass-half"></i>
                            </div>
                            <div class="mr-5"><?php echo $total_laporan_menunggu; ?> Belum Keluar</div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1" href="index">
                            <span class="float-left">PIlih Lowongan</span>
                            <span class="float-right">
                                <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-success o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fa fa-fw fa-check"></i>
                            </div>
                            <div class="mr-5"><?php echo $total_laporan_ditanggapi; ?> Sudah Diterima</div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1" href="laporan_masuk">
                            <span class="float-left">Laporan Masuk</span>
                            <span class="float-right">
                                <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-danger o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fa fa-fw fa-times"></i>
                            </div>
                            <div class="mr-5"><?php echo $total_laporan_menunggu; ?> Ditolak</div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1" href="laporan_masuk">
                            <span class="float-left">Sudah Ditanggapi</span>
                            <span class="float-right">
                                <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                    </div>
                </div> -->
            </div>

            <!-- DataTables Example -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Data Laporan Pelamar
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="sorting_asc_disabled sorting_desc_disabled">No</th>
                                    <th>Nama Pelamar</th>
                                    <th>Judul Lowongan</th>
                                    <th>Perusahaan</th>
                                    <th>Status</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($results as $key): ?>
                        <tr>
                            <td><?php echo $no++;  ?></td>
                            <td><?php echo $key['nama']; ?></td>
                            <td><?php echo $key['title']; ?></td>
                            <td><?php echo $key['perusahaan']; ?></td>
                            <td><?php echo $key['slamaran2'] ? $key['slamaran2'] : 'Belum Ditanggapi'; ?></td>
                            <td>
                                <button class="btn btn-warning btn-edit-status" 
                                        data-id="<?php echo $key['id']; ?>"
                                        data-nama="<?php echo $key['nama']; ?>"
                                        data-email="<?php echo $key['email']; ?>"
                                        data-telpon="<?php echo $key['telpon']; ?>"
                                        data-title="<?php echo $key['title']; ?>"
                                        data-perusahaan="<?php echo $key['perusahaan']; ?>"
                                        data-cv="<?php echo $key['cv']; ?>">
                                    Proses
                                </button>
                                <?php if (!empty($key['cv'])): ?>
                <a href="../uploads/<?php echo $key['cv']; ?>" class="btn btn-primary" download>Unduh CV</a>
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

        <!-- Footer-->
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
                        <h5 class="modal-title" id="exampleModalLabel">Yakin ingin Logout?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Klik "Logout" dibawah jika Anda ingin mengakhiri sesi Anda saat ini.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>

 <!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStatusModalLabel">Edit Status Lamaran</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editStatusForm">
                    <input type="hidden" name="lamaran_id" id="lamaran_id" value="">
                    <div class="form-group">
                        <label for="nama">Nama Pelamar</label>
                        <input type="text" class="form-control" id="nama" name="nama" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" readonly>
                    </div>
                    <div class="form-group">
                        <label for="telpon">Telpon</label>
                        <input type="text" class="form-control" id="telpon" name="telpon" readonly>
                    </div>
                    <div class="form-group">
                        <label for="title">Judul Lowongan</label>
                        <input type="text" class="form-control" id="title" name="title" readonly>
                    </div>
                    <div class="form-group">
                        <label for="perusahaan">Perusahaan</label>
                        <input type="text" class="form-control" id="perusahaan" name="perusahaan" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cv">CV</label>
                        <input type="text" class="form-control" id="cv" name="cv" readonly>
                    </div>
                    <div class="form-group">
                        <label for="status_lamaran">Status Lamaran</label>
                        <input type="hidden" name="status_lamaran" id="status_lamaran_hidden" value="validdd">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="salahButton" type="button">Invalid</button>
                <button class="btn btn-primary" id="benarButton" type="button">Valid</button>
            </div>
        </div>
    </div>
</div>



    </div>
    <!-- /.content-wrapper-->

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

    <!-- Custom Script -->
    <script>
$(document).ready(function() {
    // Buka modal saat tombol "Edit Status" diklik
    $('.btn-edit-status').on('click', function() {
        var lamaranId = $(this).data('id');
        var nama = $(this).data('nama');
        var email = $(this).data('email');
        var telpon = $(this).data('telpon');
        var title = $(this).data('title');
        var perusahaan = $(this).data('perusahaan');
        var cv = $(this).data('cv');

        $('#lamaran_id').val(lamaranId);
        $('#nama').val(nama);
        $('#email').val(email);
        $('#telpon').val(telpon);
        $('#title').val(title);
        $('#perusahaan').val(perusahaan);
        $('#cv').val(cv);
        $('#status_lamaran_hidden').val(''); // Reset status_lamaran_hidden
        $('#editStatusModal').modal('show');
    });

    // Kirim data saat tombol "Invalid" diklik
    $('#salahButton').on('click', function() {
        $('#status_lamaran_hidden').val('Invalid'); // Set status to "Invalid"
        submitStatusForm(); // Kirim data secara langsung
    });

    // Kirim data saat tombol "Valid" diklik
    $('#benarButton').on('click', function() {
        $('#status_lamaran_hidden').val('Valid'); // Set status to "Valid"
        submitStatusForm(); // Kirim data secara langsung
    });

    // Fungsi untuk mengirim form status lamaran
    function submitStatusForm() {
        var form = $('#editStatusForm');
        $.ajax({
            type: 'POST',
            url: '', // Menggunakan URL yang sama dengan halaman ini
            data: form.serialize(),
            success: function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.reload(); // Memuat ulang halaman jika sukses
                } else {
                    alert('Gagal memperbarui status.');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error); // Log error AJAX untuk debug
            }
        });
    }
});
</script>



</body>

</html>
