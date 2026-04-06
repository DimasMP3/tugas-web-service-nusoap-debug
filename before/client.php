<?

require_once "lib/nusoap.php";

$client = new nusoap_client("http://localhost/XML/server.php", true);

$a = 10;
$b = 15;

$result = $client->call('jumlahkan', array("a" => $a, "b" => $b));

echo "Hasil penjumlahand dari " . $a . " dan " . $b . " adalah " . $result;

?>

