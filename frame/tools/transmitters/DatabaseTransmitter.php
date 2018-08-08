<?php namespace frame\tools\transmitters;

use frame\database\Records;

/**
 * Так как этот класс использует базу данных, следует использовать его только 
 * в редких ситуациях, когда без БД не обойтись, во благо производительности.
 */
class DatabaseTransmitter extends DataTransmitter
{
    /**
     * Имя таблицы БД, используемая для трансмиттинга.
     * В ней должно быть два поля: name и value.
     * Оба имеют тип VARCHAR.
     */
    const TABLE = 'transmitting';

    /**
     * @var array Сами данные
     */
    private $data = [];

    /**
     * @var bool Были ли изменены данные с помощью этого объекта
     */
    private $changed = false;

    /**
     * @var Records Записи из таблицы
     */
    private $records;

    /**
     * О конструкторе сказать нечего
     */
    public function __construct()
    {
        $this->records = Records::select(self::TABLE);
        $data = $this->records->load()->readAll();
        for ($i = 0, $c = count($data); $i < $c; ++$i) {
            $this->data[$data[$i]['name']] = $data[$i]['value'];
        }
    }

    /**
     * О деструкторе тоже
     */
    public function __destruct()
    {
        $this->save();  
    }

    /**
     * {@inheritDoc}
     */
    public function setData($name, $value)
    {
        if (!$this->isSetData($name) || $this->data[$name] !== $value) {
            $this->data[$name] = $value;
            $this->changed = true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getData($name)
    {
        if (!$this->isSetData($name)) throw new \Exception('The data "'.$name.'" does not exist');
        return $this->data[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function isSetData($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function removeData($name)
    {
        if ($this->isSetData($name)) {
            unset($this->data[$name]);
            $this->changed = true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Изменения будут !полностью применены только !после загрузки страницы.
     * Если нужно ускорить процесс и сделать это мгновенно, используется 
     * данный метод.
     */
    public function save()
    {
        if ($this->changed) {
            $this->records->delete();
            if (!empty($this->data)) {
                $end = [];
                foreach ($this->data as $name => $value) $end[] = '"'.$name.'", "'.$value.'"';
                $end = implode(' UNION ALL SELECT ', $end);
                $this->records->db->query('INSERT INTO '.self::TABLE.' SELECT '.$end);
            }
            $this->changed = false;
        }
    }
}