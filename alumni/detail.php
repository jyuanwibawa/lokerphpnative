<?php
require_once("../admin/database.php");
require_once("../admin/auth.php");

// Ambil ID lowongan dari parameter URL
$lowongan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil detail lowongan dari database 
try {
    $query = "SELECT * FROM lowongan WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $lowongan_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Ambil data lowongan
    $lowongan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lowongan) {
        die("Lowongan tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
    <div class="alert alert-success">
        Lamaran berhasil dikirim!
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Dashboard | Sistem Bursa Kerja Khusus</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Main Styles CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="js/bootstrap.js"></script>
</head>

<body>
    <div class="shadow">
        <nav class="navbar navbar-fixed navbar-inverse form-shadow">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <img src="../images/logo_dinamika.png" width="50" height="50" alt="Logo SMK Dinamika" class="img-responsive">
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a href="index">Lowongan Pekerjaan</a></li>
                        <li><a href="lihat">Cek Lamaran</a></li>
                        <li class="navbar-right"><a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-fw fa-sign-out"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="container my-5">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm border-light">
                        <div class="card-header text-black">
                            <h4 class="mb-0">Detail Lowongan Pekerjaan</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h5 class="card-title"><?php echo htmlspecialchars($lowongan['title']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($lowongan['perusahaan']); ?></h6>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Tanggal Posting:</strong> <?php echo htmlspecialchars($lowongan['tanggal']); ?></p>
                                </div>
                                <div class="col-md-12">
                                    <p><strong>Deskripsi:</strong></p>
                                    <p><?php echo nl2br(htmlspecialchars($lowongan['deskripsi'])); ?></p>
                                </div>
                            </div>
                            <a href="lamar.php?id=<?php echo htmlspecialchars($lowongan['id']); ?>" class="btn btn-primary-custom btn-lg mt-3">Lamar Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Content -->
        <hr>
        <div class="copyright py-4 text-center text-white">
            <small>Copyright &copy; SMK DINAMIKA KOTA TEGAL</small>
        </div>
    </div>
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
                    <button class="btn btn-close card-shadow-2 btn-sm" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary-custom btn-sm card-shadow-2" href="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
</body>
</html>
