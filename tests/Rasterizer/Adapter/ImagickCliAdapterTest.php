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

use Alto\Favicon\Exception\RasterizerUnavailableException;
use Alto\Favicon\Rasterizer\Adapter\ImagickCliAdapter;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Process\Exception\ProcessFailedException;

#[CoversClass(ImagickCliAdapter::class)]
class ImagickCliAdapterTest extends FaviconTestCase
{
    public function testRun(): void
    {
        $adapter = new ImagickCliAdapter();

        if (!$adapter->isAvailable()) {
            $this->markTestSkipped('magick/convert not found.');
        }

        $source = $this->getFixturesDir().'/logo.svg';
        $dest = $this->getOutputDir().'/dest_magick_cli.png';

        $adapter->run($source, 50, $dest);

        $this->assertFileExists($dest);

        $info = getimagesize($dest);
        $this->assertSame(50, $info[0]);
        $this->assertSame(50, $info[1]);
    }

    public function testRunFailure(): void
    {
        $adapter = new ImagickCliAdapter();

        if (!$adapter->isAvailable()) {
            $this->markTestSkipped('magick/convert not found.');
        }

        $this->expectException(ProcessFailedException::class);

        $adapter->run('non_existent.svg', 50, 'out.png');
    }

    public function testUnavailable(): void
    {
        $this->expectException(RasterizerUnavailableException::class);
        $this->expectExceptionMessage('ImageMagick CLI (magick or convert) is not available');

        $adapter = new ImagickCliAdapter();

        $reflection = new \ReflectionClass($adapter);

        $propBinary = $reflection->getProperty('binary');
        $propBinary->setValue($adapter, null);

        $propChecked = $reflection->getProperty('checked');
        $propChecked->setValue($adapter, true);

        $adapter->run('in.svg', 32, 'out.png');
    }
}
