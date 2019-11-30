<?php namespace engine\articles\cash;

use frame\tools\Cash;
use engine\articles\Article;
use engine\users\User;
use engine\users\cash\user_me;

/**
 * Является ли статья новой для пользователя (не читал ли он ее еще).
 */
class is_article_new extends Cash
{
    /**
     * @var User|null $for По умолчанию - user_me.
     */
    public static function get(Article $article, ?User $for = null): bool
    {
        $for = $for ?? user_me::get();
        return self::cash("{$article->id}_{$for->id}", 
            function() use ($article, $for)
        {
            return $article->loadIsNew($for);
        });
    }
}