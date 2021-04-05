<?php declare(strict_types=1);

return [
    'default' => 'file1',
    'channels' => [
        'file1' => [ // 文件类型的日志
            'driver' => 'stack',
            'path' => FRAME_BASE_PATH . '/storage',
            'format' => '[%s][%s] %s'  // 格式化：[日期][日志级别]消息
        ],
        'file2' => [
            'driver' => 'daily',
            'path' => FRAME_BASE_PATH . '/storage',
            'format' => '[%s][%s] %s'
        ]
    ]
];