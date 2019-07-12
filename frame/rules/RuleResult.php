<?php namespace frame\rules;

/**
 * Экземпляр этого класса передается по цепочке обработчиков правил поля экшна. 
 * Обработчики могут сохранять в него промежуточные данные, которые затем можно 
 * использовать в дальнейших обработчиках и теле экшна дабы не дублировать 
 * получение/загрузку тех же данных.
 */
class RuleResult
{
    public function result(bool $result) : self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Аналогично result(true).
     */
    public function succeed() : self
    {
        return $this->result(true);
    }

    /**
     * Аналогично result(false).
     */
    public function fail() : self
    {
        return $this->result(false);
    }

    /**
     * Был ли задан результат.
     */
    public function hasResult() : bool
    {
        return $this->result !== null;
    }

    public function isSuccess() : bool
    {
        return $this->result === true;
    }

    public function isFail() : bool
    {
        return $this->result === false;
    }

    public function restoreResult()
    {
        $this->result = null;
    }

    public function stop(): self
    {
        $this->isStopped = true;
        return $this;
    }

    public function isStopped(): bool
    {
        return $this->isStopped;
    }

    public function setInterData(string $name, $data)
    {
        $this->interData[$name] = $data;
    }

    /**
     * Если заданной данной нет, вернет null.
     * @return mixed
     */
    public function getInterData(string $name)
    {
        if (!isset($this->interData[$name])) return null;
        return $this->interData[$name];
    }

    /**
     * @return mixed
     * @throws \Exception Если запрашиваемой данной нет (или она null).
     */
    public function requireInterData(string $name)
    {
        $data = $this->getInterData($name);
        if ($data === null) 
            throw new \Exception('There is no inter data "' . $name . '"');
        return $data;
    }

    /**
     * Массив вида [name => data].
     */
    public function getInterDataAll(): array
    {
        return $this->interData;
    }

    /**
     * @var bool|null
     */
    private $result;

    /**
     * @var bool
     */
    private $isStopped = false;

    /**
     * @var array [name => data]
     */
    private $interData = [];
}