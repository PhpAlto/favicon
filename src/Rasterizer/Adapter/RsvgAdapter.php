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
 * Adapter using rsvg-convert CLI.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class RsvgAdapter implements AdapterInterface
{
    private ?bool $available = null;

    public function isAvailable(): bool
    {
        return $this->available ??= null !== (new ExecutableFinder())->find('rsvg-convert');
    }

    public function run(string $source, int $size, string $destination): void
    {
        if (!$this->isAvailable()) {
            throw new RasterizerUnavailableException('rsvg-convert is not available.');
        }

        (new Process(['rsvg-convert', '-w', (string) $size, '-h', (string) $size, $source, '-o', $destination]))
            ->mustRun();
    }
}
