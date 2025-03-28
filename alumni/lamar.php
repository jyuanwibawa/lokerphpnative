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

// Cek apakah data form sudah dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lowongan_id = $_POST['lowongan_id'];
    $nama = $nama_user;
    $email = $email_user;
    $telpon = $telpon_user;
    $cv = $_FILES['cv']['name'];

    // Ambil detail lowongan dari database
    try {
        $query = "SELECT title, perusahaan FROM lowongan WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $lowongan_id, PDO::PARAM_INT);
        $stmt->execute();
        $lowongan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lowongan) {
            die("Lowongan tidak ditemukan.");
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    // Validasi
    if (empty($lowongan_id) || empty($nama) || empty($email) || empty($telpon) || empty($cv)) {
        $error = "Semua kolom harus diisi!";
    } else {
        // Pisahkan nama file dan ekstensi
        $ext = pathinfo($cv, PATHINFO_EXTENSION); // Ambil ekstensi file
        $basename = pathinfo($cv, PATHINFO_FILENAME); // Ambil nama file tanpa ekstensi
        
        // Format nama file yang diunggah
        $nama_file_baru = $nama . "_" . $lowongan['title'] . "_" . $lowongan['perusahaan'];
        $nama_file_baru = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nama_file_baru); // Mengganti karakter khusus dengan underscore
        $nama_file_baru .= '.' . $ext; // Tambahkan ekstensi kembali
        
        $target_dir = "../uploads/";
        $target_file = $target_dir . $nama_file_baru;

        // Simpan file CV ke server
        if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
            // Simpan informasi lamaran ke database
            try {
                $query = "INSERT INTO lamaran (lowongan_id, user_id, nama, email, telpon, cv) VALUES (:lowongan_id, :user_id, :nama, :email, :telpon, :cv)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':lowongan_id', $lowongan_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $id_user, PDO::PARAM_INT); // Tambahkan user_id ke dalam query
                $stmt->bindParam(':nama', $nama);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telpon', $telpon);
                $stmt->bindParam(':cv', $nama_file_baru); // Simpan nama file yang sudah diubah
                $stmt->execute();

                // Redirect kembali ke halaman detail lowongan setelah berhasil
                header("Location: detail.php?id=$lowongan_id&success=true");
                exit(); // Menghentikan eksekusi script lebih lanjut
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        } else {
            $error = "Terjadi kesalahan saat mengunggah file.";
        }
    }
}

// Ambil ID lowongan dari parameter URL
$lowongan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil detail lowongan dari database jika belum dilakukan di atas
if (empty($lowongan)) {
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
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lamaran Pekerjaan | Sistem Bursa Kerja Khusus</title>
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
</head>
<body>
    <div class="shadow">
        <nav class="navbar navbar-fixed navbar-inverse form-shadow">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
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

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a href="index">Lowongan Pekerjaan</a></li>
                        <li><a href="lihat">Cek lamaran</a></li>
                        <li class="navbar-right"><a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-fw fa-sign-out"></i>Logout
                    </a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav><!-- /.nav -->

        <!-- Content -->
        <div class="container"><br>
            <h2>Lamaran Pekerjaan</h2><hr>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <!-- Tampilkan detail lowongan yang dipilih -->
            <h4><?php echo htmlspecialchars($lowongan['title']); ?><br>
            <?php echo htmlspecialchars($lowongan['perusahaan']); ?></h4><br>
            <form action="lamar.php?id=<?php echo htmlspecialchars($lowongan['id']); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="lowongan_id" value="<?php echo htmlspecialchars($lowongan['id']); ?>">
                <div class="form-group">
                    <label for="nama">Nama:</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($nama_user); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email_user); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="telpon">Nomor Telepon:</label>
                    <input type="text" class="form-control" id="telpon" name="telpon" value="<?php echo htmlspecialchars($telpon_user); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="cv">CV (PDF, Max 2MB):</label>
                    <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                </div>
                <button type="submit" class="btn btn-primary-custom">Kirim Lamaran</button>
            </form>
        </div>
        <!-- /Content -->

        <hr>

        <div class="copyright py-4 text-center text-white">
            <small>Copyright &copy; SMK DINAMIKA KOTA TEGAL</small>
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

    </div>
    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="js/bootstrap.js"></script>
</body>
</html>
