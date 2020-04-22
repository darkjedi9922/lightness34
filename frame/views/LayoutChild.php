<?php namespace frame\views;

class LayoutChild
{
    private $child;

    public function __construct(Layouted $child)
    {
        $this->child = $child;
    }

    public function show()
    {
        echo $this->child->getHtmlWithoutLayout();
    }

    /** @return mixed|null */
    public function getMeta(string $name)
    {
        return $this->child->getMeta($name);
    }
}