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

namespace Alto\Favicon\Generator;

use Alto\Favicon\Exception\FaviconException;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
class IcoGenerator
{
    /**
     * Generate a 32x32 .ico file from a 32x32 PNG file.
     */
    public function generateFromPng32(string $pngFile, string $targetIcoFile): void
    {
        $png = @file_get_contents($pngFile);

        if (false === $png) {
            throw new FaviconException(sprintf('Cannot read PNG for ICO: %s', $pngFile));
        }

        $iconDir = pack('vvv', 0, 1, 1);

        $width = 32;
        $height = 32;

        $bytes = strlen($png);
        $offset = 6 + 16;

        $entry = pack(
            'CCCCvvVV',
            $width,
            $height,
            0,
            0,
            1,
            32,
            $bytes,
            $offset
        );

        $ico = $iconDir.$entry.$png;

        if (false === @file_put_contents($targetIcoFile, $ico)) {
            throw new FaviconException(sprintf('Cannot write ICO: %s', $targetIcoFile));
        }
    }
}
