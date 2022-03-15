<?php

use Kordal\ImageEditor\WebpConverter;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/config.php';

$connection = new AMQPStreamConnection(
    RABBITMQ_HOST,
    RABBITMQ_PORT,
    RABBITMQ_USERNAME,
    RABBITMQ_PASSWORD
);

$channel = $connection->channel();

$channel->queue_declare(
    $queue = RABBITMQ_QUEUE_NAME,
    $passive = false,
    $durable = true,
    $exclusive = false,
    $auto_delete = false,
    $nowait = false,
    $arguments = null,
    $ticket = null
);

$images = [
    array('path' => __DIR__ . '/img/image1.jpg'),
    array('path' => __DIR__ . '/img/car.jpg'),
    array('path' => __DIR__ . '/img/fish.jpg'),
    array('path' => __DIR__ . '/img/mountains.jpg'),
];

foreach ($images as $image) {
    $data = array(
        'image_path' => $image['path']
    );
    
    $msg = new AMQPMessage(json_encode($data));
    $channel->basic_publish($msg, '', RABBITMQ_QUEUE_NAME);
}

