<?php

return [
   'TIMEZONE' =>'America/Costa_Rica',
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

   'SUPPORTED_SIGN_FORMAT' => ['xml_cofirma','xml_contrafirma','odf','msoffice'],
   'SUPPORTED_VALIDATE_FORMAT' => ['certificate','cofirma','contrafirma','odf','msoffice'],

   'PUBLIC_CERTIFICATE' => '',
   'SERVER_PUBLIC_KEY' =>openssl_pkey_get_public(file_get_contents('./cert.crt')),
   'CODE' =>'',
   'PRIVATE_KEY' => openssl_get_privatekey(file_get_contents('./cert.key')),
   'URL_NOTIFY' =>'N/D'
];

?>
