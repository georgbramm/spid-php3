<?php

namespace SpidPHP\Spid\Saml;

use SpidPHP\Spid\Interfaces\IdpInterface;

class Idp implements IdpInterface
{
    private $metadata;

    public function loadFromXml($xmlFile)
    {
        if (!file_exists($xmlFile . ".xml")) {
            throw new \Exception("Invalid IDP Requested", 1);
        }

        $xml = simplexml_load_file($xmlFile . '.xml');

        $metadata = array();
        $metadata['idpEntityId'] = $xml->attributes()->entityID->__toString();
        $metadata['idpSSO'] = $xml->xpath('//SingleSignOnService')[0]->attributes()->Location->__toString();
        $metadata['idpSLO'] = $xml->xpath('//SingleLogoutService')[0]->attributes()->Location->__toString();
        $metadata['idpCertValue'] = $xml->xpath('//X509Certificate')[0]->__toString();

        $this->idp = $metadata;
        return $metadata;
    }

    public function authnRequest($ass = 0, $attr = 0, $level = 1, $returnTo = null)
    {
        // TODO: Implement authnRequest() method.
    }
}