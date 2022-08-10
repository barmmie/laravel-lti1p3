
# Package for LTI 1.3 implementations as platforms and /or tools

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require wien/laravel-lti1p3
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-lti-config"
```

This is the contents of the published config file:

## Usage

```php
<?php

use OAT\Library\Lti1p3Core\Message\Launch\Builder\PlatformOriginatingLaunchBuilder;
use OAT\Library\Lti1p3Core\Message\LtiMessageInterface;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\ContextClaim;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;

// Create a builder instance
$builder = new PlatformOriginatingLaunchBuilder();

// Get related registration of the launch
/** @var RegistrationRepositoryInterface $registrationRepository */
$registration = $registrationRepository->find(...);

// Build a launch message
$message = $builder->buildPlatformOriginatingLaunch(
    $registration,                                               // related registration
    LtiMessageInterface::LTI_MESSAGE_TYPE_RESOURCE_LINK_REQUEST, // message type of the launch, as an example: 'LtiDeepLinkingResponse'
    'http://tool.com/launch',                                    // target link uri of the launch (final destination after OIDC flow)
    'loginHint',                                                 // login hint that will be used afterwards by the platform to perform authentication
    null,                                                        // will use the registration default deployment id, but you can pass a specific one
    [
        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner' // role
    ],
    [
        'myCustomClaim' => 'myCustomValue',    // custom claim
        new ContextClaim('contextIdentifier')  // LTI claim representing the context of the launch 
    ]
);

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/barmmie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [barmmie](https://github.com/barmmie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
