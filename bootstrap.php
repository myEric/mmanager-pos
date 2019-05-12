<?php

declare(strict_types=1);

use MmanagerPOS\Provider\Doctrine;
use MmanagerPOS\Provider\Slim;
use MmanagerPOS\Provider\Plates;
use Slim\Container;

require_once __DIR__ . '/vendor/autoload.php';

// Define root path
defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('APP_ROOT') ?: define('APP_ROOT', __DIR__. DS);

if (!file_exists(APP_ROOT . '/config/config.php')) {
    copy(APP_ROOT . '/config_example.php', APP_ROOT . '/config/config.php');
}

// Load .env file
if (!file_exists(APP_ROOT . '.env')) {
	copy(APP_ROOT . '.env.example', APP_ROOT . '.env');
}

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$cnt = new Container(require __DIR__ . '/config/config.php');

$cnt->register(new Doctrine())
	->register(new Plates())
    ->register(new Slim());

return $cnt;
