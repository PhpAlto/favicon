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

namespace Alto\Favicon\Tests;

use Alto\Favicon\Generator\FaviconGenerator;
use Alto\Favicon\Options\FaviconOptionsBuilder;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FaviconGenerator::class)]
class FaviconGeneratorTest extends FaviconTestCase
{
    public function testGenerateFromSvg(): void
    {
        $this->requireSvgRasterizer();

        $inputFile = $this->getFixturesDir().'/logo.svg';
        $outputDir = $this->getOutputDir().'/svg_test';
        $this->cleanupDir($outputDir);

        $options = (new FaviconOptionsBuilder($inputFile, $outputDir))
            ->generateManifest(true)
            ->build();

        $generator = new FaviconGenerator();
        $report = $generator->generate($options);

        $this->assertFileExists($outputDir.'/favicon.ico');
        $this->assertFileExists($outputDir.'/icon.svg');
        $this->assertFileExists($outputDir.'/apple-touch-icon.png');
        $this->assertFileExists($outputDir.'/manifest.webmanifest');

        // Check report
        $this->assertArrayHasKey('favicon.ico', $report->files);
        $this->assertSame('created', $report->files['favicon.ico']);
        $this->assertSame('created', $report->files['icon.svg']);
        $this->assertStringContainsString('<link rel="icon" href="/favicon.ico" sizes="32x32">', $report->htmlSnippet);
    }

    public function testGenerateFromPng(): void
    {
        $inputFile = $this->getOutputDir().'/logo.png';
        $outputDir = $this->getOutputDir().'/png_test';
        $this->cleanupDir($outputDir);

        // Create dummy PNG
        $im = imagecreatetruecolor(512, 512);
        imagepng($im, $inputFile);

        $options = (new FaviconOptionsBuilder($inputFile, $outputDir))
            ->generateManifest(true)
            ->generateSearchPng48(true)
            ->build();

        $generator = new FaviconGenerator();
        $report = $generator->generate($options);

        $this->assertFileExists($outputDir.'/favicon.ico');
        $this->assertFileDoesNotExist($outputDir.'/icon.svg');
        $this->assertFileExists($outputDir.'/favicon-32x32.png');
        $this->assertFileExists($outputDir.'/favicon-16x16.png');
        $this->assertFileExists($outputDir.'/favicon-48x48.png');
        $this->assertFileExists($outputDir.'/apple-touch-icon.png');
        $this->assertFileExists($outputDir.'/manifest.webmanifest');

        $this->assertSame('created', $report->files['favicon.ico']);
        $this->assertSame('created', $report->files['favicon-32x32.png']);
        $this->assertSame('created', $report->files['favicon-16x16.png']);
        $this->assertSame('created', $report->files['favicon-48x48.png']);
        $this->assertSame('created', $report->files['apple-touch-icon.png']);
        $this->assertSame('created', $report->files['manifest.webmanifest']);
    }

    public function testMissingInputFile(): void
    {
        $this->expectException(\Alto\Favicon\Exception\MissingInputFileException::class);

        $options = (new FaviconOptionsBuilder('missing.svg', 'output'))->build();
        (new FaviconGenerator())->generate($options);
    }

    public function testSkipExistingFiles(): void
    {
        $this->requireSvgRasterizer();

        $inputFile = $this->getFixturesDir().'/logo.svg';
        $outputDir = $this->getOutputDir().'/skip_test';
        $this->cleanupDir($outputDir);

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Create a dummy file that should NOT be overwritten
        file_put_contents($outputDir.'/favicon.ico', 'dummy content');
        $originalMtime = filemtime($outputDir.'/favicon.ico');

        // Wait a second to ensure mtime difference if overwritten
        sleep(1);

        $options = (new FaviconOptionsBuilder($inputFile, $outputDir))
            ->force(false)
            ->build();

        $generator = new FaviconGenerator();
        $report = $generator->generate($options);

        $this->assertStringEqualsFile($outputDir.'/favicon.ico', 'dummy content');
        $this->assertEquals($originalMtime, filemtime($outputDir.'/favicon.ico'));
        $this->assertSame('skipped', $report->files['favicon.ico']);
    }

    public function testForceOverwrite(): void
    {
        $this->requireSvgRasterizer();

        $inputFile = $this->getFixturesDir().'/logo.svg';
        $outputDir = $this->getOutputDir().'/force_test';
        $this->cleanupDir($outputDir);

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Create a dummy file that SHOULD be overwritten
        file_put_contents($outputDir.'/favicon.ico', 'dummy content');

        $options = (new FaviconOptionsBuilder($inputFile, $outputDir))
            ->force(true)
            ->build();

        $generator = new FaviconGenerator();
        $report = $generator->generate($options);

        $this->assertStringNotEqualsFile($outputDir.'/favicon.ico', 'dummy content');
        $this->assertSame('created', $report->files['favicon.ico']);
    }

    public function testSkipAllExistingFiles(): void
    {
        $inputFile = $this->getOutputDir().'/logo.png';
        $outputDir = $this->getOutputDir().'/skip_all_test';
        $this->cleanupDir($outputDir);

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Create dummy PNG input
        $im = imagecreatetruecolor(512, 512);
        imagepng($im, $inputFile);

        // Create all expected files
        $files = [
            // 'icon.svg', // Not generated for PNG input
            'favicon.ico',
            'favicon-32x32.png',
            'favicon-16x16.png',
            'favicon-48x48.png',
            'apple-touch-icon.png',
            'icon-192.png',
            'icon-512.png',
            'icon-maskable.png',
            'manifest.webmanifest',
            'favicon.html',
        ];

        foreach ($files as $file) {
            file_put_contents($outputDir.'/'.$file, 'dummy content');
        }

        $options = (new FaviconOptionsBuilder($inputFile, $outputDir))
            ->generateManifest(true)
            ->generateSearchPng48(true)
            ->force(false)
            ->build();

        $generator = new FaviconGenerator();
        $report = $generator->generate($options);

        foreach ($files as $file) {
            $this->assertArrayHasKey($file, $report->files);
            $this->assertSame('skipped', $report->files[$file], "File $file should be skipped");
        }
    }
}
