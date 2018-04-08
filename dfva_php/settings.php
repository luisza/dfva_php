<?php

return [
   'TIMEZONE' =>'America/Costa_Rica',
   'DATE_FORMAT'=> "Y-m-d h:m:s",
   'ALGORITHM' =>'sha512', 
   'DFVA_SERVER_URL' =>'http://localhost:8000',
   'AUTHENTICATE_INSTITUTION' =>'/authenticate/institution/',
   'CHECK_AUTHENTICATE_INSTITUTION' =>'/authenticate/%s/institution_show/',
   'AUTHENTICATE_DELETE' =>'/authenticate/%s/institution_delete/',
   'SIGN_INSTUTION' =>'/sign/institution/',
   'CHECK_SIGN_INSTITUTION' =>'/sign/%s/institution_show/',
   'SIGN_DELETE' =>'/sign/%s/institution_delete/',
   'VALIDATE_CERTIFICATE' =>'/validate/institution_certificate/',
   'VALIDATE_DOCUMENT' =>'/validate/institution_document/',
   'SUSCRIPTOR_CONNECTED' =>'/validate/institution_suscriptor_connected/',

   'SUPPORTED_SIGN_FORMAT' => ['xml_cofirma','xml_contrafirma','odf','msoffice', 'pdf'],
   'SUPPORTED_VALIDATE_FORMAT' => ['certificate','cofirma','contrafirma','odf','msoffice', 'pdf'],

   'PUBLIC_CERTIFICATE' => './cert.crt',
   'SERVER_PUBLIC_KEY' =>'./cert_pub.key',
   'INSTITUTION_CODE' =>'96b3c534-24f2-43d8-9093-d3f1eabc51d6',
   'PRIVATE_KEY' => './cert.key',
   'URL_NOTIFY' =>'N/D',
   'CIPHER' => "aes-256-cfb",
   'SESSION_KEY_SIZE'=> 32
];

?>
