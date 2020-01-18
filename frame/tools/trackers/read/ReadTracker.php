<?php namespace frame\tools\trackers\read;

use frame\database\Records;

class ReadTracker
{
    const TABLE = 'read_tracking';

    private $name;
    private $what;
    private $for;
    private $records;

    public function __construct(string $name, int $what, ?int $for = null)
    {
        $this->name = $name;
        $this->what = $what;
        $this->for = $for;

        $where = ['name' => $this->name, 'what_id' => $this->what];
        if ($this->for !== null) $where['for_id'] = $this->for;
        $this->records = Records::from(static::TABLE, $where);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getWhat(): int
    {
        return $this->what;
    }

    public function setWhat(int $what)
    {
        $this->what = $what;
    }

    public function setFor(?int $for = null)
    {
        $this->for = $for;
    }

    public function getFor(): ?int
    {
        return $this->for;
    }

    public function reset()
    {
        $this->getRecords()->delete();
    }

    protected function getRecords(): Records
    {
        return $this->records;
    }
}