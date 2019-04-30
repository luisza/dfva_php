<?php
// Singleton class
class Settings {
    private static $instance = null;

    private function __construct()
    {
        $this->timezone = 'America/Costa_Rica';
        $this->date_format = "Y-m-d h:m:s";
        $this->algorithm = 'sha512';
        $this->dfva_server_url = 'http://159.89.119.117:8000';
        $this->authentication_institution = '/authenticate/institution/';
        $this->check_authentication_institution = '/authenticate/%s/institution_show/';
        $this->authentication_delete = '/authenticate/%s/institution_delete/';
        $this->sign_institution = '/sign/institution/';
        $this->check_sign_institution = '/sign/%s/institution_show/';
        $this->sign_delete = '/sign/%s/institution_delete/';
        $this->validate_certificate = '/validate/institution_certificate/';
        $this->validate_document = '/validate/institution_document/';
        $this->suscriptor_connected = '/validate/institution_suscriptor_connected/';
        $this->supported_sign_format = ['xml_cofirma','xml_contrafirma','odf','msoffice', 'pdf'];
        $this->supported_validate_format = ['certificate','cofirma','contrafirma','odf','msoffice', 'pdf'];
        $this->public_certificate = './cert.crt';
        $this->server_public_key = './cert_pub.key';
        $this->institution_code = 'c30e75ea-66db-4262-bb1d-b775d1c47179';
        $this->private_key = './cert.key';
        $this->url_notify = 'N/D';
        $this->cipher = "aes-256-cfb";
        $this->session_key_size = 32;
    }


    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Settings();
        }

        return self::$instance;
    }


    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        return null;
    }


    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }
}
