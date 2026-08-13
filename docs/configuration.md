# Configuration

The CLI flags and `FaviconOptionsBuilder` methods map to the same options.

| Builder method | Default | Purpose |
| --- | --- | --- |
| Constructor input file | Required | SVG or PNG source path |
| Constructor output directory | Required | Filesystem destination |
| `publicPath()` | `/` | URL prefix used in generated markup |
| `appName()` | `App` | Name written to the Web App Manifest |
| `themeColor()` | `#0b0b0b` | Browser UI and manifest theme color |
| `backgroundColor()` | `#ffffff` | Manifest background color |
| `generateManifest()` | `false` | Add manifest and Android icons |
| `generateSearchPng48()` | `false` | Add the 48 by 48 search icon |
| `force()` | `false` | Replace existing generated files |

## Public paths

The output directory is a filesystem path. The public path is the URL prefix
written to `favicon.html` and `manifest.webmanifest`.

```php
$options = (new FaviconOptionsBuilder(
    inputFile: 'assets/logo.svg',
    outputDir: 'public/assets/favicons',
))
    ->publicPath('/assets/favicons')
    ->build();
```

## Existing files

Generation is conservative by default: each existing output is marked
`skipped`. Enable `force()` or pass `--force` to replace it. This decision is
made per file, so a partially generated directory can be completed without
rewriting successful outputs.

## Manifest options

Application name and colors affect the manifest only when manifest generation
is enabled. Theme color also appears in the generated HTML metadata. Colors
must use the `#rrggbb` form.
