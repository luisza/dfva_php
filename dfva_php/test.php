<?php namespace dfva_php;

require 'client.php';
$client= new DfvaClient;

$response = $client->authentication("04-0212-0119", AUTHENTICATION["authenticate"]);
var_dump($response);
