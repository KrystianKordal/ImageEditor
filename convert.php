<?php

use Kordal\ImageEditor\File;
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

$uploadPath = UPLOAD_DIR . 'images/image.zip';

$tempDir = File::createTempDir();

if($tempDir) {
    $zipFile = new PhpZip\ZipFile();
    $zipFile
        ->openFile($uploadPath)
        ->extractTo($tempDir)
        ->close();


    $images = File::getImagesFromDir($tempDir);
    
    foreach ($images as $image) {
        $data = array(
            'source' => $image['source'],
            'destination' => $image['destination']
        );
        
        $msg = new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg, '', RABBITMQ_QUEUE_NAME);
    }
} else {
    echo "Error while creating temporary directory";
    die();
}

