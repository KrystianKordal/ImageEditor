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
    public function convert(string $source, string $destination) : string
    {
        $dir = pathinfo($source, PATHINFO_DIRNAME);
        $name = pathinfo($source, PATHINFO_FILENAME);
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

        $destination_dir = pathinfo($destination, PATHINFO_DIRNAME);
        if (!file_exists($destination_dir) || !is_dir($destination_dir)) {
            File::createDir($destination_dir);
        }

        imagewebp($image, $destination, $this->quality);

        return $destination;
    }
}