<?php

namespace Italia\Spid3\Spid\Saml;

use Italia\Spid3\Spid\Interfaces\IdpInterface;
use Italia\Spid3\Spid\Saml\Out\AuthnRequest;
use Italia\Spid3\Spid\Saml\Out\LogoutRequest;

class Idp implements IdpInterface
{
    public $idpFileName;
    public $metadata;
    public $settings;
    public $assertID;
    public $attrID;
    public $level = 1;
    public $session;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function loadFromXml($xmlFile)
    {
        $fileName = $this->settings['idp_metadata_folder'] . $xmlFile . ".xml";
        if (!file_exists($fileName)) {
            throw new \Exception("Metadata file $fileName not found", 1);
        }

        $xml = simplexml_load_file($fileName);

        $xml->registerXPathNamespace('md', 'urn:oasis:names:tc:SAML:2.0:metadata');
        $xml->registerXPathNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

        $metadata = array();
        $metadata['idpEntityId'] = $xml->attributes()->entityID->__toString();
        $metadata['idpSSO'] = $xml->xpath('//md:SingleSignOnService')[0]->attributes()->Location->__toString();
        $metadata['idpSLO'] = $xml->xpath('//md:SingleLogoutService')[0]->attributes()->Location->__toString();
        $metadata['idpCertValue'] = $xml->xpath('//ds:X509Certificate')[0]->__toString();

        $this->idpFileName = $xmlFile;
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
        $_SESSION['idpName'] = $this->idpFileName;

        header('Pragma: no-cache');
        header('Cache-Control: no-cache, must-revalidate');
        header('Location: ' . $url);
        exit();
    }

    public function logoutRequest(Session $session, $redirectTo = null)
    {
        $this->session = $session;
        $request = new LogoutRequest($this);
        $url = $request->redirectUrl($redirectTo);

        header('Pragma: no-cache');
        header('Cache-Control: no-cache, must-revalidate');
        header('Location: ' . $url);
        exit();
    }
}
