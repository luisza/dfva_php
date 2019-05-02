<?php 

require_once dirname(__FILE__).'/crypto.php';
require_once dirname(__FILE__).'/settings.php';
class DfvaClientInternal {
    private $crypt;
   function __construct() {
     //$this->settings = Settings::getInstance();
     $this->crypt=new dfva_crypto();
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



  public function authenticate($identification){
      date_default_timezone_set(Settings::getTimezone());
      $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'identification'=> $identification,
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);

      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> Settings::getAlgorithm(),
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> Settings::getInstitutionCode(),
                  "data"=> $edata,
                  'encrypt_method'=> Settings::getCipher()
      ];
   
      $url=Settings::getDfvaServerUrl() . Settings::getAuthenticateInstitution();
      $result = $this->send_post($url, $params);
      return   $this->crypt->decrypt($result);
  }


  public function autenticate_check($code){
      // check code format
      date_default_timezone_set(Settings::getTimezone());
      $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> Settings::getAlgorithm(),
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> Settings::getInstitutionCode(),
                  "data"=> $edata,
                  'encrypt_method'=> Settings::getCipher()
      ]; 

      $url=Settings::getDfvaServerUrl() . Settings::getCheckAuthenticateInstitution();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
      return $this->crypt->decrypt($result);
 }

  public function autenticate_delete($code){
      // check code format
      date_default_timezone_set(Settings::getTimezone());
      $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> Settings::getAlgorithm(),
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> Settings::getInstitutionCode(),
                  "data"=> $edata,
                  'encrypt_method'=>Settings::getCipher()
      ]; 

      $url=Settings::getDfvaServerUrl() . Settings::getAuthenticateDelete();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
      $datar=$this->crypt->decrypt($result);
      
      return isset($datar['result']) ? $datar['result'] : False;
 }

 public function sign($identification, $document, $resume, 
          $format='xml_cofirma'){
          date_default_timezone_set(Settings::getTimezone());

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
          $params = [
                      "data_hash"=> $hashsum,
                      "algorithm"=> Settings::getAlgorithm(),
                      "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                      'institution'=> Settings::getInstitutionCode(),
                      "data"=> $edata,
                      'encrypt_method'=>Settings::getCipher()
          ]; 

          $url=Settings::getDfvaServerUrl() . Settings::getSignInstitution();
          $result = $this->send_post($url, $params);
          return $this->crypt->decrypt($result);
  }

  public function sign_check($code){
      // check code format
      date_default_timezone_set(Settings::getTimezone());
      $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> Settings::getAlgorithm(),
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> Settings::getInstitutionCode(),
                  "data"=> $edata,
                  'encrypt_method'=>Settings::getCipher()
      ]; 

      $url=Settings::getDfvaServerUrl() . Settings::getCheckSignInstitution();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
      return $this->crypt->decrypt($result);
 }

  public function sign_delete($code){
      // check code format
      date_default_timezone_set(Settings::getTimezone());
      $data = json_encode ([
                  'institution'=> Settings::getInstitutionCode(),
                  'notification_url'=> Settings::getUrlNotify(),
                  'request_datetime'=> date(Settings::getDateFormat()),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> Settings::getAlgorithm(),
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> Settings::getInstitutionCode(),
                  "data"=> $edata,
                  'encrypt_method'=> Settings::getCipher()
      ]; 

      $url=Settings::getDfvaServerUrl() . Settings::getSignDelete();
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
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
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> Settings::getAlgorithm(),
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> Settings::getInstitutionCode(),
                  "data"=> $edata,
                  'encrypt_method'=> Settings::getCipher()
      ]; 

      if ($type == 'certificate'){
          $url = Settings::getValidateCertificate();
      }else{
          $url = Settings::getValidateDocument();
      }
      $url= Settings::getDfvaServerUrl() .$url;

      $result = $this->send_post($url, $params);
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
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> Settings::getAlgorithm(),
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> Settings::getInstitutionCode(),
                  "data"=> $edata,
                  'encrypt_method'=> Settings::getCipher()
      ]; 
      $url= Settings::getDfvaServerUrl() . Settings::getSuscriptorConnected();
      $datar = $this->send_post($url, $params);      
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
          $dev=parent::authenticate($identification);
        } catch (Exception $e) {
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
        return $dev;
    }
    public function autenticate_check($code){
        try{
          $dev=parent::autenticate_check($code);
        } catch (Exception $e) {
          $dev=$this->error_sign_auth_data ;
        }
        if($dev==null) $dev=$this->error_sign_auth_data ;
      return $dev;        
    }

    public function autenticate_delete($code){
        try{
           $dev= parent::autenticate_delete($code);
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

