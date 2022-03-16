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
     * 
     * @return array Images data
     * [
     *  [
     *      'source', // Source of an image
     *      'destination' // Converted image destionation
     *  ]
     * ]
     */
    public static function getImagesFromDir(string $path) : array
    {
        $path = self::makeAbsolutePath($path, UPLOAD_DIR);

        $nodes = scandir($path);

        $images_paths = array();

        foreach ($nodes as $node) {
            if (in_array($node, ['..', '.'])) {
                continue;
            }
            $node_path = $path . DIRECTORY_SEPARATOR . $node;

            if(is_dir($node_path)) {
                $images_paths = array_merge($images_paths, self::getImagesFromDir($node_path));
            }

            if(self::isImage($node_path)) {
                $images_paths[] = array(
                    'source' => $node_path,
                    'destination' => str_replace(UPLOAD_DIR, DOWNLOAD_DIR, self::setWebpExtension($node_path))
                );
            }
        }

        return $images_paths;
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
     * Change path from relative to absolute
     * 
     * @param string $path Relative or absolute path
     * @param string $parent_path Absolute path to parent
     * 
     * @return string Absolute path
     */
    public static function makeAbsolutePath(string $path, string $parent_path)
    {
        if (strpos($path, $parent_path) === false) {
            $last_char = substr($parent_path, -1);

            if ($last_char != DIRECTORY_SEPARATOR) {
                $parent_path .= DIRECTORY_SEPARATOR;
            }

            return $parent_path . $path;
        }

        return $path;
    }

    /**
     * Create dir
     */
    public static function createDir($dir)
    {
        mkdir($dir, 0755, true);
    }

}