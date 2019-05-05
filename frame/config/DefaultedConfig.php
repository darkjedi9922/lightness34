<?php namespace frame\config;

use frame\config\Config;

class DefaultedConfig implements Config
{
    /**
     * @param Config $main
     * @param Config $default
     */
    public function __construct($main, $default) {
        $this->main = $main;
        $this->default = $default;
    }

    /**
     * @return Config
     */
    public function getMain() {
        return $this->main;
    }

    /**
     * @param Config $main
     */
    public function setMain($main) {
        $this->main = $main;
    }

    /**
     * @return Config
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @param Config $default
     */
    public function setDefault($default) {
        $this->default = $default;
    }

    /**
     * Смотрит значение сначала в главном конфиге, а потом в конфиге по-умолчанию.
     * {@inheritDoc}
     */
    public function get($name) {
        if (!$this->main->isset($name)) return $this->default->get($name);
        return $this->main->get($name);
    }

    /**
     * Устанавливает значение в главный конфиг.
     * {@inheritDoc}
     */
    public function set($name, $value) {
        $this->main->set($name, $value);
    }

    /**
     * Смотрит значение сначала в главном конфиге, а потом в конфиге по-умолчанию.
     * {@inheritDoc}
     */
    public function isset($name) {
        return $this->main->isset($name) || $this->default->isset($name);
    }

    /**
     * Смотрит значение сначала в главном конфиге, а потом в конфиге по-умолчанию.
     * {@inheritDoc}
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * Устанавливает значение в главный конфиг.
     * {@inheritDoc}
     */
    public function __set($name, $value) {
        $this->set($name, $value);
    }

    /**
     * Смотрит значение сначала в главном конфиге, а потом в конфиге по-умолчанию.
     * {@inheritDoc}
     */
    public function __isset($name) {
        return $this->isset($name);
    }

    /**
     * Устанавливает данные в главном конфиге.
     * {@inheritDoc}
     */
    public function setData($data) {
        $this->main->setData($data);
    }

    /**
     * Возвращает объединенные данные из обоих конфигов.
     * {@inheritDoc}
     */
    public function getData() {
        $mainData = $this->main->getData();
        $defaultData = $this->default->getData();
        return $this->mergeData($mainData, $defaultData);
    }

    /**
     * @param array $main
     * @param array $default
     * @return array
     */
    private function mergeData($main, $default) {
        foreach ($default as $key => $value) {
            if (!isset($main[$key])) $main[$key] = $value;
            else if (is_array($value)) 
                $main[$key] = $this->mergeData($main[$key], $value);
        }
        return $main;
    }

    private $main;
    private $default;
}