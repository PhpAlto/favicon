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

use Alto\Favicon\Options\FaviconOptions;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
final class WebManifestGenerator
{
    /**
     * Generate the manifest content array.
     *
     * @return array<string,mixed>
     */
    public function generate(FaviconOptions $options): array
    {
        return [
            'name' => $options->appName,
            'short_name' => $options->appName,
            'theme_color' => $options->themeColor,
            'background_color' => $options->backgroundColor,
            'display' => 'standalone',
            'start_url' => '/',
            'icons' => [
                [
                    'src' => $options->publicAsset('icon-192.png'),
                    'type' => 'image/png',
                    'sizes' => '192x192',
                ],
                [
                    'src' => $options->publicAsset('icon-maskable.png'),
                    'type' => 'image/png',
                    'sizes' => '512x512',
                    'purpose' => 'maskable',
                ],
                [
                    'src' => $options->publicAsset('icon-512.png'),
                    'type' => 'image/png',
                    'sizes' => '512x512',
                ],
            ],
        ];
    }
}
