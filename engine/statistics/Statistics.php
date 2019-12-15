<?php namespace engine\statistics;

use frame\config\Json;

abstract class Statistics
{
    private $output;

    public function __construct(string $name)
    {
        if (!is_dir('runtime')) mkdir('runtime');
        if (!is_dir('runtime/statistics')) mkdir('runtime/statistics');
        $this->output = new Json(ROOT_DIR . "/runtime/statistics/$name.json");
    }

    public function __destruct()
    {
        $this->output->setData($this->toArray());
        $this->output->save();
    }

    public abstract function toArray(): array;

    protected function getPreviousState(): array
    {
        return $this->output->getData();
    }
}