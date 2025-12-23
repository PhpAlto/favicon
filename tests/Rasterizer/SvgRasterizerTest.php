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
use Alto\Favicon\Rasterizer\SvgRasterizer;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SvgRasterizer::class)]
class SvgRasterizerTest extends FaviconTestCase
{
    public function testSupports(): void
    {
        $rasterizer = new SvgRasterizer([]);
        $this->assertTrue($rasterizer->supports('file.svg'));
        $this->assertTrue($rasterizer->supports('FILE.SVG'));
        $this->assertFalse($rasterizer->supports('file.png'));
    }

    public function testRasterizeToPngSuccess(): void
    {
        $adapter1 = $this->createStub(AdapterInterface::class);
        $adapter1->method('isAvailable')->willReturn(false);

        $adapter2 = $this->createMock(AdapterInterface::class);
        $adapter2->method('isAvailable')->willReturn(true);
        $adapter2->expects($this->once())->method('run')->with('in.svg', 32, 'out.png');

        $rasterizer = new SvgRasterizer([$adapter1, $adapter2]);
        $rasterizer->rasterizeToPng('in.svg', 32, 'out.png');
    }

    public function testRasterizeToPngFallback(): void
    {
        $adapter1 = $this->createStub(AdapterInterface::class);
        $adapter1->method('isAvailable')->willReturn(true);
        $adapter1->method('run')->willThrowException(new \RuntimeException('Failed'));

        $adapter2 = $this->createMock(AdapterInterface::class);
        $adapter2->method('isAvailable')->willReturn(true);
        $adapter2->expects($this->once())->method('run');

        $rasterizer = new SvgRasterizer([$adapter1, $adapter2]);
        $rasterizer->rasterizeToPng('in.svg', 32, 'out.png');
    }

    public function testRasterizeToPngNoAdapter(): void
    {
        $this->expectException(RasterizerUnavailableException::class);

        $rasterizer = new SvgRasterizer([]);
        $rasterizer->rasterizeToPng('in.svg', 32, 'out.png');
    }
}
