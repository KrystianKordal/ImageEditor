<?php

namespace Kordal\ImageEditor;

/**
 * File handler
 */
class File
{
    /**
     * Get all images from directory
     * 
     * @param string $path Path to directory
     * @param strin $mainPath Path to parent directory
     * 
     * @return array Images data
     * [
     *  [
     *      'source', // Source of an image
     *      'destination' // Converted image destionation
     *  ]
     * ]
     */
    public static function getImagesFromDir(string $path, $mainPath = null) : array
    {
        if (!$mainPath) {
            $mainPath = $path;
        }
        $nodes = scandir($path);

        $imagesPaths = array();

        foreach ($nodes as $node) {
            if (in_array($node, ['..', '.'])) {
                continue;
            }
            $nodePath = $path . DIRECTORY_SEPARATOR . $node;

            if(is_dir($nodePath)) {
                $imagesPaths = array_merge($imagesPaths, self::getImagesFromDir($nodePath, $mainPath));
            }

            if(self::isImage($nodePath)) {
                $destination = self::setWebpExtension($nodePath);
                $destination = str_replace($mainPath, DOWNLOAD_DIR, $destination);
                $imagesPaths[] = array(
                    'source' => $nodePath,
                    'destination' => $destination
                );
            }
        }

        return $imagesPaths;
    }

    /**
     * Check if file is image
     * 
     * @param string $file Absolute path to file
     * 
     * @return bool
     */
    public static function isImage(string $file): bool
    {
        if (!file_exists($file) || !is_file($file)) {
            return false;
        }

        if (in_array(
            pathinfo($file, PATHINFO_EXTENSION), 
            ['png', 'jpg', 'gif']
        )) {
            return true;
        }

        return false;
    }

    /**
     * Replace file extension with webp
     * 
     * @param string $path
     * 
     * @return string Path to webp file
     */
    public static function setWebpExtension(string $path) : string
    {
        $path_arr = explode('.', $path);
        array_pop($path_arr);
        $new_path = implode('.', $path_arr);

        return $new_path . '.webp';
    }

    /**
     * Create dir
     */
    public static function createDir($dir)
    {
        mkdir($dir, 0755, true);
    }

    /**
     * Create dir in temp directory
     * 
     * @return string|bool
     */
    public static function createTempDir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000)
    {
        if (is_null($dir))
        {
            $dir = sys_get_temp_dir();
        }
    
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
    
        if (!is_dir($dir) || !is_writable($dir))
        {
            return false;
        }
    
        if (strpbrk($prefix, '\\/:*?"<>|') !== false)
        {
            return false;
        }
    
        $attempts = 0;
        do
        {
            $path = sprintf('%s%s%s%s', $dir, DIRECTORY_SEPARATOR, $prefix, mt_rand(100000, mt_getrandmax()));
        } while (
            !mkdir($path, $mode) &&
            $attempts++ < $maxAttempts
        );
    
        return $path;
    }

}