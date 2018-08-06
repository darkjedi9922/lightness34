<?php namespace frame\tools;

/**
 * Работает с json файлами одноуровневой вложенности.
 */
class Json
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var bool
     */
    private $changed = false;

    /**
     * Если файла не существует, работа будет как с пустым файлом. В него можно записать новые
     * значения и он будет создан с этими значениями.
     * 
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        if (file_exists($file)) $this->data = json_decode(file_get_contents($file), true);
    }

    public function __destruct()
    {
        if ($this->changed) file_put_contents($this->getFile(), json_encode($this->data, JSON_PRETTY_PRINT));
    }

    /**
     * @param string $name
     * @return string|int|float|array
     */
    public function get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param string|int|float|array $value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
        $this->changed = true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @see get()
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @see set()
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @see isset()
     */
    public function __isset($name)
    {
        return $this->isset($name);
    }
}