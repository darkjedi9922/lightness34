<?php namespace frame\modules;

use engine\users\User;

abstract class RightsDesc
{
    /**
     * Ассоциативный массив ['right' => 'description']
     */
    public abstract function listRights(): array;

    public function listAdditionChecks(User $user): array { return []; }

    /**
     * Для каждого права расчитывается степень двойки, соответствующий индексу права
     * в списке прав.
     * 
     * В побитовом виде у одного права стоит 1 только в одном месте в позиции,
     * соответствующей индексу права.
     * 
     * Если все числа каждого права сложить в одно число, получится маска всех прав
     * в виде целого числа.
     * 
     * @param array $rights An index array of the names of rights from the list.
     */
    public final function calcMask(array $rights)
    {
        $result = 0;
        $list = array_keys($this->listRights());
        for ($i = 0; $i < count($rights); ++$i) {
            $index = array_search($rights[$i], $list);
            $result += pow(2, $index);
        }
        return $result;
    }
}