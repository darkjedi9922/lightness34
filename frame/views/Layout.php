<?php namespace frame\views;

class Layout extends Layouted
{
    private $child;

    public static function getExtensions(): array
    {
        return ['php'];
    }

    public static function getFolder(): string
    {
        return View::getFolder() . '/layouts';
    }

    /**
     * @param string $name Имя вида
     * @param string $layout Вид компоновщика
     */
    public function __construct(string $name, Layouted $child, string $layout = '')
    {
        $this->child = $child;
        parent::__construct($name, $layout);
    }

    public function showChild()
    {
        echo $this->child->getContent();
    }

    /** @return mixed|null */
    public function getChildMeta(string $name)
    {
        return $this->child->getMeta($name);
    }
}