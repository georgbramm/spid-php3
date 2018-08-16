<?php
declare(strict_types=1);

require_once(__DIR__ . "/../vendor/autoload.php");

final class SpidTest extends PHPUnit\Framework\TestCase
{
    public function testCanBeCreatedFromValidSettings(): void
    {
        $settings = [
            'sp_entityid' => 'http://sp3.simevo.com/',
            'sp_key_file' => './example/sp.key',
            'sp_cert_file' => './example/sp.crt',
            'sp_assertionconsumerservice' => 'http://sp3.simevo.com/acs',
            'sp_singlelogoutservice' => 'http://sp3.simevo.com/slo',
            'sp_org_name' => 'test_simevo',
            'sp_org_display_name' => 'Test Simevo',
            'idp_metadata_folder' => './example/idp_metadata/'
        ];        
        $this->assertInstanceOf(
            SpidPHP\SpidPHP::class,
            new SpidPHP\SpidPHP($settings)
        );
    }

    private function validateXml($xmlString, $schemaFile, $valid = true): void
    {
        $xml = new DOMDocument();
        $xml->loadXML($xmlString, LIBXML_NOBLANKS);
        $this->assertEquals($xml->schemaValidate($schemaFile), $valid);
    }

    public function testMetatadaValid(): void
    {
        $settings = [
            'sp_entityid' => 'http://sp3.simevo.com/',
            'sp_key_file' => '../example/sp.key',
            'sp_cert_file' => './example/sp.crt',
            'sp_assertionconsumerservice' => 'http://sp3.simevo.com/acs',
            'sp_singlelogoutservice' => 'http://sp3.simevo.com/slo',
            'sp_org_name' => 'test_simevo',
            'sp_org_display_name' => 'Test Simevo',
            'idp_metadata_folder' => './example/idp_metadata/'
        ];        
        $spid = new SpidPHP\SpidPHP($settings);
        $metadata = $spid->getSPMetadata();
        $this->validateXml($metadata, "./tests/schemas/saml-schema-metadata-SPID-SP.xsd");
    }
}
