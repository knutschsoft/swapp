<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/node_modules',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
        $rectorConfig->sets([
            LevelSetList::UP_TO_PHP_83,
        ]);
    $rectorConfig->skip([
        \Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class,
        \Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class,
        \Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector::class,
        \Rector\Php80\Rector\FunctionLike\MixedTypeRector::class,
    ]);
};
