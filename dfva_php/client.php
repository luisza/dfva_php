<?php 

require_once dirname(__FILE__).'/crypto.php';
require_once dirname(__FILE__).'/settings.php';

const AUTHENTICATION = [
    "authenticate" => 1,
    "authenticate_check" => 2,
    "authenticate_delete" => 3
];

class DfvaClientInternal {
    private $crypt;
    private $params;

   function __construct() {
        $this->crypt=new dfva_crypto();
        date_default_timezone_set(Settings::getTimezone());
        $this->params = [
           "data_hash"=> null,
           "algorithm"=> Settings::getAlgorithm(),
           "public_certificate"=> $this->crypt->get_public_certificate_pem(),
           'institution'=> Settings::getInstitutionCode(),
           "data"=> null,
           'encrypt_method'=>Settings::getCipher()
        ];
   }


  private function send_post($url, $data){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
  }

  public function authentication($identification, $action){
       /*
        * $action is a value from the AUTHENTICATION constant
        * */
      $data = $this->getAuthData($identification, $action);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);
      $this->setParams($hashsum, $edata);

      $url=Settings::getDfvaServerUrl() . $this->getURI($action, $identification);
      $result = $this->send_post($url, $this->params);
      $result_decrypted = $this->crypt->decrypt($result);
      if($action == AUTHENTICATION["authenticate_delete"]){
          return isset($result_decrypted['result']) ? $result_decrypted['result'] : False;
      }
      return $result_decrypted;
  }

  private function setParams($hashsum, $edata){
      $this->params["data_hash"] = $hashsum;
      $this->params["data"] = $edata;
  }

  private function getURI($action, $identification=null){
       switch ($action){
           case AUTHENTICATION["authenticate"]:
               return Settings::getAuthenticateInstitution();
           case AUTHENTICATION["authenticate_check"]:
               return sprintf(Settings::getCheckAuthenticateInstitution(), $identification);
           case AUTHENTICATION["authenticate_delete"]:
               return sprintf(Settings::getAuthenticateDelete(), $identification);
           default:
               return null;
       }
  }

  private function getAuthData($identification ,$action){
       switch ($action){
           case AUTHENTICATION["authenticate"]:
               return json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'identification'=> $identification,
                  'request_datetime'=> date(Settings::getDateFormat()),

              ]);
           case AUTHENTICATION["authenticate_check"]:
           case AUTHENTICATION["authenticate_delete"]:
               return $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'request_datetime'=> date(Settings::getDateFormat()),

              ]);
           default:
               return null;
       }
  }


 public function sign($identification, $document, $resume,
          $format='xml_cofirma'){
          $data = [
            'institution'=> Settings::getInstitutionCode(),
            'notification_url'=> Settings::getUrlNotify(),
            'document'=> $document,
            'format'=> $format,
            'algorithm_hash'=> Settings::getAlgorithm(),
            'document_hash'=> $this->crypt->get_hash_sum($document),
            'identification'=> $identification,
            'resumen'=> $resume,
            'request_datetime'=> date(Settings::getDateFormat())
          ];
          $data = json_encode ($data);
          $edata=$this->crypt->encrypt($data);
          $hashsum = $this->crypt->get_hash_sum($edata);

          $this->setParams($hashsum, $edata);

          $url=Settings::getDfvaServerUrl() . Settings::getSignInstitution();
          $result = $this->send_post($url, $this->params);
          return $this->crypt->decrypt($result);
  }

  public function sign_check($code){
      $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'request_datetime'=> date(Settings::getDateFormat()),
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);

      $this->setParams($hashsum, $edata);

      $url=Settings::getDfvaServerUrl() . Settings::getCheckSignInstitution();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $this->params);
      return $this->crypt->decrypt($result);
 }

  public function sign_delete($code){
      $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    

      $this->setParams($hashsum, $edata);

      $url=Settings::getDfvaServerUrl() . Settings::getSignDelete();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $this->params);
      $datar=$this->crypt->decrypt($result);
      
      return isset($datar['result']) ? $datar['result'] : False;
 }

  public function validate($document, $type, $format=Null){
      date_default_timezone_set(Settings::getTimezone());
      $data = [
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'document'=> $document,
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ];
      if(isset($format)){
        $data['format']=$format;
      }

      $data =json_encode($data);
      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    

      $this->setParams($hashsum, $edata);

      if ($type == 'certificate'){
          $url = Settings::getValidateCertificate();
      }else{
          $url = Settings::getValidateDocument();
      }
      $url= Settings::getDfvaServerUrl() .$url;

      $result = $this->send_post($url, $this->params);
      return $this->crypt->decrypt($result);
      
  }

  public function is_suscriptor_connected($identification){
     date_default_timezone_set(Settings::getTimezone());
      $data = [
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'identification'=> $identification,
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ];
      $data =json_encode($data);
      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);
      $this->setParams($hashsum, $edata);
      $url= Settings::getDfvaServerUrl() . Settings::getSuscriptorConnected();
      $datar = $this->send_post($url, $this->params);
      return isset($datar['is_connected']) ? $datar['is_connected'] : False;
  }
}

