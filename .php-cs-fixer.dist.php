<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

$config = new PhpCsFixer\Config();

$config->setRiskyAllowed(true)
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@DoctrineAnnotation' => true,
        '@PHP71Migration' => true,
        'visibility_required' => [
            'elements' => ['property', 'method', 'const']
        ],
        '@PHP71Migration:risky' => true,
        'declare_strict_types' => false,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'modernize_types_casting' => true,
        'native_function_invocation' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'break',
                'continue',
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'no_superfluous_phpdoc_tags' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => true,
        ],
        'phpdoc_order' => true,
        'psr_autoloading' => true,
        'semicolon_after_instruction' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'ternary_to_null_coalescing' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'general_phpdoc_annotation_remove' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => true,
        'no_php4_constructor' => true,
        'ordered_class_elements' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
;

return $config;
