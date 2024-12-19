<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
  ->withPaths([
   __DIR__ . '/config',
   __DIR__ . '/public',
   __DIR__ . '/src',
  ])
  // uncomment to reach your current PHP version
  ->withPhpSets()
  ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
  ->withSets([
    SymfonySetList::SYMFONY_CODE_QUALITY,
    SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    LevelSetList::UP_TO_PHP_84,
  ])
  ->withRules([
   AddVoidReturnTypeWhereNoReturnRector::class,
  ]);
