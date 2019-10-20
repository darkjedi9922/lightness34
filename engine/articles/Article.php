<?php namespace engine\articles;

use frame\database\Identity;

class Article extends Identity
{
    public static function getTable(): string
    {
        return 'articles';
    }
}