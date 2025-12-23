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

use Alto\Favicon\Exception\FaviconException;
use Alto\Favicon\Exception\RasterizerUnavailableException;

/**
 * Adapter using the GD extension.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class GdAdapter implements AdapterInterface
{
    private ?bool $available = null;

    public function isAvailable(): bool
    {
        return $this->available ??= extension_loaded('gd');
    }

    public function run(string $source, int $size, string $destination): void
    {
        if (!$this->isAvailable()) {
            throw new RasterizerUnavailableException('GD extension is not available.');
        }

        $src = @imagecreatefrompng($source);

        if (!$src) {
            throw new FaviconException(sprintf('Cannot read PNG: %s', $source));
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        if ($size < 1) {
            throw new FaviconException('Target size must be at least 1px.');
        }

        $dst = imagecreatetruecolor($size, $size);

        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);

        if (false === $transparent) {
            throw new FaviconException('Could not allocate transparent color.');
        }

        imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);

        $scale = min($size / $srcW, $size / $srcH);
        $newW = (int) floor($srcW * $scale);
        $newH = (int) floor($srcH * $scale);

        $dstX = (int) floor(($size - $newW) / 2);
        $dstY = (int) floor(($size - $newH) / 2);

        imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);

        if (!@imagepng($dst, $destination, 9)) {
            throw new FaviconException(sprintf('Cannot write PNG: %s', $destination));
        }
    }
}
