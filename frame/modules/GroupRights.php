<?php namespace frame\modules;

use frame\modules\UserGroup;
use frame\modules\RightsDesc;

class GroupRights
{
    private $desc;
    private $moduleId;
    private $groupId;
    private $rights = 0;

    public function __construct(RightsDesc $desc, int $moduleId, int $groupId)
    {
        $this->desc = $desc;
        $this->moduleId = $moduleId;
        $this->groupId = $groupId;
        if ($groupId !== UserGroup::ROOT_ID) {
            $this->rights = RightsStore::get()->loadRights($moduleId, $groupId);
        }
    }

    public function can(string $right): bool
    {
        return $this->groupId === UserGroup::ROOT_ID
            || (bool) ($this->rights & $this->desc->calcMask([$right]));
    }

    /**
     * @throws \Exception if the group is root.
     * 
     * Чтобы применить изменения, нужно вызвать метод save().
     */
    public function set(string $right, bool $can)
    {
        if ($this->groupId === UserGroup::ROOT_ID)
            throw new \Exception('The root rights cannot be modified.');
            
        $this->rights = $can ? 
            $this->rights | $this->desc->calcMask([$right]) :
            $this->rights & ~$this->desc->calcMask([$right]) ;
    }

    /**
     * @throws \Exception if the group is root.
     */
    public function save()
    {
        if ($this->groupId === UserGroup::ROOT_ID) 
            throw new \Exception('The root rights cannot be modified.');
        
        RightsStore::get()->saveRights(
            $this->moduleId, $this->groupId, $this->rights
        );
    }
}