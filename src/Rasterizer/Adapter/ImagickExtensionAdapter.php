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
 * Adapter using the Imagick extension.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class ImagickExtensionAdapter implements AdapterInterface
{
    private ?bool $available = null;

    public function isAvailable(): bool
    {
        return $this->available ??= extension_loaded('imagick');
    }

    public function run(string $source, int $size, string $destination): void
    {
        if (!$this->isAvailable()) {
            throw new RasterizerUnavailableException('Imagick extension is not available.');
        }

        try {
            $imagick = new \Imagick($source);
            $imagick->resizeImage($size, $size, \Imagick::FILTER_LANCZOS, 1, true);
            $imagick->writeImage($destination);
            $imagick->clear();
            $imagick->destroy();
        } catch (\Throwable $e) {
            throw new FaviconException(sprintf('Imagick failed to resize PNG: %s', $e->getMessage()), 0, $e);
        }
    }
}
