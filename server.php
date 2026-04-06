<?php

require_once __DIR__ . "/lib/nusoap.php";

$server = new soap_server();
$namespace = "urn:kalkulator";
$server->configureWSDL("KalkulatorService", $namespace);

$server->register(
    "jumlahkan",
    array("a" => "xsd:int", "b" => "xsd:int"),
    array("return" => "xsd:int"),
    $namespace,
    $namespace . "#jumlahkan",
    "rpc",
    "encoded",
    "Menjumlahkan dua angka"
);

function jumlahkan($a, $b) {
    return $a + $b;
}

$rawPostData = file_get_contents("php://input");
$server->service($rawPostData);
?>
