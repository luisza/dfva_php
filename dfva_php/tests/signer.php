<?php


use PHPUnit\Framework\TestCase;
require_once dirname(__FILE__).'/utils.php';
require_once dirname(__FILE__).'/../client.php';

$ALLOWED_TEST = array();
$transactions = array();

$client = new DfvaClient;


function load_signdocuments(){
    global $DOCUMENT_RESPONSE_TABLE, $DOCUMENT_FORMATS, $ALLOWED_TEST, $client, $transactions, $FORMAT_WAIT;
    foreach ($DOCUMENT_RESPONSE_TABLE as $identification){
        foreach ($DOCUMENT_FORMATS as $format){
            if(!empty($ALLOWED_TEST)){
                if(!(in_array($identification, $ALLOWED_TEST) && in_array($format, $ALLOWED_TEST[$identification]))){
                    continue;
                }
            }

            $auth_resp = $client->sign(
                    $identification,
                    read_files($format),
                    sprintf("test %s", $format),
                    $_format=$format,
                    );
            if(!in_array($identification, $transactions)){
                $transactions[$identification] = array();
            }
            $transactions[$identification][$format] = $auth_resp;
            if($auth_resp['id_transaction'] == 0 && !in_array($identification, [
                                                "500000000000",
                                                "01-1919-2222",
                                                "01-1919-2020",
                                                "01-1919-2121",
                                                "9-0000-0000-000"])){
            throw new Exception();
            }
        }
        sleep($FORMAT_WAIT);
    }
}


class TestDocumentReceived extends TestCase
{
    function test_setUpClass()
    {
        global $TIMEWAIT;
        load_signdocuments();
        sleep($TIMEWAIT);
        echo "Recuerde modificar los archivos de configuración y registrar " .
            "la institución en dfva";
    }

    function do_checks($format, $identification)
    {
        global $DOCUMENT_RESPONSE_TABLE, $transactions, $client;
        if (!empty($ALLOWED_TEST)) {
            if (!(in_array($identification, $ALLOWED_TEST) && in_array($format, $ALLOWED_TEST[$identification]))) {
                return null;
            }
        }

        if (in_array($identification,
            ["500000000000",
                "01-1919-2222",
                "01-1919-2020",
                "01-1919-2121",
                "9-0000-0000-000"])) {
            $this->assertSame($DOCUMENT_RESPONSE_TABLE[$identification][0], $transactions[$identification][$format]['status']);
            return null;
        }
        $res = $client->sign_check($transactions[$identification][$format]['id_transaction']);
        $this->assertSame($DOCUMENT_RESPONSE_TABLE[$identification][3],
            $res['status']);
        $client->sign_delete($transactions[$identification][$format]['id_transaction']);
    }

    function test_xml_cofirma_0180809090()
    {
        $this->do_checks("xml_cofirma", "01-8080-9090");
    }

    function test_xml_cofirma_0177889900()
    {
        $this->do_checks("xml_cofirma", "01-7788-9900");
    }

    function test_xml_cofirma_0111002211()
    {
        $this->do_checks("xml_cofirma", "01-1100-2211");
    }

    function test_xml_cofirma_0119192121()
    {
        $this->do_checks("xml_cofirma", "01-1919-2121");
    }

    function test_xml_cofirma_0133445566()
    {
        $this->do_checks("xml_cofirma", "01-3344-5566");
    }

    function test_xml_cofirma_0110102020()
    {
        $this->do_checks("xml_cofirma", "01-1010-2020");
    }

    function test_xml_cofirma_0119192222()
    {
        $this->do_checks("xml_cofirma", "01-1919-2222");
    }

    function test_xml_cofirma_0119192020()
    {
        $this->do_checks("xml_cofirma", "01-1919-2020");
    }

    function test_xml_cofirma_0160607070()
    {
        $this->do_checks("xml_cofirma", "01-6060-7070");
    }

    function test_xml_cofirma_0120203030()
    {
        $this->do_checks("xml_cofirma", "01-2020-3030");
    }

