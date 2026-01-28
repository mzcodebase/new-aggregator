<?php

return [
    'parameters' => [
        'include' => 'include',
        'filter' => 'filter',
        'sort' => 'sort',
        'fields' => 'fields',
        'append' => 'append',
    ],

    'count_suffix' => 'Count',

    'exists_suffix' => 'Exists',

    'disable_invalid_filter_query_exception' => false,

    'disable_invalid_sort_query_exception' => false,

    'disable_invalid_includes_query_exception' => false,

    'convert_relation_names_to_snake_case_plural' => true,

    'convert_relation_table_name_strategy' => false,

    'convert_field_names_to_snake_case' => false,
];
