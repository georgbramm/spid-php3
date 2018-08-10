<?php

namespace SpidPHP\Spid\Saml\Out;

use SpidPHP\Spid\Interfaces\AuthnRequestInterface;

class AuthnRequest implements AuthnRequestInterface
{
    public function generateXml()
    {

        /*
        ID              => $self->ID,
        IssueInstant    => $self->IssueInstant->strftime('%FT%TZ'),
        Version         => '2.0',
        Destination     => $self->_idp->sso_urls->{$args{binding}},
        ForceAuthn      => ($self->level > 1) ? 'true' : 'false',
        */
        $xml = new SimpleXMLElement("<samlp:AuthnRequest></samlp:AuthnRequest>");
        $xml->addAttribute('xmlns:samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $xml->addAttribute('ID', $this->idp['idpEntityId']);
        $xml->addAttribute('IssueInstant', $this->idp['idpEntityId']);
        $xml->addAttribute('Version', $this->idp['idpEntityId']);
        $xml->addAttribute('Destination', $this->idp['idpEntityId']);
        $xml->addAttribute('ForceAuthn', $this->idp['idpEntityId']);
        $signature = $xml->addChild('ds:Signature', 'valore');
        $signature->addAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $issuer = $xml->addChild('saml:Issuer');
        $issuer->addAttribute('NameQualifier', '');
        $issuer->addAttribute('Format', '');
        $nameIDPolicy = $xml->addChild('samlp:NameIDPolicy');
        $nameIDPolicy->addAttribute('Format', 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient');
        $authnContext = $xml->addChild('samlp:RequestedAuthnContext');
        $authnContext->addAttribute('Comparison', 'exact');
        $authnContextClassRef = $authnContext->addChild('saml:AuthnContextClassRef', 'valore');

        header('Content-type: text/xml');
        echo $xml->asXML();
    }

    public function redirectUrl()
    {
        // TODO: Implement redirectUrl() method.
    }
}