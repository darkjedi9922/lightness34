<?php namespace frame\views;

use frame\LatePropsObject;

/**
 * @property-read string $content Внутреннее полностью сгенерированное содержимое вида.
 * Layout включает сюда содержимое дочернего вида.
 * Предупреждение: вызов в самом себе может привести к бесконечной рекурсии и/или ошибкам.
 */
class View extends LatePropsObject
{
    /**
     * @var string Имя вида
     */
    public $name;

    /**
     * @var string Файл вида
     */
    public $file;

    /**
     * @var Layout|null Шаблон
     */
    public $layout = null;

    /**
     * @var string|null Имя шаблона
     */
    public $layoutname = null;

    /**
     * Ищет сам view файл. Он может быть таких типов (в порядке приоритета): php, html. 
     * Приоритетность - если есть два файла: один - php, а другой - html, - будет выбран php.
     * 
     * @param string $name Имя вида - путь к файлу без расширения. 
     * Например: view/blocks/header
     * @return string|null
     */
    public static function find($name)
    {
        if (file_exists(ROOT_DIR . '/' . $name . '.php')) return $name . '.php';
        else if (file_exists(ROOT_DIR . '/' . $name . '.html')) return $name . '.html';
        else return null;
    }

    /**
     * @param string $name Имя вида - путь к файлу без расширения. 
     * Например: view/blocks/header
     * @param string|null Имя шаблона
     * @throws \Exception Если файл вида не найден
     */
    public function __construct($name, $layout = null)
    {
        $this->file = static::find($name);
        $this->name = $name;
        if (!$this->file) throw new \Exception('Viewfile for view "'.$name.'" was not found');
        $this->layoutname = $layout;
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
     * Возвращает свое уже сгенерированное содержимое.
     * Layout учитывает сюда содержимое дочернего вида.
     * 
     * Это нужно, чтобы перед показом, загрузить само содержимое, внутри которого могли
     * изменится настройки вида, чтобы успеть подстроиться под новые настройки.
     */
    protected function __create__content()
    {
        ob_start();
        require $this->file;
        return ob_get_clean();
    }

    /**
     * Выводит вид вместе со своим шаблоном, если он есть.
     * Предупреждение: вызов в самом себе может привести к бесконечной рекурсии и/или ошибкам.
     */
    public function show()
    {
        $content = $this->content; // загружаем на случай, если внутри шаблон изменился
        if ($this->layoutname) {
            $this->layout = new Layout($this->layoutname, $this);
            $this->layout->show(); // внутри layout сам выведет содержимое текущего вида
        } else echo $content;
    }

    /**
     * @param string $name Имя блока без расширения
     */
    public function includeBlock($name)
    {
        (new Block($name))->show();
    }

    /**
     * @param string $name Имя виджета без расширения
     */
    public function includeWidget($name)
    {
        (new Widget($name))->show();
    }
}