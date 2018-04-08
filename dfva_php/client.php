<?php 

require dirname(__FILE__).'/crypto.php';
class dfva_client {
   
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


  public function check_autenticate($code){
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
          $_format='xml_cofirma'){
          date_default_timezone_set($this->settings['TIMEZONE']);

          $data = [
            'institution'=> $this->settings['INSTITUTION_CODE'],
            'notification_url'=> $this->settings['URL_NOTIFY'],
            'document'=> $document,
            'format'=> $_format,
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

  public function check_sign($code){
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

  public function validate($document, $_type, $format=Null){
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

      if ($_type == 'certificate'){
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

?>
