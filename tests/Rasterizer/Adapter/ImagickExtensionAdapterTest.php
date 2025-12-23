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
use Alto\Favicon\Rasterizer\Adapter\ImagickExtensionAdapter;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

#[CoversClass(ImagickExtensionAdapter::class)]
#[RequiresPhpExtension('imagick')]
class ImagickExtensionAdapterTest extends FaviconTestCase
{
    public function testRun(): void
    {
        $adapter = new ImagickExtensionAdapter();

        if (!$adapter->isAvailable()) {
            $this->markTestSkipped('Imagick extension not loaded.');
        }

        $source = $this->getOutputDir().'/source_imagick.png';
        $dest = $this->getOutputDir().'/dest_imagick.png';

        // Create source PNG using GD just for setup
        if (extension_loaded('gd')) {
            $im = imagecreatetruecolor(100, 100);
            imagepng($im, $source);
        } else {
            // Fallback if GD is missing but Imagick is present (unlikely dev setup but possible)
            $im = new \Imagick();
            $im->newImage(100, 100, new \ImagickPixel('white'));
            $im->setImageFormat('png');
            $im->writeImage($source);
        }

        $adapter->run($source, 50, $dest);

        $this->assertFileExists($dest);

        $info = getimagesize($dest);
        $this->assertSame(50, $info[0]);
        $this->assertSame(50, $info[1]);
    }

    public function testResizeFailure(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('Imagick failed to resize PNG');

        $adapter = new ImagickExtensionAdapter();
        $adapter->run('non_existent.png', 32, 'out.png');
    }

    public function testUnavailable(): void
    {
        $this->expectException(RasterizerUnavailableException::class);
        $this->expectExceptionMessage('Imagick extension is not available');

        $adapter = new ImagickExtensionAdapter();

        $reflection = new \ReflectionClass($adapter);
        $property = $reflection->getProperty('available');
        $property->setValue($adapter, false);

        $adapter->run('in.png', 32, 'out.png');
    }
}
