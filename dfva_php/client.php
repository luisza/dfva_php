<?php 

require dirname(__FILE__).'/crypto.php';
class DfvaClientInternal {
   
   function __construct() {
     $this->settings = include('settings.php');
     $this->crypt=new dfva_crypto();
   }


  private function send_post($url, $data){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
  }



  public function authenticate($identification){
      date_default_timezone_set($this->settings['TIMEZONE']);
      $data = json_encode ([
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  'notification_url'=> $this->settings['URL_NOTIFY'],
                  'identification'=> $identification,
                  'request_datetime'=> date($this->settings['DATE_FORMAT']),
                  
      ]);

      

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);

      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> $this->settings['ALGORITHM'],
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  "data"=> $edata,
                  'encrypt_method'=>$this->settings['CIPHER']
      ];

     

      $url=$this->settings['DFVA_SERVER_URL'] . $this->settings['AUTHENTICATE_INSTITUTION'];
      $result = $this->send_post($url, $params);
      return $this->crypt->decrypt($result['data']);
  }


  public function autenticate_check($code){
      // check code format
      date_default_timezone_set($this->settings['TIMEZONE']);
      $data = json_encode ([
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  'notification_url'=> $this->settings['URL_NOTIFY'],
                  'request_datetime'=> date($this->settings['DATE_FORMAT']),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> $this->settings['ALGORITHM'],
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  "data"=> $edata,
                  'encrypt_method'=>$this->settings['CIPHER']
      ]; 

      $url=$this->settings['DFVA_SERVER_URL'] . $this->settings['CHECK_AUTHENTICATE_INSTITUTION'];
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
      return $this->crypt->decrypt($result['data']);
 }

  public function autenticate_delete($code){
      // check code format
      date_default_timezone_set($this->settings['TIMEZONE']);
      $data = json_encode ([
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  'notification_url'=> $this->settings['URL_NOTIFY'],
                  'request_datetime'=> date($this->settings['DATE_FORMAT']),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> $this->settings['ALGORITHM'],
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  "data"=> $edata,
                  'encrypt_method'=>$this->settings['CIPHER']
      ]; 

      $url=$this->settings['DFVA_SERVER_URL'] . $this->settings['AUTHENTICATE_DELETE'];
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
      $datar=$this->crypt->decrypt($result['data']);
      
      return isset($datar['result']) ? $datar['result'] : False;
 }

 public function sign($identification, $document, $resume, 
          $format='xml_cofirma'){
          date_default_timezone_set($this->settings['TIMEZONE']);

          $data = [
            'institution'=> $this->settings['INSTITUTION_CODE'],
            'notification_url'=> $this->settings['URL_NOTIFY'],
            'document'=> $document,
            'format'=> $format,
            'algorithm_hash'=> $this->settings['ALGORITHM'],
            'document_hash'=> $this->crypt->get_hash_sum($document,  
                                            $this->settings['ALGORITHM']),
            'identification'=> $identification,
            'resumen'=> $resume,
            'request_datetime'=> date($this->settings['DATE_FORMAT'])
          ];
          $data = json_encode ($data);
          $edata=$this->crypt->encrypt($data);
          $hashsum = $this->crypt->get_hash_sum($edata);    
          $params = [
                      "data_hash"=> $hashsum,
                      "algorithm"=> $this->settings['ALGORITHM'],
                      "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                      'institution'=> $this->settings['INSTITUTION_CODE'],
                      "data"=> $edata,
                      'encrypt_method'=>$this->settings['CIPHER']
          ]; 

          $url=$this->settings['DFVA_SERVER_URL'] . $this->settings['SIGN_INSTUTION'];
          $result = $this->send_post($url, $params);
          return $this->crypt->decrypt($result['data']);
  }

  public function sign_check($code){
      // check code format
      date_default_timezone_set($this->settings['TIMEZONE']);
      $data = json_encode ([
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  'notification_url'=> $this->settings['URL_NOTIFY'],
                  'request_datetime'=> date($this->settings['DATE_FORMAT']),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> $this->settings['ALGORITHM'],
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  "data"=> $edata,
                  'encrypt_method'=>$this->settings['CIPHER']
      ]; 

      $url=$this->settings['DFVA_SERVER_URL'] . $this->settings['CHECK_SIGN_INSTITUTION'];
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
      return $this->crypt->decrypt($result['data']);
 }

  public function sign_delete($code){
      // check code format
      date_default_timezone_set($this->settings['TIMEZONE']);
      $data = json_encode ([
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  'notification_url'=> $this->settings['URL_NOTIFY'],
                  'request_datetime'=> date($this->settings['DATE_FORMAT']),
                  
      ]);

      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> $this->settings['ALGORITHM'],
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  "data"=> $edata,
                  'encrypt_method'=>$this->settings['CIPHER']
      ]; 

      $url=$this->settings['DFVA_SERVER_URL'] . $this->settings['SIGN_DELETE'];
      $url=str_replace("%s", strval($code),  $url);
      $result = $this->send_post($url, $params);
      $datar=$this->crypt->decrypt($result['data']);
      
      return isset($datar['result']) ? $datar['result'] : False;
 }

  public function validate($document, $type, $format=Null){
      date_default_timezone_set($this->settings['TIMEZONE']);
      $data = [
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  'notification_url'=> $this->settings['URL_NOTIFY'],
                  'document'=> $document,
                  'request_datetime'=> date($this->settings['DATE_FORMAT']),
                  
      ];
      if(isset($format)){
        $data['format']=$format;
      }

      $data =json_encode($data);
      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> $this->settings['ALGORITHM'],
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  "data"=> $edata,
                  'encrypt_method'=>$this->settings['CIPHER']
      ]; 

      if ($type == 'certificate'){
          $url = $this->settings['VALIDATE_CERTIFICATE'];
      }else{
          $url = $this->settings['VALIDATE_DOCUMENT'];
      }
      $url=$this->settings['DFVA_SERVER_URL'] .$url;

      $result = $this->send_post($url, $params);
      return $this->crypt->decrypt($result['data']);
      
  }

  public function is_suscriptor_connected($identification){
     date_default_timezone_set($this->settings['TIMEZONE']);
      $data = [
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  'notification_url'=> $this->settings['URL_NOTIFY'],
                  'identification'=> $identification,
                  'request_datetime'=> date($this->settings['DATE_FORMAT']),
                  
      ];
      $data =json_encode($data);
      $edata=$this->crypt->encrypt($data);
      $hashsum = $this->crypt->get_hash_sum($edata);    
      $params = [
                  "data_hash"=> $hashsum,
                  "algorithm"=> $this->settings['ALGORITHM'],
                  "public_certificate"=> $this->crypt->get_public_certificate_pem(),
                  'institution'=> $this->settings['INSTITUTION_CODE'],
                  "data"=> $edata,
                  'encrypt_method'=>$this->settings['CIPHER']
      ]; 
      $url=$this->settings['DFVA_SERVER_URL'] . $this->settings['SUSCRIPTOR_CONNECTED'];
      $datar = $this->send_post($url, $params);      
      return isset($datar['is_connected']) ? $datar['is_connected'] : False;
  }
}

class DfvaClient extends DfvaClientInternal{
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
              $format='xml_cofirma'){

        if (!in_array($format, $this->settings['SUPPORTED_SIGN_FORMAT']))
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
                            ",",$this->settings['SUPPORTED_SIGN_FORMAT'])
              ];

        try{
          $dev=parent::sign($identification, $document, $resume, 
              $format=$format);
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
    public function validate($document, $type, $format=Null){
        if ( isset($format) && !in_array($format, $this->settings['SUPPORTED_VALIDATE_FORMAT']))
            return ["code"=> "N/D",
			              "status"=> 14,
			              "identification"=>null,
			              "received_notification"=>null,
                    "status_text"=> "Formato inválido posibles: ".implode(
                            ",", $this->settings['SUPPORTED_VALIDATE_FORMAT'])
                    ];


      try{
         $dev=parent::validate($document, $type, $format=$format);
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


?>
