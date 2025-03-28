<?php
require_once("admin/database.php");

$message = "";
$errors = [];

// Inisialisasi variabel untuk menjaga nilai input
$username = '';
$password = '';
$nama = '';
$email = '';
$telpon = '';
$role = '';

if (isset($_POST['register']) && $_POST['register'] === "Register") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $telpon = $_POST['telpon'];
    $role = $_POST['role'];

    // Cek apakah username sudah ada
    $sql = "SELECT COUNT(*) FROM user WHERE username = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Username sudah digunakan.";
    }

    // Cek apakah email sudah ada
    $sql = "SELECT COUNT(*) FROM user WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Email sudah digunakan.";
    }

    // Cek apakah telpon sudah ada
    $sql = "SELECT COUNT(*) FROM user WHERE telpon = :telpon";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':telpon', $telpon);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Nomor telepon sudah digunakan.";
    }


    // Jika tidak ada kesalahan, lakukan insert
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (username, password, nama, email, telpon, role, status) VALUES (:username, :password, :nama, :email, :telpon, :role, 'Pending')";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $hashed_password);
        $stmt->bindValue(':nama', $nama);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':telpon', $telpon);
        $stmt->bindValue(':role', $role);

        if ($stmt->execute()) {
            $message = "Pendaftaran berhasil! Silakan tunggu verifikasi dari admin.";
            echo "<script>
                    alert('$message');
                    window.location.href = 'index'; // Ganti 'index' dengan URL ke formulir login Anda
                  </script>";
            exit();
        } else {
            $message = "Terjadi kesalahan. Silakan coba lagi.";
        }
    } else {
        $message = implode( '' , $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Sistem Informasi Bursa Kerja Khusus</title>
    <link href="admin/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="admin/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="admin/css/admin.css" rel="stylesheet">

    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card-login {
            width: 100%;
            max-width: 500px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background: white;
            padding: 20px;
        }

        .card-header {
            background: url('images/logo_dinamika.png') no-repeat center;
            background-size: contain;
            height: 150px;
        }

        .card-body {
            padding: 30px;
        }

        .form-group label {
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #adb5bd;
            padding: 12px;
            font-size: 16px;
        }

        .btn-primary {
            background-color: #0056b3;
            border-color: #004085;
            font-size: 16px;
            padding: 10px;
        }

        .btn-primary:hover {
            background-color: #004085;
            border-color: #003770;
        }

        .text-danger {
            color: #dc3545;
        }

        .table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 12px;
            border: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card card-login mx-auto">
            <div class="card-header"></div>
            <div class="card-body">
                <form method="post">
                    <table class="table">
                        <tr>
                            <th>Label</th>
                            <th>Input</th>
                        </tr>
                        <tr>
                            <td><label for="nama">Nama</label></td>
                            <td><input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Lengkap" required></td>
                        </tr>
                        <tr>
                            <td><label for="email">Email</label></td>
                            <td><input class="form-control" id="email" name="email" type="email" placeholder="Email" required></td>
                        </tr>
                        <tr>
                            <td><label for="username">Username</label></td>
                            <td><input class="form-control" id="username" name="username" type="text" placeholder="Enter Username" required></td>
                        </tr>
                        <tr>
                            <td><label for="role">Saya Adalah</label></td>
                            <td>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="Alumni">Alumni</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="telpon">Telepon</label></td>
                            <td><input class="form-control" id="telpon" name="telpon" type="text" placeholder="Nomor Telepon" required></td>
                        </tr>
                        <tr>
                            <td><label for="password">Password</label></td>
                            <td><input class="form-control" id="password" name="password" type="password" placeholder="Password" required></td>
                        </tr>
                    </table>
                    <button type="submit" class="btn btn-primary btn-block" name="register" value="Register">Register</button>
                    <div class="text-center mt-3">
                        Sudah punya akun? <a href="index">Masuk Disini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>