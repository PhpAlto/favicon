# Errors

Generation fails early for missing input, unsupported image formats, invalid
colors, or an unavailable rasterization pipeline.

| Error | Cause |
| --- | --- |
| `MissingInputFileException` | The configured source path is not a file |
| `RasterizerUnavailableException` | No compatible adapter completed rasterization |
| `FaviconException` | Base runtime failure for package generation errors |
| `InvalidArgumentException` | A color is not a six-digit hexadecimal value |

```php
use Alto\Favicon\Exception\FaviconException;

try {
    $report = $generator->generate($options);
} catch (FaviconException $error) {
    // Report the failed generation.
}
```

The rasterizer pipeline logs adapter failures through the optional PSR logger
before trying the next adapter. Inject a logger when diagnostics need to expose
which executable or extension failed.

Existing output files are not errors. They are skipped unless force mode is
enabled and appear as `skipped` in the generation report.
