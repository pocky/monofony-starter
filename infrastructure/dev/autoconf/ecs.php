<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\RuleSet\Sets\PHP81MigrationSet;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(SetList::PSR_12);
    $ecsConfig->parallel();

    $ecsConfig->paths([
        __DIR__.'/src',
        __DIR__.'/config'
    ]);

    $services = $ecsConfig->services();
    $services->set(ArrayIndentationFixer::class);
    $services->set(DeclareStrictTypesFixer::class);
    $services->set(OrderedImportsFixer::class);
    $services->set(NoUnusedImportsFixer::class);
    $services->set(StrictComparisonFixer::class);
    $services->set(PHP81MigrationSet::class);

    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]])
    ;

    $services->set(PhpdocAlignFixer::class)
        ->call('configure', [[
            'align' => 'left',
            'tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var'],
        ]])
    ;

    $services->set(TrailingCommaInMultilineFixer::class)
        ->call('configure', [['elements' => ['arrays', 'arguments', 'parameters']]])
    ;

    $parameters = $ecsConfig->parameters();
    $parameters->set('skip', [
        VisibilityRequiredFixer::class => ['*Spec.php'],
        PhpUnitTestClassRequiresCoversFixer::class => ['*Test.php'],
        PhpUnitInternalClassFixer::class => ['*Test.php'],
        PhpUnitMethodCasingFixer::class => ['*Test.php'],
        NoUnusedImportsFixer::class => [
            __DIR__.'/src/Shared/Infrastructure/Maker/Resources/skeleton/config/packages.tpl.php',
        ],
    ]);
};
