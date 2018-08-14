<?php

namespace SpidPHP\Spid\Saml\Out;

class BaseRequest
{
    var $xml;
    var $id;
    var $issueInstant;

    public function generateID()
    {
        $this->id = '_' . random_bytes(16);
        return $this->id;
    }

    public function generateIssueInstant()
    {
        $this->issueInstant = date();
        return $this->issueInstant;
    }

    public function redirectUrl($url)
    {
        $parameters['SAMLRequest'] = base64_encode($this->xml);
        $parameters['RelayState'] = '';
        $parameters['SigAlg'] = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
        $parameters['Signature'] = '';
    }

    private static function cleanOpenSsl($k)
    {
        $ck = '';
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $k) as $l) {
            if (strpos($l, '-----') === false) {
                $ck .= $l;
            }
        }
        return $ck;
    }

    public function buildRequestSignature($ref)
    {
        $sp_cert_raw = file_get_contents("cert file");
        $cert = self::cleanOpenSsl($sp_cert_raw);

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