<?php namespace frame\database;

/**
 * Records of Identity type must have `id` field as a primary key.
 */
abstract class Identity
{
    /** 
     * @var bool $exists Был загружен БД (true) 
     * или создан как пустой объект (false). 
     */
    private $exists = false;

    private $data;

    public abstract static function getTable(): string;

    /** 
     * @return static|null
     * @throws Exception if the record is not an identity. 
     */
    public static function select(array $fields)
    {
        $records = Records::select(static::getTable(), $fields);
        $data = $records->load()->readLine();
        if (!$data) return null;
        if (!isset($data['id']))
            throw new \Exception('The record is not an identity');
        $record = new static;
        $record->id = $data['id'];
        $record->data = $data;
        $record->exists = true;
        return $record;
    }

    /** 
     * @return static|null
     * @throws Exception if the record is not an identity. 
     */
    public static function selectIdentity(int $id)
    {
        return static::select(['id' => $id]);
    }

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /** @return mixed|null */
    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set(string $name, $value)
    {
        if ($name === 'id' && $this->exists) throw new \Exception(
            'It is not possible to change primary key 
            value on already inserted record.');
        $this->data[$name] = $value;
    }

    public function update()
    {
        if (!$this->exists) throw new \Exception('The record does not exist yet.');
        $records = Records::select(static::getTable(), ['id' => $this->id]);
        $newData = $this->data;
        unset($newData['id']);
        // $records->update($newData);
    }

    public function insert()
    {
        unset($this->data['id']);
        $records = Records::select(static::getTable(), $this->data);
        $this->data['id'] = $records->insert();
        $this->exists = true;
    }
}