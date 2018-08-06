<?php namespace frame;

/**
 * Класс позволяет добавлять свойства объекта (не статические), которые 
 * будут инициализированы только один раз перед первым использованием.
 * 
 * Эти свойства при этом будут доступны как обычные публичные свойства и будут
 * защищены от записи (read-only).
 * 
 * Чтобы использовать эту возможность, класс должен быть унаследован от данного.
 * Для добавления нового свойства нужно определить метод вида: 
 * protected function __create__nameOfProperty() { //код, возвращающий значение }
 * 
 * После этого можно получить доступ к свойству: $obj->nameOfProperty
 * 
 * Этот метод будет вызван лишь один раз когда/если понадобится свойство.
 * 
 * При этом, перед классом стоит указать phpDoc комментарий вида (без кавычек):
 * "@property-read int $nameOfProperty Описание свойства"
 * Он нужен, чтобы IDE и другие разработчки могли проще понять. что в классе 
 * определяется свойство nameOfProperty.
 */
class LatePropsObject
{
    /**
     * @var array Ассоциативный массив инициализированных свойств
     */
    private $props = [];

    /**
     * При переопределении нужно вызывать родительский метод через parent::__get($name)
     */
    public function __get($name)
    {
        if (!isset($this->props[$name])) {
            $method = '__create__' . $name;
            $this->props[$name] = $this->$method();
        }
        return $this->props[$name];
    }

    /**
     * При переопределении нужно вызывать родительский метод через parent::__isset($name)
     */
    public function __isset($name)
    {
        return isset($this->props[$name]);
    }
}