<!-- Case study Operator Matematika Plus dan Minus dengan function-->
<!-- Mata Kuliah : Teknologi Web Service -->
<!-- Tim : -->
<!-- Kelas : -->
<!-- Kampus : Kramat 98 -->

<?php
//Bagian Function

function jumlahkan($x, $y) //funtion dengan nama jumlahkan
{
    $hasil=$x + $y;
    return $hasil;
}

function kurangi($a, $b) //funtion dengan nama kurangi
{
    $hasil=$a - $b;
    return $hasil;
}

//Bagian utama
$bilangan1 = 10; //pemberian nilai awal var bil1
$bilangan2 = 15; //pemberian nilai awal var bil2

$hasilplus=jumlahkan($bilangan1,$bilangan2); //pemanggilan fungsi jumlahkan
$hasilminus=kurangi($bilangan1,$bilangan2); //pemanggilan fungsi kurangi

//menampilkan hasil penjumlahan
echo "<p>Hasil penjumlahan ".$bil1." dan ".$bil2." adalah ".$hasilplus."</p>";

//menampilkan hasil pengurangan
echo "<p>Hasil pengurangan ".$bil1." dan ".$bil2." adalah ".$hasilminus."</p>";
echo "<br />";
?>
