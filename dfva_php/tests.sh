#!/usr/bin/env bash

cd tests/
./phpunit-8.1.phar authentication.php --dont-report-useless-tests --debug --verbose
echo "###Pruebas unitarias de authentication.php terminadas.###"
echo "Presiona cualquier tecla para continuar..."
read
./phpunit-8.1.phar TestValidateDocuments validation.php --dont-report-useless-tests --verbose --debug
echo "###Pruebas unitarias de la clase TestValidateDocuments de validation.php terminadas.###"
echo "Presiona cualquier tecla para continuar..."
read
./phpunit-8.1.phar TestValidateCertificates validation.php --dont-report-useless-tests --verbose --debug
echo "###Pruebas unitarias de la clase TestValidateCertificates de validation.php terminadas.###"
echo "Presiona cualquier tecla para continuar..."
read
./phpunit-8.1.phar signer.php --dont-report-useless-tests --debug --verbose
echo "###Pruebas unitarias de signer.php terminadas.###"
echo "Presiona cualquier tecla para continuar..."
read
