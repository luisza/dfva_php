DFVA Cliente para PHP
#############################

Este cliente permite comunicarse con DFVA_ para proveer servicios de firma digital para Costa Rica a institutiones.

.. _DFVA: https://github.com/luisza/dfva

Instalación y configuración
--------------------------------

Copie el contenido de la carpeta dfva_php, en el proyecto donde desea utilizarlo.
Edite el archivo settings.php según los datos proporcionados en dfva.

Las opciones `PUBLIC_CERTIFICATE, SERVER_PUBLIC_KEY, INSTITUTION_CODE, PRIVATE_KEY`, son rutas a archivos donde se almacenan los certificados y llaves.

Modo de uso 
################

Este cliente permite:

* Autenticar personas y verificar estado de autenticación
* Firmar documento xml, odf, ms office y verificar estado de firma durante el tiempo que el usuario está firmando
* Validar un certificado emitido con la CA nacional de Costa Rica provista por el BCCR
* Validar un documento XML firmado.
* Revisar si un suscriptor está conectado.


Ejemplo de uso
----------------

**Nota:** notificationURL debe estar registrado en dfva o ser N/D en clientes no web

Si se desea autenticar y revisar estado de la autenticación

.. code:: php 

    require 'client.php';
    $client= new dfva_client;
    $response = $client->authenticate("0802880199");
    var_dump($response);
    $check_response = $client->check_autenticate($response["id_transaction"]);
    var_dump($check_response);
    $delete_response =$client->autenticate_delete($response["id_transaction"]);
    var_dump($delete_response);



Si se desea revisar si un suscriptor está conectado

.. code:: php

    require 'client.php';
    $client= new dfva_client;
    $isconnect=$client->is_suscriptor_connected("0802880199");
    var_dump($isconnect);

Si se desea firmar y revisar estado de la firma.

.. code:: php

    require 'client.php';
    $client= new dfva_client;
    $document=base64_encode(file_get_contents ('document.format'));
    $response=$client->sign("0402120119", $document, "test");
    var_dump($response);
    $check_response = $client->check_sign($response["id_transaction"]);
    var_dump($check_response);
    $delete_response =$client->sign_delete($response["id_transaction"]);
    var_dump($delete_response);

**Nota:** La revisión de estado de la autenticación/firma no es necesaria en servicios web ya que estos son notificados por en la URL de institución proporcionado.

Si se desea validar un certificado

.. code:: php

    $document=file_get_contents ('cert.crt'); // remove BEGIN CERTIFICATE and END CERTIFICATE part
    $response_validate=$client->validate($document, 'certificate');
    var_dump($response_validate);
      

Si se desea validar un documento 

.. code:: php

    // VALIDATE 
    $document=base64_encode(file_get_contents ('document.format'));
    $response_validate=$client->validate($document, 'document', 'pdf');
    // cofirma, contrafirma, odf, msoffice, pdf
    var_dump($response_validate);

