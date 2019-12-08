<?php namespace engine\messages;

use frame\lists\paged\PagedList;
use frame\cash\database;
use frame\cash\config;
use frame\lists\iterators\IdentityIterator;

class MessagePagedList extends PagedList
{
    private $userId1;
    private $userId2;
    private $list;
    private $iterator;

    public function __construct(int $page, int $userId1, int $userId2)
    {
        $this->userId1 = $userId1;
        $this->userId2 = $userId2;
        
        $db = database::get();
        $config = config::get('messages');

        $countAll = (int) $db->query(
            "SELECT COUNT(id) FROM messages WHERE
                (from_id = $userId1 AND to_id = $userId2) OR 
                (from_id = $userId2 AND to_id = $userId1)"
        )->readScalar();

        $pageLimit = $config->{'messages.list.amount'};

        parent::__construct($page, $countAll, $pageLimit);

        $from = $this->getPager()->getStartMaterialIndex();

        $this->list = $db->query(
            "SELECT * FROM messages WHERE
                (from_id = $userId1 AND to_id = $userId2) OR 
                (from_id = $userId2 AND to_id = $userId1)
            ORDER BY id DESC
            LIMIT $from, $pageLimit"
        );

        $this->iterator = new IdentityIterator($this->list, Message::class);
    }

    public function countOnPage(): int
    {
        return $this->list->count();
    }

    public function getIterator(): \Iterator
    {
        return $this->iterator;
    }
}