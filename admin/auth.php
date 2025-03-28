
<?php

// Memulai sesi hanya jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Jika Belum Login Redirect Ke Index
if(!isset($_SESSION["admin"])) header("Location: login");

?>
