<?php

use PHPUnit\Framework\TestCase;
require_once dirname(__FILE__).'/utils.php';
require_once dirname(__FILE__).'/../client.php';

$valclient = new DfvaClient;

function pem_to_base64($certificate){
    return utf8_decode(base64_encode($certificate));
}


$CERT_FUNC = function($x){
    return pem_to_base64($x);
};

$base64_encode = function($str){
    return base64_encode($str);
};
$path = "";
$experated = [];

class TestValidateCertificates extends TestCase{

    function test_setUp(){
        global $path, $experated;
        $path = "dfva_testdocument/files/certs/";
        $experated = [
            '01-0001-0002'=> ['ANA ROJAS PRUEBA', 0, True],
            '199887755443'=> ['NARCISO CASCANTE PRUEBA', 0, True],

            '01-0001-0002exp'=> ['ANA ROJAS PRUEBA', 3, False],
            '199887755443exp'=> ['NARCISO CASCANTE PRUEBA', 3, False],

            '01-0001-0002rev'=> ['ANA ROJAS PRUEBA', 4, False],
            '199887755443rev'=> ['NARCISO CASCANTE PRUEBA', 4, False]
        ];
    }

    function make_validation($identification){
        global $valclient, $CERT_FUNC, $path, $experated;
        $cert = read_files('crt',$doc_path=$path,
            $post_read_fn = $CERT_FUNC,
            $name=str_replace('-', '', $identification).'.');
        $result = $valclient->validate($cert, 'certificate');
        $data = $experated[$identification];
        $this->assertSame($result['status'], $data[1]);
        if($data[2]) {
            $this->assertSame($result['full_name'], $data[0]);
            $this->assertSame($result['was_successfully'], $data[2]);
        }
    }

    function test_0100010002(){
        $this->make_validation("01-0001-0002");
    }

    function test_199887755443(){
        $this->make_validation("199887755443");
    }

    function test_0100010002exp(){
        $this->make_validation("01-0001-0002exp");
    }

    function test_199887755443exp(){
        $this->make_validation("199887755443exp");
    }

    function test_0100010002rev(){
        $this->make_validation("01-0001-0002rev");
    }

    function test_199887755443rev(){
        $this->make_validation("199887755443rev");
    }
}


$expected = [];

class TestValidateDocuments extends TestCase
{
    function test_setUp()
    {
        global $expected;
        $expected = [
            'cofirma'=> [
                '527789139593,José María Montealegre Fernández
                145764968887,José Figueres Ferrer', True, [23, 45, 21, 48, 12, 16]],
            'contrafirma'=> ['09-2171-6656,Ascensión Esquivel Ibarra
                08-9841-4375,Francisco Orlich Bolmarcich',
                True, [13, 24, 11, 80]],
            'msoffice'=> ['06-5980-2076,Federico Tinoco Granados
                01-4121-6048,Vicente Herrera Zeledón',
                True, [32, 47, 69, 36]],
            'odf'=> ['04-2191-3685,Luis Monge Álvarez
                06-2119-5314,José María Alfaro Zamora',
                True, [67, 51, 52, 53, 55]],
            'pdf'=> ['01-2645-3949,Juan Mora Fernández
                05-9062-3516,Rafael Calderón Fournier',
                True, [1]]
        ];
    }

    function arrays_are_similar($a, $b) {
        // if the indexes don't match, return immediately
        if (count(array_diff_assoc($a, $b))) {
            return false;
        }
        // we know that the indexes, but maybe not values, match.
        // compare the values between the two arrays
        foreach($a as $k => $v) {
            if ($v !== $b[$k]) {
                return false;
            }
        }
        // we have identical indexes, and no unequal values
        return true;
    }

    function get_list_names($namestr){
        $dev = [];
        foreach(explode("\n", $namestr) as $cedname){
            if($cedname) {
                $aux = explode(',', $cedname);
                $ced = $aux[0];
                //$name = $aux[1];
                array_push($dev, $ced);
            }
        }
        $dev = sort($dev);
        return $dev;
    }

    function prepare_names($nameslist){
        $dev = [];
        foreach ($nameslist as $data){
            # collectdata = {}
            if(in_array('identification_number', $data)){
                array_push($dev, $data['identification_number']);
            }
        }
        $dev = sort($dev);
        return $dev;
    }

    function extract_codes($codes){
        $dev = [];
        foreach ($codes as $data) {
            if (in_array('code', $data)) {
                array_push($dev, (int) $data['code']);
            }
        }
        $dev = sort($dev);
        return $dev;
    }

    function do_check($format, $filename){
        global $base64_encode, $valclient, $expected;
        $document = null;
        if(in_array($format, ['cofirma','contrafirma', 'pdf', 'odf', 'msoffice'])){
            $document = utf8_decode(read_files($filename, "dfva_testdocument/files/", $post_read_fn=$base64_encode));
        }else{
            $document = utf8_decode(read_files($filename));
        }
        $result = $valclient->validate($document, 'document', $_format=$format);
        //print_r($result);
        $extracted_errors = $this->extract_codes($result['errors']);
        $extracted_signers = $this->prepare_names($result['signers']);

        # expected
        $expected_signers = $this->get_list_names(
                $expected[$format][0]);
        $expected_errors = $expected[$format][2];

        $expected_errors = sort($expected_errors);
        $expected_signers = sort($expected_signers);

        $this->assertTrue($this->arrays_are_similar($extracted_signers, $expected_signers));
        $this->assertTrue($this->arrays_are_similar($extracted_errors, $expected_errors));
        $this->assertSame($expected[$_format][1],
            $result['was_successfully']);
    }

    function test_document_cofirma(){
        $this->do_check('cofirma', 'xml');
    }

    function est_document_contrafirma(){
        $this->do_check('contrafirma', 'xml');
    }

    function est_document_msoffice(){
        $this->do_check('msoffice', 'msoffice');
    }

    function est_document_odf(){
        $this->do_check('odf', 'odf');
    }

    function est_document_pdf(){
        $this->do_check('pdf', 'pdf');
    }
}
