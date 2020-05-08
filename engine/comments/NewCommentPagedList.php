<?php namespace engine\comments;

use frame\lists\paged\PagedList;
use frame\lists\iterators\IdentityIterator;
use frame\stdlib\cash\config;
use frame\database\SqlDriver;
use engine\users\cash\user_me;

class NewCommentPagedList extends PagedList
{
    private $result;
    private $iterator;

    public function __construct(int $page)
    {
        $me = user_me::get();
        $countAll = Comment::countUnreaded($me->id);
        $config = config::get('comments');
        $pageLimit = $config->{'list.amount'};
        $order = $config->{'list.order'};

        parent::__construct($page, $countAll, $pageLimit);

        $this->result = SqlDriver::getDriver()->query(
            "SELECT comments.*
            FROM comments
            LEFT OUTER JOIN (
                SELECT what_id FROM read_tracking
                WHERE `name` = 'comments' AND `for_id` = {$me->id}
            ) AS readed ON comments.id = readed.what_id
            WHERE what_id IS NULL AND author_id <> {$me->id}
            ORDER BY comments.id $order"
        );
        $this->iterator = new IdentityIterator($this->result, Comment::class);
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