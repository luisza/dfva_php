<?php namespace dfva_php;

class Settings {
    private $timezone = 'America/Costa_Rica';
    private $date_format = "Y-m-d h:m:s";
    private $algorithm = 'sha512';
    private $dfva_server_url = 'http://localhost:8000';
    private $authenticate_institution = '/authenticate/institution/';
    private $check_authenticate_institution = '/authenticate/%s/institution_show/';
    private $authenticate_delete = '/authenticate/%s/institution_delete/';
    private $sign_institution = '/sign/institution/';
    private $check_sign_institution = '/sign/%s/institution_show/';
    private $sign_delete = '/sign/%s/institution_delete/';
    private $validate_certificate = '/validate/institution_certificate/';
    private $validate_document = '/validate/institution_document/';
    private $suscriptor_connected = '/validate/institution_suscriptor_connected/';
    private $supported_sign_format = ['xml_cofirma','xml_contrafirma','odf','msoffice', 'pdf'];
    private $supported_validate_format = ['certificate','cofirma','contrafirma','odf','msoffice', 'pdf'];
    private $public_certificate = ''; // cert.crt
    private $server_public_key = ''; // cert_pub.key
    private $institution_code = '';
    private $private_key = ''; // cert.key
    private $private_key_passphrase = '';
    private $url_notify = 'N/D';
    private $cipher = "aes-256-cfb";
    private $session_key_size = 32;
    private $homePath = null;

    public  function getAlgorithm()
    {
        return $this->algorithm;
    }

    private function get_home_folder(){
        if($this->homePath != null){
            return $this->homePath;
        }
        
        if(isset($_SERVER['HOME'])) {
            $result = $_SERVER['HOME'];
        } else {
            $result = getenv("HOME");
        }
    
        if(empty($result) && function_exists('exec')) {
            if(strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                $result = exec("echo %userprofile%");
            } else {
                $result = exec("echo ~");
            }
        }

        $this->homePath = $result. DIRECTORY_SEPARATOR .".dfva_php";
        return $this->homePath;
    }


    public function SetHomePath($homePath){
        $this->homePath = $homePath;
    }

    public function save(){
        $data = json_encode(['timezone' => $this->timezone,
        'date_format' => $this->date_format,
        'algorithm' => $this->algorithm,
        'dfva_server_url' => $this->dfva_server_url,
        'authenticate_institution' => $this->authenticate_institution,
        'check_authenticate_institution' => $this->check_authenticate_institution,
        'authenticate_delete' => $this->authenticate_delete,
        'sign_institution' => $this->sign_institution,
        'check_sign_institution' => $this->check_sign_institution,
        'sign_delete' => $this->sign_delete,
        'validate_certificate' => $this->validate_certificate,
        'validate_document' => $this->validate_document,
        'suscriptor_connected' => $this->suscriptor_connected,
        'supported_sign_format' => $this->supported_sign_format,
        'supported_validate_format' => $this->supported_validate_format,
        'public_certificate' => $this->getPublicCertificate(),
        'server_public_key' => $this->getServerPublicKey(),
        'institution_code' => $this->institution_code,
        'private_key' => $this->getPrivateKey(),
        'private_key_passphrase' => $this->private_key_passphrase,
        'url_notify' => $this->url_notify,
        'cipher' => $this->cipher,
        'session_key_size' => $this->session_key_size], JSON_PRETTY_PRINT);


        if (!file_exists($this->get_home_folder())) {
            mkdir($this->get_home_folder(), 0750, true);
        }

        if(file_put_contents($this->get_config_filename(), $data)) {
	        return true;
        }
        return false;
    }

