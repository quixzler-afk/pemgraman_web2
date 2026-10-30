<?php
include "koneksi.php";

$pesan = "";

// PROSES SIMPAN DATA
if (isset($_POST['simpan'])) {

    $tgl_input = $_POST['tgl_input'];
    $kode = strtoupper(trim($_POST['kode']));
    $nama = $_POST['nama'];
    $jk = $_POST['jk'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $pekerjaan_ortu = $_POST['pekerjaan_ortu'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tgl_lahir = $_POST['tgl_lahir'];

    $mat = (int) $_POST['mat'];
    $bing = (int) $_POST['bing'];
    $umum = (int) $_POST['umum'];

    // Menghitung rata-rata
    $rata = ($mat + $bing + $umum) / 3;

    // Menentukan keterangan
    if ($rata >= 70) {
        $keterangan = "Lulus";
    } elseif ($rata >= 60) {
        $keterangan = "Cadangan";
    } else {
        $keterangan = "Tidak Lulus";
    }

    // Menentukan tempat tes
    $kode_awal = substr($kode, 0, 1);

    if ($kode_awal == "A") {
        $tempat_tes = "Gedung A";
    } elseif ($kode_awal == "B") {
        $tempat_tes = "Gedung B";
    } elseif ($kode_awal == "V") {
        $tempat_tes = "Viktor";
    } else {
        $tempat_tes = "Tidak Diketahui";
    }

    // Menyimpan ke database
    $sql = "INSERT INTO mahasiswa
    (tgl_input, kode, nama, jk, asal_sekolah,
    pekerjaan_ortu, tempat_lahir, tgl_lahir,
    mat, bing, umum, rata_rata, tempat_tes, keterangan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssssiiidss",
        $tgl_input,
        $kode,
        $nama,
        $jk,
        $asal_sekolah,
        $pekerjaan_ortu,
        $tempat_lahir,
        $tgl_lahir,
        $mat,
        $bing,
        $umum,
        $rata,
        $tempat_tes,
        $keterangan
    );

    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Data mahasiswa berhasil disimpan!";
    } else {
        $pesan = "Gagal menyimpan data: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// MENGAMBIL DATA DARI DATABASE
$query = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY id DESC");

// REKAP DATA
$jumlah_pendaftar = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM mahasiswa")
)['total'];

$jumlah_lulus = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM mahasiswa WHERE keterangan='Lulus'")
)['total'];

$jumlah_tidak_lulus = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM mahasiswa WHERE keterangan='Tidak Lulus'")
)['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UTS PHP - Pendaftaran Mahasiswa</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 30px;
            background: #f4f4f4;
        }

        .container {
            width: 95%;
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
        }

        input[type=text],
        input[type=number],
        input[type=date] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .btn {
            background: blue;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .btn:hover {
            background: darkblue;
        }

        .info {
            margin-top: 20px;
            padding: 10px;
            background: #eef;
        }

        .pesan {
            margin: 15px 0;
            padding: 12px;
            background: #d4edda;
            color: #155724;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 1000px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>FORM PENDAFTARAN MAHASISWA BARU</h2>

        <form method="POST">

            <label>Tanggal Input</label>
            <input type="date" name="tgl_input" required>

            <label>Kode Pendaftaran</label>
            <input type="text" name="kode" placeholder="Contoh: A2" required>

            <label>Nama Pendaftar</label>
            <input type="text" name="nama" required>

            <label>Jenis Kelamin</label><br>
            <input type="radio" name="jk" value="Laki-Laki" required>
            Laki-Laki

            <input type="radio" name="jk" value="Perempuan">
            Perempuan

            <br><br>

            <label>Asal Sekolah</label>
            <input type="text" name="asal_sekolah" required>

            <label>Pekerjaan Orang Tua</label>
            <input type="text" name="pekerjaan_ortu" required>

            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir" required>

            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" required>

            <label>Matematika</label>
            <input type="number" name="mat" min="0" max="100" required>

            <label>Bahasa Inggris</label>
            <input type="number" name="bing" min="0" max="100" required>

            <label>Umum</label>
            <input type="number" name="umum" min="0" max="100" required>

            <button type="submit" name="simpan" class="btn">
                SIMPAN
            </button>

        </form>

        <?php if ($pesan != "") { ?>
            <div class="pesan">
                <?php echo htmlspecialchars($pesan); ?>
            </div>
        <?php } ?>

        <h2>DATA PENDAFTAR MAHASISWA</h2>

        <div class="table-wrapper">
            <table>

                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Tempat, Tgl Lahir</th>
                    <th>Asal Sekolah</th>
                    <th>Pekerjaan Ortu</th>
                    <th>Tempat Tes</th>
                    <th>MAT</th>
                    <th>B. Inggris</th>
                    <th>Umum</th>
                    <th>Rata-rata</th>
                    <th>Keterangan</th>
                </tr>

                <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                    <tr>
                        <td><?php echo htmlspecialchars($data['kode']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama']); ?></td>
                        <td><?php echo htmlspecialchars($data['jk']); ?></td>

                        <td>
                            <?php echo htmlspecialchars($data['tempat_lahir']); ?>,
                            <?php echo htmlspecialchars($data['tgl_lahir']); ?>
                        </td>

                        <td><?php echo htmlspecialchars($data['asal_sekolah']); ?></td>

                        <td><?php echo htmlspecialchars($data['pekerjaan_ortu']); ?></td>

                        <td><?php echo htmlspecialchars($data['tempat_tes']); ?></td>

                        <td><?php echo $data['mat']; ?></td>
                        <td><?php echo $data['bing']; ?></td>
                        <td><?php echo $data['umum']; ?></td>

                        <td><?php echo number_format($data['rata_rata'], 2); ?></td>

                        <td><?php echo htmlspecialchars($data['keterangan']); ?></td>
                    </tr>

                <?php } ?>

            </table>
        </div>

        <div class="info">

            <h3>Rekap Data:</h3>

            <p>Jumlah Pendaftar:
                <b><?php echo $jumlah_pendaftar; ?></b>
            </p>

            <p>Jumlah Peserta Lulus:
                <b><?php echo $jumlah_lulus; ?></b>
            </p>

            <p>Jumlah Tidak Lulus:
                <b><?php echo $jumlah_tidak_lulus; ?></b>
            </p>

        </div>

        <div class="info">

            <h3>Keterangan:</h3>

            <p><b>Kode Tempat Tes:</b></p>

            <ul>
                <li>A = Gedung A</li>
                <li>B = Gedung B</li>
                <li>V = Viktor</li>
            </ul>

            <p><b>Hasil Seleksi:</b></p>

            <ul>
                <li>Rata-rata ≥ 70 = Lulus</li>
                <li>Rata-rata 60 - 69 = Cadangan</li>
                <li>Rata-rata &lt; 60 = Tidak Lulus</li>
            </ul>

        </div>

    </div>

</body>

</html>