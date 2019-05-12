<?php

define('APP_ROOT', __DIR__);

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // App Settings
        'app'                    => [
            'name' => getenv('APP_NAME'),
            'url'  => getenv('APP_URL'),
            'env'  => getenv('APP_ENV'),
            'cors' => null !== getenv('CORS_ALLOWED_ORIGINS') ? getenv('CORS_ALLOWED_ORIGINS') : '*',
            'license' => null !== getenv('APP_LICENSE') ? getenv('APP_LICENSE') : 'Limited',
        ], 
        // Doctrine ORM
        'doctrine' => [
            // if true, metadata caching is forcefully disabled
            'dev_mode' => true,

            // path where the compiled metadata info will be cached
            // make sure the path exists and it is writable
            'cache_dir' => APP_ROOT . '/var/doctrine',

            // you should add any other path containing annotated entity classes
            'metadata_dirs' => [APP_ROOT . '/src/Domain'],

            'connection' => [
                'host'      => getenv('DB_HOST'),
                'driver'    => getenv('DB_DRIVER'),
                'dbname'    => getenv('DB_DATABASE'),
                'user'      => getenv('DB_USERNAME'),
                'password'  => getenv('DB_PASSWORD'),
                'port'      => getenv('DB_PORT'),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]
        ],
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        // Plates Renderer settings
        'plates' => [
            // Path to view directory (default: null)
            'directory' => __DIR__ . '/../templates/',
            // Path to asset directory (default: null)
            'assetPath' => __DIR__ . '/../public/',
            // Template extension (default: 'php')
            'fileExtension' => 'phtml',
            // Template extension (default: false) see: http://platesphp.com/extensions/asset/
            'timestampInFilename' => false,
            'adminPath' => __DIR__ . '/../templates/admin/',
            'emailPath' => __DIR__ . '/../templates/emails/',
        ],
        // Monolog settings
        'logger' => [
            'name' => getenv('APP_NAME'),
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
