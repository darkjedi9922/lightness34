<?php namespace frame\views;

use frame\events\Events;

/**
 * @see parent
 */
class Layouted extends View
{
    /**
     * @var string|null Имя шаблона
     */
    private $layoutname = null;

    private $isRendering = false;

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
     * @param string|null $name Имя шаблона или null, чтобы убрать его.
     * @throws \Exception При попытке изменить шаблон уже во время рендеринга
     * (изнутри файла вида).
     */
    public function setLayout(?string $name)
    {
        if ($this->isRendering) throw new \Exception(
            "View {$this->name} is rendering yet. " .
            "You cannot change the layout during its rendering.");
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
        Events::getDriver()->emit(self::EVENT_BEFORE_RENDER, $this);
        $this->isRendering = true;
        if ($this->layoutname !== null) {
            $layout = new Layout($this->layoutname, $this);
            $layout->show(); // внутри layout сам выведет содержимое текущего вида
        } else echo $this->getContent();
        $this->isRendering = false;
        Events::getDriver()->emit(self::EVENT_AFTER_RENDER, $this);
    }

    public function getHtmlWithoutLayout(): string
    {
        $this->isRendering = true;
        $result = $this->getContent();
        $this->isRendering = false;
        return $result;
    }
}