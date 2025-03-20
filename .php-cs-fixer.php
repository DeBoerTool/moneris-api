<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())
    ->in(__DIR__);

return (new Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
    ])
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setIndent("\t")
    ->setLineEnding("\n")
    ->setFinder($finder);
