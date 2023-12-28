<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

// Constants or Configuration Parameters
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
