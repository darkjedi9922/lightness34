<?php namespace engine\articles;

use frame\config\Json;
use frame\lists\IdentityPagedList;

class ArticlePagedList extends IdentityPagedList
{
    public static function getIdentityClass(): string
    {
        return Article::class;
    }

    public static function getPageLImit(): int
    {
        return (new Json('config/articles.json'))->{'list.amount'};
    }

    public static function getOrderFields(): array
    {
        return ['id' => 'DESC'];
    }
}