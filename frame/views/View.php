<?php namespace frame\views;

use frame\events\Events;

function __show(View $self) 
{
    require $self->file;
}

class View
{
    /**
     * Happens at first time when view starts to load.
     * Event argument: View object.
     */
    const EVENT_LOAD_START = 'view-load-start';

    /**
     * Happens at first time when view ends to load.
     * Event argument: View object.
     */
    const EVENT_LOAD_END = 'view-load-end';

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
     * Это может быть использовано, например, в layout'e для получения данных из 
     * дочернего элемента, или из родительского вида, после обработки (показа)
     * дочернего.
     */
    private $meta = [];

    /**
     * Содержимое файла вида кешируется при первом обращении к нему.
     */
    private $cachedContent = null;

    public static function getNamespace(): string
    {
        return '';
    }

    /**
     * Расширения файлов видов, которые поддерживет реализуемый вид.
     * Задаются в порядке приоритета. Например: ['php', 'html'].
     * 
     * Можно переопределить в потомках, чтобы фиксировать доступные расширения вида.
     */
    public static function getExtensions(): array
    {
        return ['php', 'html'];
    }

    /**
     * Ищет сам view файл. По-умолчанию, он может быть таких типов: php, html. 
     * 
     * @param string $name Имя вида - путь к файлу без расширения.
     * Например: blocks/header
     */
    public static function find(string $name): ?string
    {
        $namespace = trim(static::getNamespace(), '/');
        $baseFolder = ViewRouter::getDriver()->getBaseFolder();
        $folder = $baseFolder . ($namespace ? "/$namespace" : '');
        foreach (static::getExtensions() as $ext) {
            $file = "$folder/$name.$ext";
            if (file_exists($file)) return $file;
        }
        return null;
    }

    /**
     * @param string $name Имя вида - путь к файлу без расширения. 
     * Например: blocks/header
     * @throws \Exception Если файл вида не найден
     */
    public function __construct($name)
    {
        $this->file = static::find($name);
        if (!$this->file) throw new \Exception('Viewfile for view "'.$name.'" was not found');
        $this->name = $name;
    }

    /**
     * Возвращает свое уже сгенерированное содержимое.
     * 
     * Это нужно, чтобы перед показом, загрузить само содержимое, внутри которого 
     * могли изменится настройки вида, чтобы успеть подстроиться под новые настройки.
     */
    protected function getContent()
    {
        if ($this->cachedContent === null) {
            Events::getDriver()->emit(self::EVENT_LOAD_START, $this);
            ob_start();
            __show($this);
            $this->cachedContent = ob_get_clean();
            Events::getDriver()->emit(self::EVENT_LOAD_END, $this);
        }
        return $this->cachedContent;
    }

    /**
     * Выводит содержимое вида.
     * Предупреждение: вызов в самом себе может привести к бесконечной рекурсии и/или ошибкам.
     */
    public function show()
    {
        echo $this->getContent();
    }

    /**
     * Аналогично show(), только вывод в строку.
     */
    public final function getHtml(): string
    {
        ob_start();
        $this->show();
        return ob_get_clean();
    }

    public function setMeta(string $name, $value)
    {
        $this->meta[$name] = $value;
    }

    /** @return mixed|null */
    public function getMeta(string $name)
    {
        return $this->meta[$name] ?? null;
    }

    public function getMetaArray(): array
    {
        return $this->meta;
    }

    /**
     * @param string $namespace Examples: "pages", "pages/admin", "blocks" etc.
     */
    public function isInNamespace(string $namespace): bool
    {
        $namespace = trim($namespace, '/');
        if (empty($namespace)) return true;
        $ownNamespace = trim($this->getNamespace(), '/');
        $name = ($ownNamespace ? "$ownNamespace/" : '') . $this->name;
        return strpos($name, $namespace) === 0;
    }
}