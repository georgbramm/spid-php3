<?php 

namespace SpidPHP\Spid;

use SpidPHP\Spid\Interfaces\SpInterface;
use SpidPHP\Spid\Saml\Idp;
use SpidPHP\Spid\Saml\Settings;

class Saml implements SpInterface
{
    private $settings;
    private $idps = [];

    public function __construct(array $settings)
    {
        Settings::validateSettings($settings);
        $this->settings = $settings;
    }

    public function loadIdpFromFile($filename)
    {
        if (array_key_exists($filename, $this->idps)) return;
        $idp = new Idp($this->settings);
        $idp = $idp->loadFromXml($filename);
        $this->idps[$filename] = $idp;
    }

    public function getSPMetadata() : string
    {
        $entityID = $this->settings['sp_entityid'];
        $id = preg_replace('/[^a-z0-9_-]/', '_', $entityID);
        $cert = Settings::cleanOpenSsl($this->settings['sp_cert_file']);

        $sloLocation = $this->settings['sp_singlelogoutservice'];
        $acsLocation = $this->settings['sp_assertionconsumerservice'];

       $xml = <<<EOD
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata"
    entityID="$entityID"
    ID="$id">
    <md:SPSSODescriptor
        protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"
        AuthnRequestsSigned="true"
        WantAssertionsSigned="true">
        <md:KeyDescriptor use="signing">
            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                <ds:X509Data>
                    <ds:X509Certificate>$cert</ds:X509Certificate>
                </ds:X509Data>
            </ds:KeyInfo>
        </md:KeyDescriptor>
        <SingleLogoutService
            Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
            Location="$sloLocation"/>
        <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:transient</NameIDFormat>
        <md:AssertionConsumerService
            index="0" isDefault="true"
            Location="$acsLocation"
            Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"/>
        <md:AssertionConsumerService
            index="1"
            Location="$acsLocation"
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
       $xml2 = '';
       if(array_key_exists('sp_org_name', $this->settings)) {
           $orgName = $this->settings['sp_org_name'];
           $orgDisplayName = $this->settings['sp_org_display_name'];
           $xml2 = <<<EOD
    <md:Organization>
        <OrganizationName xml:lang="it">$orgName</OrganizationName>
        <OrganizationDisplayName xml:lang="it">$orgDisplayName</OrganizationDisplayName>
        <OrganizationURL xml:lang="it">$entityID</OrganizationURL>
    </md:Organization>
EOD;
       }
    $xml3 = <<<EOD
</md:EntityDescriptor>
EOD;

        $xml = new \SimpleXMLElement($xml . $xml2 . $xml3);

        return $xml->asXML();
    }

    public function getIdp($idpName)
    {
        return key_exists($idpName, $this->idps) ? $this->idps[$idpName] : false;
    }

    public function login($idpName, $ass, $attr, $redirectTo = null, $level = 1){
        $this->loadIdpFromFile($idpName);
        $idp = $this->idps[$idpName];
        $idp->authnRequest($ass, $attr, $redirectTo, $level);
    }

    public function isAuthenticated(){}

    public function logout(){}

    public function getAttributes(){}

    public function loadIdpMetadata($path)
    {
        // TODO: Implement loadIdpMetadata() method.
    }
}
