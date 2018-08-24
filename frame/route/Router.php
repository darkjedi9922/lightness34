<?php namespace frame\route;

use frame\route\Request;
use frame\LatePropsObject;

/**
 * Router занимается обработкой URL.
 * 
 * @property-read Router|null $previous Роутер предыдущего запроса. Если
 * предыдущего запроса нет - вернет null.
 * @property-read int $pagenumber Номер страницы по счету в списке. Определяется get параметром "p".
 * Если его нет, то всегда равен 1.
 */
class Router extends LatePropsObject
{
    /**
     * @var string $url Url страницы
     */
    public $url;

    /**
     * @var string $pagename Имя страницы. Может быть пустой строкой
     * (да, страница с пустым именем может быть)
     */
    public $pagename;

    /**
     * @var array Массив GET параметров из заданного url
     */
    public $args;

    /**
     * @var array Части в имени страницы (которые разделяются через "/")
     */
    private $pathElements;

    /**
     * Преобразует url в тот же url с обновленными get параметрами
     * 
     * @param string $url
     * @param array $newGet Новые значения get параметров. Чтобы удалить
     * существующий параметр, нужно присвоить ему значение null
     * @return string
     */
    public static function toUrlOf($url, $newGet = [])
    {
        if (empty($newGet)) return $url;
        else {
            $url = trim($url, '=&');
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $args);
            $newArgs = array_merge($args, $newGet);
            $newQuery = http_build_query($newArgs);
            $oldQuery = $query;
            if (!empty($oldQuery)) return str_replace($oldQuery, $newQuery, $url);
            else return $url.'?'.$newQuery;
        }
    }
    
    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = trim($url, '=&');
        $this->pagename = trim(parse_url($this->url, PHP_URL_PATH), '/');
        $this->pathElements = explode('/', $this->pagename);
        $query = parse_url($this->url, PHP_URL_QUERY);
        parse_str($query, $this->args);
    }

    /**
     * Возвращает значение GET аргумента.
     * Вернет null, если он не задан.
     * 
     * @param string $name Имя аргумента
     * @return string|null
     */
    public function getArg($name)
    {
        if (isset($this->args[$name])) return $this->args[$name];
        else return null;
    }

    /**
     * Возвращает часть имени страницы.
     * Вернет пустую строку, если заданной части нет.
     * 
     * @param int $index
     * @return string
     * @see $pathElements
     */
    public function getPathPart($index)
    {
        if (isset($this->pathElements[$index])) return $this->pathElements[$index];
        else return '';
    }

    /**
     * Преобразует url в тот же url с обновленными get параметрами
     * 
     * @param array $newGet Новые значения get параметров. Чтобы удалить
     * существующий параметр, нужно присвоить ему значение null
     * @return string
     */
    public function toUrl(array $newGet = array()) : string
    {
        return static::toUrlOf($this->url, $newGet);
    }

    /**
     * @see $previous
     */
    protected function __create__previous()
    {
        if (Request::hasReferer()) return new Router(Request::getReferer());
        else return null;
    }

    /**
     * @see $pagenumber
     */
    protected function __create__pagenumber()
    {
        $p = $this->getArg('p');
        if ($p === null || $p === '' || $p <= 0) return 1;
        else return (int) $p;
    }
}
