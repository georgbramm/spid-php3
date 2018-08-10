<?php

namespace SpidPHP\Spid\Interfaces;

interface AuthnRequestInterface
{
    public function generateXml();
    // prepare a HTTP-Redirect binding and returns it as a string
    // https://github.com/italia/spid-perl/blob/master/lib/Net/SPID/SAML/Out/AuthnRequest.pm#L61
    public function redirectUrl();
}
