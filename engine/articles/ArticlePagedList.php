<?php namespace engine\articles;

use frame\lists\paged\IdentityPagedList;
use frame\stdlib\configs\JsonConfig;

class ArticlePagedList extends IdentityPagedList
{
    public function getIdentityClass(): string
    {
        return Article::class;
    }

    public function getOrderFields(): array
    {
        return ['id' => 'DESC'];
    }

    protected function loadPageLImit(): int
    {
        return (new JsonConfig('config/articles'))->{'list.amount'};
    }
}