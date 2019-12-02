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
        $record = new static($data);
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

    /**
     * @throws Exception if the non-empty data do not contains id
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            if (!isset($data['id'])) 
                throw new \Exception('The data do not contain id');
            $data['id'] = (int) $data['id'];
        }
        $this->data = $data;
    }

    /** 
     * @throws Exception if there is no such value.
     * @return string|int
     */
    public function __get(string $name)
    {
        if (!isset($this->data[$name])) 
            throw new \Exception("There is no '$name' in this identity.");
        return $this->data[$name];
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
        $records->update($newData);
    }

    public function insert(): int
    {
        unset($this->data['id']);
        $records = Records::select(static::getTable(), $this->data);
        $this->data['id'] = $records->insert();
        $this->exists = true;
        return $this->data['id'];
    }

    public function delete()
    {
        Records::select(static::getTable(), ['id' => $this->id])->delete();
    }
}