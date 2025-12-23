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
 * Adapter using ImageMagick CLI (magick or convert).
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class ImagickCliAdapter implements AdapterInterface
{
    private ?string $binary = null;
    private bool $checked = false;

    public function isAvailable(): bool
    {
        if (!$this->checked) {
            $finder = new ExecutableFinder();
            $this->binary = $finder->find('magick') ?? $finder->find('convert');
            $this->checked = true;
        }

        return null !== $this->binary;
    }

    public function run(string $source, int $size, string $destination): void
    {
        if (null === $this->binary && !$this->isAvailable()) {
            throw new RasterizerUnavailableException('ImageMagick CLI (magick or convert) is not available.');
        }

        (new Process([
            $this->binary,
            $source,
            '-background', 'none',
            '-resize', $size.'x'.$size,
            $destination,
        ]))->mustRun();
    }
}
