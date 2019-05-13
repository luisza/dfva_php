<?php

use PHPUnit\Framework\TestCase;
require_once dirname(__FILE__).'/../client.php';
require_once dirname(__FILE__).'/utils.php';

$TEST_WITH_BCCR = getenv('TEST_WITH_BCCR') == 'True';
$AUTH_ALLOWED_TEST = [];

$authtransactions = [];
$authclient = new DfvaClient;


class AuthenticationTest extends TestCase
{
    public function load_authentication()
    {
        global $AUTH_ALLOWED_TEST, $authclient, $authtransactions;
        foreach(AUTHENTICATION_RESPONSE_TABLE as $identification => $value){
            if(!empty($AUTH_ALLOWED_TEST) && !in_array($identification, $AUTH_ALLOWED_TEST)){
                continue;
            }
            settype($identification, 'string');
            $auth_resp = $authclient->authentication($identification, AUTHENTICATION["authenticate"]);
            $authtransactions[$identification] = $auth_resp;
            //var_dump($authtransactions[$identification]);
            $eq = AUTHENTICATION_RESPONSE_TABLE[$identification][1];
            $idx = AUTHENTICATION_RESPONSE_TABLE[$identification][2];
            if ($eq == '=') {
                if ($auth_resp['id_transaction'] != $idx)
                    throw new Exception("");
            } else if ($eq == '!') {
                if ($auth_resp['id_transaction'] == $idx)
                    throw new Exception("");
            }
        }
    }
    public function test_setUp()
    {
        global $TIMEWAIT;
        try {
            $this->load_authentication();
        }catch (Exception $e)
        {
            throw new $e;
        }
        sleep($TIMEWAIT);
        echo "\nRecuerde modificar el archivo settings.php y registrar la institución en dfva\n
                export TEST_WITH_BCCR=True si se ejecuta con el BCCR\n";
    }

    public function do_checks($identification){
        global $AUTH_ALLOWED_TEST, $authtransactions, $authclient;
        if(!empty($AUTH_ALLOWED_TEST) && !in_array($identification, $AUTH_ALLOWED_TEST))
        {
            return;
        }
        if(in_array($identification, ['500000000000',
                              '01-1919-2222',
                              '01-1919-2020',
                              '01-1919-2121',
                              '9-0000-0000-000'])){
                $this->assertSame(AUTHENTICATION_RESPONSE_TABLE[$identification][0], $authtransactions[$identification]['status']);
                return;
        }else{
            $res = $authclient->authentication($authtransactions[$identification]['id_transaction'],
                AUTHENTICATION["authenticate_check"]);
            $this->assertSame(AUTHENTICATION_RESPONSE_TABLE[$identification][3], $res['status']);
            $delauth = $authclient->authentication($authtransactions[$identification]['id_transaction'],
                AUTHENTICATION["authenticate_delete"]);
            $this->assertSame($delauth, True);
        }

    }

    function test_common_auth()
    {
        global $authclient, $TEST_WITH_BCCR, $auth_resp;
        # BCCR have not 88-8888-8888 identififcation
        if($TEST_WITH_BCCR)
            return;
        $auth_resp = $authclient->authentication('88-8888-8888', AUTHENTICATION["authenticate"]);
        $this->assertSame($auth_resp['status'], 0);
        $this->assertNotSame($auth_resp['id_transaction'], 0);
        $authclient->authentication($auth_resp['id_transaction'], AUTHENTICATION["authenticate_delete"]);
    }

    /*
     * @depends test_setUp
     * */
    function test_auth_0119192020(){
        $this->do_checks("01-1919-2020");
    }
    function test_auth_0111002211(){
        $this->do_checks("01-1100-2211");
    }
    function test_auth_0177889900(){
        $this->do_checks("01-7788-9900");
    }

    function test_auth_0133445566(){
        $this->do_checks("01-3344-5566");
    }

    function test_auth_0160607070()
    {
        $this->do_checks("01-6060-7070");
    }
    function test_auth_900000000000()
    {
        $this->do_checks("9-0000-0000-000");
    }
    function test_auth_100000000000()
    {
        $this->do_checks("100000000000");
    }
    function test_auth_0120203030()
    {
        $this->do_checks("01-2020-3030");
    }
    function test_auth_0110102020()
    {
        $this->do_checks("01-1010-2020");
    }
    function test_auth_500000000000(){
        $this->do_checks("500000000000");
    }
    function test_auth_0119192222()
    {
        $this->do_checks("01-1919-2222");
    }
    function test_auth_0140405050()
    {
        $this->do_checks("01-4040-5050");
    }
    function test_auth_0180809090()
    {
        $this->do_checks("01-8080-9090");
    }
    function test_auth_0119192121()
    {
        $this->do_checks("01-1919-2121");
    }
}