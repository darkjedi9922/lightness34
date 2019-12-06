<?php namespace engine\articles;

use frame\config\Json;
use frame\lists\paged\IdentityPagedList;

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
        return (new Json('config/articles.json'))->{'list.amount'};
    }
}