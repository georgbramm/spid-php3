<?php 

namespace SpidPHP\Spid;

use SpidPHP\Spid\Interfaces\SettingsInterface;
use SpidPHP\Spid\Interfaces\SpInterface;
use SpidPHP\Spid\Saml\Idp;
use SpidPHP\Spid\Saml\Out\AuthnRequest;
use SpidPHP\Spid\Saml\Settings;

class Saml implements SpInterface
{
    private $settings;
    private $idps;
    private $validSettings = [
        'sp_entityid' => 1,
        'sp_key_file' => 1,
        'sp_cert_file' => 1,
        'sp_assertionconsumerservice' => 1,
        'sp_singlelogoutservice' => 1,
        'sp_attributeconsumingservice' => 0
    ];

    public function __construct(array $settings)
    {
        Settings::validateSettings($settings);
        $this->settings = $settings;
    }

    public function loadIdpMetadata($path)
    {
        $authn = new AuthnRequest();
        return $authn->generateXml();
    }

    public function loadIdpFromFile($filename)
    {
        $idp = new Idp();
        $metadata = $idp->loadFromXml($filename);
        $this->idps[$metadata['idpEntityId']] = $metadata;
    }

    public function getSPMetadata()
    {
       $xml = <<<EOD
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata"
    entityID="https://spid.serviceprovider.it"
    ID="_0j40cj0848d8e3jncjdjss...">
    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        [...]
    </ds:Signature>
    <md:SPSSODescriptor
        protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"
        AuthnRequestsSigned="true"
        WantAssertionsSigned="true">
        <md:KeyDescriptor use="signing">
    [...]
        </md:KeyDescriptor>
        <SingleLogoutService
            Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
            Location="https://spid.serviceprovider.it/slo-location"
            ResponseLocation="https://spid.serviceprovider.it/slo-location"/>
        <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:transient</NameIDFormat>
        <md:AssertionConsumerService
            index="0" isDefault="true"
            Location="https://spid.serviceprovider.it/sso-location"
            Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"/>
        <md:AssertionConsumerService
            index="1"
            Location="https://spidSP.serviceProvider.it/sso-location"
            Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"/>
        <md:AttributeConsumingService index="0">
            <md:ServiceName xml:lang="it">Set 0</md:ServiceName>
            <md:RequestedAttribute Name="name"/>
            <md:RequestedAttribute Name="familyName"/>
            <md:RequestedAttribute Name="fiscalNumber"/>
            <md:RequestedAttribute Name="email"/>
        </md:AttributeConsumingService>
            <md:AttributeConsumingService index="1">
            <md:ServiceName xml:lang="it">Set 1</md:ServiceName>
            <md:RequestedAttribute Name="spidCode"/>
            <md:RequestedAttribute Name="fiscalNumber"/>
        </md:AttributeConsumingService>
    </md:SPSSODescriptor>
EOD;
       $xml2 = <<<EOD
    <md:Organization>
        <OrganizationName xml:lang="it">Service provider</OrganizationName>
        <OrganizationDisplayName xml:lang="it">Nome service provider</OrganizationDisplayName>
        <OrganizationURL xml:lang="it">http://spid.serviceprovider.it</OrganizationURL>
    </md:Organization>
</md:EntityDescriptor>
EOD;

        $xml = new \SimpleXMLElement($xml . $xml2);
/*        $xml->addAttribute('xmlns:md', 'urn:oasis:names:tc:SAML:2.0:metadata');
        $xml->addAttribute('EntityID', 'entitid');*/
        /*$signature = $xml->addChild('ds:Signature', 'valore');
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
        $organization->addChild('OrganizationURL', 'valore');*/

        header('Content-type: text/xml');
        echo $xml->asXML();
    }

    public function getIdp($idpName)
    {
        return key_exists($idpName, $this->idps) ? $this->idps[$idpName] : false;
    }

    public function login($idpName, $ass, $attr, $redirectTo = '', $level = 1){}

    public function isAuthenticated(){}

    public function logout(){}

    public function getAttributes(){}
}
