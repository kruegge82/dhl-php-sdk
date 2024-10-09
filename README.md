# DEPRECATED
updated to the new REST API -> [DHL PHP REST API](https://github.com/kruegge82/dhl-php-rest-sdk)

# DHL PHP SDK

This *unofficial* library is wrapping some functions of the DHL REST API in order to easy create/delete shipments and labels.

## Requirements

- You need a [DHL developer Account](https://developer.dhl.com/) and - as long as you want to use the API in production systems - a DHL Geschäftskundenportal Account.
- PHP-Version 7.4 or higher _(It may work on older Versions, but I don't offer Support for these)_

## Installation

### Composer

You can use [Composer](https://getcomposer.org/) to install the package to your project:

```
composer require kruegge82/dhl-php-sdk
```

The classes are then added to the autoloader automatically.

### Without Composer

If you can't use Composer (or don't want to), you can also use this SDK without it.

To initial this SDK, just require the [_nonComposerLoader.php](https://github.com/kruegge82/dhl-php-sdk/blob/master/includes/_nonComposerLoader.php)-File from the `/includes/` directory.

```php
require_once(__DIR__ . '/includes/_nonComposerLoader.php');
```

## Compatibility

This Project is written for the DHL-REST-API **Version 2.1.1 or higher**.

## Usage / Getting started

- [Getting started (Just a quick guide how you have to use it)](https://github.com/kruegge82/dhl-php-sdk/blob/master/examples/getting-started.md)
- _More examples soon_

Please have a look at the `examples` [Directory](https://github.com/kruegge82/dhl-php-sdk/tree/master/examples). There you can find how to use this SDK also with Code-Examples.

## Donate

If you like this Project may consider to [Donate](https://www.paypal.me/jahn82). I usually do this Project in my spare time and it's completely free. So I appreciate anything, which helps the Project (Pull-Requests, Bug Report etc), these are more worth than Donations but I'm happy for every amount as well. ^.^

## Contact

- You can Report Bugs here in the "[Issue](https://github.com/kruegge82/dhl-php-sdk/issues)"-Section of the Project.
	- Of course you can also ask any stuff there, feel free for that!
	- If you want to use German, you can do it. Please keep in mind that not everybody can speak German, so it's better to use english =)
