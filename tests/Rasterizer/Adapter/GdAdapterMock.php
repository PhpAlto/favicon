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

use Alto\Favicon\Tests\Rasterizer\Adapter\GdAdapterTest;

function imagecolorallocatealpha($image, $red, $green, $blue, $alpha)
{
    if (GdAdapterTest::$failColorAllocation) {
        return false;
    }

    return \imagecolorallocatealpha($image, $red, $green, $blue, $alpha);
}