    public function copy($other_settings){
        $this->timezone = $other_settings['timezone']; 
        $this->date_format = $other_settings['date_format']; 
        $this->algorithm = $other_settings['algorithm']; 
        $this->dfva_server_url = $other_settings['dfva_server_url']; 
        $this->authenticate_institution = $other_settings['authenticate_institution']; 
        $this->check_authenticate_institution = $other_settings['check_authenticate_institution']; 
        $this->authenticate_delete = $other_settings['authenticate_delete']; 
        $this->sign_institution = $other_settings['sign_institution']; 
        $this->check_sign_institution = $other_settings['check_sign_institution']; 
        $this->sign_delete = $other_settings['sign_delete']; 
        $this->validate_certificate = $other_settings['validate_certificate']; 
        $this->validate_document = $other_settings['validate_document']; 
        $this->suscriptor_connected = $other_settings['suscriptor_connected']; 
        $this->supported_sign_format = $other_settings['supported_sign_format']; 
        $this->supported_validate_format = $other_settings['supported_validate_format']; 
        $this->public_certificate = $other_settings['public_certificate']; 
        $this->server_public_key = $other_settings['server_public_key']; 
        $this->institution_code = $other_settings['institution_code']; 
        $this->private_key = $other_settings['private_key']; 
        $this->private_key_passphrase = $other_settings['private_key_passphrase']; 
        $this->url_notify = $other_settings['url_notify']; 
        $this->cipher = $other_settings['cipher']; 
        $this->session_key_size = $other_settings['session_key_size'];
    }


    public function load(){
        $config_file=$this->get_config_filename();

        if (!file_exists($config_file)){
            $this->save();
        }

        $jsondata = file_get_contents($config_file);
  	    // converts json data into array
        $arr_data = json_decode($jsondata, true);
        $this->copy($arr_data);
    }

    private function get_config_filename(){
        return $this->get_home_folder(). DIRECTORY_SEPARATOR ."dfva_settings.json";
    }

    public  function getAuthenticateDelete()
    {
        return $this->authenticate_delete;
    }

    public  function getAuthenticateInstitution()
    {
        return $this->authenticate_institution;
    }

    public  function getCheckAuthenticateInstitution()
    {
        return $this->check_authenticate_institution;
    }

    public  function getCheckSignInstitution()
    {
        return $this->check_sign_institution;
    }

    public  function getCipher()
    {
        return $this->cipher;
    }

    public  function getDateFormat()
    {
        return $this->date_format;
    }

    public  function getDfvaServerUrl()
    {
        return $this->dfva_server_url;
    }

    public  function getInstitutionCode()
    {
        return $this->institution_code;
    }

    public  function getPrivateKey()
    {
        if($this->private_key == ''){
            $this->private_key = $this->get_home_folder().DIRECTORY_SEPARATOR.'private_key.pem';
        }
        return  $this->private_key;
    }

    public  function getPublicCertificate()
    {
        if($this->public_certificate == ''){
            $this->public_certificate = $this->get_home_folder().DIRECTORY_SEPARATOR.'certificate.pem';
        }

        return  $this->public_certificate;
    }

    public  function getServerPublicKey()
    {
        if($this->server_public_key == ''){
            $this->server_public_key = $this->get_home_folder().DIRECTORY_SEPARATOR.'public_key.pem';
        }
        return  $this->server_public_key;
    }

    public  function getSessionKeySize()
    {
        return $this->session_key_size;
    }

    public  function getSignDelete()
    {
        return $this->sign_delete;
    }

    public  function getSignInstitution()
    {
        return $this->sign_institution;
    }

    public  function getSupportedSignFormat()
    {
        return $this->supported_sign_format;
    }

    public  function getSupportedValidateFormat()
    {
        return $this->supported_validate_format;
    }

    public  function getSuscriptorConnected()
    {
        return $this->suscriptor_connected;
    }

    public  function getTimezone()
    {
        return $this->timezone;
    }

    public  function getUrlNotify()
    {
        return $this->url_notify;
    }

    public  function getValidateCertificate()
    {
        return $this->validate_certificate;
    }

    public  function getValidateDocument()
    {
        return $this->validate_document;
    }

    public function __get($name)
    {
         
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }


}

const FILE_PATH = '/var/log/dfva_php.log';
