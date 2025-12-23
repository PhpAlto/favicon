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

namespace Alto\Favicon\Exception;

/**
 * Exception thrown when no suitable rasterizer is available.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class RasterizerUnavailableException extends FaviconException
{
    public function __construct(string $message = 'No rasterizer available on this system.')
    {
        parent::__construct($message);
    }
}
