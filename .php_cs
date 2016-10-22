<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('vendor')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
    ->fixers([
        'psr0',
        'encoding',
        'short_tag',
        'braces',
        'elseif',
        'eof_ending',
        'function_call_space',
        'function_declaration', // ?
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'php_closing_tag',
        'single_line_after_imports',
        'trailing_spaces',
        'visibility',
        'array_element_white_space_after_comma',
        'blankline_after_open_tag',
        'duplicate_semicolon',
        'extra_empty_lines',
        'function_typehint_space',
        'namespace_no_leading_whitespace',
        'no_blank_lines_after_class_opening',
        'no_empty_lines_after_phpdocs',
        'phpdoc_scalar',
        'phpdoc_separation',
        'phpdoc_types',
        'remove_leading_slash_use',
        'remove_lines_between_uses',
        'return',
        'self_accessor',
        'short_bool_cast',
        'single_array_no_trailing_comma',
        'spaces_before_semicolon',
        'spaces_cast',
        'standardize_not_equal',
        'ternary_spaces',
        'trim_array_spaces',
        'unused_use',
        'whitespacy_lines',
        'align_double_arrow',
        'header_comment',
        'newline_after_open_tag',
        'short_array_syntax',
    ])
    ->finder($finder)
;
