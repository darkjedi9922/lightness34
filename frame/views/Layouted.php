<?php namespace frame\views;

/**
 * @see parent
 */
class Layouted extends View
{
    /**
     * @var Layout|null Шаблон
     */
    public $layout = null;

    /**
     * @var string|null Имя шаблона
     */
    public $layoutname = null;

    /**
     * @param string $name Имя вида - путь к файлу без расширения. 
     * Например: view/blocks/header
     * @param string|null $layout Имя шаблона
     * @throws \Exception Если файл вида не найден
     */
    public function __construct($name, $layout = null)
    {
        parent::__construct($name);
        $this->setLayout($layout);
    }

    /**
     * Метод может быть вызван внутри своего же файла вида. Тогда он переопределит
     * шаблон, заданный изначально
     * 
     * @param string|null $name Имя шаблона или null, чтобы убрать его
     */
    public function setLayout($name)
    {
        $this->layoutname = $name;
    }

    /**
     * Возвращает содержимое вида вместе со своим шаблоном, если он есть.
     * Предупреждение: вызов в самом себе может привести к бесконечной рекурсии и/или ошибкам.
     */
    public function __toString()
    {
        $content = $this->getContent(); // загружаем на случай, если внутри шаблон изменился
        if ($this->layoutname) {
            $this->layout = new Layout($this->layoutname, $this);
            return $this->layout; // внутри layout сам выведет содержимое текущего вида
        } else return $content;
    }
}