<?php namespace frame\tools;

use frame\LatePropsObject;

class File extends LatePropsObject
{
    /**
     * @param string $path
     * @return bool
     */
    public static function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Создает новый файл по указанному пути. Если такой уже был, он стирается.
     * 
     * @param string $path
     * @param bool $return Если true, создаст экземпляр этого класса для создаваемого
     * файла и вернет его.
     * @return self|null
     */
    public static function create($path, $return = false)
    {
        fopen($path, 'w');
        if ($return) return new self($path);
    }

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->finfo->file($this->path);
    }

    protected function __create__finfo()
    {
        return new \finfo(FILEINFO_MIME_TYPE);
    }

    /**
     * @return string
     */
    public function getBaseName()
    {
        return basename($this->path);
    }

    /**
     * @var string
     */
    private $path;
}