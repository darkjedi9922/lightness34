<?php namespace frame\route;

class Route
{
    /**
     * @var string $url Url страницы
     */
    public $url;

    /**
     * @var string $pagename Имя страницы. Может быть пустой строкой
     * (да, страница с пустым именем может быть)
     */
    public $pagename;

    /**
     * @var array Массив GET параметров из заданного url
     */
    public $args;

    /**
     * @var array Части в имени страницы (которые разделяются через "/")
     */
    private $pathElements;
    
    public function __construct(
        string $url,
        string $pagename,
        array $parts,
        array $args
    ) {
        $this->url = $url;
        $this->pagename = $pagename;
        $this->pathElements = $parts;
        $this->args = $args;
    }

    /**
     * Возвращает значение GET аргумента.
     * Вернет null, если он не задан.
     * 
     * @param string $name Имя аргумента
     * @return string|null
     */
    public function getArg($name)
    {
        if (isset($this->args[$name])) return $this->args[$name];
        else return null;
    }

    public function getPathPart(int $index): ?string
    {
        return $this->pathElements[$index] ?? null;
    }

    public function getPathParts(): array
    {
        return $this->pathElements;
    }

    public function isInNamespace(string $namespace): bool
    {
        $namespace = ltrim($namespace, '/');
        if ($namespace === '') return true;
        return strpos($this->pagename, $namespace) === 0;
    }

    public function isInAnyNamespace(array $namespaces): bool
    {
        for ($i = 0, $c = count($namespaces); $i < $c; ++$i)
            if ($this->isInNamespace($namespaces[$i])) return true;
        return false;
    }

    /**
     * Преобразует url в тот же url с обновленными get параметрами
     * 
     * @param array $newGet Новые значения get параметров. Чтобы удалить
     * существующий параметр, нужно присвоить ему значение null
     * @return string
     */
    public function toUrl(array $newGet = array()) : string
    {
        return Router::getDriver()->makeRoute($this->url, $newGet);
    }
}