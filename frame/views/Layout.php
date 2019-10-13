<?php namespace frame\views;

/**
 * Внутри вида Layout для вывода дочернего вида нужно использовать $layout->child->content.
 */
class Layout extends Layouted
{
    /**
     * @var View $child Вид-содержимое
     */
    public $child;

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
     * @param View $child Вид-содержимое
     * @param string $layout Вид компоновщика
     */
    public function __construct($name, $child, $layout = '')
    {
        $this->child = $child;
        parent::__construct($name, $layout);
    }
}