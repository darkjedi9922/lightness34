<?php namespace engine\comments;

use frame\database\Identity;
use frame\database\Records;
use frame\modules\Modules;
use engine\users\User;
use frame\tools\trackers\read\ReadStateTracker;
use frame\stdlib\drivers\cash\StaticCashStorage;

class Comment extends Identity
{
    public static function getTable(): string
    {
        return 'comments';
    }

    public static function count(string $module, int $materialId): int
    {
        return Records::from(static::getTable(), [
            'module_id' => Modules::getDriver()->findByName($module)->getId(),
            'material_id' => $materialId
        ])->count('id');
    }

    public function isNewFor(User $for): bool
    {
        return $this->author_id !== $for->getId() 
            && !$this->getReadTracker($for)->isReaded();
    }

    public function setReadedFor(User $for)
    {
        if ($this->author_id === $for->getId()) return;
        $this->getReadTracker($for)->setReaded();
    }

    private function getReadTracker(User $for): ReadStateTracker
    {
        $cash = StaticCashStorage::getDriver();
        if (!$cash->isCashed("comment-{$for->getId()}-rt")) 
            $cash->cash(
                "comment-{$for->getId()}-rt",
                new ReadStateTracker('comments', $this->getId(), $for->getId())
            );
        /** @var ReadStateTracker $result */
        $result = $cash->getValue("comment-{$for->getId()}-rt");
        return $result;
    }
}