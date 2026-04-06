<?php
require_once __DIR__ . '/lib/nusoap.php';

$client = new nusoap_client('http://127.0.0.1/xml/server.php?wsdl', true);
var_dump(['endpoint' => $client->endpoint]);
var_dump(['endpointType' => $client->endpointType]);

$result = $client->call('jumlahkan', ['a' => 10, 'b' => 15]);

var_dump(['fault' => $client->fault]);
var_dump(['error' => $client->getError()]);
var_dump(['result' => $result]);
var_dump(['request' => $client->request]);
var_dump(['response' => $client->response]);
