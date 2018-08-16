<?php 

namespace SpidPHP\Spid;

use SpidPHP\Spid\Interfaces\SpInterface;
use SpidPHP\Spid\Saml\Idp;
use SpidPHP\Spid\Saml\In\Base;
use SpidPHP\Spid\Saml\In\Response;
use SpidPHP\Spid\Saml\Settings;

class Saml implements SpInterface
{
    private $settings;
    private $idps = [];
    private $session;

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
        $assertcsArray = $this->settings['sp_assertionconsumerservice'] ?? array();
        $attrcsArray = $this->settings['sp_attributeconsumingservice'] ?? array();

       $xml = <<<XML
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="$entityID" ID="$id">
    <md:SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" AuthnRequestsSigned="true" WantAssertionsSigned="true">
        <md:KeyDescriptor use="signing">
            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                <ds:X509Data><ds:X509Certificate>$cert</ds:X509Certificate></ds:X509Data>
            </ds:KeyInfo>
        </md:KeyDescriptor>
        <SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="$sloLocation"/>
        <NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:transient</NameIDFormat>
XML;
       for ($i = 0; $i < count($assertcsArray); $i++)
       {
            $xml .= <<<XML
            <md:AssertionConsumerService index="$i" isDefault="true" Location="$assertcsArray[$i]" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"/>
XML;
       }
        for ($i = 0; $i < count($attrcsArray); $i++) {
            $xml .= <<<XML
<md:AttributeConsumingService index="$i">
    <md:ServiceName xml:lang="it">Set $i</md:ServiceName>       
XML;
           foreach ($attrcsArray[$i] as $attr) {
               $xml .= <<<XML
<md:RequestedAttribute Name="$attr"/>
XML;
           }
    $xml .= '</md:AttributeConsumingService>';

        }
       $xml .= '</md:SPSSODescriptor>';


       if(array_key_exists('sp_org_name', $this->settings)) {
           $orgName = $this->settings['sp_org_name'];
           $orgDisplayName = $this->settings['sp_org_display_name'];
           $xml .= <<<XML
<md:Organization>
    <OrganizationName xml:lang="it">$orgName</OrganizationName>
    <OrganizationDisplayName xml:lang="it">$orgDisplayName</OrganizationDisplayName>
    <OrganizationURL xml:lang="it">$entityID</OrganizationURL>
</md:Organization>
XML;
       }
    $xml .= '</md:EntityDescriptor>';

        header('Content-type: text/xml');
    echo $xml; die;
        $xml = new \SimpleXMLElement($xml);

        return $xml->asXML();
    }

    public function getIdp($idpName)
    {
        return key_exists($idpName, $this->idps) ? $this->idps[$idpName] : false;
    }

    public function login($idpName, $assertId, $attrId, $redirectTo = null, $level = 1){
        if (isset($_SESSION) && isset($_SESSION['spidSession'])) {
            return false;
        }
        if (!array_key_exists($assertId, $this->settings['sp_assertionconsumerservice'])) {
            throw new \Exception("Invalid Assertion Consumer Service ID");
        }
        if (isset($this->settings['sp_attributeconsumingservice']) && !array_key_exists($attrId, $this->settings['sp_attributeconsumingservice'])) {
            throw new \Exception("Invalid Attribute Consuming Service ID");
        }  else {
            $attrId = null;
        }

        $this->loadIdpFromFile($idpName);
        $idp = $this->idps[$idpName];
        $idp->authnRequest($assertId, $attrId, $redirectTo, $level);
    }

    public function isAuthenticated()
    {
        if (isset($_SESSION) && isset($_SESSION['spidSession'])) {
            $this->session = $_SESSION['spidSession'];
            return true;
        }

        $response = new Response();
        $validated = $response->validate();
        if ($validated instanceof Session) {
            $_SESSION['spidSession'] = $validated;
            $this->session = $validated;
            return true;
        }
        return false;
    }

    public function logout(){}

    public function getAttributes()
    {
        return $this->session->attributes;
    }

    public function loadIdpMetadata($path)
    {
        // TODO: Implement loadIdpMetadata() method.
    }
}
