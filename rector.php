<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\FuncCall\ArrayMergeOfNonArraysToSimpleArrayRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodeQuality\Rector\If_\ShortenElseIfRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\Stmt\RemoveUselessAliasInUseStatementRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveNullTagValueNodeRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveDeadReturnRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\DeadCode\Rector\Plus\RemoveDeadZeroAndOneOperationRector;
use Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Visibility\Rector\ClassConst\ChangeConstantVisibilityRector;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\Rector\ClassMethod\ExplicitPublicClassMethodRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withCache(
        cacheDirectory: __DIR__ . '/var/.rector.cache',
        cacheClass: FileCacheStorage::class,
    )
    ->withImportNames(
        removeUnusedImports: true
    )
    ->withSets([
        SetList::DEAD_CODE,
        SetList::INSTANCEOF,
        SetList::TYPE_DECLARATION,
        SetList::STRICT_BOOLEANS,
        SetList::CODING_STYLE,
        SetList::CODE_QUALITY,
        SetList::NAMING,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::PHP_82,
        SetList::PHP_83,
        SetList::PHP_84,
    ])
    ->withRules([
        /*
        |--------------------------------------------------------------------------
        | Visibility
        |--------------------------------------------------------------------------
        */
        ChangeConstantVisibilityRector::class,
        ChangeMethodVisibilityRector::class,
        ExplicitPublicClassMethodRector::class,
    ])
    ->withPHPStanConfigs([__DIR__ . '/rector.phpstan.dist.neon',
    ])
    ->withSkip([
        RemoveUnusedConstructorParamRector::class,
        RemoveEmptyClassMethodRector::class,
        RemoveDeadReturnRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        RemoveNonExistingVarAnnotationRector::class,
        DisallowedEmptyRuleFixerRector::class,
        CatchExceptionNameMatchingTypeRector::class,
        ArrayMergeOfNonArraysToSimpleArrayRector::class,
        CombineIfRector::class,
        ShortenElseIfRector::class,
        SimplifyRegexPatternRector::class,
        RemoveUselessAliasInUseStatementRector::class,
        SymplifyQuoteEscapeRector::class,
        RemoveDeadZeroAndOneOperationRector::class,
        RemoveNullPropertyInitializationRector::class,
        RemoveNullTagValueNodeRector::class,
        RenamePropertyToMatchTypeRector::class,
        RenameForeachValueVariableToMatchExprVariableRector::class,
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        RenameParamToMatchTypeRector::class,
        CompleteDynamicPropertiesRector::class,
        PrivatizeFinalClassMethodRector::class,
    ])
    ->withParallel(
        timeoutSeconds: 120,
        maxNumberOfProcess: 16,
        jobSize: 16  
    );
