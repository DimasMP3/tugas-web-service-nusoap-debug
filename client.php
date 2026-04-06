<?php

require_once __DIR__ . "/lib/nusoap.php";

$client = new nusoap_client("http://127.0.0.1/xml/server.php?wsdl", true);

$a = 10;
$b = 15;

$resultTambah = $client->call('jumlahkan', array("a" => $a, "b" => $b));

if ($client->fault) {
    echo "SOAP Fault:\n";
    print_r($resultTambah);
    exit;
}

$err = $client->getError();
if ($err) {
    echo "SOAP Error: " . $err;
    exit;
}

echo "Hasil penjumlahan dari " . $a . " dan " . $b . " adalah " . $resultTambah . "<br>";

?>
