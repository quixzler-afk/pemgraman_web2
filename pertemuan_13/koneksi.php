
<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "db_mahasiswa";

// Membuat koneksi ke database
$Open = mysqli_connect($host, $user, $password, $database);

// Mengecek koneksi
if (!$Open) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
