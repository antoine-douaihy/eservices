<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        // "public" and "private" drivers default to local disk for local
        // development, but can be switched to "s3" (works with any
        // S3-compatible provider, including Cloudflare R2) per-environment
        // by setting FILESYSTEM_PUBLIC_DRIVER / FILESYSTEM_PRIVATE_DRIVER —
        // so citizen-uploaded documents and generated certificates survive
        // host restarts/redeploys on platforms with an ephemeral filesystem
        // (e.g. Render's free tier), without touching any call sites.
        'public' => [
            'driver' => env('FILESYSTEM_PUBLIC_DRIVER', 'local'),
            'root' => storage_path('app/public'),
            'url' => env('FILESYSTEM_PUBLIC_DRIVER', 'local') === 's3'
                ? env('AWS_URL')
                : rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
            // S3/R2 credentials — unused when the driver above is "local".
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            '