<?php

namespace SpidPHP\Spid\Interfaces;

// service provider class
interface SpInterface
{
    // $settings = array(
    //     'entityId' => 'https://example.com/myservice', // https protocol, no trailing slash
    //     'keyFile' => '/srv/spid-wordpress/sp.key',
    //     'certFile' => '/srv/spid-wordpress/sp.crt',
    //     'sls' => '/?sls', // path relative to entityId base url or full url
    //     'assCs' => array(
    //         // array of assertion consuming services
    //         // order is important ! the 0-base index in this array will be used in the calls
    //         '/?acs1', // path relative to entityId base url or full url
    //         '/acs2/?test',
    //     ),
    //     'attrCs' => array(
    //         // array of attribute consuming services
    //         // order is important ! the 0-base index in this array will be used in the calls
    //         array('name', 'familyName', 'fiscalNumber', 'email'),
    //         array('fiscalNumber', 'email')
    //     ),
    //     'organisationName' => 'xxx', // optional
    //     'organizationUrl' => 'xxx', // optional
    // );
    public function __construct($settings);

    // loads all Identity Providers metadata found in path
    public function loadIdpMetadata($path);

    // load selected Identity Provider
    public function loadIdpFromFile($filename);

    // returns SP XML metadata as a string
    public function getSPMetadata();

    // LOW-LEVEL FUNCTION:

    // get an IdP
    // the IdP can be used to generate an AuthnRequest:
    //   $idp = getIdp('idp_1');
    //   $authnRequest = idp->$authnRequest(0, 1, 2, 'https://example.com/return_to_url');
    //   $url = $authnRequest->redirect_url();
    // $idpName: shortname of IdP, same as the name of corresponding IdP metadata file, without .xml
    public function getIdp($idpName);

    // HIGH-LEVEL FUNCTIONS:

    // performs login
    // $idpName: shortname of IdP, same as the name of corresponding IdP metadata file, without .xml
    // $ass: index of assertion consumer service as per the SP metadata
    // $attr: index of attribute consuming service as per the SP metadata
    // $level: SPID level (1, 2 or 3)
    // $returnTo: return url
    public function login($idpName, $ass, $attr, $redirectTo = '', $level = 1);

    // returns false if no response from IdP is found
    // else processes the response, reports errors if any
    // and finally returns true if login was successful
    public function isAuthenticated();

    // performs logout
    public function logout();

    // returns attributes as an array or null if not authenticated
    // example: array('name' => 'Franco', 'familyName' => 'Rossi', 'fiscalNumber' => 'FFFRRR88A12T4441R',)
    public function getAttributes();
}
