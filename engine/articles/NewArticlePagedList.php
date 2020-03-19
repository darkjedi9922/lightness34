<?php namespace engine\articles;

use frame\lists\paged\PagedList;
use frame\lists\iterators\IdentityIterator;
use frame\stdlib\cash\config;
use frame\stdlib\cash\database;
use engine\users\cash\user_me;

class NewArticlePagedList extends PagedList
{
    private $result;
    private $iterator;

    public function __construct(int $page)
    {
        $me = user_me::get();
        $countAll = Article::countUnreaded($me->id);
        $pageLimit = config::get('articles')->{'list.amount'};

        parent::__construct($page, $countAll, $pageLimit);

        $this->result = database::get()->query(
            "SELECT articles.*
            FROM articles LEFT OUTER JOIN 
                (SELECT article_id FROM readed_articles 
                    WHERE user_id = {$me->id}) AS readed
                ON id = article_id
            WHERE article_id IS NULL AND author_id <> {$me->id}"
        );
        $this->iterator = new IdentityIterator($this->result, Article::class);
    }

    public function countOnPage(): int
    {
        return $this->result->count();
    }

    public function getIterator(): \Iterator
    {
        return $this->iterator;
    }
}