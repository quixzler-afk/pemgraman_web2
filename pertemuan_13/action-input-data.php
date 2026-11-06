
<?php

// Cek button
if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit") {

    $id_mahasiswa = $_POST['id_mahasiswa'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];

    // Validasi data kosong
    if (
        empty($id_mahasiswa) ||
        empty($nama) ||
        empty($jurusan) ||
        empty($alamat) ||
        empty($telepon)
    ) {

        ?>
        <script>
            alert('Data Harap Dilengkapi!');
            document.location = 'download-script-form-input-data.php';
        </script>
        <?php

    } else {

        // Memanggil koneksi database
        include "koneksi.php";

        // Cek NIM di database
        $cek = mysqli_prepare(
            $Open,
            "SELECT id_mahasiswa FROM mahasiswa WHERE id_mahasiswa = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $id_mahasiswa);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            ?>
            <script>
                alert('NIM sudah dipakai!, silahkan ganti NIM yang lain');
                document.location = 'download-script-form-input-data.php';
            </script>
            <?php

        } else {

            // Masukkan data ke tabel
            $input = mysqli_prepare(
                $Open,
                "INSERT INTO mahasiswa
                (id_mahasiswa, nama, jurusan, alamat, telepon)
                VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $input,
                "sssss",
                $id_mahasiswa,
                $nama,
                $jurusan,
                $alamat,
                $telepon
            );

            $query_input = mysqli_stmt_execute($input);

            if ($query_input) {

                // Jika sukses
                ?>
                <script>
                    alert('Input Data Mahasiswa Berhasil');
                    document.location = 'download-script-form-input-data.php';
                </script>
                <?php

            } else {

                // Jika gagal
                echo "Input Data Mahasiswa Gagal!, Silahkan diulangi!";

            }

            mysqli_stmt_close($input);

        }

        mysqli_stmt_close($cek);

        // Tutup koneksi database
        mysqli_close($Open);

    }

}

?>
