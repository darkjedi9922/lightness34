<?php namespace frame\tools;

use finfo;

class File
{
    private $path;

    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Создает новый файл по указанному пути. Если такой уже был, он стирается.
     * @param string $mode See fopen() mode.
     * @param bool $return Если true, создаст экземпляр этого класса для создаваемого
     * файла и вернет его.
     */
    public static function create(string $path, bool $return = false): ?self
    {
        $handle = fopen($path, 'w');
        fclose($handle);
        return $return ? new self($path) : null;
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