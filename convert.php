<?php

use Kordal\ImageEditor\WebpConverter;

require_once __DIR__ . '/vendor/autoload.php';

$converter = new WebpConverter();

$converter->convert(__DIR__ . "/img/image.jpg");