<?php namespace dfva_php;

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

   function __construct($settings=null) {

        if($settings == null){
          $this->settings = new Settings;
          $this->settings->load();
        }else{
          $this->settings=$settings;
        }

        $this->crypt=new dfva_crypto($this->settings);
        date_default_timezone_set($this->settings->getTimezone());
        $this->params = [
           "data_hash"=> null,
           "algorithm"=> $this->settings->getAlgorithm(),
           "public_certificate"=> $this->crypt->get_public_certificate_pem(),
           'institution'=> $this->settings->getInstitutionCode(),
           "data"=> null,
           'encrypt_method'=>$this->settings->getCipher()
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
      $log = sprintf("[%s] [%s] [%s] Info authenticate: %s %s", date("d-m-Y h:m:s"),
                    __FILE__, 'INFO', $identification, $action).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      $data = $this->getAuthData($identification, $action);
      $log = sprintf("[%s] [%s] [%s] Data authenticate: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', $data).PHP_EOL;
      error_log($log, 3, FILE_PATH);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);
      $this->setParams($hashsum, $edata);

      $url=$this->settings->getDfvaServerUrl() . $this->getURI($action, $identification);
      $result = $this->send_post($url, $this->params);
      $result_decrypted = $this->crypt->decrypt($result);
      $log = sprintf("[%s] [%s] [%s] Decrypted authenticate: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', print_r($result_decrypted, true)).PHP_EOL;
      error_log($log, 3, FILE_PATH);
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
               return $this->settings->getAuthenticateInstitution();
           case AUTHENTICATION["authenticate_check"]:
               return sprintf($this->settings->getCheckAuthenticateInstitution(), $identification);
           case AUTHENTICATION["authenticate_delete"]:
               return sprintf($this->settings->getAuthenticateDelete(), $identification);
           default:
               return null;
       }
  }

  private function getAuthData($identification ,$action){
	  settype($identification, 'string');
       switch ($action){
           case AUTHENTICATION["authenticate"]:
               return json_encode ([
                  'institution'=> $this->settings->getInstitutionCode(),
                  'notification_url'=> $this->settings->getUrlNotify(),
                  'identification'=> $identification,
                  'request_datetime'=> date($this->settings->getDateFormat()),

              ]);
           case AUTHENTICATION["authenticate_check"]:
           case AUTHENTICATION["authenticate_delete"]:
               return $data = json_encode ([
                  'institution'=> $this->settings->getInstitutionCode(),
                  'notification_url'=> $this->settings->getUrlNotify(),
                  'request_datetime'=> date($this->settings->getDateFormat()),

              ]);
           default:
               return null;
       }
  }


 public function sign($identification, $document, $resume,
          $format='xml_cofirma', $reason=null, $place=null){
			  
		 settype($identification, 'string');
         $log = sprintf("[%s] [%s] [%s] Info sign: %s %s %s", date("d-m-Y h:m:s"),
                 __FILE__, 'DEBUG', $identification, $resume, $format).PHP_EOL;
         error_log($log, 3, FILE_PATH);
          $data = [
            'institution'=> $this->settings->getInstitutionCode(),
            'notification_url'=> $this->settings->getUrlNotify(),
            'document'=> $document,
            'format'=> $format,
            'algorithm_hash'=> $this->settings->getAlgorithm(),
            'document_hash'=> $this->crypt->get_hash_sum($document),
            'identification'=> $identification,
            'resumen'=> $resume,
            'request_datetime'=> date($this->settings->getDateFormat())
          ];

          if ($reason != null){
            $data['reason'] = $reason;
          }
          if ($place != null){
            $data['place'] = $place;
          }
          $data = json_encode($data);
          $log = sprintf("[%s] [%s] [%s] Data sign: %s", date("d-m-Y h:m:s"),
                   __FILE__, 'DEBUG', $data).PHP_EOL;
          error_log($log, 3, FILE_PATH);
          $edata=$this->crypt->encrypt($data);
          $hashsum = $this->crypt->get_hash_sum($edata);

          $this->setParams($hashsum, $edata);

          $url=$this->settings->getDfvaServerUrl() . $this->settings->getSignInstitution();
          $result = $this->send_post($url, $this->params);
          return $this->crypt->decrypt($result);
  }

  public function sign_check($code){
      $log = sprintf("[%s] [%s] [%s] Check sign: %s", date("d-m-Y h:m:s"),
              __FILE__, 'INFO', $code).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      $data = json_encode ([
                  'institution'=> $this->settings->getInstitutionCode(),
                  'notification_url'=> $this->settings->getUrlNotify(),
                  'request_datetime'=> date($this->settings->getDateFormat()),
      ]);
      $log = sprintf("[%s] [%s] [%s] Data Check sign: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', $data).PHP_EOL;
      error_log($log, 3, FILE_PATH);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);

      $this->setParams($hashsum, $edata);

      $url=$this->settings->getDfvaServerUrl() . $this->settings->getCheckSignInstitution();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $this->params);
      return $this->crypt->decrypt($result);
 }

  public function sign_delete($code){
      $log = sprintf("[%s] [%s] [%s] Delete sign: %s", date("d-m-Y h:m:s"),
              __FILE__, 'INFO', $code).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      $data = json_encode ([
                  'institution'=> $this->settings->getInstitutionCode(),
                  'notification_url'=> $this->settings->getUrlNotify(),
                  'request_datetime'=> date($this->settings->getDateFormat()),
      ]);
      $log = sprintf("[%s] [%s] [%s] Data Delete sign: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', $data).PHP_EOL;
      error_log($log, 3, FILE_PATH);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    

      $this->setParams($hashsum, $edata);

      $url=$this->settings->getDfvaServerUrl() . $this->settings->getSignDelete();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $this->params);
      $datar=$this->crypt->decrypt($result);
      $log = sprintf("[%s] [%s] [%s] Decrypted Delete sign: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', json_encode($datar)).PHP_EOL;
      error_log($log, 3, FILE_PATH);

      return isset($datar['result']) ? $datar['result'] : False;
 }

  public function validate($document, $type, $format=null){
      $log = sprintf("[%s] [%s] [%s] Validate: %s %s", date("d-m-Y h:m:s"),
              __FILE__, 'INFO', $type, $format).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      date_default_timezone_set($this->settings->getTimezone());
      $data = [
                  'institution'=> $this->settings->getInstitutionCode(),
                  'notification_url'=> $this->settings->getUrlNotify(),
                  'document'=> $document,
                  'request_datetime'=> date($this->settings->getDateFormat()),
                  
      ];
      if($format != null){
        $data['format']=$format;
      }

      $data =json_encode($data);
      $log = sprintf("[%s] [%s] [%s] Data Validate: %s ", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', $data).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    

      $this->setParams($hashsum, $edata);

      if ($type == 'certificate'){
          $url = $this->settings->getValidateCertificate();
      }else{
          $url = $this->settings->getValidateDocument();
      }
      $url= $this->settings->getDfvaServerUrl() .$url;

      $result = $this->send_post($url, $this->params);
      $data = $this->crypt->decrypt($result);
      $log = sprintf("[%s] [%s] [%s] Decrypted Validate: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', $data['status']).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      return $data;
      
  }

  public function is_suscriptor_connected($identification){
      $log = sprintf("[%s] [%s] [%s] Suscriptor connected: %s", date("d-m-Y h:m:s"),
              __FILE__, 'INFO', $identification).PHP_EOL;
      error_log($log, 3, FILE_PATH);
     date_default_timezone_set($this->settings->getTimezone());
      $data = [
                  'institution'=> $this->settings->getInstitutionCode(),
                  'notification_url'=> $this->settings->getUrlNotify(),
                  'identification'=> $identification,
                  'request_datetime'=> date($this->settings->getDateFormat()),
                  
      ];
      $data =json_encode($data);
      $log = sprintf("[%s] [%s] [%s] Suscriptor connected: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', $data).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);
      $this->setParams($hashsum, $edata);
      $url= $this->settings->getDfvaServerUrl() . $this->settings->getSuscriptorConnected();
      $datar = $this->send_post($url, $this->params);
      $log = sprintf("[%s] [%s] [%s] Recieved Suscriptor connected: %s", date("d-m-Y h:m:s"),
              __FILE__, 'DEBUG', $datar).PHP_EOL;
      error_log($log, 3, FILE_PATH);
      return isset($datar['is_connected']) ? $datar['is_connected'] : False;
  }
}

class DfvaClient extends DfvaClientInternal{
    private $error_sign_auth_data;
    private $error_validate_data;
    function __construct($settings=null){
      
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


      parent::__construct($settings);

    }

    public function authenticate($identification){

        try {
          $dev=parent::authentication($identification, AUTHENTICATION["authenticate"]);
        } catch (Exception $e) {
            $log = sprintf("[%s] [%s] [%s] Authenticate: %s %s", date("d-m-Y h:m:s"),
                    __FILE__, 'ERROR', $e, "authenticate").PHP_EOL;
            error_log($log, 3, FILE_PATH);
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
        return $dev;
    }
    public function autenticate_check($code){
        try{
          $dev=parent::authentication($code, AUTHENTICATION["authenticate_check"]);
        } catch (Exception $e) {
            $log = sprintf("[%s] [%s] [%s] Authenticate: %s %s", date("d-m-Y h:m:s"),
                    __FILE__, 'ERROR', $e, "authenticate_check").PHP_EOL;
            error_log($log, 3, FILE_PATH);
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
      return $dev;        
    }

    public function autenticate_delete($code){
        try{
           $dev= parent::authentication($code, AUTHENTICATION["authenticate_delete"]);
         } catch (Exception $e) {
            $log = sprintf("[%s] [%s] [%s] Authenticate: %s %s", date("d-m-Y h:m:s"),
                    __FILE__, 'ERROR', $e, "authenticate_delete").PHP_EOL;
            error_log($log, 3, FILE_PATH);
           $dev=False;
        }
        if($dev==null) $dev=False ;
      return $dev;
    }
    public function sign($identification, $document, $resume, 
              $_format='xml_cofirma', $reason=null, $place=null){

        if (!in_array($_format, $this->settings->getSupportedSignFormat()))
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
                            ",", $this->settings->getSupportedSignFormat())
              ];

        try{
          $dev=parent::sign($identification, $document, $resume, $format=$_format,
          $reason=$reason, $place=$place);
        } catch (Exception $e) {
            $log = sprintf("[%s] [%s] [%s] Sign: %s", date("d-m-Y h:m:s"),
                    __FILE__, 'ERROR', $e).PHP_EOL;
            error_log($log, 3, FILE_PATH);
          $dev=$this->error_sign_auth_data ;
        }
      if($dev==null) $dev=$this->error_sign_auth_data ;
      return $dev;  

    }
    public function sign_check($code){
        try{
          $dev=parent::sign_check($code);
        } catch (Exception $e) {
            $log = sprintf("[%s] [%s] [%s] Sign check: %s", date("d-m-Y h:m:s"),
                    __FILE__, 'ERROR', $e).PHP_EOL;
            error_log($log, 3, FILE_PATH);
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
      return $dev;  
    }
    public function sign_delete($code){
        try{
          $dev=parent::sign_delete($code);
        } catch (Exception $e) {
            $log = sprintf("[%s] [%s] [%s] Sign delete: %s", date("d-m-Y h:m:s"),
                    __FILE__, 'ERROR', $e).PHP_EOL;
            error_log($log, 3, FILE_PATH);
           $dev=False;
        }
      if($dev==null) $dev=False ;
      return $dev;
    }
    public function validate($document, $type, $_format=null){
        if ( $_format!=null && !in_array($_format, $this->settings->getSupportedValidateFormat()))
            return ["code"=> "N/D",
			              "status"=> 14,
			              "identification"=>null,
			              "received_notification"=>null,
                    "status_text"=> "Formato inválido posibles: ".implode(
                            ",", $this->settings->getSupportedValidateFormat())
                    ];


      try{
         $dev=parent::validate($document, $type, $format=$_format);
      } catch (Exception $e) {
          $log = sprintf("[%s] [%s] [%s] Validate: %s", date("d-m-Y h:m:s"),
                  __FILE__, 'ERROR', $e).PHP_EOL;
          error_log($log, 3, FILE_PATH);
        $dev=$this->error_validate_data;
      }
      if($dev==null) $dev=$this->error_validate_data;
      return $dev;
    }
    public function is_suscriptor_connected($identification){
      try{
        $dev=parent::is_suscriptor_connected($identification);
      } catch (Exception $e) {
          $log = sprintf("[%s] [%s] [%s] Is Suscriptor Connected: %s", date("d-m-Y h:m:s"),
                  __FILE__, 'ERROR', $e).PHP_EOL;
          error_log($log, 3, FILE_PATH);
        $dev=False ;
      }
      if($dev==null) $dev=False;
      return $dev;
    }
}

