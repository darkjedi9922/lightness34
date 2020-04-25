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
    private $modifiedData = [];

    public abstract static function getTable(): string;

    /** 
     * @return static|null
     * @throws Exception if the record is not an identity. 
     */
    public static function select(array $fields)
    {
        $records = Records::from(static::getTable(), $fields);
        $data = $records->select()->readLine();
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

    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /** 
     * @throws Exception if there is no such value.
     * @return string|int|null
     */
    public function __get(string $name)
    {
        if (!array_key_exists($name, $this->data)) 
            throw new \Exception("There is no '$name' field in this identity.");
        return $this->data[$name];
    }

    public function __set(string $name, $value)
    {
        if ($name === 'id') throw new \Exception(
            'It is not possible to change id field of Identity.');
        if (array_key_exists($name, $this->data) && $this->data[$name] !== $value)
            $this->modifiedData[$name] = $value;
        $this->data[$name] = $value;
    }

    public function update()
    {
        if (!$this->exists) throw new \Exception('The record does not exist yet.');
        if (empty($this->modifiedData)) return;
        $records = Records::from(static::getTable(), ['id' => $this->id]);
        $records->update($this->modifiedData);
        $this->modifiedData = [];
    }

    public function insert(): int
    {
        unset($this->data['id']);
        $records = Records::from(static::getTable(), $this->data);
        $this->data['id'] = $records->insert();
        $this->exists = true;
        return $this->data['id'];
    }

    public function delete()
    {
        Records::from(static::getTable(), ['id' => $this->id])->delete();
    }
}