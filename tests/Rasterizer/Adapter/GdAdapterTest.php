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

namespace Alto\Favicon\Tests\Rasterizer\Adapter;

use Alto\Favicon\Exception\FaviconException;
use Alto\Favicon\Exception\RasterizerUnavailableException;
use Alto\Favicon\Rasterizer\Adapter\GdAdapter;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

#[CoversClass(GdAdapter::class)]
#[RequiresPhpExtension('gd')]
class GdAdapterTest extends FaviconTestCase
{
    public static bool $failColorAllocation = false;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__.'/GdAdapterMock.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$failColorAllocation = false;
    }

    public function testRun(): void
    {
        $adapter = new GdAdapter();

        if (!$adapter->isAvailable()) {
            $this->markTestSkipped('GD extension not loaded.');
        }

        $source = $this->getOutputDir().'/source.png';
        $dest = $this->getOutputDir().'/dest.png';

        // Create source PNG
        $im = imagecreatetruecolor(100, 100);
        imagepng($im, $source);

        $adapter->run($source, 50, $dest);

        $this->assertFileExists($dest);

        $info = getimagesize($dest);
        $this->assertSame(50, $info[0]);
        $this->assertSame(50, $info[1]);
    }

    public function testReadFailure(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('Cannot read PNG');

        $adapter = new GdAdapter();
        $adapter->run('non_existent.png', 32, 'out.png');
    }

    public function testInvalidSize(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('Target size must be at least 1px');

        $source = $this->getOutputDir().'/source_size.png';
        $im = imagecreatetruecolor(100, 100);
        imagepng($im, $source);

        $adapter = new GdAdapter();
        $adapter->run($source, 0, 'out.png');
    }

    public function testWriteFailure(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('Cannot write PNG');

        $source = $this->getOutputDir().'/source_write.png';
        $im = imagecreatetruecolor(100, 100);
        imagepng($im, $source);

        $dest = $this->getOutputDir().'/blocked_dir';
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        $adapter = new GdAdapter();
        $adapter->run($source, 32, $dest);
    }

    public function testUnavailable(): void
    {
        $this->expectException(RasterizerUnavailableException::class);
        $this->expectExceptionMessage('GD extension is not available');

        $adapter = new GdAdapter();

        $reflection = new \ReflectionClass($adapter);
        $property = $reflection->getProperty('available');
        $property->setValue($adapter, false);

        $adapter->run('in.png', 32, 'out.png');
    }

    public function testColorAllocationFailure(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('Could not allocate transparent color');

        self::$failColorAllocation = true;

        $source = $this->getOutputDir().'/source_alloc.png';
        $im = imagecreatetruecolor(100, 100);
        imagepng($im, $source);

        $adapter = new GdAdapter();
        $adapter->run($source, 32, 'out.png');
    }
}
