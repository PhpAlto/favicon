# PHP API

Use `FaviconOptionsBuilder` and `FaviconGenerator` when favicon generation is
part of an application, build command, or content pipeline.

```php
use Alto\Favicon\Generator\FaviconGenerator;
use Alto\Favicon\Options\FaviconOptionsBuilder;

$options = (new FaviconOptionsBuilder(
    inputFile: 'assets/logo.svg',
    outputDir: 'public/favicons',
))
    ->publicPath('/favicons')
    ->appName('My App')
    ->themeColor('#00b7ff')
    ->backgroundColor('#050608')
    ->generateManifest(true)
    ->generateSearchPng48(true)
    ->build();

$report = (new FaviconGenerator())->generate($options);
```

The returned `GenerationReport` exposes:

| Property | Contents |
| --- | --- |
| `outputDir` | Filesystem output directory |
| `publicPath` | URL prefix used by generated markup |
| `files` | Map of filenames to `created` or `skipped` status |
| `htmlSnippet` | Tags to add to the document head |
| `manifestFile` | Manifest filename, or `null` when disabled |

```php
foreach ($report->files as $file => $status) {
    printf("%s: %s\n", $file, $status);
}

echo $report->htmlSnippet;
```

`FaviconGenerator` accepts custom rasterizers, generators, a Symfony
Filesystem instance, and a PSR logger through its constructor. Leave them
unset to use the default pipeline described in [Rasterizers](rasterizers.md).
