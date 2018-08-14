<?php

namespace SpidPHP\Spid\Saml\Out;

use SpidPHP\Spid\Interfaces\AuthnRequestInterface;

class AuthnRequest extends BaseRequest implements AuthnRequestInterface
{
    public function generateXml()
    {
        $id = $this->generateID();
        $signature = $this->buildRequestSignature($id);
        $issueInstant = $this->generateIssueInstant();
        // example ID _4d38c302617b5bf98951e65b4cf304711e2166df20
        $authnRequestXml = <<<XML
<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="$id" 
    Version="2.0"
    IssueInstant="$issueInstant"
    Destination="https://spid.identityprovider.it"
    AssertionConsumerServiceURL="http://spid.serviceprovider.it"
    ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
    AttributeConsumingServiceIndex="1">
    $signature
    <saml:Issuer
        NameQualifier="http://spid.serviceprovider.it"
        Format="urn:oasis:names:tc:SAML:2.0:nameid-format:entity">
        spid-sp
    </saml:Issuer>
    <samlp:NameIDPolicy Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient" />
    <samlp:RequestedAuthnContext Comparison="exact">
        <saml:AuthnContextClassRef>
            https://www.spid.gov.it/SpidL2
        </saml:AuthnContextClassRef>
    </samlp:RequestedAuthnContext>
</samlp:AuthnRequest>
XML;


        $xml = new \SimpleXMLElement($authnRequestXml);
        $this->xml = $xml->asXML();

        header('Content-type: text/xml');
        echo $this->xml;
    }
}