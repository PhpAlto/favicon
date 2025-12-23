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

namespace Alto\Favicon\Rasterizer\Adapter;

use Alto\Favicon\Exception\RasterizerUnavailableException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Adapter using Inkscape CLI.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class InkscapeAdapter implements AdapterInterface
{
    private ?bool $available = null;

    public function isAvailable(): bool
    {
        return $this->available ??= null !== (new ExecutableFinder())->find('inkscape');
    }

    public function run(string $source, int $size, string $destination): void
    {
        if (!$this->isAvailable()) {
            throw new RasterizerUnavailableException('inkscape is not available.');
        }

        (new Process([
            'inkscape',
            $source,
            '--export-type=png',
            '--export-filename='.$destination,
            '--export-width='.$size,
            '--export-height='.$size,
            '--export-background-opacity=0',
        ]))->mustRun();
    }
}
