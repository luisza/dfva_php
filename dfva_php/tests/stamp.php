<?php


use PHPUnit\Framework\TestCase;
require_once dirname(__FILE__).'/utils.php';
require_once dirname(__FILE__).'/../client.php';

$ALLOWED_TEST = array();
$transactions = array();

$client = new dfva_php\DfvaClient;

class TestDocumentStamp extends TestCase
{

	public static function setUpBeforeClass(): void
    {
        global $TIMEWAIT;
		global $ALLOWED_TEST, $client, $transactions, $FORMAT_WAIT;
        foreach (DOCUMENT_STAMP_TABLE as $identification => $value){
            foreach (DOCUMENT_FORMATS as $format){
                if(!empty($ALLOWED_TEST)){
                    if(!in_array($identification, array_keys($ALLOWED_TEST))){						
                        continue;
                    }else{
						if(!in_array($format, $ALLOWED_TEST[$identification])){
						   continue;
						}
					}
                }
				
                $auth_resp = $client->stamp(
                    read_files($format),
                    $_format=$format,
                    $id_functionality=$identification,
                    $reason="Firma sin razon", 
                    $place="Lugar desconocido"
                    );
                if(!in_array($identification, array_keys($transactions))){
                    $transactions[$identification] = array();
                }
                $transactions[$identification][$format] = $auth_resp;
                
                if($auth_resp['status'] != 0){
					print_r($auth_resp);
                    throw new Exception();
                }
            }
		}
        sleep($TIMEWAIT);
        echo "Recuerde modificar los archivos de configuración y registrar " .
            "la institución en dfva";
    }
	

    function do_checks($format, $identification)
    {
		settype($identification, 'string');
        global  $ALLOWED_TEST, $transactions, $client;
        if (!empty($ALLOWED_TEST)) {
            if (!in_array($identification,  array_keys($ALLOWED_TEST)) ) {
                return null;
            }else{
				if(!in_array($format, $ALLOWED_TEST[$identification])){
					return null;
				}
			}
        }
        $this->assertSame($transactions[$identification][$format]['status'], 0);
        $res = $client->stamp_check($transactions[$identification][$format]['id_transaction']);
        $this->assertSame(DOCUMENT_STAMP_TABLE[$identification][3], $res['status']);
        $delresul=$client->stamp_delete($transactions[$identification][$format]['id_transaction']);
        $this->assertSame($delresul, true);
    }

    function test_stamp_xml_cofirma_1000(){
        $this->do_checks("xml_cofirma", "1000");
	}

    function test_stamp_xml_cofirma_1001(){
        $this->do_checks("xml_cofirma", "1001");
	}

    function test_stamp_xml_cofirma_1004(){
        $this->do_checks("xml_cofirma", "1004");
	}	

    function test_stamp_xml_cofirma_1005(){
        $this->do_checks("xml_cofirma", "1005");
	}

    function test_stamp_xml_cofirma_1007(){
        $this->do_checks("xml_cofirma", "1007");
	}

    function test_stamp_xml_cofirma_1008(){
        $this->do_checks("xml_cofirma", "1008");
	}

    function test_stamp_xml_contrafirma_1000(){
        $this->do_checks("xml_contrafirma", "1000");
	}

    function test_stamp_xml_contrafirma_1001(){
        $this->do_checks("xml_contrafirma", "1001");
	}

    function test_stamp_xml_contrafirma_1004(){
        $this->do_checks("xml_contrafirma", "1004");
	}

    function test_stamp_xml_contrafirma_1005(){
        $this->do_checks("xml_contrafirma", "1005");
	}

    function test_stamp_xml_contrafirma_1007(){
        $this->do_checks("xml_contrafirma", "1007");
	}

    function test_stamp_xml_contrafirma_1008(){
        $this->do_checks("xml_contrafirma", "1008");
	}

    function test_stamp_odf_1000(){
        $this->do_checks("odf", "1000");
	}

    function test_stamp_odf_1001(){
        $this->do_checks("odf", "1001");
	}

    function test_stamp_odf_1004(){
        $this->do_checks("odf", "1004");
	}

    function test_stamp_odf_1005(){
        $this->do_checks("odf", "1005");
	}

    function test_stamp_odf_1007(){
        $this->do_checks("odf", "1007");
	}

    function test_stamp_odf_1008(){
        $this->do_checks("odf", "1008");
	}

    function test_stamp_msoffice_1000(){
        $this->do_checks("msoffice", "1000");
	}

    function test_stamp_msoffice_1001(){
        $this->do_checks("msoffice", "1001");
	}

    function test_stamp_msoffice_1004(){
        $this->do_checks("msoffice", "1004");
	}

    function test_stamp_msoffice_1005(){
        $this->do_checks("msoffice", "1005");
	}

    function test_stamp_msoffice_1007(){
        $this->do_checks("msoffice", "1007");
	}

    function test_stamp_msoffice_1008(){
        $this->do_checks("msoffice", "1008");
	}

    function test_stamp_pdf_1000(){
        $this->do_checks("pdf", "1000");
	}

    function test_stamp_pdf_1001(){
        $this->do_checks("pdf", "1001");
	}

    function test_stamp_pdf_1004(){
        $this->do_checks("pdf", "1004");
	}

    function test_stamp_pdf_1005(){
        $this->do_checks("pdf", "1005");
	}

    function test_stamp_pdf_1007(){
        $this->do_checks("pdf", "1007");
	}

    function test_stamp_pdf_1008(){
        $this->do_checks("pdf", "1008");
	}

  
}
