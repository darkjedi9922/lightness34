<?php namespace frame\config;

use frame\config\FileConfig;
use frame\tools\data\ArrayData;

/**
 * Работает с json файлами многоуровневой вложенности.
 * 
 * @todo Стоит подумать над тем, чтобы принимать в параметрах строку json.
 * И сделать статический метод для считывания json с файла. Но тогда нужна
 * переменная, с названием файла. Можно либо унаследовать, либо воспользоваться
 * композицией.
 */
class Json implements FileConfig
{
    private $file;
    private $data;

    /**
     * @param string $file
     */
    public function __construct($file) {
        $this->data = new ArrayData;
        $this->setFile($file);
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
        if (file_exists($file))
            $this->data->setData(json_decode(file_get_contents($file), true));
    }

    public function save() {
        file_put_contents($this->file, json_encode($this->getData(), 
            JSON_PRETTY_PRINT | JSON_HEX_AMP));   
    }

    public function get($name) {
        return $this->data->get($name);
    }

    public function set($name, $value) {
        $this->data->set($name, $value);
    }
    
    public function isset($name) {
        return $this->data->isset($name);
    }

    public function __get($name){
        return $this->data->__get($name);
    }

    public function __set($name, $value) {
        $this->data->__set($name, $value);
    }
    
    public function __isset($name) {
        return $this->data->__isset($name);
    }

    public function setData($data) {
        $this->data->setData($data);
    }

    public function getData() {
        return $this->data->getData();
    }
}