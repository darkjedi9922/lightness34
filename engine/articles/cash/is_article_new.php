<?php namespace engine\articles\cash;

use frame\cash\CashValue;
use engine\articles\Article;
use engine\users\User;
use engine\users\cash\user_me;
use frame\cash\CashStorage;
use frame\stdlib\drivers\cash\StaticCashStorage;

/**
 * Является ли статья новой для пользователя (не читал ли он ее еще).
 */
class is_article_new extends CashValue
{
    public static function getStorage(): CashStorage
    {
        return StaticCashStorage::getDriver();
    }

    /**
     * @var User|null $for По умолчанию - user_me.
     * @return bool
     */
    public static function get(Article $article, ?User $for = null)
    {
        $for = $for ?? user_me::get();
        return self::cash("{$article->id}_{$for->id}", 
            function() use ($article, $for)
        {
            return $article->loadIsNew($for);
        });
    }
}