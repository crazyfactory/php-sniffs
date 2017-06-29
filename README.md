php-codestyles
=======

Crazy Factory default code styles to be used with Squizlabs' codesniffer.

## Install

1) Copy `phpcs.example.xml` over to your project and rename it to `phpcs.xml`

2) Remove the `<file>` tag that does not exist in your project.

3) Run `composer require-dev squizlabs/php_codesniffer`

4) Add a linting command to `composer.json`

```
  "scripts": {
    "lint": "phpcs --standard=phpcs.xml",
    "lint:fix": "phpcbf --standard=phpcs.xml"
  }
```
