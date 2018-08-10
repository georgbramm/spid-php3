<?php

namespace SpidPHP\Spid\Saml;

class Settings
{
    private $validSettings = [
    'sp_entityid' => 1,
    'sp_key_file' => 1,
    'sp_cert_file' => 1,
    'sp_assertionconsumerservice' => 1,
    'sp_singlelogoutservice' => 1,
    'sp_attributeconsumingservice' => 0
];
    public static function validateSettings(array $settings)
    {
        $missingSettings = array_diff_key(self::validSettings, $settings);
        if (count($missingSettings) > 0) throw new \Exception('Missing settings fields: ' . implode(', ', $missingSettings));
        $invalidFields = array_diff_key($settings, self::validSettings);
        if (count($invalidFields) > 0) throw new \Exception('Invalid settings fields: ' . implode(', ', $invalidFields));
    }
}