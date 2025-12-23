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

namespace Alto\Favicon\Tests\Generator;

use Alto\Favicon\Generator\HtmlGenerator;
use Alto\Favicon\Options\FaviconOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlGenerator::class)]
class HtmlGeneratorTest extends TestCase
{
    public function testGenerateMinimalSvg(): void
    {
        $options = new FaviconOptions(
            inputFile: 'logo.svg',
            outputDir: 'public',
            publicPath: '/',
            appName: 'App',
            themeColor: '#000000',
            backgroundColor: '#ffffff',
            generateManifest: false,
            generateSearchPng48: false,
        );

        $generator = new HtmlGenerator();
        $html = $generator->generate($options, true);

        $this->assertStringContainsString('<link rel="icon" href="/favicon.ico" sizes="32x32">', $html);
        $this->assertStringContainsString('<link rel="icon" href="/icon.svg" type="image/svg+xml">', $html);
        $this->assertStringContainsString('<link rel="apple-touch-icon" href="/apple-touch-icon.png">', $html);
        $this->assertStringContainsString('<meta name="theme-color" content="#000000">', $html);

        $this->assertStringNotContainsString('manifest.webmanifest', $html);
        $this->assertStringNotContainsString('favicon-48x48.png', $html);
    }

    public function testGenerateFullPng(): void
    {
        $options = new FaviconOptions(
            inputFile: 'logo.png',
            outputDir: 'public',
            publicPath: '/assets',
            appName: 'App',
            themeColor: '#000000',
            backgroundColor: '#ffffff',
            generateManifest: true,
            generateSearchPng48: true,
        );

        $generator = new HtmlGenerator();
        $html = $generator->generate($options, false);

        $this->assertStringContainsString('<link rel="manifest" href="/assets/manifest.webmanifest">', $html);
        $this->assertStringContainsString('<link rel="icon" href="/assets/favicon.ico" sizes="32x32">', $html);
        $this->assertStringContainsString('<link rel="icon" href="/assets/favicon-32x32.png" type="image/png" sizes="32x32">', $html);
        $this->assertStringContainsString('<link rel="icon" href="/assets/favicon-16x16.png" type="image/png" sizes="16x16">', $html);
        $this->assertStringContainsString('<link rel="icon" href="/assets/favicon-48x48.png" type="image/png" sizes="48x48">', $html);
        $this->assertStringContainsString('<link rel="apple-touch-icon" href="/assets/apple-touch-icon.png">', $html);

        $this->assertStringNotContainsString('icon.svg', $html);
    }
}
