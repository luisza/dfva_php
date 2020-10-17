#!/usr/bin/env bash

PARAMS=""
#PARAMS="--debug --verbose"

cd tests/
if [ ! -f phpunit.phar ]; then
    wget -O phpunit.phar  https://phar.phpunit.de/phpunit-8.1.phar
fi
chmod +x phpunit.phar
./phpunit.phar authentication.php --dont-report-useless-tests $PARAMS

echo "###Pruebas unitarias de authentication.php terminadas.###"
./phpunit.phar TestValidateDocuments validation.php --dont-report-useless-tests $PARAMS
echo "###Pruebas unitarias de la clase TestValidateDocuments de validation.php terminadas.###"
./phpunit.phar TestValidateCertificates validation.php --dont-report-useless-tests $PARAMS
echo "###Pruebas unitarias de la clase TestValidateCertificates de validation.php terminadas.###"
./phpunit.phar signer.php --dont-report-useless-tests $PARAMS
echo "###Pruebas unitarias de signer.php terminadas.###"


#./phpunit.phar authentication.php signer.php validation.php 

