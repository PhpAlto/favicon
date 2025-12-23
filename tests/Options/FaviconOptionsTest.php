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

namespace Alto\Favicon\Tests\Options;

use Alto\Favicon\Options\FaviconOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FaviconOptions::class)]
class FaviconOptionsTest extends TestCase
{
    public function testPublicAsset(): void
    {
        $options = new FaviconOptions(
            inputFile: 'input',
            outputDir: 'output',
            publicPath: '/assets/',
            appName: 'App',
            themeColor: '#000',
            backgroundColor: '#fff',
            generateManifest: true,
            generateSearchPng48: true,
        );

        $this->assertSame('/assets/file.png', $options->publicAsset('file.png'));
    }

    public function testPublicAssetRoot(): void
    {
        $options = new FaviconOptions(
            inputFile: 'input',
            outputDir: 'output',
            publicPath: '/',
            appName: 'App',
            themeColor: '#000',
            backgroundColor: '#fff',
            generateManifest: true,
            generateSearchPng48: true,
        );

        $this->assertSame('/file.png', $options->publicAsset('file.png'));
    }
}
