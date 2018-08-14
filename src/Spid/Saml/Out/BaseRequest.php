<?php

namespace SpidPHP\Spid\Saml\Out;

use SpidPHP\Spid\Saml\Idp;
use SpidPHP\Spid\Saml\Settings;

class BaseRequest
{
    var $idp;
    var $xml;
    var $id;
    var $issueInstant;

    public function __construct(Idp $idp)
    {
        $this->idp = $idp;
    }

    public function generateID()
    {
        $this->id = '_' . bin2hex(random_bytes(16));
        return $this->id;
    }

    public function generateIssueInstant()
    {
        $this->issueInstant = gmdate('Y-m-d\TH:i:sP');
        return $this->issueInstant;
    }

    public function redirectUrl($url)
    {
        $parameters['SAMLRequest'] = base64_encode($this->xml);
        $parameters['RelayState'] = '';
        $parameters['SigAlg'] = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
        $parameters['Signature'] = '';
    }

    public function buildRequestSignature($ref)
    {
        $cert = Settings::cleanOpenSsl($this->idp->settings['sp_cert_file']);

        $signatureXml = <<<XML
<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
<ds:SignedInfo>
  <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#" />
  <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256" />
  <ds:Reference URI="#$ref">
    <ds:Transforms>
      <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" />
      <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#" />
    </ds:Transforms>
    <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" />
    <ds:DigestValue></ds:DigestValue>
  </ds:Reference>
</ds:SignedInfo>
<ds:SignatureValue></ds:SignatureValue>
<ds:KeyInfo>
  <ds:X509Data>
    <ds:X509Certificate>$cert</ds:X509Certificate>
  </ds:X509Data>
</ds:KeyInfo>
</ds:Signature>
XML;
        return $signatureXml;
    }
}