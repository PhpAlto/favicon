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

use Alto\Favicon\Generator\WebManifestGenerator;
use Alto\Favicon\Options\FaviconOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WebManifestGenerator::class)]
class WebManifestGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $options = new FaviconOptions(
            inputFile: 'logo.svg',
            outputDir: 'public',
            publicPath: '/static',
            appName: 'My App',
            themeColor: '#123456',
            backgroundColor: '#abcdef',
            generateManifest: true,
            generateSearchPng48: false,
        );

        $generator = new WebManifestGenerator();
        $manifest = $generator->generate($options);

        $this->assertSame('My App', $manifest['name']);
        $this->assertSame('My App', $manifest['short_name']);
        $this->assertSame('#123456', $manifest['theme_color']);
        $this->assertSame('#abcdef', $manifest['background_color']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/', $manifest['start_url']);

        $this->assertCount(3, $manifest['icons']);
        $this->assertSame('/static/icon-192.png', $manifest['icons'][0]['src']);
        $this->assertSame('192x192', $manifest['icons'][0]['sizes']);

        $this->assertSame('/static/icon-maskable.png', $manifest['icons'][1]['src']);
        $this->assertSame('maskable', $manifest['icons'][1]['purpose']);

        $this->assertSame('/static/icon-512.png', $manifest['icons'][2]['src']);
    }
}
