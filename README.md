locale-discovery
===============

_locale-discovery_ is a Composer-enabled PHP script to locate assets
(JS, CSS, Coffee, SASS, images) across multiple PHP components which then
can be copied automatically into generated stylesheets, scripts and
image folders.

Based on [asset-discovery](https://github.com/soundasleep/asset-discovery).

## Configuring

First include `locale-discovery` as a requirement in your project `composer.json`,
and run `composer update` to install it into your project:

```json
{
  "require": {
    "soundasleep/locale-discovery": "dev-master"
  },
  "repositories": [{
    "type": "vcs",
    "url": "https://github.com/soundasleep/locale-discovery"
  }]
}
```

Now create a `locale-discovery.json` in your project, to define the types of assets to discover,
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

_locale-discovery_ will look in all the `src` folders for files called `locales.json`
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
php -f vendor/soundasleep/locale-discovery/generate.php .
```

_locale-discovery_ will then load all identified locale JSON files, combine all of the
locale strings into one large JSON file, and then write this out to each locale destination JSON.

If `generate_php` is set to `true`, a `.php` file will also be generated which returns
the locale strings in a format suitable for PHP `require()`.

These files can then be passed along to the next step in a build chain.

## TODOs

1. Actually publish on Packagist
2. More documentation, especially default `locale-discovery.json` parameters
3. Tests
4. Example projects using _locale-discovery_
5. Create `grunt` task `grunt-php-locale-discovery` to wrap the manual PHP command
6. Release 0.1 version

## See also

1. [asset-discovery](https://github.com/soundasleep/asset-discovery)
1. [component-discovery](https://github.com/soundasleep/component-discovery)
1. [openclerk/i18n](https://github.com/openclerk/i18n)
