<?php namespace frame\modules;

abstract class Module
{
    private $parent;
    private $name;

    /**
     * @param string $name Должно быть уникальным в пределах всего приложения.
     */
    public function __construct(string $name, Module $parent = null) {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * Имя является строковым идентификатором каждого модуля.
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Id является числовым идентификатором каждого модуля, основанный на имени.
     */
    public function getId(): int {
        return crc32($this->name);
    }

    public function getParent(): ?Module {
        return $this->parent;
    }

    /**
     * Модуль может не иметь прав, тогда нужно вернуть null.
     */
    public abstract function createRightsDescription(): ?RightsDesc;
}