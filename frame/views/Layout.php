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

    public function showChild()
    {
        echo $this->child->getContent();
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

    /** @return mixed|null */
    public function getChildMeta(string $name)
    {
        return $this->child->getMeta($name);
    }
}