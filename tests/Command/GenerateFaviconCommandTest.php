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

namespace Alto\Favicon\Tests\Command;

use Alto\Favicon\Command\GenerateFaviconCommand;
use Alto\Favicon\Tests\FaviconTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(GenerateFaviconCommand::class)]
class GenerateFaviconCommandTest extends FaviconTestCase
{
    public function testExecute(): void
    {
        $this->requireSvgRasterizer();

        $application = new Application();
        $application->addCommand(new GenerateFaviconCommand());

        $command = $application->find(GenerateFaviconCommand::NAME);
        $commandTester = new CommandTester($command);

        $inputFile = $this->getFixturesDir().'/logo.svg';
        $outputDir = $this->getOutputDir().'/command_test';

        $commandTester->execute([
            'input' => $inputFile,
            '--output' => $outputDir,
            '--no-interaction' => true,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Output: '.$outputDir, $output);
        $this->assertStringContainsString('HTML snippet', $output);

        $this->assertFileExists($outputDir.'/favicon.ico');
    }
}
