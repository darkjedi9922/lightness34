<?php namespace frame\tools\files;

class Directory
{
    public static function deleteNonEmpty(string $path): bool
    {
        if (!file_exists($path)) return true;
        if (!is_dir($path)) return unlink($path);

        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!self::deleteNonEmpty($path . DIRECTORY_SEPARATOR . $item))
                return false;
        }

        return rmdir($path);
    }

    public static function delete(string $path): bool
    {
        return rmdir($path);
    }
}