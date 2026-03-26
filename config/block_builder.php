<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Block Registry
    |--------------------------------------------------------------------------
    |
    | Contract:
    | - extends: parent module key for inheritance
    | - categories: additional/override categories for block palette
    | - each category requires id and can define label/icon/blocks
    | - blocks are merged by "type" and preserve first-seen order
    |
    */
    'module_registry' => [
        'base_content' => [
            'categories' => [],
        ],

        'pages' => [
            'extends' => 'base_content',
            'categories' => [],
        ],

        'posts' => [
            'extends' => 'base_content',
            'categories' => [
                [
                    'id' => 'extended',
                    'label' => 'Extended',
                    'icon' => 'PhPlusCircle',
                    'blocks' => [
                        ['type' => 'posts_list', 'label' => 'Posts', 'icon' => 'PhArticle'],
                    ],
                ],
            ],
        ],

        'projects' => [
            'extends' => 'base_content',
            'categories' => [
                [
                    'id' => 'extended',
                    'label' => 'Extended',
                    'icon' => 'PhPlusCircle',
                    'blocks' => [
                        ['type' => 'projects_list', 'label' => 'Projects', 'icon' => 'PhBriefcase'],
                    ],
                ],
            ],
        ],

        'templates' => [
            'extends' => 'base_content',
            'categories' => [
                [
                    'id' => 'building',
                    'label' => 'Building',
                    'icon' => 'PhCube',
                    'blocks' => [
                        ['type' => 'template_reference', 'label' => 'Template Part', 'icon' => 'PhLayout'],
                        ['type' => 'content_slot', 'label' => 'Content Slot', 'icon' => 'PhSquare', 'template_types' => ['page']],
                        ['type' => 'header_slot', 'label' => 'Header Slot', 'icon' => 'PhArrowUp', 'template_types' => ['page']],
                        ['type' => 'footer_slot', 'label' => 'Footer Slot', 'icon' => 'PhArrowDown', 'template_types' => ['page']],
                        ['type' => 'sidebar_slot', 'label' => 'Sidebar Slot', 'icon' => 'PhSidebars', 'template_types' => ['page']],
                    ],
                ],
            ],
        ],

        'forms' => [
            'extends' => 'base_content',
            'categories' => [
                [
                    'id' => 'data_input',
                    'label' => 'Data Input',
                    'icon' => 'PhPencilSimple',
                    'blocks' => [
                        ['type' => 'form', 'label' => 'Form Container', 'icon' => 'PhFiles'],
                        ['type' => 'text_input', 'label' => 'Text Input', 'icon' => 'PhTextT'],
                        ['type' => 'textarea', 'label' => 'Textarea', 'icon' => 'PhTextAlignLeft'],
                        ['type' => 'select', 'label' => 'Select', 'icon' => 'PhListNumbers'],
                        ['type' => 'checkbox', 'label' => 'Checkbox', 'icon' => 'PhCheckSquare'],
                        ['type' => 'radio', 'label' => 'Radio', 'icon' => 'PhRadioButton'],
                    ],
                ],
            ],
        ],
    ],
];
