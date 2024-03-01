<?php

return [
    'disks' => [
        'sheet-base' => [
            'driver' => 'local',
            'root' => storage_path('sheet-base'),
        ],
        'root' => [
            'driver' => 'local',
            'root' => base_path(''),
        ],
    ],
];
