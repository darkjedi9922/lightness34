<?php namespace frame\modules;

use frame\core\Driver;

class Modules extends Driver
{
    private $modules = [];

    /**
     * @throws \Exception если модуль с таким именем уже существует.
     */
    public function set(Module $module)
    {
        if (isset($this->modules[$module->getName()])) throw new \Exception(
            "The module with name {$module->getName()} have already been added.");
        $this->modules[$module->getName()] = $module;
    }

    public function findByName(string $name): ?Module
    {
        return $this->modules[$name] ?? null;
    }

    public function findById(int $id): ?Module
    {
        foreach ($this->modules as $module) {
            /** @var Module $module */
            if ($module->getId() === $id) return $module;
        }
        return null;
    }

    public function toArray(): array
    {
        return $this->modules;
    }
}