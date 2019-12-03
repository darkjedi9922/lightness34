<?php namespace frame\modules;

use engine\users\Group;
use frame\database\Records;
use frame\modules\RightsDesc;

class GroupRights
{
    private $desc;
    private $groupId;
    private $rights = 0;
    private $record = null;

    public function __construct(RightsDesc $desc, int $moduleId, int $groupId)
    {
        $this->desc = $desc;
        $this->groupId = $groupId;
        if ($groupId !== Group::ROOT_ID) {
            $this->record = Records::select('group_rights', [
                'module_id' => $moduleId,
                'group_id' => $groupId
            ]);
            $this->rights = $this->loadRights($this->record);
        }
    }

    public function can(string $right): bool
    {
        return $this->groupId === Group::ROOT_ID
            || (bool) ($this->rights & $this->desc->calcMask([$right]));
    }

    /**
     * @throws \Exception if the group is root.
     * 
     * Чтобы применить изменения, нужно вызвать метод save().
     */
    public function set(string $right, bool $can)
    {
        if ($this->groupId === Group::ROOT_ID)
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
        if ($this->groupId === Group::ROOT_ID) 
            throw new \Exception('The root rights cannot be modified.');
        
        if ($this->rights === 0) $this->record->delete();

        // Тут лучше снова сделать запрос, чтобы узнать существует ли запись.
        // Потому что, может быть два процесса, в одном из которых запись уже
        // вставили. Тогда получится, что в этом процессе мы снова вставляем такую
        // запись. А если проверять сразу перед вставкой, задержка меньше ->
        // вероятность такой ошибки меньше.
        else if ($this->record->count('rights') === 0)
            $this->record->insert(['rights' => $this->rights]);
            
        else $this->record->update(['rights' => $this->rights]);
    }

    protected function loadRights(Records $record): int
    {
        return (int) $record->load(['rights'])->readScalar();
    }
}