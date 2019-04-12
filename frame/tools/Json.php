<?php namespace frame\tools;

use frame\tools\Data;

/**
 * Работает с json файлами многоуровневой вложенности.
 * 
 * @todo Стоит подумать над тем, чтобы принимать в параметрах строку json.
 * И сделать статический метод для считывания json с файла. Но тогда нужна
 * переменная, с названием файла. Можно либо унаследовать, либо воспользоваться
 * композицией.
 * @todo Сделать наконец метод save(), вместо встроенного автоматического
 * пересохранения. Очень мешает при тестировании как минимум.
 */
class Json extends Data
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var bool
     */
    private $changed = false;

    /**
     * @param string $file
     * @return bool
     */
    public static function exists($file)
    {
        return file_exists($file);
    }

    /**
     * Если файла не существует, работа будет как с пустым файлом. В него можно 
     * записать новые значения и он будет создан с этими значениями. Если было
     * передано null, никакого файла создано не будет.
     * 
     * @param string|null $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        if (file_exists($file)) 
            parent::__construct(json_decode(file_get_contents($file), true));
    }

    public function __destruct()
    {
        if (!$this->file) return;
        if ($this->changed) 
            file_put_contents($this->getFile(), json_encode($this->getData(), JSON_PRETTY_PRINT));
    }

    /**
     * @return string Путь к файлу.
     */
    public function getFile()
    {
        return $this->file;
    }
}