<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Zenstruck\Foundry\Utils\Rector\FoundrySetList;

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
    FoundrySetList::UP_TO_FOUNDRY_2,
  ])
  ->withRules([
   AddVoidReturnTypeWhereNoReturnRector::class,
  ]);
