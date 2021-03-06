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
    
    $source = $data['source'];
    $destination = $data['destination'];

    if (file_exists($source)) {
        echo "> Converting " . pathinfo($source, PATHINFO_BASENAME) . "\n";
        $start = microtime(true);
        $converter = new WebpConverter();
        $converter->convert($source, $destination);
        $end = round((microtime(true) - $start) * 100) / 100;
        echo "> Image converted after " . $end . "s \n\n";
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