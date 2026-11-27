
<?php
$host = "localhost";
$user = "root";
$password = "password_salah";

$koneksi = mysql_connect($host, $user, $password);

if (!$koneksi) {
    die("Koneksi database gagal");
}

?>
