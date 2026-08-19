<?php

declare(strict_types=1);

use CrazyFactory\Sniffs\ControlStructures\ControlSignatureSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\NoPhp4ConstructorFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ControlStructure\ControlStructureContinuationPositionFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Functions\ValidDefaultValueSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        // fixers without configuration
        FunctionToConstantFixer::class,
        NoUnusedImportsFixer::class,
        OrderedImportsFixer::class,
        NoLeadingImportSlashFixer::class,
        SingleBlankLineAtEofFixer::class,
        FunctionDeclarationFixer::class,
        TrimArraySpacesFixer::class,
        SingleQuoteFixer::class,
        LowercaseKeywordsFixer::class,
        // LowercaseConstantsFixer (php-cs-fixer 2.x) => ConstantCaseFixer
        ConstantCaseFixer::class,
        NativeFunctionCasingFixer::class,
        SingleBlankLineBeforeNamespaceFixer::class,
        NoBlankLinesAfterClassOpeningFixer::class,
        NoPhp4ConstructorFixer::class,
        VisibilityRequiredFixer::class,
        NormalizeIndexBraceFixer::class,
        NoShortBoolCastFixer::class,
        // NoExtraConsecutiveBlankLinesFixer (php-cs-fixer 2.x) => NoExtraBlankLinesFixer
        NoExtraBlankLinesFixer::class,
        BracesFixer::class,
        // sniffs
        ControlSignatureSniff::class,
        ValidDefaultValueSniff::class,
    ])
    ->withConfiguredRule(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ])
    // MethodSeparationFixer (php-cs-fixer 2.x) => ClassAttributesSeparationFixer (methods only)
    ->withConfiguredRule(ClassAttributesSeparationFixer::class, [
        'elements' => ['method' => 'one'],
    ])
    // TrailingCommaInMultilineArrayFixer (php-cs-fixer 2.x) => TrailingCommaInMultilineFixer
    ->withConfiguredRule(TrailingCommaInMultilineFixer::class, [
        'elements' => ['arrays'],
    ])
    ->withConfiguredRule(LineLengthSniff::class, [
        'lineLimit' => 120,
        'absoluteLineLimit' => 140,
    ])
    ->withConfiguredRule(BlankLineBeforeStatementFixer::class, [
        'statements' => ['return', 'continue', 'break'],
    ])
    // else/elseif/catch on the next line, as CrazyFactory ControlSignatureSniff requires
    ->withConfiguredRule(ControlStructureContinuationPositionFixer::class, [
        'position' => 'next_line',
    ])
    ->withConfiguredRule(ForbiddenFunctionsSniff::class, [
        'forbiddenFunctions' => [
            'var_dump' => null,
            'sizeof' => 'count',
            'delete' => 'unset',
            'print' => 'echo',
            'join' => 'implode',
            'split' => 'explode',
            'pos' => 'current',
        ],
    ]);
