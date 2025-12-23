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
use Alto\Favicon\Generator\FaviconGenerator;
use Alto\Favicon\Generator\IcoGenerator;
use Alto\Favicon\Options\FaviconOptionsBuilder;
use Alto\Favicon\Rasterizer\RasterizerInterface;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

#[CoversClass(FaviconGenerator::class)]
class FaviconGeneratorCoverageTest extends FaviconTestCase
{
    public function testUnsupportedInputFile(): void
    {
        $this->expectException(FaviconException::class);
        $this->expectExceptionMessage('No rasterizer supports input file');

        $outputDir = $this->getOutputDir().'/coverage_unsupported';
        $options = (new FaviconOptionsBuilder($this->getFixturesDir().'/logo.svg', $outputDir))->build();

        $rasterizer = $this->createStub(RasterizerInterface::class);
        $rasterizer->method('supports')->willReturn(false);

        $generator = new FaviconGenerator(rasterizers: [$rasterizer]);
        $generator->generate($options);
    }

    public function testMkdirFailure(): void
    {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('Failed');

        $filesystem = $this->createStub(Filesystem::class);
        $filesystem->method('mkdir')->willThrowException(new IOException('Failed'));

        $outputDir = $this->getOutputDir().'/coverage_mkdir';
        $options = (new FaviconOptionsBuilder($this->getFixturesDir().'/logo.svg', $outputDir))->build();

        $generator = new FaviconGenerator(filesystem: $filesystem);
        $generator->generate($options);
    }

    public function testCopyFailure(): void
    {
        $this->expectException(IOException::class);

        $filesystem = $this->createStub(Filesystem::class);
        $filesystem->method('copy')->willThrowException(new IOException('Failed'));

        $outputDir = $this->getOutputDir().'/coverage_copy';
        $options = (new FaviconOptionsBuilder($this->getFixturesDir().'/logo.svg', $outputDir))->build();

        $generator = new FaviconGenerator(filesystem: $filesystem);
        $generator->generate($options);
    }

    public function testMoveFailure(): void
    {
        $this->expectException(IOException::class);

        $filesystem = $this->createStub(Filesystem::class);
        // mkdir works
        $filesystem->method('mkdir');
        // rasterize works (we need a mock rasterizer to avoid real processing)
        $rasterizer = $this->createStub(RasterizerInterface::class);
        $rasterizer->method('supports')->willReturn(true);

        // rename fails
        $filesystem->method('rename')->willThrowException(new IOException('Failed'));

        // Mock IcoGenerator to avoid file operations
        $icoGenerator = $this->createStub(IcoGenerator::class);

        // Use PNG input to trigger the "move" logic (renaming .tmp-32.png to favicon-32x32.png)
        // We need a dummy file that exists for the initial check
        $dummyPng = $this->getOutputDir().'/dummy.png';
        touch($dummyPng);

        $outputDir = $this->getOutputDir().'/coverage_move';
        $options = (new FaviconOptionsBuilder($dummyPng, $outputDir))->build();

        $generator = new FaviconGenerator(
            rasterizers: [$rasterizer],
            icoGenerator: $icoGenerator,
            filesystem: $filesystem
        );
        $generator->generate($options);
    }

    public function testRemoveFailure(): void
    {
        $this->expectException(IOException::class);

        $filesystem = $this->createStub(Filesystem::class);
        $rasterizer = $this->createStub(RasterizerInterface::class);
        $rasterizer->method('supports')->willReturn(true);

        // remove fails
        $filesystem->method('remove')->willThrowException(new IOException('Failed'));

        // Mock IcoGenerator
        $icoGenerator = $this->createStub(IcoGenerator::class);

        // Use SVG input to trigger the "remove" logic (removing .tmp-32.png)
        $outputDir = $this->getOutputDir().'/coverage_remove';
        $options = (new FaviconOptionsBuilder($this->getFixturesDir().'/logo.svg', $outputDir))->build();

        $generator = new FaviconGenerator(
            rasterizers: [$rasterizer],
            icoGenerator: $icoGenerator,
            filesystem: $filesystem
        );
        $generator->generate($options);
    }

    public function testDumpFailure(): void
    {
        $this->expectException(IOException::class);

        $filesystem = $this->createStub(Filesystem::class);
        $rasterizer = $this->createStub(RasterizerInterface::class);
        $rasterizer->method('supports')->willReturn(true);

        // dumpFile fails
        $filesystem->method('dumpFile')->willThrowException(new IOException('Failed'));

        // Mock IcoGenerator
        $icoGenerator = $this->createStub(IcoGenerator::class);

        $outputDir = $this->getOutputDir().'/coverage_dump';
        $options = (new FaviconOptionsBuilder($this->getFixturesDir().'/logo.svg', $outputDir))->build();

        $generator = new FaviconGenerator(
            rasterizers: [$rasterizer],
            icoGenerator: $icoGenerator,
            filesystem: $filesystem
        );
        $generator->generate($options);
    }
}
