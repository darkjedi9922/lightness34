<?php namespace frame\config;

use frame\config\Config;

/**
 * Если файла не существует, работа будет как с пустым файлом. В него можно 
 * записать новые значения и он будет создан с этими значениями. Если было
 * передано null, никакого файла создано не будет.
 */
interface FileConfig extends Config
{
    /**
     * Устанавливает новый файл и загружает из него данные.
     * @param string|null $file
     */
    public function setFile($file);

    /**
     * @return string Путь к файлу.
     */
    public function getFile();

    /**
     * Чтобы применить изменения конфига, нужно его сохранить.
     */
    public function save();
}