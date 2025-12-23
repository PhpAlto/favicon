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
final class HtmlGenerator
{
    /**
     * Generate the HTML tags.
     */
    public function generate(FaviconOptions $options, bool $hasSvg): string
    {
        $lines = [];

        if ($options->generateManifest) {
            $lines[] = sprintf('<link rel="manifest" href="%s">', $options->publicAsset('manifest.webmanifest'));
        }

        $lines[] = sprintf('<link rel="icon" href="%s" sizes="32x32">', $options->publicAsset('favicon.ico'));

        if ($hasSvg) {
            $lines[] = sprintf('<link rel="icon" href="%s" type="image/svg+xml">', $options->publicAsset('icon.svg'));
        } else {
            $lines[] = sprintf('<link rel="icon" href="%s" type="image/png" sizes="32x32">', $options->publicAsset('favicon-32x32.png'));
            $lines[] = sprintf('<link rel="icon" href="%s" type="image/png" sizes="16x16">', $options->publicAsset('favicon-16x16.png'));
        }

        if ($options->generateSearchPng48) {
            $lines[] = sprintf('<link rel="icon" href="%s" type="image/png" sizes="48x48">', $options->publicAsset('favicon-48x48.png'));
        }

        $lines[] = sprintf('<link rel="apple-touch-icon" href="%s">', $options->publicAsset('apple-touch-icon.png'));
        $lines[] = sprintf('<meta name="theme-color" content="%s">', $options->themeColor);

        return implode("\n", $lines)."\n";
    }
}
