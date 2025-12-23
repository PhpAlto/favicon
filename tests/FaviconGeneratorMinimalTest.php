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

use Alto\Favicon\Generator\FaviconGenerator;
use Alto\Favicon\Options\FaviconOptionsBuilder;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FaviconGenerator::class)]
class FaviconGeneratorMinimalTest extends FaviconTestCase
{
    public function testGenerateMinimalSet(): void
    {
        $inputFile = $this->getFixturesDir().'/logo.svg';
        $outputDir = $this->getOutputDir().'/minimal_test';

        $options = (new FaviconOptionsBuilder($inputFile, $outputDir))
            ->build();

        $generator = new FaviconGenerator();
        $report = $generator->generate($options);

        // Should exist
        $this->assertFileExists($outputDir.'/favicon.ico');
        $this->assertFileExists($outputDir.'/icon.svg');
        $this->assertFileExists($outputDir.'/apple-touch-icon.png');

        // Should NOT exist
        $this->assertFileDoesNotExist($outputDir.'/manifest.webmanifest');
        $this->assertFileDoesNotExist($outputDir.'/icon-192.png');
        $this->assertFileDoesNotExist($outputDir.'/icon-512.png');
        $this->assertFileDoesNotExist($outputDir.'/favicon-48x48.png');
    }
}
