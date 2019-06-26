<?php namespace frame\views;

use frame\Core;
use frame\GlobalAccess;

class View extends GlobalAccess
{
    /**
     * @var Core Ссылка на экземпляр приложения для удобства
     */
    public $app;

    /**
     * @var string Имя вида
     */
    public $name;

    /**
     * @var string Файл вида
     */
    public $file;

    /**
     * @var array Ассоциативный массив мета-данных вида. 
     * Это может быть использовано, например, в layout'e для получения данных из дочернего элемента.
     */
    private $meta = [];

    /**
     * Ищет сам view файл. Он может быть таких типов (в порядке приоритета): php, html. 
     * Приоритетность - если есть два файла: один - php, а другой - html, - будет выбран php.
     * 
     * @param string $name Имя вида - путь к файлу без расширения. 
     * Например: view/blocks/header
     * @return string|null
     */
    public static function find($name)
    {
        if (file_exists(ROOT_DIR . '/' . $name . '.php')) return $name . '.php';
        else if (file_exists(ROOT_DIR . '/' . $name . '.html')) return $name . '.html';
        else return null;
    }

    /**
     * @param string $name Имя вида - путь к файлу без расширения. 
     * Например: view/blocks/header
     * @throws \Exception Если файл вида не найден
     */
    public function __construct($name)
    {
        $this->file = static::find($name);
        if (!$this->file) throw new \Exception('Viewfile for view "'.$name.'" was not found');
        $this->name = $name;
        $this->app = Core::$app;
    }

    /**
     * Возвращает свое уже сгенерированное содержимое.
     * Это нужно, чтобы перед показом, загрузить само содержимое, внутри которого могли
     * изменится настройки вида, чтобы успеть подстроиться под новые настройки.
     */
    public function getContent()
    {
        ob_start();
        require $this->file;
        return ob_get_clean();
    }

    /**
     * Возвращает содержимое вида.
     * Предупреждение: вызов в самом себе может привести к бесконечной рекурсии и/или ошибкам.
     */
    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * Выводит содержимое вида.
     * Предупреждение: вызов в самом себе может привести к бесконечной рекурсии и/или ошибкам.
     */
    public function show()
    {
        echo $this->__toString();
    }

    /**
     * Устанавливает мета-информацию вида.
     * Это может быть использовано, например, в layout'e для получения данных из дочернего элемента.
     * @param string $name
     * @param mixed $value
     */
    public function setMetaOne($name, $value)
    {
        $this->meta[$name] = $value;
    }

    /**
     * Устанавливает мета-информацию вида.
     * Это может быть использовано, например, в layout'e для получения данных из дочернего элемента.
     * @param array $data Ассоциативный массив мета-данных вида.
     */
    public function setMetaArray($data)
    {
        $this->meta = $data;
    }

    /**
     * Возвращает мета-информацию вида.
     * Это может быть использовано, например, в layout'e для получения данных из дочернего элемента.
     * @param string $name
     * @return mixed
     */
    public function getMeta($name)
    {
        return $this->meta[$name];
    }
}