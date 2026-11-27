
<?php
class Mahasiswa {

    public function tampilkan() {
        echo "Halo Mahasiswa<br>";
    }

    public function belajar() {
        echo "Sedang belajar PHP<br>";
    }

    public function belajar123() {
        echo "Belajar Pemrograman Web";
    }

}

// Membuat objek
$mhs = new Mahasiswa();

// Menampilkan data
$mhs->tampilkan();
$mhs->belajar();
$mhs->belajar123();

?>
