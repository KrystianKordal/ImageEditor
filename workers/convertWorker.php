<?php

require_once dirname(__DIR__) . '/config.php';

use Kordal\ImageEditor\WebpConverter;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(
    RABBITMQ_HOST,
    RABBITMQ_PORT,
    RABBITMQ_USERNAME,
    RABBITMQ_PASSWORD
);

$channel = $connection->channel();

$callback = function($msg) {
    $data = json_decode($msg->body, true);
    echo "> Received message \n";
    
    $image_path = $data['image_path'];

    if (file_exists($image_path)) {
        echo "> Converting image " . pathinfo($image_path, PATHINFO_BASENAME) . "\n";
        $converter = new WebpConverter();
        $converter->convert($image_path);
        echo "> Image converted \n";
    } else {
        echo "> File doesnt exists \n";
    }
};


$channel->basic_consume(
    RABBITMQ_QUEUE_NAME,
    '',
    false,
    true,
    false,
    false,
    $callback
);

echo "> Waiting for messages \n";
while(count($channel->callbacks)) {
    $channel->wait();
}