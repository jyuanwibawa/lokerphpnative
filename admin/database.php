<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "bkk";

try {
    // Create PDO connection
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Tambahkan ini untuk menangani kesalahan
} catch(PDOException $e) {
    // Show error
    die("Terjadi masalah: " . $e->getMessage());
}

?>
