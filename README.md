# ALTO Favicon

Generate a focused, modern favicon set from one SVG or PNG source.

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-00B7FF?logoColor=00B7FF&labelColor=050608)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/altophp/favicon/CI.yml?branch=main&label=Tests&labelColor=050608&color=00B7FF)
&nbsp; [![Packagist](https://img.shields.io/packagist/v/alto/favicon?label=Packagist&labelColor=050608&color=00B7FF)](https://packagist.org/packages/alto/favicon)
&nbsp; ![License](https://img.shields.io/github/license/altophp/favicon?label=License&labelColor=050608&color=00B7FF)
&nbsp; [![GitHub Sponsors](https://img.shields.io/github/sponsors/smnandre?logo=githubsponsors&logoColor=00B7FF&label=%20Sponsor&labelColor=050608&color=00B7FF)](https://github.com/sponsors/smnandre)

ALTO Favicon produces the small set of files current browsers and devices need,
plus the exact HTML tags required to use them. The CLI and PHP API share the
same options, preserve SVG input for modern browsers, and select an available
rasterizer automatically.

```bash
vendor/bin/favicon assets/logo.svg --output public/favicons
```

The default result includes `icon.svg`, `favicon.ico`,
`apple-touch-icon.png`, and `favicon.html`. Optional flags add a Web App
Manifest, Android icons, a maskable icon, and a Google Search PNG.

## Installation

Install ALTO Favicon with Composer:

```bash
composer require alto/favicon
```

ALTO Favicon requires PHP 8.3 or later. SVG input needs `rsvg-convert`,
Inkscape, ImageMagick CLI, or the Imagick extension. PNG input needs Imagick or
GD. Composer installs the PHP package dependencies automatically.

## Quick Start

Generate the default set from an SVG:

```bash
vendor/bin/favicon assets/logo.svg --output public/favicons
```

Then copy the generated contents of `public/favicons/favicon.html` into the
document `<head>`.

Add a manifest and a dedicated Google Search icon when needed:

```bash
vendor/bin/favicon assets/logo.svg \
    --output public/favicons \
    --public-path /favicons \
    --app-name "My App" \
    --manifest \
    --search-png
```

## Documentation

| Guide | Contents |
| --- | --- |
| [Documentation index](docs/index.md) | Browse the complete guide set |
| [Getting started](docs/getting-started.md) | Generate and install the first favicon set |
| [CLI](docs/cli.md) | Commands, flags, and examples |
| [PHP API](docs/php-api.md) | Programmatic generation and reports |
| [Configuration](docs/configuration.md) | Options, defaults, and overwrite behavior |
| [Rasterizers](docs/rasterizers.md) | SVG and PNG adapter selection |
| [Generated files](docs/generated-files.md) | Default and optional outputs |
| [Errors](docs/errors.md) | Input and rasterization failures |

## Contributing

Contributions of all kinds are welcome. Visit the
[project on GitHub](https://github.com/altophp/favicon) to
[report a bug](https://github.com/altophp/favicon/issues/new),
[suggest a feature](https://github.com/altophp/favicon/issues/new), or
[open a pull request](https://github.com/altophp/favicon/pulls).

Before submitting code, run:

```bash
# Runs PHP CS Fixer, PHPStan, and PHPUnit
composer qa
```

## Support

ALTO Favicon is open source. You can support its continued development through
[GitHub Sponsors](https://github.com/sponsors/smnandre).

Sharing this package with others or
[starring it on GitHub](https://github.com/altophp/favicon) is also much
appreciated.

## License

ALTO Favicon is released by [ALTO PHP](https://altophp.com) under the
[MIT License](LICENSE).
