<?php



function pem_to_base64($certificate){
    $text=str_replace("-----BEGIN CERTIFICATE-----\n", "", $certificate);
    $text=str_replace("\n-----END CERTIFICATE-----", "", $text);
    $text=str_replace("\n", "", $text);
    return $text;
}

function get_hash_sum($data, $algorithm){
  return hash($algorithm, $data);
}

function get_private_key(){
  $SETTINGS = include('settings.php');
  return $SETTINGS['PRIVATE_KEY'];
}

function get_public_key(){
  $SETTINGS = include('settings.php');
  return $SETTINGS['SERVER_PUBLIC_KEY'];
}

function get_private_key_size($key){
  $keyinfo= openssl_pkey_get_details ( $key );
  #var_dump($keyinfo);
  return $keyinfo["bits"];
}

function decrypt($cipher_text, $as_str=true){
  $cipher="aes-256-cfb";
  $stream = fopen('php://memory','r+');
  fwrite($stream, base64_decode($cipher_text));
  rewind($stream);
  $key=get_private_key();
  $keysize=get_private_key_size($key)/8;
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
  
  if($as_str){
   $decrypted = json_decode($decrypted);
  }
  return $decrypted;
}


function encrypt($message){
    $public_key=get_public_key();
    $stream = fopen('php://memory','rw');
    $cipher="aes-256-cfb";
    
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted_session_key='';
    $session_key=openssl_random_pseudo_bytes(32);
    
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




?>
