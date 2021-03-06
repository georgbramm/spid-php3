<?php

namespace Italia\Spid3\Spid\Saml\In;

use Italia\Spid3\Spid\Session;

class Response extends Base
{
    public function validate()
    {
        if (!isset($_POST) || !isset($_POST['SAMLResponse'])) {
            return false;
        }

        $xmlString = base64_decode($_POST['SAMLResponse']);
        $xml = new \DOMDocument();
        $xml->loadXML($xmlString);

        $root = $xml->getElementsByTagName('Response')->item(0);
        if ($root->getAttribute('Version') == "") {
            throw new \Exception("missing Version attribute");
        } elseif ($root->getAttribute('Version') != '2.0') {
            throw new \Exception("Invalid Version attribute");
        }
        if ($root->getAttribute('IssueInstant') == "") {
            throw new \Exception("Missing IssueInstant attribute");
        }
        if ($root->getAttribute('InResponseTo') == "" || !isset($_SESSION['RequestID'])) {
            throw new \Exception("Missing InResponseTo attribute, or request ID was not saved correctly for comparison");
        } elseif ($root->getAttribute('InResponseTo') != $_SESSION['RequestID']) {
            throw new \Exception("Invalid InResponseTo attribute, expected " . $_SESSION['RequestID']);
        }
        if ($root->getAttribute('Destination') == "") {
            throw new \Exception("Missing Destination attribute");
        }

        if ($xml->getElementsByTagName('Status')->length <= 0) {
            throw new \Exception("Missing Status element");
        } elseif ($xml->getElementsByTagName('StatusCode')->item(0)->getAttribute('Value') == 'urn:oasis:names:tc:SAML:2.0:status:Success') {
            if ($xml->getElementsByTagName('Assertion')->length <= 0) {
                throw new \Exception("Missing Assertion element");
            } elseif ($xml->getElementsByTagName('AuthnStatement')->length <= 0) {
                throw new \Exception("Missing AuthnStatement element");
            }
        } else {
            // Status code != success
            return false;
        }

        // Response OK
        $session = $this->spidSession($xml);
        unset($_SESSION['RequestID']);
        unset($_SESSION['idpName']);
        return $session;
    }

    public function spidSession(\DOMDocument $xml)
    {

        $session = new Session();

        $attributes = array();
        if ($xml->getElementsByTagName('AttributeStatement')->length >0) {
            foreach ($xml->getElementsByTagName('AttributeStatement')->item(0)->childNodes as $attr) {
                $attributes[$attr->getAttribute('Name')] = $attr->nodeValue;
            }
        }

        $session->idp = $_SESSION['idpName'];
        $session->attributes = $attributes;
        $session->level = substr($xml->getElementsByTagName('AuthnContextClassRef')->item(0)->nodeValue, -1);
        return $session;
    }
}
