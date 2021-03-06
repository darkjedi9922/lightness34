<?php namespace tests\modules\drivers;

use frame\auth\RightsStore;
use tests\stubs\ModuleStub;

class RightsStoreStub extends RightsStore
{
    private $table;

    public function __construct()
    {
        $this->table = [[
            'module_id' => (new ModuleStub('stub'))->getId(),
            'group_id' => 1,
            'rights' => 0b0000110 // can 'make' and 'create'
        ], [
            'module_id' => (new ModuleStub('stub'))->getId(),
            'group_id' => 2,
            'rights' => 0b0001000 // can 'see-own'
        ], [
            'module_id' => (new ModuleStub('stub'))->getId(),
            'group_id' => 3,
            'rights' => 0b0010000 // can 'execute-order'
        ]];
    }

    public function loadRights(int $moduleId, int $groupId): int
    {
        foreach ($this->table as $row) {
            if ($row['module_id'] === $moduleId && $row['group_id'] === $groupId)
                return $row['rights'];
        }

        return 0;
    }

    public function saveRights(int $moduleId, int $groupId, int $rights)
    {
        foreach ($this->table as &$row) {
            if ($row['module_id'] === $moduleId && $row['group_id'] === $groupId)
                $row['rights'] = $rights;
        }
    }
}