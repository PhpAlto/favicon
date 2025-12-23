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
use Alto\Favicon\Exception\MissingInputFileException;
use Alto\Favicon\GenerationReport;
use Alto\Favicon\Options\FaviconOptions;
use Alto\Favicon\Rasterizer\PngRasterizer;
use Alto\Favicon\Rasterizer\RasterizerInterface;
use Alto\Favicon\Rasterizer\SvgRasterizer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
final class FaviconGenerator
{
    private const string MANIFEST_FILE = 'manifest.webmanifest';

    /**
     * @param list<RasterizerInterface> $rasterizers
     */
    public function __construct(
        private array $rasterizers = [],
        private IcoGenerator $icoGenerator = new IcoGenerator(),
        private WebManifestGenerator $manifestGenerator = new WebManifestGenerator(),
        private HtmlGenerator $htmlGenerator = new HtmlGenerator(),
        private Filesystem $filesystem = new Filesystem(),
        private LoggerInterface $logger = new NullLogger(),
    ) {
        if ([] === $this->rasterizers) {
            $this->rasterizers = [
                new SvgRasterizer(logger: $this->logger),
                new PngRasterizer(logger: $this->logger),
            ];
        }
    }

    /**
     * Generate all favicon files based on the provided options.
     */
    public function generate(FaviconOptions $options): GenerationReport
    {
        if (!is_file($options->inputFile)) {
            throw new MissingInputFileException($options->inputFile);
        }

        $this->filesystem->mkdir($options->outputDir);

        $ext = strtolower(pathinfo($options->inputFile, PATHINFO_EXTENSION));
        $hasSvg = 'svg' === $ext;

        $files = [];

        if ($hasSvg) {
            $target = $options->outputDir.'/icon.svg';
            if ($options->force || !is_file($target)) {
                $this->filesystem->copy($options->inputFile, $target, true);
                $files['icon.svg'] = 'created';
            } else {
                $files['icon.svg'] = 'skipped';
            }
        }

        $png32 = $options->outputDir.'/.tmp-32.png';
        $icoTarget = $options->outputDir.'/favicon.ico';
        $png32Target = $options->outputDir.'/favicon-32x32.png';

        $updateIco = $options->force || !is_file($icoTarget);
        $updatePng32 = !$hasSvg && ($options->force || !is_file($png32Target));

        if ($updateIco || $updatePng32) {
            $this->rasterize($options->inputFile, 32, $png32);

            if ($updateIco) {
                $this->icoGenerator->generateFromPng32($png32, $icoTarget);
            }

            if ($updatePng32) {
                $this->filesystem->rename($png32, $png32Target, true);
            } else {
                $this->filesystem->remove($png32);
            }
        }

        $files['favicon.ico'] = $updateIco ? 'created' : 'skipped';

        if (!$hasSvg) {
            $files['favicon-32x32.png'] = $updatePng32 ? 'created' : 'skipped';

            $target = $options->outputDir.'/favicon-16x16.png';
            if ($options->force || !is_file($target)) {
                $this->rasterize($options->inputFile, 16, $target);
                $files['favicon-16x16.png'] = 'created';
            } else {
                $files['favicon-16x16.png'] = 'skipped';
            }
        }

        if ($options->generateSearchPng48) {
            $target = $options->outputDir.'/favicon-48x48.png';
            if ($options->force || !is_file($target)) {
                $this->rasterize($options->inputFile, 48, $target);
                $files['favicon-48x48.png'] = 'created';
            } else {
                $files['favicon-48x48.png'] = 'skipped';
            }
        }

        $target = $options->outputDir.'/apple-touch-icon.png';
        if ($options->force || !is_file($target)) {
            $this->rasterize($options->inputFile, 180, $target);
            $files['apple-touch-icon.png'] = 'created';
        } else {
            $files['apple-touch-icon.png'] = 'skipped';
        }

        $manifestFile = null;

        if ($options->generateManifest) {
            $target = $options->outputDir.'/icon-192.png';
            if ($options->force || !is_file($target)) {
                $this->rasterize($options->inputFile, 192, $target);
                $files['icon-192.png'] = 'created';
            } else {
                $files['icon-192.png'] = 'skipped';
            }

            $target = $options->outputDir.'/icon-512.png';
            if ($options->force || !is_file($target)) {
                $this->rasterize($options->inputFile, 512, $target);
                $files['icon-512.png'] = 'created';
            } else {
                $files['icon-512.png'] = 'skipped';
            }

            $target = $options->outputDir.'/icon-maskable.png';
            if ($options->force || !is_file($target)) {
                $this->filesystem->copy($options->outputDir.'/icon-512.png', $target, true);
                $files['icon-maskable.png'] = 'created';
            } else {
                $files['icon-maskable.png'] = 'skipped';
            }

            $manifestFile = self::MANIFEST_FILE;
            $target = $options->outputDir.'/'.self::MANIFEST_FILE;

            if ($options->force || !is_file($target)) {
                $manifest = $this->manifestGenerator->generate($options);
                $json = json_encode($manifest, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n";
                $this->filesystem->dumpFile($target, $json);
                $files[self::MANIFEST_FILE] = 'created';
            } else {
                $files[self::MANIFEST_FILE] = 'skipped';
            }
        }

        $html = $this->htmlGenerator->generate($options, $hasSvg);
        $target = $options->outputDir.'/favicon.html';
        if ($options->force || !is_file($target)) {
            $this->filesystem->dumpFile($target, $html);
            $files['favicon.html'] = 'created';
        } else {
            $files['favicon.html'] = 'skipped';
        }

        return new GenerationReport(
            outputDir: $options->outputDir,
            publicPath: $options->publicPath,
            files: $files,
            htmlSnippet: $html,
            manifestFile: $manifestFile,
        );
    }

    private function rasterize(string $inputFile, int $size, string $targetPngFile): void
    {
        foreach ($this->rasterizers as $rasterizer) {
            if ($rasterizer->supports($inputFile)) {
                $rasterizer->rasterizeToPng($inputFile, $size, $targetPngFile);

                return;
            }
        }

        throw new FaviconException(sprintf('No rasterizer supports input file: %s', $inputFile));
    }
}
