<?php
// Bug 1 fixed: typo library 'nusoup' telah diperbaiki menjadi 'nusoap'
require_once "lib/nusoap.php";

$client = new nusoap_client('http://localhost/xml/nusoap-debug/server.php?wsdl', true);

$err = $client->getError();
if ($err) {
    echo "<p>Constructor error: " . $err . "</p>";
}

$a = 10;
$b = 25;

$resultTambah = $client->call('jumlahkan', array("a" => $a, "b" => $b));
$resultKurang = $client->call('kurangi', array("x" => $a, "y" => $b));

echo "<p>Hasil penjumlahan " . $a . " dan " . $b . " adalah " . $resultTambah . "</p>";
echo "<p>Hasil pengurangan " . $a . " dan " . $b . " adalah " . $resultKurang . "</p>";
?>
