<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

define('REDIS_HOST', $_ENV['REDIS_HOST']);
define('REDIS_PORT', $_ENV['REDIS_PORT']);

define('AWS_ACCESS_KEY_ID', $_ENV['AWS_ACCESS_KEY_ID']);
define('AWS_SECRET_ACCESS_KEY', $_ENV['AWS_SECRET_ACCESS_KEY']);
define('AWS_REGION', $_ENV['AWS_REGION']);
define('AWS_BUCKET_NAME', $_ENV['AWS_BUCKET_NAME']);