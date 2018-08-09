<?php
require_once(__DIR__ . "/../vendor/autoload.php");

use SpidPHP\SpidPHP;
use SpidPHP\Spid\Saml;

$saml = new Saml();

$spid = new SpidPHP($saml);
?>