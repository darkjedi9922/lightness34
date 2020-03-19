<?php namespace engine\articles;

use frame\stdlib\cash\database;
use frame\database\Identity;
use engine\users\User;
use engine\users\Group;
use frame\database\Records;
use engine\articles\cash\is_article_new;

class Article extends Identity
{
    public static function getTable(): string
    {
        return 'articles';
    }

    public static function countUnreaded(int $userId): int
    {
        return (int) database::get()->query(
            "SELECT COUNT(id)
            FROM articles LEFT OUTER JOIN 
                (SELECT article_id FROM readed_articles WHERE user_id = $userId) AS readed
                ON id = article_id
            WHERE article_id IS NULL AND author_id <> $userId"
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

        if (is_article_new::get($this, $for)) Records::from('readed_articles', [
            'article_id' => $this->id,
            'user_id' => $for->id
        ])->insert();
    }

    /**
     * Для гостей всегда будет false.
     */
    public function loadIsNew(User $for): bool
    {
        if (   $for->group_id === Group::GUEST_ID 
            || $for->id === $this->author_id
        ) return false;
        
        return Records::from('readed_articles', [
            'article_id' => $this->id,
            'user_id' => $for->id
        ])->count('article_id') === 0;
    }
}