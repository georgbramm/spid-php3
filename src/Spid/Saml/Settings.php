<?php

namespace SpidPHP\Spid\Saml;

class Settings
{
    private static $validSettings = [
    'sp_entityid' => 1,
    'sp_key_file' => 1,
    'sp_cert_file' => 1,
    'sp_assertionconsumerservice' => 1,
    'sp_singlelogoutservice' => 1,
    'sp_attributeconsumingservice' => 0
];
    public static function validateSettings(array $settings)
    {
        $missingSettings = array_diff_key(self::$validSettings, $settings);
        $msg = 'Missing settings fields: ';
        foreach ($missingSettings as $k => $v) {
            $msg .= $k . ', ';
        }
        if (count($missingSettings) > 0) throw new \Exception($msg);
        $invalidFields = array_diff_key($settings, self::$validSettings);
        $msg = 'Invalid settings fields: ';
        foreach ($invalidFields as $k => $v) {
            $msg .= $k . ', ';
        }
        if (count($invalidFields) > 0) throw new \Exception($msg);
    }
}