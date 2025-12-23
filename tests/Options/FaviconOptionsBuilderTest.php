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

use Alto\Favicon\Options\FaviconOptionsBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FaviconOptionsBuilder::class)]
class FaviconOptionsBuilderTest extends TestCase
{
    public function testBuildDefaults(): void
    {
        $options = (new FaviconOptionsBuilder('input.svg', 'output'))->build();

        $this->assertSame('input.svg', $options->inputFile);
        $this->assertSame('output', $options->outputDir);
        $this->assertSame('/', $options->publicPath);
        $this->assertSame('App', $options->appName);
        $this->assertSame('#0b0b0b', $options->themeColor);
        $this->assertSame('#ffffff', $options->backgroundColor);
        $this->assertFalse($options->generateManifest);
        $this->assertFalse($options->generateSearchPng48);
    }

    public function testBuildCustom(): void
    {
        $options = (new FaviconOptionsBuilder('input.svg', 'output'))
            ->publicPath('/assets/')
            ->appName('My App')
            ->themeColor('#ff0000')
            ->backgroundColor('#000000')
            ->generateManifest(true)
            ->generateSearchPng48(true)
            ->build();

        $this->assertSame('/assets/', $options->publicPath);
        $this->assertSame('My App', $options->appName);
        $this->assertSame('#ff0000', $options->themeColor);
        $this->assertSame('#000000', $options->backgroundColor);
        $this->assertTrue($options->generateManifest);
        $this->assertTrue($options->generateSearchPng48);
    }

    public function testForce(): void
    {
        $options = (new FaviconOptionsBuilder('in', 'out'))->force(true)->build();
        $this->assertTrue($options->force);

        $options = (new FaviconOptionsBuilder('in', 'out'))->force(false)->build();
        $this->assertFalse($options->force);
    }

    public function testInvalidThemeColor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Color "red" is invalid');

        (new FaviconOptionsBuilder('in', 'out'))->themeColor('red');
    }

    public function testInvalidBackgroundColor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Color "#123" is invalid');

        (new FaviconOptionsBuilder('in', 'out'))->backgroundColor('#123');
    }
}
