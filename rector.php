<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/src',
		__DIR__ . '/tests',
	])
	->withSets([
		PHPUnitSetList::PHPUNIT_100,
	])
	->withPhpSets(php82: true)
	->withTypeCoverageLevel(0)
	->withDeadCodeLevel(0)
	->withCodeQualityLevel(0);
