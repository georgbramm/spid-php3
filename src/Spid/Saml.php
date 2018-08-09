<?php 

namespace SpidPHP\Spid;

use SpidPHP\Spid\Interfaces\SpInterface;

class Saml implements SpInterface
{
    public function __construct($settings = null) {}

    public function loadIdpMetadata($path){}

    public function loadIdpFromFile($filename){
        if (!file_exists(Constants::APP_PATH . $folder . $filename . ".xml")) {
            throw new \Exception("Invalid IDP Requested", 1);
        }
        
        $xml = simplexml_load_file(Constants::APP_PATH . $filename . '.xml');
        
        $metadata = array();
        $metadata['idpEntityId'] = $xml->attributes()->entityID->__toString();
        $metadata['idpSSO'] = $xml->xpath('//SingleSignOnService')[0]->attributes()->Location->__toString();
        $metadata['idpSLO'] = $xml->xpath('//SingleLogoutService')[0]->attributes()->Location->__toString();
        $metadata['idpCertValue'] = $xml->xpath('//X509Certificate')[0]->__toString();
         
        return $metadata;
    }

    public function getSPMetadata(){}

    public function getIdp($idpName){}

    public function login($idpName, $ass, $attr, $redirectTo = '', $level = 1){}

    public function isAuthenticated(){}

    public function logout(){}

    public function getAttributes(){}
}
