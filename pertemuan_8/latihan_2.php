<!DOCTYPE html>
<html>
<head>
    <title>Contoh Penggunaan UDF</title>
</head>
<body>
    <!-- Menentukan Form Input -->
    <form method="post">
        Masukkan Bilangan Pertama : <br>
        <input type="number" name="A" size="10" required><br>
        Masukkan Bilangan Kedua : <br>
        <input type="number" name="B" size="10" required><br>
        <input type="submit" name="hitung" value="Hitung">
    </form>

    <!-- membandingkan 2 buah bilangan yang diinput -->
    <?php
    if (isset($_POST["hitung"])) {
        $A = $_POST["A"];
        $B = $_POST["B"];
        function jumlah($A, $B)
        {
            return $A + $B;
        }

        function kurang($A, $B)
        {
            return $A - $B;
        }

        function kali($A, $B)
        {
            return $A * $B;
        }

        function bagi($A, $B)
        {
            return $A / $B;
        }

        echo "<br>";
        echo "Bilangan Pertama : " . $A;
        echo "<br>";

        echo "Bilangan Kedua : " . $B;
        echo "<br><br>";

        echo "Hasil Penjumlahan 2 buah bilangan";
        echo "<br>";
        $jumlahbil = jumlah($A, $B);
        printf("Penjumlahan antara : %d + %d = %d", $A, $B, $jumlahbil);
        echo "<br><br>";


        echo "Hasil Pengurangan 2 buah bilangan";
        echo "<br>";
        $kurangbil = kurang($A, $B);
        printf("Pengurangan antara : %d - %d = %d", $A, $B, $kurangbil);
        echo "<br><br>";

        echo "Hasil Perkalian 2 buah bilangan";
        echo "<br>";
        $kalibil = kali($A, $B);
        printf("Perkalian antara : %d * %d = %d", $A, $B, $kalibil);
        echo "<br><br>";

        echo "Hasil Pembagian 2 buah bilangan";
        echo "<br>";
        if ($B != 0) {
            $bagibil = bagi($A, $B);
            printf("Pembagian antara : %d / %d = %.2f", $A, $B, $bagibil);
        } else {
            echo "Pembagian dengan angka 0 tidak diperbolehkan.";
        }
        echo "<br><br>";
    }
    ?>
</body>
</html>