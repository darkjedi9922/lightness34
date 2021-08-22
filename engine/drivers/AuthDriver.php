<?php namespace engine\drivers;

use engine\users\User;
use frame\auth\GroupUser;
use frame\database\Records;

class AuthDriver extends \frame\auth\AuthDriver
{
  public function getUserMe(): GroupUser
  {
    return User::getMe();
  }

  public function loadRights(int $moduleId, int $groupId): int
  {
    return $this->getRecord($moduleId, $groupId)->select(['rights'])->readScalar() ?? 0;
  }
  public function saveRights(int $moduleId, int $groupId, int $rights)
  {
    $record = $this->getRecord($moduleId, $groupId);
    if ($rights === 0) {
      $record->delete();
    } else if ($record->count('rights') === 0) {
      $record->insert(['rights' => $rights]);
    } else {
      $record->update(['rights' => $rights]);
    }
  }

  private function getRecord(int $moduleId, int $groupId): Records
  {
    return Records::from('group_rights', [
      'module_id' => $moduleId,
      'group_id' => $groupId
    ]);
  }
}