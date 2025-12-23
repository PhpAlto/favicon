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

namespace Alto\Favicon\Tests\Rasterizer;

use Alto\Favicon\Exception\RasterizerUnavailableException;
use Alto\Favicon\Rasterizer\Adapter\AdapterInterface;
use Alto\Favicon\Rasterizer\PngRasterizer;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PngRasterizer::class)]
class PngRasterizerTest extends FaviconTestCase
{
    public function testSupports(): void
    {
        $rasterizer = new PngRasterizer([]);
        $this->assertTrue($rasterizer->supports('file.png'));
        $this->assertTrue($rasterizer->supports('FILE.PNG'));
        $this->assertFalse($rasterizer->supports('file.svg'));
    }

    public function testRasterizeToPngSuccess(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('isAvailable')->willReturn(true);
        $adapter->expects($this->once())->method('run')->with('in.png', 32, 'out.png');

        $rasterizer = new PngRasterizer([$adapter]);
        $rasterizer->rasterizeToPng('in.png', 32, 'out.png');
    }

    public function testRasterizeToPngFallback(): void
    {
        $adapter1 = $this->createStub(AdapterInterface::class);
        $adapter1->method('isAvailable')->willReturn(true);
        $adapter1->method('run')->willThrowException(new \RuntimeException('Failed'));

        $adapter2 = $this->createMock(AdapterInterface::class);
        $adapter2->method('isAvailable')->willReturn(true);
        $adapter2->expects($this->once())->method('run')->with('in.png', 32, 'out.png');

        $rasterizer = new PngRasterizer([$adapter1, $adapter2]);
        $rasterizer->rasterizeToPng('in.png', 32, 'out.png');
    }

    public function testRasterizeToPngNoAdapter(): void
    {
        $this->expectException(RasterizerUnavailableException::class);

        $rasterizer = new PngRasterizer([]);
        $rasterizer->rasterizeToPng('in.png', 32, 'out.png');
    }
}
