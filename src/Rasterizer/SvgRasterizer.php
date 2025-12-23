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

use Alto\Favicon\Exception\RasterizerUnavailableException;
use Alto\Favicon\Rasterizer\Adapter\AdapterInterface;
use Alto\Favicon\Rasterizer\Adapter\ImagickCliAdapter;
use Alto\Favicon\Rasterizer\Adapter\InkscapeAdapter;
use Alto\Favicon\Rasterizer\Adapter\RsvgAdapter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Rasterizer for SVG input files.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class SvgRasterizer implements RasterizerInterface
{
    protected LoggerInterface $logger;

    /** @var list<AdapterInterface> */
    private array $adapters;

    /**
     * @param list<AdapterInterface>|null $adapters
     */
    public function __construct(?array $adapters = null, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->adapters = $adapters ?? [
            new RsvgAdapter(),
            new InkscapeAdapter(),
            new ImagickCliAdapter(),
        ];
    }

    public function supports(string $inputFile): bool
    {
        return 'svg' === strtolower(pathinfo($inputFile, PATHINFO_EXTENSION));
    }

    public function rasterizeToPng(string $inputFile, int $size, string $targetPngFile): void
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->isAvailable()) {
                try {
                    $this->logger->debug(sprintf('Rasterizing SVG with %s', $adapter::class));
                    $adapter->run($inputFile, $size, $targetPngFile);

                    return;
                } catch (\Throwable $e) {
                    $this->logger->warning(sprintf('Rasterizer %s failed: %s', $adapter::class, $e->getMessage()));
                    // Try next adapter
                    continue;
                }
            }
        }

        throw new RasterizerUnavailableException('No suitable SVG rasterizer found (checked: rsvg-convert, inkscape, magick/convert).');
    }
}
