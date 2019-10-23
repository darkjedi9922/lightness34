<?php namespace frame\modules;

use engine\users\Group;
use frame\database\Records;
use frame\modules\RightsDesc;

class GroupRights
{
    private $groupId;
    private $list = [];
    private $rights = 0;

    public function __construct(RightsDesc $desc, int $moduleId, int $groupId)
    {
        $this->groupId = $groupId;
        if ($groupId !== Group::ROOT_ID) {
            $this->list = $desc->listRights();
            $this->rights = (int) Records::select('group_rights', [
                'module_id' => $moduleId,
                'group_id' => $groupId
            ])->load(['rights'])->readScalar();
        }
    }

    public function can(string $right): bool
    {
        return $this->groupId === Group::ROOT_ID
            || (bool) ($this->rights & $this->calcMask($right));
    }

    private function calcMask(string $right): int
    {
        $index = array_search($right, array_keys($this->list));
        return pow(2, $index);
    }
}