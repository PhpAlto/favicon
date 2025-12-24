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

namespace Alto\Favicon\Tests\Functional;

use Alto\Favicon\Command\GenerateFaviconCommand;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(GenerateFaviconCommand::class)]
class FaviconCommandTest extends FaviconTestCase
{
    public function testGenerateFromSvg(): void
    {
        $this->requireSvgRasterizer();

        $application = new Application();
        $application->addCommand(new GenerateFaviconCommand());

        $command = $application->find(GenerateFaviconCommand::NAME);
        $commandTester = new CommandTester($command);

        $inputFile = $this->getFixturesDir().'/logo.svg';
        $outputDir = $this->getOutputDir().'/functional_svg';

        $commandTester->execute([
            'input' => $inputFile,
            '--output' => $outputDir,
            '--no-interaction' => true,
            '--force' => true,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Output: '.$outputDir, $output);

        $this->assertFileExists($outputDir.'/favicon.ico');
        $this->assertFileExists($outputDir.'/icon.svg');
        $this->assertFileExists($outputDir.'/apple-touch-icon.png');
        $this->assertFileExists($outputDir.'/favicon.html');
    }

    public function testGenerateFromPng(): void
    {
        $application = new Application();
        $application->addCommand(new GenerateFaviconCommand());

        $command = $application->find(GenerateFaviconCommand::NAME);
        $commandTester = new CommandTester($command);

        // Create a dummy PNG source
        $inputFile = $this->getOutputDir().'/source.png';
        $im = imagecreatetruecolor(512, 512);
        imagepng($im, $inputFile);

        $outputDir = $this->getOutputDir().'/functional_png';

        $commandTester->execute([
            'input' => $inputFile,
            '--output' => $outputDir,
            '--no-interaction' => true,
            '--force' => true,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Output: '.$outputDir, $output);

        $this->assertFileExists($outputDir.'/favicon.ico');
        $this->assertFileDoesNotExist($outputDir.'/icon.svg');
        $this->assertFileExists($outputDir.'/favicon-32x32.png');
        $this->assertFileExists($outputDir.'/favicon-16x16.png');
        $this->assertFileExists($outputDir.'/apple-touch-icon.png');
        $this->assertFileExists($outputDir.'/favicon.html');
    }
}
