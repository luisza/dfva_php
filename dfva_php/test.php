<?php

function pem_to_base64($certificate){
    $text=str_replace("-----BEGIN CERTIFICATE-----\n", "", $certificate);
    $text=str_replace("\n-----END CERTIFICATE-----", "", $text);
    $text=str_replace("\n", "", $text);
    return $text;
}



require 'client.php';
$client= new DfvaClient;
/**  AUTHENTICATE  **/

$response = $client->authentication("04-0212-0119", AUTHENTICATION["authenticate"]);
var_dump($response);

/**
$check_response = $client->autenticate_check($response["id_transaction"]);
var_dump($check_response);
$delete_response =$client->autenticate_delete($response["id_transaction"]);
var_dump($delete_response);

// SIGN
$document=base64_encode(file_get_contents ('cert_pub.key'));
$response=$client->sign("0402120119", $document, "test");
//$response=$client->sign("0402120119", $document, "test", $format='ppt');
var_dump($response);

$check_response = $client->sign_check($response["id_transaction"]);
var_dump($check_response);
$delete_response =$client->sign_delete($response["id_transaction"]);
var_dump($delete_response);


// VALIDATE 
$document=base64_encode(file_get_contents ('cert.crt'));
$response_validate=$client->validate($document, 'document', 'pdf');
//$response_validate=$client->validate($document, 'document', 'ppt');
var_dump($response_validate);


$document=pem_to_base64(file_get_contents ('cert.crt'));
$response_validate=$client->validate($document, 'certificate');
var_dump($response_validate);


$isconnect=$client->is_suscriptor_connected('0402120119');
var_dump($isconnect);
echo "fin";
**/
?>
