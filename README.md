# Alto Favicon

A modern, efficient PHP library and CLI tool to generate a complete favicon set from a single SVG or PNG source.

It follows the [modern best practices (2025)](https://evilmartians.com/chronicles/how-to-favicon-in-2021-six-files-that-fit-most-needs) to generate a minimal yet robust set of icons for all browsers and devices.

## Features

- **Minimal Output**: Generates only the necessary files to cover all modern use cases.
- **SVG Support**: Preserves your vector logo for modern browsers (`icon.svg`).
- **Legacy Support**: Generates a 32x32 `favicon.ico` for older browsers.
- **Mobile Ready**: Generates `apple-touch-icon.png` and Android icons (`icon-192.png`, `icon-512.png`).
- **PWA Ready**: Automatically generates a `manifest.webmanifest`.
- **Search Friendly**: Optionally generates a 48x48 PNG for Google Search results.
- **HTML Snippet**: Outputs the exact HTML tags you need to include in your `<head>`.
- **Flexible Rasterization**: Supports multiple adapters for image processing:
  - SVG: `rsvg-convert`, `inkscape`, or `imagemagick` (CLI)
  - PNG: `ext-imagick` or `ext-gd`

## Requirements

- PHP 8.3 or higher
- For PNG processing: `ext-imagick` (recommended) OR `ext-gd`
- For SVG input, one of the following must be installed on your system:
  - `rsvg-convert` (part of `librsvg`, recommended)
  - `inkscape`
  - `imagemagick` (`magick` or `convert`)

## Installation

```bash
composer require alto/favicon
```

## Usage

### CLI

Run the tool using the binary:

```bash
vendor/bin/favicon path/to/logo.svg
```

### Programmatic Usage

You can use the `FaviconGenerator` class directly in your PHP application (e.g., in a CMS, build script, or controller).

```php
use Alto\Favicon\Generator\FaviconGenerator;
use Alto\Favicon\Options\FaviconOptionsBuilder;

// 1. Configure options
$options = (new FaviconOptionsBuilder(
    inputFile: 'assets/logo.svg',
    outputDir: 'public/favicons'
))
    ->publicPath('/favicons')
    ->appName('My App')
    ->themeColor('#0b0b0b')
    ->build();

// 2. Generate
$generator = new FaviconGenerator();
$report = $generator->generate($options);

// 3. Use the result
echo "Generated " . count($report->files) . " files.\n";
echo "HTML Snippet:\n" . $report->htmlSnippet;
```

#### `FaviconOptionsBuilder` API

| Method | Description | Default |
|--------|-------------|---------|
| `__construct(string $inputFile, string $outputDir)` | Initialize with input file and output directory. | - |
| `publicPath(string $path)` | Set the public URL prefix for generated files. | `/` |
| `appName(string $name)` | Set the application name for the manifest. | `App` |
| `themeColor(string $color)` | Set the theme color (meta tag & manifest). | `#0b0b0b` |
| `backgroundColor(string $color)` | Set the background color (manifest). | `#ffffff` |
| `generateManifest(bool $generate)` | Enable/disable manifest generation. | `false` |
| `generateSearchPng48(bool $generate)` | Enable/disable 48x48 PNG generation. | `false` |
| `force(bool $force)` | Enable/disable overwriting existing files. | `false` |
| `build(): FaviconOptions` | Build the options object. | - |

#### `GenerationReport` Properties

The `generate()` method returns a `GenerationReport` object with the following properties:

- `outputDir` (string): The directory where files were written.
- `publicPath` (string): The public URL prefix used.
- `files` (array<string, int>): Map of generated filenames to their size in bytes.
- `htmlSnippet` (string): The HTML tags to include in your `<head>`.
- `manifestFile` (?string): The name of the manifest file (if generated).
- `totalBytes()` (int): Helper method to get total size of generated files.

### Options (CLI)

| Option | Description | Default |
|--------|-------------|---------|
| `--output`, `-o` | Output directory for generated files | `public` |
| `--public-path` | Public path prefix used in HTML/manifest | `/` |
| `--app-name` | Application name used in the manifest | `App` |
| `--theme-color` | Theme color for browser UI and manifest | `#0b0b0b` |
| `--background-color` | Background color for the manifest | `#ffffff` |
| `--manifest` | Generate `manifest.webmanifest` and Android icons | `false` |
| `--search-png` | Generate the 48x48 PNG favicon | `false` |
| `--force`, `-f` | Overwrite existing files | `false` |

### Examples

**Basic usage (Minimal set):**

```bash
vendor/bin/favicon assets/logo.svg
```

**Full set with Manifest and Search Icon:**

```bash
vendor/bin/favicon assets/logo.svg --manifest --search-png
```

**Customizing output and colors:**

```bash
vendor/bin/favicon assets/logo.svg \
    --output public/assets/favicons \
    --public-path /assets/favicons \
    --app-name "My Awesome App" \
    --theme-color "#3b82f6"
```

**Using a PNG source (if you don't have SVG):**

```bash
vendor/bin/favicon assets/logo.png
```
*Note: When using PNG input, `icon.svg` will not be generated, and 16x16/32x32 PNGs will be used as fallbacks.*

## Generated Files

The tool generates the following structure in your output directory:

- `favicon.ico` (Legacy support)
- `icon.svg` (Modern browsers)
- `apple-touch-icon.png` (iOS)
- `favicon.html` (HTML snippet to include)

**Optional files (with `--manifest` and `--search-png`):**

- `icon-192.png` (Android)
- `icon-512.png` (Android)
- `icon-maskable.png` (Android maskable icon)
- `manifest.webmanifest` (Web App Manifest)
- `favicon-48x48.png` (Google Search)

## Testing

To run the test suite:

```bash
composer test
```

## License

This project is licensed under the MIT License.
