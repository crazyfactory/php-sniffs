php-codestyles
=======

Crazy Factory default code styles to be used with Squizlabs' codesniffer.

## Install

1) Copy phpcs.xml over to your project

2) Run `composer require-dev squizlabs/php_codesniffer`

3) Add a linting command to `composer.json`

```
  "scripts": {
    "lint": "phpcs --standard=phpcs.xml",
    "lint:fix": "phpcbf --standard=phpcs.xml"
  }
```
