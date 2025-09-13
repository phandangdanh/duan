<?php
use Illuminate\Support\Facades\Route;
return [
    'module' => [
        [
            'title' => 'Ql Nhóm bài viết',
            'icon' => 'fa fa-user',
            'name' =>['user'],
            'subModule' => [
                [
                    'title' => 'Ql Nhóm thành viên',
                    'route' => 'user/catalogue/index'
                ],
                [
                    'title' => 'Ql Thành Viên',
                    'route' => 'user/index'
                ]
            ]
        ],
        // [
        //     'title' => 'Ql Bài Viết',
        //     'icon' => 'fa fa-file',
        //     'name' => ['post'],
        //     'subModule' => [
        //         [
        //             'title' => 'Ql Nhóm Bài Viết',
        //             'route' => 'post/catalogue/index'
        //         ],
        //         [
        //             'title' => 'Ql Thành Viên',
        //             'route' => 'post/index'
        //         ]
        //     ]
        // ],
        // [
        //     'title' => 'Cấu hình chung',
        //     'icon' => 'fa fa-file',
        //     'name' => ['language'],
        //     'subModule' => [
        //         [
        //             'title' => 'Ql Ngôn Ngữ',
        //             'route' => 'language/index'
        //         ],
        //     ]
        // ],
    ]
];
