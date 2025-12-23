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

namespace Alto\Favicon\Rasterizer;

/**
 * Interface for rasterizing input files to PNG.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
interface RasterizerInterface
{
    /**
     * Check if the rasterizer supports the given input file.
     */
    public function supports(string $inputFile): bool;

    /**
     * Rasterize the input file to a PNG file of the given size.
     */
    public function rasterizeToPng(string $inputFile, int $size, string $targetPngFile): void;
}
