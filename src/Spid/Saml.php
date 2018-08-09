<?php 

namespace SpidPHP\Spid;

use SpidPHP\Spid\Interfaces\SpInterface;

class Saml implements SpInterface
{
    public function __construct($settings = null) {}

    public function loadIdpMetadata($path){}

    public function loadIdpFromFile($filename){}

    public function getSPMetadata(){}

    public function getIdp($idpName){}

    public function login($idpName, $ass, $attr, $redirectTo = '', $level = 1){}

    public function isAuthenticated(){}

    public function logout(){}

    public function getAttributes(){}
}
