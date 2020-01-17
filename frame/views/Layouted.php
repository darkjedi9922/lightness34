<?php namespace frame\views;

/**
 * @see parent
 */
class Layouted extends View
{
    /**
     * @var string|null Имя шаблона
     */
    private $layoutname = null;

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
    public function setLayout(?string $name)
    {
        $this->layoutname = $name;
    }

    public function getLayout(): ?string
    {
        return $this->layoutname;
    }

    /**
     * Возвращает содержимое вида вместе со своим шаблоном, если он есть.
     * Предупреждение: вызов в самом себе может привести к бесконечной рекурсии и/или ошибкам.
     */
    public function show()
    {
        $content = $this->getContent(); // загружаем на случай, если внутри шаблон изменился
        if ($this->layoutname !== null) {
            $layout = new Layout($this->layoutname, $this);
            $layout->show(); // внутри layout сам выведет содержимое текущего вида
        } else echo $content;
    }
}