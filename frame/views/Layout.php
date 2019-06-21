<?php namespace frame\views;

use frame\Core;

/**
 * Внутри вида Layout для вывода дочернего вида нужно использовать $layout->child->content.
 */
class Layout extends Layouted
{
    const FOLDER = 'view/layouts';

    /**
     * @var View $child Вид-содержимое
     */
    public $child;

    /**
     * @see parent::find()
     */
    public static function find($name)
    {
        return parent::find(self::FOLDER . '/' . $name);
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