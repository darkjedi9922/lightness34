<?php namespace engine\articles;

use cash\database;
use frame\database\Identity;

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
}