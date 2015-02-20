translation-discovery
=====================

_translation-discovery_ is a Composer-enabled PHP script to locate
translations across multiple PHP components from JSON files, which can then
be combined together into single translations for project runtime.

Based on [asset-discovery](https://github.com/soundasleep/asset-discovery).

## Configuring

First include `translation-discovery` as a requirement in your project `composer.json`,
and run `composer update` to install it into your project:

```json
{
  "require": {
    "soundasleep/translation-discovery": "dev-master"
  }
}
```

Now create a `translation-discovery.json` in your project, to define the types of assets to discover,
and where to place source files:

```json
{
  "src": ["vendor/*/*", "core"],
  "locales": {
    "fr": "generated/locales/fr.json",
    "de": "generated/locales/de.json"
  },
  "generate_php": true
}
```

_translation-discovery_ will look in all the `src` folders for files called `locales.json`
to find matching assets. Wildcards are supported. For example, in your
`vendor/my/package/locales.json`:

```json
{
  "fr": "locales/fr.json",
  "de": ["locales/de/*.json"]
}
```

## Building

Run the generate script, either with your build script or manually, with
a given root directory:

```
php -f vendor/soundasleep/translation-discovery/generate.php .
```

_translation-discovery_ will then load all identified locale JSON files, combine all of the
locale strings into one large JSON file, and then write this out to each locale destination JSON.

If `generate_php` is set to `true`, a `.php` file will also be generated which returns
the locale strings in a format suitable for PHP `require()`.

These files can then be passed along to the next step in a build chain.

## Discovering translation strings

Particularly if you are using the [openclerk/i18n](https://github.com/openclerk/i18n) project, you
can use the find script to locale potentially matching translation strings and output them into
a template file in JSON.

Update `translation-discovery.json` in your project, to define the source locations:

```json
{
  "templates": ["vendor/openclerk", "core", "site"],
  "template": "site/locale/template.json"
}
```

Run the find script, either with your build script or manually, with
a given root directory:

```
php -f vendor/soundasleep/translation-discovery/find.php .
```

This script will find all instances of the following translation strings, and output them to
the `template` JSON folder:

1. `t("string")`
1. `ht("string")`
1. `plural("string", 1)` and `plural("string", "strings", 1)`
1. `"string" /* i18n */`
1. And the single-quote versions of these patterns

## Example projects

# [Openclerk](https://github.com/soundasleep/openclerk)

## TODOs

1. More documentation, especially default `translation-discovery.json` parameters
1. Create `grunt` task `grunt-php-translation-discovery` to wrap the manual PHP command
1. Release 0.1 version

## See also

1. [asset-discovery](https://github.com/soundasleep/asset-discovery)
1. [component-discovery](https://github.com/soundasleep/component-discovery)
1. [openclerk/i18n](https://github.com/openclerk/i18n)
