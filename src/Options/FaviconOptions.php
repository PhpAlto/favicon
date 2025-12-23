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

namespace Alto\Favicon\Options;

/**
 * Configuration options for favicon generation.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final readonly class FaviconOptions
{
    public function __construct(
        /** @var string Path to the input SVG or PNG file */
        public string $inputFile,

        /** @var string Directory where generated files will be written */
        public string $outputDir,

        /** @var string Public URL prefix for generated files (e.g. "/favicons") */
        public string $publicPath,

        /** @var string Application name used in the manifest */
        public string $appName,

        /** @var string Theme color for browser UI and manifest */
        public string $themeColor,

        /** @var string Background color for the manifest */
        public string $backgroundColor,

        /** @var bool Whether to generate manifest.webmanifest and Android icons */
        public bool $generateManifest,

        /** @var bool Whether to generate the 48x48 PNG favicon */
        public bool $generateSearchPng48,

        /** @var bool Whether to overwrite existing files */
        public bool $force = false,
    ) {
    }

    /**
     * Returns the public URL for a given filename.
     */
    public function publicAsset(string $filename): string
    {
        $base = rtrim($this->publicPath, '/');

        if ('' === $base) {
            return '/'.$filename;
        }

        return $base.'/'.$filename;
    }
}