class DfvaClient extends DfvaClientInternal{
    private $error_sign_auth_data;
    private $error_validate_data;
    function __construct(){
      
      $this->error_sign_auth_data = ["code"=> "N/D",
			      "status"=> 2,
			      "identification"=>Null,
			      "id_transaction"=> 0,
			      "request_datetime"=> "",
			      "sign_document"=> "",
			      "expiration_datetime"=> "",
			      "received_notification"=> true,
			      "duration"=> 0,
            "status_text"=> "Problema de comunicación interna"];

      $this->error_validate_data = ["code"=> "N/D",
			"status"=> 2,
			"identification"=>null,
			"received_notification"=>Null,
      "status_text"=> "Problema de comunicación interna"];


      parent::__construct();

    }

    public function authenticate($identification){

        try {
          $dev=parent::authentication($identification, AUTHENTICATION["authenticate"]);
        } catch (Exception $e) {
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
        return $dev;
    }
    public function autenticate_check($code){
        try{
          $dev=parent::authentication($code, AUTHENTICATION["authenticate_check"]);
        } catch (Exception $e) {
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
      return $dev;        
    }

    public function autenticate_delete($code){
        try{
           $dev= parent::authentication($code, AUTHENTICATION["authenticate_delete"]);
         } catch (Exception $e) {
           $dev=False;
        }
        if($dev==null) $dev=False ;
      return $dev;
    }
    public function sign($identification, $document, $resume, 
              $_format='xml_cofirma'){

        if (!in_array($_format, Settings::getSupportedSignFormat()))
            return [
              "code"=> "N/D",
		          "status"=> 12,
		          "identification"=>Null,
		          "id_transaction"=> 0,
		          "request_datetime"=> "",
		          "sign_document"=> "",
		          "expiration_datetime"=> "",
		          "received_notification"=> true,
		          "duration"=> 0,
              "status_text"=> "Formato de documento inválido, posibles:".implode(
                            ",", Settings::getSupportedSignFormat())
              ];

        try{
          $dev=parent::sign($identification, $document, $resume, $format=$_format);
        } catch (Exception $e) {
          $dev=$this->error_sign_auth_data ;
        }
      if($dev==null) $dev=$this->error_sign_auth_data ;
      return $dev;  

    }
    public function sign_check($code){
        try{
          $dev=parent::sign_check($code);
        } catch (Exception $e) {
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
      return $dev;  
    }
    public function sign_delete($code){
        try{
          $dev=parent::sign_delete($code);
        } catch (Exception $e) {
           $dev=False;
        }
      if($dev==null) $dev=False ;
      return $dev;
    }
    public function validate($document, $type, $_format=Null){
        if ( isset($_format) && !in_array($_format, Settings::getSupportedValidateFormat()))
            return ["code"=> "N/D",
			              "status"=> 14,
			              "identification"=>null,
			              "received_notification"=>null,
                    "status_text"=> "Formato inválido posibles: ".implode(
                            ",", Settings::getSupportedValidateFormat())
                    ];


      try{
         $dev=parent::validate($document, $type, $format=$_format);
      } catch (Exception $e) {
        $dev=$this->error_validate_data;
      }
      if($dev==null) $dev=$this->error_validate_data;
      return $dev;
    }
    public function is_suscriptor_connected($identification){
      try{
        $dev=parent::is_suscriptor_connected($identification);
      } catch (Exception $e) {
        $dev=False ;
      }
      if($dev==null) $dev=False;
      return $dev;
    }
}

