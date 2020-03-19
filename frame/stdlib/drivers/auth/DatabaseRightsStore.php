<?php namespace frame\stdlib\drivers\auth;

use frame\database\Records;

class DatabaseRightsStore extends \frame\auth\RightsStore
{
    public function loadRights(int $moduleId, int $groupId): int
    {
        return $this->getRecord($moduleId, $groupId)
            ->select(['rights'])
            ->readScalar() ?? 0;
    }

    public function saveRights(int $moduleId, int $groupId, int $rights)
    {
        $record = $this->getRecord($moduleId, $groupId);
        if ($rights === 0) $record->delete();

        // Тут лучше снова сделать запрос, чтобы узнать существует ли запись.
        // Потому что, может быть два процесса, в одном из которых запись уже
        // вставили. Тогда получится, что в этом процессе мы снова вставляем такую
        // запись. А если проверять сразу перед вставкой, задержка меньше ->
        // вероятность такой ошибки меньше.
        else if ($record->count('rights') === 0)
            $record->insert(['rights' => $rights]);

        else $record->update(['rights' => $rights]);
    }

    private function getRecord(int $moduleId, int $groupId): Records
    {
        return Records::from('group_rights', [
            'module_id' => $moduleId,
            'group_id' => $groupId
        ]);
    }
}