<?php

require_once __DIR__ . '/vendor/autoload.php';

define('RABBITMQ_HOST', 'localhost');
define('RABBITMQ_PORT', '5672');
define('RABBITMQ_USERNAME', 'guest');
define('RABBITMQ_PASSWORD', 'guest');
define('RABBITMQ_QUEUE_NAME', 'convert_queue');

define('UPLOAD_DIR', __DIR__ . '/upload/');
define('DOWNLOAD_DIR', __DIR__ . '/download/');
