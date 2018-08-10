<img src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-b-lb.png" alt="SPID" data-canonical-src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-b-lb.png" width="500" height="98" />

[![Join the #spid-perl channel](https://img.shields.io/badge/Slack%20channel-%23spid--perl-blue.svg?logo=slack)](https://developersitalia.slack.com/messages/C7ESTMQDQ)
[![Get invited](https://slack.developers.italia.it/badge.svg)](https://slack.developers.italia.it/)
[![SPID on forum.italia.it](https://img.shields.io/badge/Forum-SPID-blue.svg)](https://forum.italia.it/c/spid)

> ⚠️ **WORK IN PROGRESS** ⚠️

# spid-php3
PHP package for SPID authentication

This PHP package is aimed at implementing SPID **Service Providers**. [SPID](https://www.spid.gov.it/) is the Italian digital identity system, which enables citizens to access all public services with a single set of credentials. This package provides a layer of abstraction over the SAML protocol by exposing just the subset required in order to implement SPID authentication in a web application.

Features:
- provides a **lean implementation** without relying on external SAML packages
- **routing-agnostic**, can be integrated in any web framework / CMS
- **sessionless** (apart from a short-lived internal session used to store the request ID until the Identity Provider responds)
- does not currently support Attribute Authority (AA).

Alternatives for PHP:
- [spid-php](https://github.com/italia/spid-php) based on [SimpleSAMLphp](https://simplesamlphp.org/)
- [spid-php2](https://github.com/simevo/spid-php2) based on [php-saml](https://github.com/onelogin/php-saml)

Alternatives for other languages:
- [spid-perl](https://github.com/italia/spid-perl)
- [spid-ruby](https://github.com/italia/spid-ruby)

## Repository layout

* [bin/](bin/) auxiliary scripts
* [example/](example/) will contain a demo application
* [src/](src/) will contain the implementation
* [test/](test/) will contain the unit tests

## Getting Started

Tested on: Debian 9.4 (stretch) amd64.

### Prerequisites

```
sudo apt install composer make openssl php-curl php-zip php-xml phpunit
```

### Configuring and Installing

Before using this package, you must:

1. Install prerequisites with composer

2. Download and verify the Identity Provider (IdP) metadata files; it is advised to place them in a separate [idp_metadata/](example/idp_metadata/) directory. A convenience tool is provided for this purpose: [bin/download_idp_metadata.php](bin/download_idp_metadata.php).

3. Generate key and certificate for the Service Provider (SP). You can use the provided [Makefile](Makefile).

All steps can be performed with:
```
composer install --no-dev
make
bin/download_idp_metadata.php
```

**NOTE**: during testing, it is highly adviced to use the test Identity Provider [spid-testenv2](https://github.com/italia/spid-testenv2).

### Usage

All classes provided by this package reside in the `Spid` namespace.

Load them using the composer-generated autoloader:
```
require_once(__DIR__ . "/../vendor/autoload.php");
```

The main class is `Spid\Sp` (service provider), sample instantiation:

```
$settings = array(
    'entityId' => 'https://example.com/myservice',
    'keyFile' => '/srv/spid-myservice/sp.key',
    'certFile' => '/srv/spid-myservice/sp.crt',
    'sls' => '/?sls',
    'assCs' => array(
        '/?acs1',
    ),
    'attrCs' => array(
        array('name', 'familyName', 'fiscalNumber', 'email'),
    )
);
$sp = new Spid\Sp($settings);
```

Register the IdPs with the service provider, either load all IdP metadata at once:
```
$sp->loadIdpMetadata('idp_metadata');
```
or load only selected IdPs:
```
$sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/testenv2.xml'); // 0 = Test IDP
$sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_1.xml');    // 1 = Infocert ID
// $sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_2.xml');    // 2 = Poste ID
// $sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_3.xml');    // 3 = Tim ID
// $sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_4.xml');    // 4 = Sielte ID
// $sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_5.xml');    // 5 = Aruba ID
// $sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_6.xml');    // 6 = Namirial ID
// $sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_7.xml');    // 7 = SPIDItalia Register.it
// $sp->loadIdpFromFile('/srv/spid-myservice/idp_metadata/idp_8.xml');    // 8 = Intesa ID

```

The service provider is now ready for use.

The low-level interface allows fine-grained control on the communication with the IdP:
```
$idp = $sp->getIdp('idp_1');
$authnRequest = idp->$authnRequest(0, 1, 2, 'https://example.com/return_to_url');
$url = $authnRequest->redirectUrl();
header($url);
```

The high-level interface is more straightforward:
```
$sp->login(0, 1, 2, 'https://example.com/return_to_url');
$sp->logout();
$attributes = $sp->getAttributes();
```

### Example

TODO

## Testing

### Unit tests

TODO

Unit tests will be performed with PHPunit.

### Linting

This project complies with the [PSR-2: Coding Style Guide](https://www.php-fig.org/psr/psr-2/).

Lint the code with:
```
./vendor/bin/phpcs --standard=PSR2 xxx.php
```

## Contributing

For your contributions please use the [git-flow workflow](https://danielkummer.github.io/git-flow-cheatsheet/).

## See also

* [SPID page](https://developers.italia.it/it/spid) on Developers Italia

## Authors

TODO

## License

Copyright (c) 2018, Paolo Greppi <paolo.greppi@simevo.com>

License: BSD 3-Clause, see [LICENSE](LICENSE) file.
