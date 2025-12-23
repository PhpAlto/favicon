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

namespace Alto\Favicon\Command;

use Alto\Favicon\Generator\FaviconGenerator;
use Alto\Favicon\Options\FaviconOptionsBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to generate a favicon set from a source file.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
#[AsCommand(
    name: self::NAME,
    description: 'Generate a modern favicon set from an SVG or PNG.',
)]
final class GenerateFaviconCommand extends Command
{
    public const string NAME = 'generate';

    protected function configure(): void
    {
        $this
            ->addArgument('input', InputArgument::REQUIRED, 'Path to the input SVG or PNG.')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output directory (ex: public/).', 'public')
            ->addOption('public-path', null, InputOption::VALUE_REQUIRED, 'Public path prefix used in HTML/manifest (ex: /).', '/')
            ->addOption('app-name', null, InputOption::VALUE_REQUIRED, 'App name used in manifest.', 'App')
            ->addOption('theme-color', null, InputOption::VALUE_REQUIRED, 'Theme color for meta+manifest.', '#0b0b0b')
            ->addOption('background-color', null, InputOption::VALUE_REQUIRED, 'Background color for manifest.', '#ffffff')
            ->addOption('manifest', null, InputOption::VALUE_NONE, 'Generate manifest.webmanifest and Android icons.')
            ->addOption('search-png', null, InputOption::VALUE_NONE, 'Generate the 48x48 PNG favicon.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files.')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command generates a modern, minimalist favicon set from a single source file (SVG or PNG).

By default, it generates a minimal set of files suitable for most websites:
  - <info>favicon.ico</info> (Legacy support)
  - <info>icon.svg</info> (Modern browsers)
  - <info>apple-touch-icon.png</info> (iOS devices)
  - <info>favicon.html</info> (HTML snippet)

<comment>Optional Features:</comment>

  Use <info>--manifest</info> to generate a Web App Manifest and Android icons:
  - manifest.webmanifest
  - icon-192.png
  - icon-512.png
  - icon-maskable.png

  Use <info>--search-png</info> to generate a 48x48 PNG specifically for Google Search results:
  - favicon-48x48.png

<comment>Examples:</comment>

  <info>vendor/bin/favicon assets/logo.svg</info>
  <info>vendor/bin/favicon assets/logo.svg --manifest --search-png</info>
  <info>vendor/bin/favicon assets/logo.svg --output public/favicons --public-path /favicons</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $inputFile */
        $inputFile = $input->getArgument('input');
        /** @var string $outputDir */
        $outputDir = $input->getOption('output');

        $builder = new FaviconOptionsBuilder(
            inputFile: $inputFile,
            outputDir: $outputDir,
        );

        /** @var string $publicPath */
        $publicPath = $input->getOption('public-path');
        /** @var string $appName */
        $appName = $input->getOption('app-name');
        /** @var string $themeColor */
        $themeColor = $input->getOption('theme-color');
        /** @var string $backgroundColor */
        $backgroundColor = $input->getOption('background-color');

        $options = $builder
            ->publicPath($publicPath)
            ->appName($appName)
            ->themeColor($themeColor)
            ->backgroundColor($backgroundColor)
            ->generateManifest((bool) $input->getOption('manifest'))
            ->generateSearchPng48((bool) $input->getOption('search-png'))
            ->force((bool) $input->getOption('force'))
            ->build();

        $generator = new FaviconGenerator(logger: new ConsoleLogger($output));
        $report = $generator->generate($options);

        $output->writeln(sprintf('<info>Output:</info> %s', $report->outputDir));

        foreach ($report->files as $file => $status) {
            if ('created' === $status) {
                $output->writeln(sprintf(' <info>✔</info> %s', $file));
            } else {
                $output->writeln(sprintf(' <comment>✔</comment> %s', $file));
            }
        }

        $output->writeln('');
        $output->writeln('<info>HTML snippet (also written to favicon.html):</info>');
        $output->writeln('');
        $output->writeln(preg_replace('/(?m)^/', '    ', $report->htmlSnippet) ?? '');

        return Command::SUCCESS;
    }
}
