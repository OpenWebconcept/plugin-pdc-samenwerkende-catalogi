<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('htdocs/wp')
    ->notPath('storage')
    ->notPath('vendor')
    ->in(__DIR__)
    ->in(__DIR__ . '/src/')
    ->in(__DIR__ . '/tests/')
    ->name('*.php')
    ->name('_ide_helper')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

    return PhpCsFixer\Config::create()
        ->setRules([
            '@PSR2'                                                               => true,
            'array_syntax'                                                        => ['syntax' => 'short'],
            'ordered_imports'                                                     => ['sortAlgorithm' => 'alpha'],
            'no_unused_imports'                                                   => true,
            'not_operator_with_successor_space'                                   => true,
            'trailing_comma_in_multiline_array'                                   => true,
            'phpdoc_scalar'                                                       => true,
            'phpdoc_var_without_name'                                             => true,
            'phpdoc_single_line_var_spacing'                                      => true,
            'unary_operator_spaces'                                               => true,
            'phpdoc_trim'                                                         => true,
            'phpdoc_trim_consecutive_blank_line_separation'                       => true,
            'align_multiline_comment'                                             => true,
            'array_indentation'                                                   => true,
            'no_superfluous_elseif'                                               => true,
            'single_blank_line_before_namespace'                                  => true,
            'blank_line_after_opening_tag'                                        => true,
            'no_blank_lines_after_phpdoc'                                         => true,
            'phpdoc_separation'                                                   => true,
            'binary_operator_spaces'                                              => [
                'align_double_arrow' => true,
                'align_equals'       => true,
            ],
            'return_type_declaration' => [
                'space_before' => 'none',
            ],
            'blank_line_before_statement' => [
                'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
            ],
            'class_attributes_separation' => [
                'elements' => [
                    'method',
                ],
            ],
            'method_argument_space' => [
                'on_multiline'                     => 'ensure_fully_multiline',
                'keep_multiple_spaces_after_comma' => true,
            ],
            'yoda_style' => [
                'always_move_variable' => true,
                'equal'                => true,
                'identical'            => true,
                'less_and_greater'     => true,
            ],
        ])
        ->setUsingCache(false)
        ->setFinder($finder);
