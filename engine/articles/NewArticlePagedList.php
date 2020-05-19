<?php namespace engine\articles;

use frame\lists\paged\PagedList;
use frame\lists\iterators\IdentityIterator;
use frame\config\ConfigRouter;
use frame\database\SqlDriver;
use engine\users\User;

class NewArticlePagedList extends PagedList
{
    private $result;
    private $iterator;

    public function __construct(int $page)
    {
        $me = User::getMe();
        $countAll = Article::countUnreaded($me->id);
        $pageLimit = ConfigRouter::getDriver()->findConfig('articles')->{'list.amount'};

        parent::__construct($page, $countAll, $pageLimit);

        $this->result = SqlDriver::getDriver()->query(
            "SELECT articles.*
            FROM articles
            LEFT OUTER JOIN (
                SELECT what_id FROM read_tracking
                WHERE `name` = 'articles' AND `for_id` = {$me->id}
            ) AS readed ON articles.id = readed.what_id
            WHERE what_id IS NULL AND author_id <> {$me->id}"
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