<?php
require_once(__DIR__ . "/../vendor/autoload.php");

use SpidPHP\SpidPHP;
use SpidPHP\Spid\Saml;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$spid = new SpidPHP([
    'sp_entityid' => 1,
    'sp_key_file' => 1,
    'sp_cert_file' => 1,
    'sp_assertionconsumerservice' => 1,
    'sp_singlelogoutservice' => 1,
    'sp_attributeconsumingservice' => 0
]);

$spid->loadIdpMetadata("");
?>