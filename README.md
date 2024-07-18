# api-client-php

PHP client for the CloudForest Marketplace API.

## Installation

```
composer require cloudforest/api-client-php
```

## Update

Fetch recent changes with:

```
composer require cloudforest/api-client-php
```

## Documentation

The client is documented with phpdoc. To read it, you could read the code in
the `./vendors/cloudforest/api-client-php/src` folder. Or
[install phpdoc](https://docs.phpdoc.org/3.0/guide/getting-started/installing.html#installation)
then extract the documentation to your project with:

```
phpdoc -d vendor/cloudforest/api-client-php/src -t ./docs
```

You can then open `./docs/index.html` in a browser.

## Code Quality

You can run php-cs-fixer and phpstan:

```
composer run phpcs:check
composer run phpcs:fix
composer run phpstan
```

## Testing

This will run a suite of tests against a CloudForest Marketplace server such as
cfdev.cloudforest.marketplace.

Copy phpunit.dist.xml to phpunit.xml and fill in the environment variables. Then
run:

```
composer run phpunit
```

