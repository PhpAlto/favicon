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

namespace Alto\Favicon\Tests\Exception;

use Alto\Favicon\Exception\FaviconException;
use Alto\Favicon\Exception\RasterizerUnavailableException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RasterizerUnavailableException::class)]
class RasterizerUnavailableExceptionTest extends TestCase
{
    public function testConstructorDefaults(): void
    {
        $exception = new RasterizerUnavailableException();

        $this->assertInstanceOf(FaviconException::class, $exception);
        $this->assertSame('No rasterizer available on this system.', $exception->getMessage());
    }

    public function testConstructorCustomMessage(): void
    {
        $message = 'Custom error message';
        $exception = new RasterizerUnavailableException($message);

        $this->assertSame($message, $exception->getMessage());
    }
}
