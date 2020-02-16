<?php

return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'local'),
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => '/storage',
            // 可见性
            'visibility' => 'public',
        ],
        'shop' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/static/uploads/shop',
            // 磁盘路径对应的外部URL路径
            'url'        => '/static/uploads/shop',
            // 可见性
            'visibility' => 'public',
        ],
        'product' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/static/uploads/product',
            // 磁盘路径对应的外部URL路径
            'url'        => '/static/uploads/product',
            // 可见性
            'visibility' => 'public',
        ],
        // 更多的磁盘配置信息
    ],

];
