# php-codestyles

Crazy Factory default code styles, shipped as PHPCS sniffs and a ruleset for
Squizlabs' PHP_CodeSniffer. Requires PHP_CodeSniffer 3.5+ on PHP 7.1+; the
ruleset is also compatible with PHP_CodeSniffer 4.x if you require it yourself.

## Install

```bash
composer require --dev crazyfactory/sniffs
```

The package requires `squizlabs/php_codesniffer` (`^3.5`), so the
`phpcs`/`phpcbf` binaries are available in `vendor/bin` automatically.

## Usage with PHP_CodeSniffer (phpcs / phpcbf)

Create a `phpcs.xml` in your project root (see `phpcs.example.xml`):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ruleset name="CrazyFactory Default Coding Standard">
    <config name="installed_paths" value="vendor/crazyfactory/sniffs/src" />

    <file>./src/</file>
    <file>./tests/</file>

    <rule ref="CrazyFactory" />
</ruleset>
```

Add lint scripts to `composer.json`:

```json
{
  "scripts": {
    "lint": "phpcs --standard=phpcs.xml",
    "lint:fix": "phpcbf --standard=phpcs.xml"
  }
}
```

## Usage with EasyCodingStandard (optional)

This repo lints itself with `symplify/easy-coding-standard` (see `ecs.php`,
dev dependency only). If you prefer ECS in your project, copy `ecs.php` from
this package, adjust the paths and require ECS yourself:

```bash
composer require --dev symplify/easy-coding-standard
```

Note: EasyCodingStandard 10+ does not support NEON configuration anymore. The
old `easy-coding-standard.neon` file was removed; `ecs.php` is the replacement.
