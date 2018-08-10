<?php 

namespace SpidPHP\Spid;

use SpidPHP\Spid\Interfaces\SpInterface;
use SpidPHP\Spid\Saml\Idp;

class Saml implements SpInterface
{
    private $settings;
    private $idps;

    public function __construct($settings = null)
    {
        $this->settings = $settings;
    }

    public function loadIdpMetadata($path)
    {

    }

    public function loadIdpFromFile($filename)
    {
        $idp = new Idp();
        $metadata = $idp->loadFromXml($filename);
        $this->idps[$metadata['idpEntityId']] = $metadata;
    }

    public function getSPMetadata()
    {
        $xml = new SimpleXMLElement("<md:EntityDescriptor></md:EntityDescriptor>");
        $xml->addAttribute('xmlns:md', 'urn:oasis:names:tc:SAML:2.0:metadata');
        $xml->addAttribute('EntityID', $this->idp['idpEntityId']);
        $signature = $xml->addChild('ds:Signature', 'valore');
        $signature->addAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $spSSODescriptor = $xml->addChild('md:SPSSODescriptor');
        $spSSODescriptor->addAttribute();
        $keyDescriptor = $spSSODescriptor->addChild('md:KeyDescriptor', 'valore');
        $keyDescriptor->addAttribute('signing', 'true');
        $singleLogoutService = $spSSODescriptor->addChild('SingleLogoutService');
        $singleLogoutService->addAttribute('Binding', 'valore');
        $singleLogoutService->addAttribute('Location', 'valore');
        $singleLogoutService->addAttribute('ResponseLocation', 'valore');
        $nameIDFormat = $spSSODescriptor->addChild('NameIDFormat', 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient');
        foreach ($this->settings->ascs as $index => $acs) {
            $currentAcs = $spSSODescriptor->addChild('md:AssertionConsumerService');
            $currentAcs->addAttribute('index', $index);
            if ($index == 0 )$currentAcs->addAttribute('isDefault', 'true');
            $currentAcs->addAttribute('Binding', 'valore');
            $currentAcs->addAttribute('Location', 'valore');
        }
        foreach ($this->settings->atcs as $index => $acs) {
            $currentAcs = $spSSODescriptor->addChild('md:AttributeConsumingService');
            $currentAcs->addAttribute('index', $index);
            $currentAcs->addChild('', 'valore');
            $currentAcs->addChild('', 'valore');
        }
        $organization = $xml->addChild('md:Organization');
        $organization->addChild('OrganizationName', 'valore');
        $organization->addChild('OrganizationDisplayName', 'valore');
        $organization->addChild('OrganizationURL', 'valore');

        header('Content-type: text/xml');
        echo $xml->asXML();
    }

    public function getIdp($idpName){}

    public function login($idpName, $ass, $attr, $redirectTo = '', $level = 1){}

    public function isAuthenticated(){}

    public function logout(){}

    public function getAttributes(){}
}
