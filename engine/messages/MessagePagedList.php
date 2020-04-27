<?php namespace engine\messages;

use engine\users\cash\user_me;
use frame\lists\paged\PagedList;
use frame\database\SqlDriver;
use frame\stdlib\cash\config;
use frame\lists\iterators\IdentityIterator;

class MessagePagedList extends PagedList
{
    private $list;
    private $iterator;

    public function __construct(int $page, int $userId)
    {
        $me = user_me::get();
        $db = SqlDriver::getDriver();
        $config = config::get('messages');

        $countAll = (int) $db->query(
            "SELECT COUNT(id) FROM messages WHERE (
                (from_id = {$me->id} AND to_id = $userId) OR 
                (from_id = $userId AND to_id = {$me->id})
            ) AND (removed_by_id IS NULL OR removed_by_id <> {$me->id})"
        )->readScalar();

        $pageLimit = $config->{'messages.list.amount'};

        parent::__construct($page, $countAll, $pageLimit);

        $from = $this->getPager()->getStartMaterialIndex();

        $this->list = $db->query(
            "SELECT * FROM messages WHERE (
                (from_id = {$me->id} AND to_id = $userId) OR 
                (from_id = $userId AND to_id = {$me->id})
            ) AND (removed_by_id IS NULL OR removed_by_id <> {$me->id})
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