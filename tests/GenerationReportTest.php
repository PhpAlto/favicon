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

use Alto\Favicon\GenerationReport;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GenerationReport::class)]
class GenerationReportTest extends TestCase
{
    public function testReport(): void
    {
        $report = new GenerationReport(
            outputDir: 'out',
            publicPath: '/',
            files: [
                'a' => 100,
                'b' => 200,
            ],
            htmlSnippet: '',
            manifestFile: null,
        );

        $this->assertSame('out', $report->outputDir);
        $this->assertSame('/', $report->publicPath);
        $this->assertCount(2, $report->files);
        $this->assertSame(100, $report->files['a']);
        $this->assertSame(200, $report->files['b']);
        $this->assertNull($report->manifestFile);
    }
}
