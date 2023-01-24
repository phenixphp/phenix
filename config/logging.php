<?php

declare(strict_types=1);

return [
    'channel' => env('LOG_CHANNEL', fn () => 'file'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | The file channel is default channel, it writes the output in storage
    | directory. The stream channel writes the output in the console.
    |
    */
    'channels' => [
        'file',
        'stream',
    ],
];
