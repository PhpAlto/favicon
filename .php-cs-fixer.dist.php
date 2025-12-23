<?php

$licence = <<<'EOF'
This file is part of the ALTO library.

© 2025–present Simon André

For full copyright and license information, please see
the LICENSE file distributed with this source code.
EOF;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'var/',
    ])
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@Symfony' => true,
        'declare_strict_types' => true,
        'header_comment' => ['header' => $licence],
    ])
;
