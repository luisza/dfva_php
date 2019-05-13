<?php
class Settings {
    private static $timezone = 'America/Costa_Rica';
    private static $date_format = "Y-m-d h:m:s";
    private static $algorithm = 'sha512';
    private static $dfva_server_url = 'http://localhost:8000';
    private static $authenticate_institution = '/authenticate/institution/';
    private static $check_authenticate_institution = '/authenticate/%s/institution_show/';
    private static $authenticate_delete = '/authenticate/%s/institution_delete/';
    private static $sign_institution = '/sign/institution/';
    private static $check_sign_institution = '/sign/%s/institution_show/';
    private static $sign_delete = '/sign/%s/institution_delete/';
    private static $validate_certificate = '/validate/institution_certificate/';
    private static $validate_document = '/validate/institution_document/';
    private static $suscriptor_connected = '/validate/institution_suscriptor_connected/';
    private static $supported_sign_format = ['xml_cofirma','xml_contrafirma','odf','msoffice', 'pdf'];
    private static $supported_validate_format = ['certificate','cofirma','contrafirma','odf','msoffice', 'pdf'];
    private static $public_certificate = 'cert.crt';
    private static $server_public_key = 'cert_pub.key';
    private static $institution_code = 'd75b1531-6297-4668-90c9-f867d15c3814';
    private static $private_key = 'cert.key';
    private static $url_notify = 'N/D';
    private static $cipher = "aes-256-cfb";
    private static $session_key_size = 32;

    public static function getAlgorithm()
    {
        return self::$algorithm;
    }

    public static function getAuthenticateDelete()
    {
        return self::$authenticate_delete;
    }

    public static function getAuthenticateInstitution()
    {
        return self::$authenticate_institution;
    }

    public static function getCheckAuthenticateInstitution()
    {
        return self::$check_authenticate_institution;
    }

    public static function getCheckSignInstitution()
    {
        return self::$check_sign_institution;
    }

    public static function getCipher()
    {
        return self::$cipher;
    }

    public static function getDateFormat()
    {
        return self::$date_format;
    }

    public static function getDfvaServerUrl()
    {
        return self::$dfva_server_url;
    }

    public static function getInstitutionCode()
    {
        return self::$institution_code;
    }

    public static function getPrivateKey()
    {
        return dirname(__FILE__)."/".self::$private_key;
    }

    public static function getPublicCertificate()
    {
        return dirname(__FILE__)."/".self::$public_certificate;
    }

    public static function getServerPublicKey()
    {
        return dirname(__FILE__)."/".self::$server_public_key;
    }

    public static function getSessionKeySize()
    {
        return self::$session_key_size;
    }

    public static function getSignDelete()
    {
        return self::$sign_delete;
    }

    public static function getSignInstitution()
    {
        return self::$sign_institution;
    }

    public static function getSupportedSignFormat()
    {
        return self::$supported_sign_format;
    }

    public static function getSupportedValidateFormat()
    {
        return self::$supported_validate_format;
    }

    public static function getSuscriptorConnected()
    {
        return self::$suscriptor_connected;
    }

    public static function getTimezone()
    {
        return self::$timezone;
    }

    public static function getUrlNotify()
    {
        return self::$url_notify;
    }

    public static function getValidateCertificate()
    {
        return self::$validate_certificate;
    }

    public static function getValidateDocument()
    {
        return self::$validate_document;
    }
}
