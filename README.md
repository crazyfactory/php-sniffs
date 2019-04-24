php-codestyles
=======

Crazy Factory default code styles to be used with Squizlabs' codesniffer and PHPCSFixer.

## Install

1) Create a file `easy-coding-standard.neon` in the root path of project with at least below contents:
```yml
includes:
  - vendor/crazyfactory/sniffs/easy-coding-standard.neon

```

2) Run `composer require-dev crazyfactory/sniffs`

43 Add a linting command to `composer.json`

```
  "scripts": {
    "lint": "ecs check src cron tests --clear-cache",
    "lint:fix": "ecs check src cron tests --clear-cache --fix"
  }
```
