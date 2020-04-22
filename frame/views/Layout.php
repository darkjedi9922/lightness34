<?php namespace frame\views;

class Layout extends Layouted
{
    private $child;

    public static function getExtensions(): array
    {
        return ['php'];
    }

    public static function getNamespace(): string
    {
        return 'layouts';
    }

    /**
     * @param string $name Имя вида
     * @param string $layout Вид компоновщика
     */
    public function __construct(
        string $name,
        Layouted $child,
        ?string $layout = null
    ) {
        $this->child = $child;
        parent::__construct($name, $layout);
    }

    public function loadChild(array $meta = []): LayoutChild
    {
        foreach ($meta as $key => $value) $this->child->setMeta($key, $value);
        // Предзагружаем сразу содержимое, чтобы все настройки внутри отработали.
        $this->child->getContent();
        return new LayoutChild($this->child);
    }

    public function hasChild(string $name): bool
    {
        if ($this->getChildName() === $name) return true;
        else if ($this->child instanceof self) $this->child->hasChild($name);
        return false; 
    }

    public function getChildName(): string
    {
        $namespace = $this->child->getNamespace();
        return ($namespace ? $namespace . '/' : '') . $this->child->name;
    }
}