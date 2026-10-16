<?php
$con = mysqli_connect("localhost", "root", "");

if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_select_db($con, "lat_dbase");

// membuat tabel
$sql = "CREATE TABLE tbl_mhs
(
    mhsID int NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(mhsID),
    FirstName varchar(15),
    LastName varchar(15),
    Age int
)";

mysqli_query($con, $sql);

?>