<?php

declare(strict_types=1);

/*
 * This file is part of the ALTO library.
 *
 * © 2025–present Simon André
 *
 * For full copyright and license information, please see
 * the LICENSE file distributed with this source code.
 */

namespace Alto\Favicon;

/**
 * Report containing the results of the favicon generation process.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final readonly class GenerationReport
{
    /**
     * @param string                $outputDir    The directory where files were written
     * @param string                $publicPath   The public URL prefix used
     * @param array<string, string> $files        Map of generated filenames to their status ('created', 'skipped')
     * @param string                $htmlSnippet  The HTML tags to include in your <head>
     * @param string|null           $manifestFile The name of the manifest file (if generated)
     */
    public function __construct(
        public string $outputDir,
        public string $publicPath,
        public array $files,
        public string $htmlSnippet,
        public ?string $manifestFile,
    ) {
    }
}
