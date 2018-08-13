<?php

namespace SpidPHP\Spid\Saml\Out;

use SpidPHP\Spid\Interfaces\AuthnRequestInterface;

class AuthnRequest implements AuthnRequestInterface
{
    public function generateXml()
    {
        $authnRequestXml = <<<XML
<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="_4d38c302617b5bf98951e65b4cf304711e2166df20"
    Version="2.0"
    IssueInstant="2015-01-29T10:00:31Z"
    Destination="https://spid.identityprovider.it"
    AssertionConsumerServiceURL="http://spid.serviceprovider.it"
    ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
    AttributeConsumingServiceIndex="1">
    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        [...]
    </ds:Signature>
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

        /*
        ID              => $self->ID,
        IssueInstant    => $self->IssueInstant->strftime('%FT%TZ'),
        Version         => '2.0',
        Destination     => $self->_idp->sso_urls->{$args{binding}},
        ForceAuthn      => ($self->level > 1) ? 'true' : 'false',
        */
        $xml = new \SimpleXMLElement($authnRequestXml);

        header('Content-type: text/xml');
        echo $xml->asXML();
    }

    public function redirectUrl()
    {
        // TODO: Implement redirectUrl() method.
    }
}