<?php
require_once("../admin/database.php");
require_once("../admin/auth.php");

// Ambil data lowongan dari database
try {
    $query = "SELECT * FROM lowongan";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $lowongan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Sistem Bursa Kerja Khusus</title>
    <link rel="shortcut icon" href="../images/logo_dinamika.png">
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
    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
        }

        .navbar-logo {
            max-width: 50px; /* Ukuran maksimum untuk logo */
            height: auto; /* Menjaga proporsi gambar */
            margin-right: 10px; /* Jarak antara logo dan teks */
        }

        .navbar-text {
            font-size: 1rem; /* Ukuran teks */
            color: #fff; /* Warna teks */
            font-weight: bold;
        }

        .card-custom {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .card-header {
            background-color: #6C757D;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .card-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }
        .card-text {
            font-size: 0.875rem;
            color: #495057;
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-custom:hover {
            background-color: #0056b3;
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
    <div class="container-wrapper">
        <div class="shadow">
            <!-- Navbar -->
            <nav class="navbar navbar-fixed navbar-inverse form-shadow">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <!-- Logo dan teks judul -->
                        <a class="navbar-brand d-flex align-items-center" href="#">
                            <img src="../images/logo_dinamika.png" alt="Logo Dinamika" class="navbar-logo">
                            <span class="navbar-text">Sistem Informasi Bursa Kerja Khusus</span>
                        </a>
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li><a href="index">Lowongan Pekerjaan</a></li>
                            <li><a href="lihat">Cek lamaran</a></li>
                            <li class="navbar-right">
                                <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                                    <i class="fa fa-fw fa-sign-out"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="container mt-4">
                <h2>Daftar Lowongan Pekerjaan</h2>
                <div class="row">
                    <?php foreach ($lowongan as $item): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card card-custom">
                                <div class="card-header">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <h6 class="card-subtitle"><?php echo htmlspecialchars($item['perusahaan']); ?></h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Tanggal Posting: <?php echo htmlspecialchars($item['tanggal']); ?></p>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="detail.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-primary-custom">Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <footer>
            <div class="container text-center">
                <small>Copyright &copy; SMK DINAMIKA KOTA TEGAL</small>
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
