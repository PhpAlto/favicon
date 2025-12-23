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

/**
 * Interface for rasterization adapters.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
interface AdapterInterface
{
    /**
     * Check if the adapter is available on the system.
     */
    public function isAvailable(): bool;

    /**
     * Run the rasterization process.
     */
    public function run(string $source, int $size, string $destination): void;
}
