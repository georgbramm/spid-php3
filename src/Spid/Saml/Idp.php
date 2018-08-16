<?php

namespace SpidPHP\Spid\Saml;

use SpidPHP\Spid\Interfaces\IdpInterface;
use SpidPHP\Spid\Saml\Out\AuthnRequest;

class Idp implements IdpInterface
{
    var $metadata;
    var $settings;
    var $assertID;
    var $attrID;
    var $level = 1;

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

    public function authnRequest($ass, $attr, $redirectTo = null, $level = 1)
    {
        $this->assertID = $ass;
        $this->attrID = $attr;
        $this->level = $level;

        $authn = new AuthnRequest($this);
        $url = $authn->redirectUrl($redirectTo);
        $_SESSION['RequestID'] = $authn->id;

        header('Pragma: no-cache');
        header('Cache-Control: no-cache, must-revalidate');
        header('Location: ' . $url);
        exit();
    }
}