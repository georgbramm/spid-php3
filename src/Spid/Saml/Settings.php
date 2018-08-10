<?php

namespace SpidPHP\Spid\Saml;

use SpidPHP\Spid\Interfaces\SettingsInterface;

class Settings implements SettingsInterface
{
    var $entityID;
    var $spKeyFile;
    var $spCertFile;
    var $spAssertionConsumerService;
    var $spSLO;
    var $spAttributeConsumingService;

    public function __construct($entityID, $spKeyFile, $spCertFile, $spAssertionConsumerService, $spSLO, $spAttributeConsumingService = null)
    {
        $this->entityID = $entityID;
        $this->spKeyFile = $spKeyFile;
        $this->spCertFile = $spCertFile;
        $this->spAssertionConsumerService = $spAssertionConsumerService;
        $this->spSLO = $spSLO;
        $this->spAttributeConsumingService = $spAttributeConsumingService;
    }
}