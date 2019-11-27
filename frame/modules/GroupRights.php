<?php namespace frame\modules;

use engine\users\Group;
use frame\database\Records;
use frame\modules\RightsDesc;

class GroupRights
{
    private $groupId;
    private $desc;
    private $rights = 0;
    private $record = null;

    public function __construct(RightsDesc $desc, int $moduleId, int $groupId)
    {
        $this->groupId = $groupId;
        $this->desc = $desc;
        if ($groupId !== Group::ROOT_ID) {
            $this->record = Records::select('group_rights', [
                'module_id' => $moduleId,
                'group_id' => $groupId
            ]);
            $this->rights = (int) $this->record->load(['rights'])->readScalar();
        }
    }

    public function can(string $right): bool
    {
        if ($this->groupId === Group::ROOT_ID) return true;

        else if ($this->desc->isComplex($right)) {
            foreach ($this->desc->complexRights()[$right] as $innerRight) {
                if ($this->can($innerRight)) return true;
            }
            return false;
        }

        return (bool) ($this->rights & $this->calcMask($right));
    }

    /**
     * Устанавливает только те права, которые перечислены в RightsDesc::listRights().
     * Чтобы применить изменения, нужно вызвать метод save().
     * 
     * @throws \Exception if the group is root.
     */
    public function set(string $right, bool $can)
    {
        if ($this->groupId === Group::ROOT_ID)
            throw new \Exception('The root rights cannot be modified.');
            
        $this->rights = $can ? 
            $this->rights | $this->calcMask($right) :
            $this->rights & ~$this->calcMask($right) ;
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

    /**
     * Возвращает степень двойки, соответствующий индексу права в списке прав,
     * которые перечислены в RightsDesc::listRights().
     * 
     * В побитовом виде у этого числа стоит 1 только в одном месте в позиции,
     * соответствующей индексу права.
     * 
     * @throws Exception if there is no such listed right.
     */
    private function calcMask(string $right): int
    {
        if (!$this->desc->isListed($right)) 
            throw new \Exception("There is no listed right $right");

        $index = array_search($right, array_keys($this->desc->listRights()));
        return pow(2, $index);
    }
}