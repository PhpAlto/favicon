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
 * Builder for FaviconOptions.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class FaviconOptionsBuilder
{
    private string $inputFile;
    private string $outputDir;
    private string $publicPath = '/';
    private string $appName = 'App';
    private string $themeColor = '#0b0b0b';
    private string $backgroundColor = '#ffffff';
    private bool $generateManifest = false;
    private bool $generateSearchPng48 = false;
    private bool $force = false;

    /**
     * @param string $inputFile Path to the input file (SVG or PNG)
     * @param string $outputDir Path to the output directory
     */
    public function __construct(string $inputFile, string $outputDir)
    {
        $this->inputFile = $inputFile;
        $this->outputDir = $outputDir;
    }

    /**
     * Set the public URL prefix for generated files (e.g. "/favicons").
     * Default: "/".
     */
    public function publicPath(string $publicPath): self
    {
        $this->publicPath = $publicPath;

        return $this;
    }

    /**
     * Set the application name for the manifest.
     * Default: "App".
     */
    public function appName(string $appName): self
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Set the theme color (meta tag & manifest).
     * Default: "#0b0b0b".
     */
    public function themeColor(string $themeColor): self
    {
        $this->validateHexColor($themeColor);
        $this->themeColor = $themeColor;

        return $this;
    }

    /**
     * Set the background color (manifest).
     * Default: "#ffffff".
     */
    public function backgroundColor(string $backgroundColor): self
    {
        $this->validateHexColor($backgroundColor);
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * Enable/disable manifest generation.
     * Default: false.
     */
    public function generateManifest(bool $generateManifest): self
    {
        $this->generateManifest = $generateManifest;

        return $this;
    }

    /**
     * Enable/disable 48x48 PNG generation.
     * Default: false.
     */
    public function generateSearchPng48(bool $generateSearchPng48): self
    {
        $this->generateSearchPng48 = $generateSearchPng48;

        return $this;
    }

    /**
     * Enable/disable overwriting existing files.
     * Default: false.
     */
    public function force(bool $force): self
    {
        $this->force = $force;

        return $this;
    }

    /**
     * Build the FaviconOptions object.
     */
    public function build(): FaviconOptions
    {
        return new FaviconOptions(
            inputFile: $this->inputFile,
            outputDir: $this->outputDir,
            publicPath: $this->publicPath,
            appName: $this->appName,
            themeColor: $this->themeColor,
            backgroundColor: $this->backgroundColor,
            generateManifest: $this->generateManifest,
            generateSearchPng48: $this->generateSearchPng48,
            force: $this->force,
        );
    }

    private function validateHexColor(string $color): void
    {
        if (!preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            throw new \InvalidArgumentException(sprintf('Color "%s" is invalid. It must be a valid 6-character hex code (e.g. #0b0b0b).', $color));
        }
    }
}
