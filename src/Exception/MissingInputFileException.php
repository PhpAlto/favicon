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
 * Exception thrown when the input file does not exist.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class MissingInputFileException extends FaviconException
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf('Input file not found: %s', $path));
    }
}
