<?php namespace frame\config;

use frame\errors\NotSupportedException;

/**
 * При создании объекта, если конфига не существует, ошибки не будет - объект
 * конфига просто будет пуст.
 */
abstract class Config
{
    private $data;
    private $modified = [];

    public function __construct()
    {
        $this->data = $this->loadConfig();
    }

    /**
     * Если значения не существует, вернет NULL. В зависимости от возможностей
     * конкретного типа конфига, NULL может быть и значением. 
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Если значения не существует, вернет NULL. В зависимости от возможностей
     * конкретного типа конфига, NULL может быть и значением. 
     * @return mixed|null 
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Значение может быть обновлено или добавлено. Удалено быть не может.
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        if (isset($this->data[$key]) && $this->data[$key] === $value) return;
        $this->data[$key] = $value;
        $this->modified[$key] = $value;
    }

    /**
     * Значение может быть обновлено или добавлено. Удалено быть не может. 
     * @param mixed $value
     */
    public function __set(string $key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Существует ли явно такой ключ в конфиге. Если он существует и его значение
     * равно NULL (если конкретный тип конфига поддерживает NULL), вернет TRUE.
     */
    public function isset(string $key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Возвращает только данные, которые были модифицированы.
     */
    public function getModifiedData(): array
    {
        return $this->data;
    }

    /**
     * Возвращает все данные, включая модифицированные.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data Могут быть обновлены все существующие данные, или только их
     * подмножество. Также могут быть добавлены новые значения.
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value)
            if (!isset($this->data[$key]) || $this->data[$key] !== $value)
                $this->modified[$key] = $value;
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Если фактически модифицированных/добавленных значений нет, попытки
     * пересохранения не будет.
     * 
     * @throws NotSupportedException Если сохранение не поддерживается.
     */
    public function save()
    {
        if (empty($this->modified)) return;
        $this->saveConfig($this->data);
    }

    /**
     * Если конфига нет, нужно вернуть пустой массив.
     */
    protected abstract function loadConfig(): array;

    /**
     * В зависимости от реализации конфига, перезаписываться могут все данные, а
     * могут только модифицированные.
     * 
     * @throws NotSupportedException Если запись не поддерживается.
     * @see getData
     * @see getModifiedData
     */
    protected abstract function saveConfig();
}