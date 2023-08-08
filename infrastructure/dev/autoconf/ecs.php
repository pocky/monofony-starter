<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Basic\OctalNotationFixer;
use PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\CleanNamespaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\StringNotation\SimpleToComplexStringVariableFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(SetList::PSR_12);
    $ecsConfig->import(SetList::CLEAN_CODE);
    $ecsConfig->parallel();

    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->rules([
        ArrayIndentationFixer::class,
        DeclareStrictTypesFixer::class,
        OrderedImportsFixer::class,
        NoUnusedImportsFixer::class,
        StrictComparisonFixer::class,
        CleanNamespaceFixer::class,
        NoUnsetCastFixer::class,
        SimpleToComplexStringVariableFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->ruleWithConfiguration(PhpdocAlignFixer::class, [
        'align' => 'left',
        'tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var'],
    ]);

    $ecsConfig->ruleWithConfiguration(TrailingCommaInMultilineFixer::class, [
        'elements' => ['arrays', 'arguments', 'parameters']
    ]);

    $ecsConfig->skip([
        VisibilityRequiredFixer::class => ['*Spec.php'],
        PhpUnitTestClassRequiresCoversFixer::class => ['*Test.php'],
        PhpUnitInternalClassFixer::class => ['*Test.php'],
        PhpUnitMethodCasingFixer::class => ['*Test.php'],
        NoUnusedImportsFixer::class => [
            __DIR__ . '/src/Shared/Infrastructure/Maker/Resources/skeleton/config/packages.tpl.php',
        ],
    ]);
};