    function test_xml_cofirma_100000000000()
    {
        $this->do_checks("xml_cofirma", "100000000000");
    }

    function test_xml_cofirma_0140405050()
    {
        $this->do_checks("xml_cofirma", "01-4040-5050");
    }

    function test_xml_cofirma_500000000000()
    {
        $this->do_checks("xml_cofirma", "500000000000");
    }

    function test_xml_cofirma_900000000000()
    {
        $this->do_checks("xml_cofirma", "9-0000-0000-000");
    }

    function test_xml_contrafirma_0180809090()
    {
        $this->do_checks("xml_contrafirma", "01-8080-9090");
    }

    function test_xml_contrafirma_0177889900()
    {
        $this->do_checks("xml_contrafirma", "01-7788-9900");
    }

    function test_xml_contrafirma_0111002211()
    {
        $this->do_checks("xml_contrafirma", "01-1100-2211");
    }

    function test_xml_contrafirma_0119192121()
    {
        $this->do_checks("xml_contrafirma", "01-1919-2121");
    }

    function test_xml_contrafirma_0133445566()
    {
        $this->do_checks("xml_contrafirma", "01-3344-5566");
    }

    function test_xml_contrafirma_0110102020()
    {
        $this->do_checks("xml_contrafirma", "01-1010-2020");
    }

    function test_xml_contrafirma_0119192222()
    {
        $this->do_checks("xml_contrafirma", "01-1919-2222");
    }

    function test_xml_contrafirma_0119192020()
    {
        $this->do_checks("xml_contrafirma", "01-1919-2020");
    }

    function test_xml_contrafirma_0160607070()
    {
        $this->do_checks("xml_contrafirma", "01-6060-7070");
    }

    function test_xml_contrafirma_0120203030()
    {
        $this->do_checks("xml_contrafirma", "01-2020-3030");
    }

    function test_xml_contrafirma_100000000000()
    {
        $this->do_checks("xml_contrafirma", "100000000000");
    }

    function test_xml_contrafirma_0140405050()
    {
        $this->do_checks("xml_contrafirma", "01-4040-5050");
    }

    function test_xml_contrafirma_500000000000()
    {
        $this->do_checks("xml_contrafirma", "500000000000");
    }

    function test_xml_contrafirma_900000000000()
    {
        $this->do_checks("xml_contrafirma", "9-0000-0000-000");
    }

    function test_odf_0180809090()
    {
        $this->do_checks("odf", "01-8080-9090");
    }

    function test_odf_0177889900()
    {
        $this->do_checks("odf", "01-7788-9900");
    }

    function test_odf_0111002211()
    {
        $this->do_checks("odf", "01-1100-2211");
    }

    function test_odf_0119192121()
    {
        $this->do_checks("odf", "01-1919-2121");
    }

    function test_odf_0133445566()
    {
        $this->do_checks("odf", "01-3344-5566");
    }

    function test_odf_0110102020()
    {
        $this->do_checks("odf", "01-1010-2020");
    }

    function test_odf_0119192222()
    {
        $this->do_checks("odf", "01-1919-2222");
    }

    function test_odf_0119192020()
    {
        $this->do_checks("odf", "01-1919-2020");
    }

    function test_odf_0160607070()
    {
        $this->do_checks("odf", "01-6060-7070");
    }

    function test_odf_0120203030()
    {
        $this->do_checks("odf", "01-2020-3030");
    }

    function test_odf_100000000000()
    {
        $this->do_checks("odf", "100000000000");
    }

    function test_odf_0140405050()
    {
        $this->do_checks("odf", "01-4040-5050");
    }

    function test_odf_500000000000()
    {
        $this->do_checks("odf", "500000000000");
    }

    function test_odf_900000000000()
    {
        $this->do_checks("odf", "9-0000-0000-000");
    }

    function test_msoffice_0180809090()
    {
        $this->do_checks("msoffice", "01-8080-9090");
    }

