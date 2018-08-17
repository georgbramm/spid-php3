<?php
/**
 * Created by PhpStorm.
 * User: lorenzocattaneo
 * Date: 17/08/18
 * Time: 10:12
 */

namespace SpidPHP\Spid\Saml\Out;


class LogoutRequest extends Base
{
    public function generateXml()
    {
        $xml = <<<XML
<LogoutRequest ID="" IssueInstant="" Version="2.0" Destination="">
    <Issuer NameQualifier="$entityId" Format="urn:oasis:names:tc:SAML:2.0:nameid-format:entity">$entityId</Issuer>
    <NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient" NameQualifier="$idpEntityId" />
    <SessionIndex>$index</SessionIndex>
</LogoutRequest>
XML;

    }
}