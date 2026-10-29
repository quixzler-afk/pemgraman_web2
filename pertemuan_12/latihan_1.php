<?php
$con = mysqli_connect("localhost", "root", "");

if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_select_db($con, "lat_dbase");

mysqli_query($con, "UPDATE tbl_mhs SET Age = '57' WHERE FirstName = 'Subianto' AND LastName = 'Prabowo'");

mysqli_close($con);
?>