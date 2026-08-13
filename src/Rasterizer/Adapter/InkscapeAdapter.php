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
    private ?string $binary = null;

    public function isAvailable(): bool
    {
        if (null !== $this->available) {
            return $this->available;
        }

        $this->binary = (new ExecutableFinder())->find('inkscape');
        if (null === $this->binary) {
            return $this->available = false;
        }

        try {
            $process = new Process([$this->binary, '--version']);
            $process->setTimeout(5);

            return $this->available = 0 === $process->run();
        } catch (\Throwable) {
            return $this->available = false;
        }
    }

    public function run(string $source, int $size, string $destination): void
    {
        if (!$this->isAvailable()) {
            throw new RasterizerUnavailableException('inkscape is not available.');
        }

        (new Process([
            $this->binary ?? 'inkscape',
            $source,
            '--export-type=png',
            '--export-filename='.$destination,
            '--export-width='.$size,
            '--export-height='.$size,
            '--export-background-opacity=0',
        ]))->mustRun();
    }
}