    function test_msoffice_0177889900()
    {
        $this->do_checks("msoffice", "01-7788-9900");
    }

    function test_msoffice_0111002211()
    {
        $this->do_checks("msoffice", "01-1100-2211");
    }

    function test_msoffice_0119192121()
    {
        $this->do_checks("msoffice", "01-1919-2121");
    }

    function test_msoffice_0133445566()
    {
        $this->do_checks("msoffice", "01-3344-5566");
    }

    function test_msoffice_0110102020()
    {
        $this->do_checks("msoffice", "01-1010-2020");
    }

    function test_msoffice_0119192222()
    {
        $this->do_checks("msoffice", "01-1919-2222");
    }

    function test_msoffice_0119192020()
    {
        $this->do_checks("msoffice", "01-1919-2020");
    }

    function test_msoffice_0160607070()
    {
        $this->do_checks("msoffice", "01-6060-7070");
    }

    function test_msoffice_0120203030()
    {
        $this->do_checks("msoffice", "01-2020-3030");
    }

    function test_msoffice_100000000000()
    {
        $this->do_checks("msoffice", "100000000000");
    }

    function test_msoffice_0140405050()
    {
        $this->do_checks("msoffice", "01-4040-5050");
    }

    function test_msoffice_500000000000()
    {
        $this->do_checks("msoffice", "500000000000");
    }

    function test_msoffice_900000000000()
    {
        $this->do_checks("msoffice", "9-0000-0000-000");
    }

    function test_pdf_0180809090()
    {
        $this->do_checks("pdf", "01-8080-9090");
    }

    function test_pdf_0177889900()
    {
        $this->do_checks("pdf", "01-7788-9900");
    }

    function test_pdf_0111002211()
    {
        $this->do_checks("pdf", "01-1100-2211");
    }

    function test_pdf_0119192121()
    {
        $this->do_checks("pdf", "01-1919-2121");
    }

    function test_pdf_0133445566()
    {
        $this->do_checks("pdf", "01-3344-5566");
    }

    function test_pdf_0110102020()
    {
        $this->do_checks("pdf", "01-1010-2020");
    }

    function test_pdf_0119192222()
    {
        $this->do_checks("pdf", "01-1919-2222");
    }

    function test_pdf_0119192020()
    {
        $this->do_checks("pdf", "01-1919-2020");
    }

    function test_pdf_0160607070()
    {
        $this->do_checks("pdf", "01-6060-7070");
    }

    function test_pdf_0120203030()
    {
        $this->do_checks("pdf", "01-2020-3030");
    }

    function test_pdf_100000000000()
    {
        $this->do_checks("pdf", "100000000000");
    }

    function test_pdf_0140405050()
    {
        $this->do_checks("pdf", "01-4040-5050");
    }

    function test_pdf_500000000000()
    {
        $this->do_checks("pdf", "500000000000");
    }

    function test_pdf_900000000000()
    {
        $this->do_checks("pdf", "9-0000-0000-000");
    }
}


class ContrafirmaWrong extends TestCase
{

    function test_setUpClass()
    {
        global $client, $TIMEWAIT, $transactions;
        $format = "xml_contrafirma";
        $auth_resp = $client->sign(
            '03-0110-2020',
            read_files("xml", $name = 'no_contrafirmado.'),
            "test %s" % ($format),
            $_format = $format,
            );
        $transactions['03-0110-2020'] = array();
        $transactions['03-0110-2020']["xml_contrafirma"] = $auth_resp;
        if ($auth_resp['id_transaction'] == 0) {
            throw new Exception();
        }
        sleep($TIMEWAIT);
    }

    function test_contrafirma_not_ok()
    {
        global $client, $transactions;
        $res = $client->sign_check(
            $transactions['03-0110-2020']["xml_contrafirma"]['id_transaction']);
        $this->assertSame(15, $res['status']);
        $client->sign_delete($transactions['03-0110-2020']["xml_contrafirma"]['id_transaction']);
    }
}