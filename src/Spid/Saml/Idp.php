<?php

namespace SpidPHP\Spid\Saml;

use SpidPHP\Spid\Interfaces\IdpInterface;
use SpidPHP\Spid\Saml\Out\Authn;

class Idp implements IdpInterface
{
    var $metadata;
    var $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function loadFromXml($xmlFile)
    {
        if (!file_exists($this->settings['idp_metadata_folder'] . $xmlFile . ".xml")) {
            throw new \Exception("Invalid IDP Requested", 1);
        }

        $xml = simplexml_load_file($this->settings['idp_metadata_folder'] . $xmlFile . '.xml');

        $metadata = array();
        $metadata['idpEntityId'] = $xml->attributes()->entityID->__toString();
        $metadata['idpSSO'] = $xml->xpath('//SingleSignOnService')[0]->attributes()->Location->__toString();
        $metadata['idpSLO'] = $xml->xpath('//SingleLogoutService')[0]->attributes()->Location->__toString();
        $metadata['idpCertValue'] = $xml->xpath('//X509Certificate')[0]->__toString();

        $this->metadata = $metadata;
        return $this;
    }

    public function authnRequest($ass = 0, $attr = 0, $redirectTo = null, $level = 1)
    {
        $authn = new Authn($this);
        echo $authn->redirectUrl($redirectTo);
    }
}