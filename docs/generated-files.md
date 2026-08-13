# Generated Files

The generated set depends on the source format and optional features.

## Default SVG output

| File | Purpose |
| --- | --- |
| `icon.svg` | Original vector icon for modern browsers |
| `favicon.ico` | 32 by 32 legacy browser icon |
| `apple-touch-icon.png` | 180 by 180 Apple touch icon |
| `favicon.html` | Ready-to-use document head tags |

## Default PNG output

PNG input replaces `icon.svg` with `favicon-16x16.png` and
`favicon-32x32.png`. The ICO, Apple touch icon, and HTML snippet are still
generated.

## Manifest output

Enable `--manifest` or `generateManifest(true)` to add:

- `manifest.webmanifest`.
- `icon-192.png`.
- `icon-512.png`.
- `icon-maskable.png`.

The maskable icon currently uses the generated 512 by 512 image. The manifest
references each file through the configured public path.

## Search output

Enable `--search-png` or `generateSearchPng48(true)` to add
`favicon-48x48.png` for Google Search results.

Every generated file appears in `GenerationReport::files` with a `created` or
`skipped` status.
