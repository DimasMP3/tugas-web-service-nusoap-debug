<?php
require_once "lib/nusoap.php";

$server = new soap_server();

$namespace = 'http://localhost/xml/nusoap-debug/server.php';
$server->configureWSDL('nusoapServer', $namespace);

$server->register(
    'jumlahkan',
    array('a' => 'xsd:int', 'b' => 'xsd:int'),
    array('return' => 'xsd:int'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Menjumlahkan dua bilangan'
);

// Bug 3 fixed: Register fungsi kurang diperbaiki dengan wsdl mapping tipe pengembalian
$server->register(
    'kurangi',
    array('x' => 'xsd:int', 'y' => 'xsd:int'),
    array('return' => 'xsd:int'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Mengurangi dua bilangan'
);

// Bug 2 fixed: menambahkan perintah return $hasil agar nilai dapat dimunculkan
function jumlahkan($a, $b) {
    $hasil = $a + $b;
    return $hasil;
}

function kurangi($x, $y) {
    return $x - $y;
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents('php://input');
$server->service($HTTP_RAW_POST_DATA);
?>
