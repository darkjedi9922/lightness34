<?php namespace tests\stubs;

use frame\modules\GroupRights;
use frame\database\Records;
use frame\modules\RightsDesc;
use tests\stubs\ModuleStub;

class GroupRightsStub extends GroupRights
{
    private $table;

    public function __construct(RightsDesc $desc, int $moduleId, int $groupId)
    {
        $this->table = [[
            'module_id' => (new ModuleStub('stub'))->getId(),
            'group_id' => 1,
            'rights' => 0b0000110 // can 'make' and 'create'
        ]];
        parent::__construct($desc, $moduleId, $groupId);
    }

    protected function loadRights(Records $record): int
    {
        $whereFields = $record->getWhereFields();
        $moduleId = $whereFields['module_id'] ?? null;
        if ($moduleId === null) throw new \Exception('Module id is not set');
        $groupId = $whereFields['group_id'] ?? null;
        if ($groupId === null) throw new \Exception('Group id is not set');
        $row = $this->findRow($moduleId, $groupId);
        return $row['rights'];
    }

    private function findRow(int $moduleId, int $groupId): array
    {
        foreach ($this->table as $row) {
            if (   $row['module_id'] === $moduleId
                && $row['group_id'] === $groupId)
            {
                return $row;
            }
        }

        throw new \Exception('There is no such row');
    }
}