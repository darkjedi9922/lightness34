<?php namespace frame\actions;

use function lightlib\move_uploaded_unique_file;

class UploadedFile
{
    /**
     * @var int Ошибок не возникло, файл был успешно загружен на сервер.
     */
    const UPLOAD_ERR_OK = 0;

    /**
     * @var int Размер принятого файла превысил максимально допустимый размер, 
     * который задан директивой upload_max_filesize конфигурационного файла php.ini.
     */
    const UPLOAD_ERR_INI_SIZE = 1;

    /**
     * @var int Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное 
     * в HTML-форме.
     */
    const UPLOAD_ERR_FORM_SIZE = 2;

    /**
     * @var int Загружаемый файл был получен только частично.
     */
    const UPLOAD_ERR_PARTIAL = 3;

    /**
     * @var int Файл не был загружен.
     */
    const UPLOAD_ERR_NO_FILE = 4;

    /**
     * @var int Отсутствует временная папка. Добавлено в PHP 5.0.3.
     */
    const UPLOAD_ERR_NO_TMP_DIR = 6;

    /**
     * @var int Не удалось записать файл на диск. Добавлено в PHP 5.1.0.
     */
    const UPLOAD_ERR_CANT_WRITE = 7;

    /**
     * @var int PHP-расширение остановило загрузку файла. PHP не предоставляет 
     * способа определить, какое расширение остановило загрузку файла; в этом может 
     * помочь просмотр списка загруженных расширений с помощью phpinfo(). Добавлено 
     * в PHP 5.2.0.
     */
    const UPLOAD_ERR_EXTENSION = 8;

    /**
     * @param string $name Имя файла из массива $_FILES. При создании экземпляра,
     * данные об этом файле автоматически будут подгружены из этого массива.
     */
    public function __construct($name)
    {
        if (isset($_FILES[$name])) $this->file = $_FILES[$name];
        else $this->file = [
            'name' => '',
            'type' => '',
            'size' => 0, // в байтах
            'tmp_name' => '',
            'error' => self::UPLOAD_ERR_NO_FILE
        ];
    }

    /**
     * Перемещает файл в директорию, добавляя номер к имени,
     * чтобы оно было уникально (если такой файл уже существует).
     * 
     * Если заданная директория не существует, создает ее.
     * 
     * Возвращает имя файла после перемещения.
     * 
     * @param string $folder
     * @return string
     */
    public function moveUnique($folder)
    {
        return move_uploaded_unique_file($this->file, $folder);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->file['type'];
    }
    
    /**
     * @return int размер в байтах
     */
    public function getSize()
    {
        return $this->file['size'];
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->file['error'];
    }

    /**
     * @param int $error
     * @return bool
     */
    public function hasError($error)
    {
        return $this->getError() === $error;
    }

    /**
     * @var array элемент $_FILES. Структуру массива смотри в конструкторе.
     */
    private $file = [];
}