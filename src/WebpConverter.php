<?php

namespace Kordal\ImageEditor;

/**
 * Used for converting images to webp
 */
class WebpConverter
{
    public function __construct()
    {
        $this->quality = 100;
    }

    /**
     * Convert image to webp
     * 
     * @param string $source absolutive path to image
     * 
     * @return string New file destination
     */
    public function convert($source) : string
    {
        $dir = pathinfo($source, PATHINFO_DIRNAME);
        $name = pathinfo($source, PATHINFO_FILENAME);
        $destination = $dir . DIRECTORY_SEPARATOR . $name . '.webp';
        $info = getimagesize($source);
        $isAlpha = false;
        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);
        elseif ($isAlpha = $info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($isAlpha = $info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } else {
            return $source;
        }
        if ($isAlpha) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }
        imagewebp($image, $destination, $this->quality);

        return $destination;
    }
}