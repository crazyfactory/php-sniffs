php-codestyles
=======

Crazy Factory default code styles to be used with Squizlabs' codesniffer and PHPCSFixer.

## Install

Run `composer require --dev crazyfactory/sniffs`

Create a file `phpcs.xml` in the root path of project with at least below contents:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="CrazyFactory Default Coding Standard">
    <config name="installed_paths" value="vendor/crazyfactory/sniffs/src" />

    <rule ref="CrazyFactory" />
</ruleset>
```

Add a linting command to `composer.json`

```
  "scripts": {
    "lint": "phpcs --standard=phpcs.xml",
    "lint:fix": "phpcbf --standard=phpcs.xml"
  }
```
