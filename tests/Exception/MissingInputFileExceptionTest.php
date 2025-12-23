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

use Alto\Favicon\Exception\MissingInputFileException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MissingInputFileException::class)]
class MissingInputFileExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $exception = new MissingInputFileException('/path/to/missing/file.svg');

        $this->assertSame('Input file not found: /path/to/missing/file.svg', $exception->getMessage());
    }
}
