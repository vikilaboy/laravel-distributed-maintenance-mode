<?php

return [
    /*
    | Supported drivers: "redis", "s3"
    */
    'driver' => env('DISTRIBUTED_MAINTENANCE_MODE_DRIVER', 'redis'),

    'redis' => [
        'database' => env('DISTRIBUTED_MAINTENANCE_MODE_REDIS_DB', 'default'),
    ],

    's3' => [
        'disk' => env('DISTRIBUTED_MAINTENANCE_MODE_DISK', 's3'),
    ],
];
