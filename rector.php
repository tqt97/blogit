<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector;
use RectorLaravel\Rector\ArrayDimFetch\RequestVariablesToRequestFacadeRector;
use RectorLaravel\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector;
use RectorLaravel\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector;
use RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector;
use RectorLaravel\Rector\ClassMethod\MakeModelAttributesAndScopesProtectedRector;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector;
use RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector;
use RectorLaravel\Rector\FuncCall\NowFuncWithStartOfDayMethodCallToTodayFuncRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use RectorLaravel\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector;
use RectorLaravel\Rector\MethodCall\ConvertEnumerableToArrayToAllRector;
use RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector;
use RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector;
use RectorLaravel\Rector\MethodCall\RedirectRouteToToRouteHelperRector;
use RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector;
use RectorLaravel\Rector\MethodCall\WhereToWhereLikeRector;
use RectorLaravel\Rector\New_\AddGuardToLoginEventRector;
use RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector;
use RectorLaravel\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector;
use RectorLaravel\Rector\PropertyFetch\ReplaceFakerPropertyFetchWithMethodCallRector;
use RectorLaravel\Rector\StaticCall\CarbonToDateFacadeRector;
use RectorLaravel\Rector\StaticCall\MinutesToSecondsInCacheRector;
use RectorLaravel\Rector\StaticCall\RequestStaticValidateToInjectRector;
use RectorLaravel\Rector\StaticCall\RouteActionCallableRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        // __DIR__.'/config',
    ])
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: false, earlyReturn: true)
    ->withPhpSets(php80: true)
    ->withRules([
        RouteActionCallableRector::class,
        EloquentOrderByToLatestOrOldestRector::class,
        ConvertEnumerableToArrayToAllRector::class,
        AvoidNegatedCollectionFilterOrRejectRector::class,
        RedirectRouteToToRouteHelperRector::class,
        EloquentWhereRelationTypeHintingParameterRector::class,
        EloquentWhereTypeHintClosureParameterRector::class,
        ValidationRuleArrayStringValueToArrayRector::class,
        RedirectBackToBackHelperRector::class,
        WhereToWhereLikeRector::class,
        NowFuncWithStartOfDayMethodCallToTodayFuncRector::class,
        ThrowIfAndThrowUnlessExceptionsToUseClassStringRector::class,
        TypeHintTappableCallRector::class,
        RemoveDumpDataDeadCodeRector::class,
        CarbonToDateFacadeRector::class,
        MinutesToSecondsInCacheRector::class,
        OptionalToNullsafeOperatorRector::class,
        SubStrToStartsWithOrEndsWithStaticMethodCallRector::class,
        AppEnvironmentComparisonToParameterRector::class,
        ApplyDefaultInsteadOfNullCoalesceRector::class,
        MakeModelAttributesAndScopesProtectedRector::class,
        SessionVariableToSessionFacadeRector::class,
        ServerVariableToRequestFacadeRector::class,
        EnvVariableToEnvHelperRector::class,
        RequestVariablesToRequestFacadeRector::class,
        RequestStaticValidateToInjectRector::class,
        ReplaceFakerPropertyFetchWithMethodCallRector::class,
        ReplaceFakerInstanceWithHelperRector::class,
        AddGuardToLoginEventRector::class,
        ModelCastsPropertyToCastsMethodRector::class,
        ScopeNamedClassMethodToScopeAttributedClassMethodRector::class,
    ]);
