<?php namespace frame\tools\files;

use finfo;

class File
{
    private $path;

    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Создает новый файл по указанному пути.
     * @param string $mode See fopen() mode.
     * @param bool $return Если true, создаст экземпляр этого класса для создаваемого
     * файла и вернет его.
     * @param bool $doEmpty Стирать ли содержимое файла, если он уже существует.
     */
    public static function create(
        string $path,
        bool $return = false,
        bool $doEmpty = true
    ): ?self {
        $handle = fopen($path, $doEmpty ? 'w' : 'a');
        fclose($handle);
        return $return ? new self($path) : null;
    }

    /**
     * Аналогично File::create(), но также создает директории в пути к файлу, если
     * они не существуют. Если файл уже создан и параметр doEmpty указан как true,
     * его содержимое будет очищено.
     */
    public static function createFullPath(
        string $path, 
        bool $return = false,
        bool $doEmpty = true
    ): ?self {
        $dir = implode('/', explode('/', $path, -1));
        if ($dir !== '' && !file_exists($dir)) Directory::createRecursive($dir);
        return self::create($path, $return, $doEmpty);
    }

    public static function delete(string $path)
    {
        unlink($path);
    }

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMime(): string
    {
        return $this->getInfo()->file($this->path);
    }

    public function getBaseName(): string
    {
        return basename($this->path);
    }

    private function getInfo(): finfo
    {
        return new finfo(FILEINFO_MIME_TYPE);
    }
}