<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available for your choosing. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Private Filesystem Disk (เพิ่มส่วนนี้ หรือตั้งชื่ออื่น)
    |--------------------------------------------------------------------------
    |
    | This disk is intended for files that should not be publicly accessible
    | directly via a URL, such as sensitive attachments.
    |
    */

    'default_private_disk' => env('FILESYSTEM_PRIVATE_DISK', 'private'), // เพิ่มบรรทัดนี้

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        // ================================================================
        // เพิ่ม Disk 'private' ตรงนี้
        // ================================================================
        'private' => [
            'driver' => 'local', // ใช้ local driver สำหรับเก็บไฟล์ใน server
            'root' => storage_path('app/private_attachments'), // กำหนด path ที่จะเก็บไฟล์
                                                             // เช่น storage/app/private_attachments
            'visibility' => 'private', // ตั้งค่า visibility (สำคัญสำหรับบาง driver)
            'throw' => false, // ถ้าเป็น true จะ throw exception เมื่อหาไฟล์ไม่เจอ
        ],
        // ================================================================

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];