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

namespace Alto\Favicon\Tests\Generator;

use Alto\Favicon\Exception\FaviconException;
use Alto\Favicon\Generator\IcoGenerator;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(IcoGenerator::class)]
class IcoGeneratorTest extends FaviconTestCase
{
    public function testGenerateFromPng32(): void
    {
        // Create a dummy 32x32 PNG
        $pngFile = $this->getOutputDir().'/dummy_32.png';
        $icoFile = $this->getOutputDir().'/favicon.ico';

        $im = imagecreatetruecolor(32, 32);
        imagepng($im, $pngFile);

        $generator = new IcoGenerator();
        $generator->generateFromPng32($pngFile, $icoFile);

        $this->assertFileExists($icoFile);
        $this->assertGreaterThan(0, filesize($icoFile));

        // Basic ICO header check (00 00 01 00)
        $handle = fopen($icoFile, 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        $this->assertSame(pack('vv', 0, 1), $header);
    }

    public function testReadFailure(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('Cannot read PNG for ICO');

        $generator = new IcoGenerator();
        $generator->generateFromPng32('non_existent.png', 'output.ico');
    }

    public function testWriteFailure(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('Cannot write ICO');

        // Create a dummy PNG
        $pngFile = $this->getOutputDir().'/dummy_write_fail.png';
        $im = imagecreatetruecolor(32, 32);
        imagepng($im, $pngFile);

        // Create a directory where the file should be to block writing
        $icoFile = $this->getOutputDir().'/blocked_ico';
        if (!is_dir($icoFile)) {
            mkdir($icoFile);
        }

        $generator = new IcoGenerator();
        $generator->generateFromPng32($pngFile, $icoFile);
    }
}
