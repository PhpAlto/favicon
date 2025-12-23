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
use Alto\Favicon\Rasterizer\Adapter\InkscapeAdapter;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InkscapeAdapter::class)]
class InkscapeAdapterTest extends FaviconTestCase
{
    public function testRun(): void
    {
        $adapter = new InkscapeAdapter();

        if (!$adapter->isAvailable()) {
            $this->markTestSkipped('inkscape not found.');
        }

        $source = $this->getFixturesDir().'/logo.svg';
        $dest = $this->getOutputDir().'/dest_inkscape.png';

        $adapter->run($source, 50, $dest);

        $this->assertFileExists($dest);

        $info = getimagesize($dest);
        $this->assertSame(50, $info[0]);
        $this->assertSame(50, $info[1]);
    }

    public function testUnavailable(): void
    {
        $this->expectException(RasterizerUnavailableException::class);
        $this->expectExceptionMessage('inkscape is not available');

        $adapter = new InkscapeAdapter();

        $reflection = new \ReflectionClass($adapter);
        $property = $reflection->getProperty('available');
        $property->setValue($adapter, false);

        $adapter->run('in.svg', 32, 'out.png');
    }
}
