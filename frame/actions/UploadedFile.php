<?php namespace frame\actions;

use frame\tools\File;
use function lightlib\generate_unique_filename;
use function lightlib\translit;
use function lightlib\last;

class UploadedFile extends File
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
     * @param array $data Массив по структуре равный элементу $_FILES:
     *  name => string,
     *  type => string,
     *  size => int, // в байтах
     *  tmp_name => string,
     *  error => int
     */
    public function __construct(array $data)
    {
        parent::__construct($data['tmp_name']);
        $this->file = $data;
        $this->throwImportantErrorException();
    }

    /**
     * Если файл не был задан (выбран пользователем в форме), вернет true.
     */
    public function isEmpty(): bool
    {
        return $this->hasError(self::UPLOAD_ERR_NO_FILE);
    }

    /**
     * Перемещает файл в директорию, добавляя номер к имени,
     * чтобы оно было уникально (если такой файл уже существует).
     * 
     * Если заданная директория не существует, создает ее.
     * 
     * @param bool $translit Нужно ли конвертировать имя файла в транслит.
     * @return string Имя файла после перемещения.
     */
    public function moveUnique(string $folder, bool $translit = true): string
    {
        $path = rtrim($folder, '/');
        $name = ($translit ? translit($this->file['name']) : $this->file['name']);
        $uniqueFile = generate_unique_filename($path . '/' . $name);
        if (!file_exists($path)) mkdir($path);
        move_uploaded_file($this->file['tmp_name'], $uniqueFile);
        return last(explode('/', $uniqueFile));
    }

    public function getType(): string
    {
        return $this->file['type'];
    }
    
    public function getSize(): int
    {
        return $this->file['size'];
    }

    public function getTempName(): string
    {
        return $this->file['tmp_name'];
    }

    public function getError(): int
    {
        return $this->file['error'];
    }

    public function hasError(int $error): bool
    {
        return $this->getError() === $error;
    }

    /**
     * @return bool Превысил ли файл максимально допустимый размер
     */
    public function hasSizeError(int $maxByteSize): bool
    {
        return $this->getSize() > $maxByteSize
            || $this->hasError(self::UPLOAD_ERR_INI_SIZE)
            || $this->hasError(self::UPLOAD_ERR_FORM_SIZE);
    }

    public function isLoaded(): bool
    {
        return $this->hasError(self::UPLOAD_ERR_OK);
    }

    public function toArray(): array
    {
        return $this->file;
    }

    private function throwImportantErrorException()
    {
        // Если одна из ошибок ниже появляется, нужно сразу бросить исключение
        // чтобы оно хотя-бы залогировалось куда-нибудь ибо при таких ошибках
        // будет не сразу ясно в чем проблема.
        if ($this->file['error'] === UPLOAD_ERR_NO_TMP_DIR)
            throw new \Exception('File uploading UPLOAD_ERR_NO_TMP_DIR error.');
        else if ($this->file['error'] === UPLOAD_ERR_CANT_WRITE)
            throw new \Exception('File uploading UPLOAD_ERR_CANT_WRITE error.');
        else if ($this->file['error'] === UPLOAD_ERR_EXTENSION)
            throw new \Exception('File uploading UPLOAD_ERR_EXTENSION error.');
    }

    /**
     * @var array
     */
    private $file = [];
}