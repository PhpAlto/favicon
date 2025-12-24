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

use Alto\Favicon\Rasterizer\Adapter\ImagickCliAdapter;
use Alto\Favicon\Rasterizer\Adapter\ImagickExtensionAdapter;
use Alto\Favicon\Rasterizer\Adapter\InkscapeAdapter;
use Alto\Favicon\Rasterizer\Adapter\RsvgAdapter;
use PHPUnit\Framework\TestCase;

abstract class FaviconTestCase extends TestCase
{
    protected function getFixturesDir(): string
    {
        return __DIR__.'/Fixtures';
    }

    protected function getOutputDir(): string
    {
        return __DIR__.'/../var/test_output';
    }

    protected function setUp(): void
    {
        if (!is_dir($this->getOutputDir())) {
            mkdir($this->getOutputDir(), 0777, true);
        }
    }

    protected function cleanupDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($dir);
    }

    protected function tearDown(): void
    {
        // Optional: clean up output dir
    }

    protected function requireSvgRasterizer(): void
    {
        $adapters = [
            new RsvgAdapter(),
            new InkscapeAdapter(),
            new ImagickCliAdapter(),
            new ImagickExtensionAdapter(),
        ];

        foreach ($adapters as $adapter) {
            if ($adapter->isAvailable()) {
                return;
            }
        }

        $this->markTestSkipped('No suitable SVG rasterizer found.');
    }
}
