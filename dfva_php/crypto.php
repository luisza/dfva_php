<?php

require_once 'settings.php';

class dfva_crypto {    
  function __construct() {
  }
  public function get_hash_sum($data){
    return hash(Settings::getAlgorithm(), $data);
  }
  public function get_public_certificate_pem(){
     return file_get_contents(Settings::getPublicCertificate());
  }

  private function get_private_key(){
    return openssl_get_privatekey(file_get_contents(Settings::getPrivateKey()));
  }

  private function get_public_key(){
    return openssl_pkey_get_public(file_get_contents(Settings::getServerPublicKey()));
  }

  private function get_private_key_size($key){
    $keyinfo= openssl_pkey_get_details ( $key );
    #var_dump($keyinfo);
      return $keyinfo["bits"];
  }
  
   private function decypted_checksum_check($data, $data_hash){
        $newhash = $this->get_hash_sum($data);
        if(strcmp($newhash, $data_hash) != 0 ){
           return json_encode( ["code"=> "N/D",
                "status"=> -2,
                "identification"=>null,
                "received_notification"=>Null,
                "status_text"=> "Problema: suma hash difiere"]);
        }
        
        return $data;
    }

  public function decrypt($cipher_data, $as_str=true){
    $cipher_text = $cipher_data['data'];
    $cipher=Settings::getCipher();
    $stream = fopen('php://memory','r+');
    fwrite($stream, base64_decode($cipher_text));
    rewind($stream);
    $key=$this->get_private_key();
    $keysize=$this->get_private_key_size($key)/8;
    $enc_session_key = fread($stream, $keysize);
    $iv=fread($stream, openssl_cipher_iv_length($cipher));  // IV
    //$tag=fread($stream, 16);
    $ciphertext=stream_get_contents($stream);

    fclose($stream);
    $session_key = null;
    $decrypted = null;

    openssl_private_decrypt(
                  $enc_session_key, $session_key, 
                  $key, 
                  OPENSSL_PKCS1_OAEP_PADDING
              );

    $decrypted = openssl_decrypt($ciphertext, $cipher, $session_key, 
                                  $options=OPENSSL_RAW_DATA, $iv);

    $decrypted = $this->decypted_checksum_check($decrypted, $cipher_data['data_hash']);
    if($as_str){
        $decrypted = json_decode($decrypted, true);
    }
    return $decrypted;
  }


  public function encrypt($message){
      $public_key=$this->get_public_key();
      $stream = fopen('php://memory','rw');
      $cipher= Settings::getCipher();
      
      $ivlen = openssl_cipher_iv_length($cipher);
      $iv = openssl_random_pseudo_bytes($ivlen);
      $encrypted_session_key='';
      $session_key=openssl_random_pseudo_bytes(Settings::getSessionKeySize());
      # Encrypt the session key with the public RSA key
      openssl_public_encrypt($session_key, $encrypted_session_key, 
                              $public_key, OPENSSL_PKCS1_OAEP_PADDING);
      fwrite($stream, $encrypted_session_key);
      fwrite($stream, $iv);
      # Encrypt the data with the AES session key
      $ciphertext_raw = openssl_encrypt($message, $cipher, 
                    $session_key, $options=OPENSSL_RAW_DATA, $iv);
     
      fwrite($stream, $ciphertext_raw);
      rewind($stream);
      $dev= base64_encode(stream_get_contents ($stream));
      fclose($stream);
      return $dev;
  }
}

