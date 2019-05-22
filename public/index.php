<?php

declare(strict_types=1);

use Slim\App;
use Slim\Container;

/** @var Container $cnt */
$cnt = require_once __DIR__ . '/../bootstrap.php';

session_cache_limiter('nocache');
session_start();

/** @var App $app */
$app = $cnt[App::class];

$app->run();
