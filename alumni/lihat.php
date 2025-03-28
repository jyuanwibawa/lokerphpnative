<?php
require_once("../admin/database.php");
// Mulai sesi
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit();
}

// Ambil informasi pengguna dari sesi dengan nilai default jika tidak ada
$id_user = isset($_SESSION["id"]) ? $_SESSION["id"] : '';
$nama_user = isset($_SESSION["nama"]) ? $_SESSION["nama"] : '';
$email_user = isset($_SESSION["email"]) ? $_SESSION["email"] : '';
$telpon_user = isset($_SESSION["telpon"]) ? $_SESSION["telpon"] : '';


// Ambil data lowongan dari database dengan filter berdasarkan user_id
$statement = $db->prepare("
    SELECT lamaran.*, lowongan.title, lowongan.perusahaan 
    FROM lamaran 
    JOIN lowongan ON lamaran.lowongan_id = lowongan.id
    WHERE lamaran.user_id = :user_id
    ORDER BY lamaran.id DESC
");
$statement->execute(['user_id' => $id_user]);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Dashboard | Sistem Bursa Kerja Khusus</title>
    <link rel="shortcut icon" href="../images/logo_dinamika.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- font Awesome CSS -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Main Styles CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="js/bootstrap.js"></script>
    <!-- Custom CSS for status colors -->
    <style>
        .status-menunggu {
            background-color: LightSlateGrey;
        }
        .status-valid {
            background-color: teal;
            color: white;
        }
        .status-invalid {
            background-color: OrangeRed;
            color: white;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container-wrapper {
            flex: 1;
        }
        footer {
            background-color: #263238;
            color: #fff;
            padding: 10px 0;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php if (isset($notFound)): ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#failedmodal").modal('show');
            });
        </script>
    <?php endif; ?>

    <div class="container-wrapper">
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
                            <li><a href="lihat">Cek lamaran</a></li>
                            <li class="navbar-right"><a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                            <i class="fa fa-fw fa-sign-out"></i>Logout
                        </a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content -->
        <div class="container-wrapper">
            <div class="container">
                <h2>Riwayat Lamaran Pekerjaan</h2>
                <table class="table table-hover table-bordered" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
                    <thead style="background-color: #2c3e50; color: white; text-transform: uppercase; letter-spacing: 1px;">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Nama Perusahaan</th>
                            <th>Title</th>
                            <th>Verifikasi Berkas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($results as $item): ?>
                            <tr style="transition: background-color 0.3s;">
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($item['perusahaan']); ?></td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td class="
                                    <?php 
                                    if ($item['slamaran'] === NULL) {
                                        echo 'status-menunggu';
                                    } elseif ($item['slamaran'] === 'Valid') {
                                        echo 'status-valid';
                                    } elseif ($item['slamaran'] === 'Invalid') {
                                        echo 'status-invalid';
                                    }
                                    ?>" style="font-weight: bold; text-align: center; border-radius: 12px;">
                                    <?php 
                                    if ($item['slamaran'] === NULL) {
                                        echo 'Menunggu';
                                    } elseif ($item['slamaran'] === 'Valid') {
                                        echo 'Valid';
                                    } elseif ($item['slamaran'] === 'Invalid') {
                                        echo 'Invalid';
                                    }
                                    ?>
                                </td>
                               
                                <td class="text-center">
                                    <a href="detail.php?id=<?php echo htmlspecialchars($item['lowongan_id']); ?>" class="btn btn-primary-custom btn-sm" style=" border-radius: 20px;">
                                        <i class="fa fa-external-link"></i> Visit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /Content -->
        </div>

        <footer>
            <div class="container text-center">
                <small>Copyright &copy; SMK DINAMIKA KOTA TEGAL </small>
            </div>
        </footer>
    </div>

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
                    <a class="btn btn-primary-custom" href="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
