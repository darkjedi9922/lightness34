<?php namespace engine\articles;

use frame\database\SqlDriver;
use frame\database\Identity;
use engine\users\User;
use engine\users\Group;
use frame\tools\trackers\read\ReadStateTracker;

class Article extends Identity
{
    public static function getTable(): string
    {
        return 'articles';
    }

    public static function countUnreaded(int $userId): int
    {
        return (int) SqlDriver::getDriver()->query(
            "SELECT COUNT(articles.id)
            FROM articles
            LEFT OUTER JOIN (
                SELECT what_id FROM read_tracking 
                WHERE name = 'articles' AND for_id = $userId
            ) AS readed ON articles.id = readed.what_id
            WHERE readed.what_id IS NULL AND author_id <> $userId"
        )->readScalar();
    }

    /**
     * Для гостей не устанавливается.
     */
    public function setReaded(User $for)
    {
        if (   $for->group_id === Group::GUEST_ID 
            || $for->id === $this->author_id
        ) return;

        $this->createReadTracker($for)->setReaded();
    }

    /**
     * Для гостей всегда будет false.
     */
    public function loadIsNew(User $for): bool
    {
        if (   $for->group_id === Group::GUEST_ID 
            || $for->id === $this->author_id
        ) return false;
        
        return !$this->createReadTracker($for)->isReaded();
    }

    private function createReadTracker(User $for): ReadStateTracker
    {
        return new ReadStateTracker('articles', $this->id, $for->id);
    }
}