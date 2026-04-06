<?php

function hasilPenjumlahan($a, $b) {
    $hasil = $a + $b;
    return $hasil;
}

function hasilPengurangan($a, $b) {
    $hasil = $a - $b;
    return $hasil;
}

$a = 15 ;
$b = 10;

echo "Hasil Penjumlahan Dari " . $a . " dan " . $b . " adalah " . hasilPenjumlahan($a, $b) . "<br>";
echo "Hasil Pengurangan Dari " . $a . " dan " . $b . " adalah " . hasilPengurangan($a, $b) . "<br>";

?>