# Installation

Install ALTO Favicon with Composer and provide a rasterizer suitable for the
source image format.

```bash
composer require alto/favicon
```

The package requires PHP 8.3 or later. Composer installs PSR Log and the
Symfony Console, Filesystem, and Process components automatically.

## SVG input

Provide at least one of these rasterizers:

1. `rsvg-convert` from librsvg.
2. Inkscape.
3. ImageMagick CLI through `magick` or `convert`.
4. The Imagick PHP extension.

The package tries them in that order and continues to the next adapter when one
is unavailable or fails.

## PNG input

Provide the Imagick PHP extension or GD. Imagick is tried first, then GD.

The selected rasterizer creates the PNG sizes required for ICO, Apple touch,
manifest, and search outputs. Continue with
[Getting Started](getting-started.md) to generate a set.
