<?php namespace frame\tools\files;

class Directory
{
    public static function exists(string $path): bool
    {
        return File::exists($path);
    }

    public static function create(string $path): bool
    {
        return mkdir($path);
    }
    
    public static function createRecursive(string $path): bool
    {
        return mkdir($path, 0777, true);
    }

    public static function empty(string $path): bool
    {
        if (!file_exists($path)) return true;
        if (!is_dir($path)) return unlink($path);

        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!self::deleteNonEmpty($path . DIRECTORY_SEPARATOR . $item))
                return false;
        }

        return true;
    }

    public static function deleteNonEmpty(string $path): bool
    {
        if (!file_exists($path)) return true;
        if (!is_dir($path)) return unlink($path);

        self::empty($path);
        return rmdir($path);
    }

    public static function delete(string $path): bool
    {
        return rmdir($path);
    }
}