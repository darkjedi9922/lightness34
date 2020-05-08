<?php namespace engine\comments;

use frame\database\Identity;
use frame\database\Records;
use frame\modules\Modules;
use engine\users\User;
use frame\tools\trackers\read\ReadStateTracker;
use frame\stdlib\drivers\cash\StaticCashStorage;
use frame\database\SqlDriver;

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

    public static function countUnreaded(int $userId): int
    {
        return SqlDriver::getDriver()->query(
            "SELECT COUNT(comments.id)
            FROM comments
            LEFT OUTER JOIN (
                SELECT what_id FROM read_tracking 
                WHERE name = 'comments' AND for_id = $userId
            ) AS readed ON comments.id = readed.what_id
            WHERE readed.what_id IS NULL AND author_id <> $userId"
        )->readScalar();
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
        return StaticCashStorage::getDriver()->cash(
            "comment-{$for->getId()}-rt",
            function() use ($for) {
                return new ReadStateTracker(
                    'comments',
                    $this->getId(),
                    $for->getId()
                );
            }
        );
    }
}