# Getting Started

Generate a favicon set from one source image, publish the files, and add the
generated tags to the document head.

```bash
vendor/bin/favicon assets/logo.svg --output public/favicons
```

The command prints a report and writes `favicon.html` beside the generated
icons. Copy that file's contents into the page `<head>`:

```html
<link rel="icon" href="/favicons/favicon.ico" sizes="32x32">
<link rel="icon" href="/favicons/icon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/favicons/apple-touch-icon.png">
<meta name="theme-color" content="#0b0b0b">
```

Set `--public-path` when the public URL differs from the filesystem output
directory:

```bash
vendor/bin/favicon assets/logo.svg \
    --output public/favicons \
    --public-path /favicons
```

Existing files are skipped by default. Pass `--force` after changing the source
or options to regenerate them.

For an installable web application, add manifest metadata and icons:

```bash
vendor/bin/favicon assets/logo.svg \
    --output public/favicons \
    --public-path /favicons \
    --app-name "My App" \
    --manifest \
    --force
```

Read [Generated Files](generated-files.md) for the exact output set and
[CLI](cli.md) for every option.
