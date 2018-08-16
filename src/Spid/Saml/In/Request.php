<?php
/**
 * Created by PhpStorm.
 * User: lorenzocattaneo
 * Date: 16/08/18
 * Time: 09:52
 */

namespace SpidPHP\Spid\Saml\In;


class Request extends Base
{

    public function validate()
    {
        if (!isset($_POST) || !isset($_POST['SAMLResponse']))
        {
            throw new \Exception("SAML response not found");
        }

        $xmlString = base64_decode($_POST['SAMLResponse']);
        $xml = new \SimpleXMLElement($xmlString);

        if (!isset($xml->attributes()->Version))
        {
            throw new \Exception("missing Version attribute");
        }
        elseif ($xml->attributes()->Version->__toString() != '2.0')
        {
            throw new \Exception("Invalid Version attribute");
        }
        if (!isset($xml->attributes()->IssueInstant))
        {
            throw new \Exception("Missing IssueInstant attribute");
        }
        if (!isset($xml->attributes()->InResponseTo) || !isset($_SESSION['RequestID']))
        {
            throw new \Exception("Missing InResponseTo attribute");
        }
        elseif ($xml->attributes()->InResponseTo->__toString() != $_SESSION['RequestID'])
        {
            throw new \Exception("Invalid InResponseTo attribute, expected " . $_SESSION['RequestID']);
        }
        if (!isset($xml->attributes()->Destination))
        {
            throw new \Exception("Missing Destination attribute");
        }
        if (!isset($xml->Status))
        {
            throw new \Exception("Missing Status element");
        }
        elseif ($xml->Status->StatusCode->__toString() == 'urn:oasis:names:tc:SAML:2.0:status:Success')
        {
            if (!isset($xml->Assertion))
            {
                throw new \Exception("Missing Assertion element");
            }
            elseif (!isset($xml->Assertion->AuthnStatement))
            {
                throw new \Exception("Missing AuthnStatement element");
            }
        }
        
        // Response OK
        unset($_SESSION['RequestID']);
    }
}