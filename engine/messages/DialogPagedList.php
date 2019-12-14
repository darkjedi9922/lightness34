<?php namespace engine\messages;

use frame\lists\paged\PagedList;
use frame\cash\database;
use engine\users\cash\user_me;
use frame\cash\config;

class DialogPagedList extends PagedList
{	
    private $result;

    public function __construct(int $page) 
    {
        $me = user_me::get();

        $countAll = (int) database::get()->query(
            "SELECT COUNT(id) FROM (
                SELECT id FROM messages
                WHERE from_id = {$me->id} OR to_id = {$me->id}
                GROUP BY from_id OR to_id) AS dialogs"
        )->readScalar();

        $pageLimit = config::get('messages')->{'dialogs.list.amount'};

        parent::__construct($page, $countAll, $pageLimit);

        $from = $this->getPager()->getStartMaterialIndex();

        // Тут выбираются все последние сообщения каждого диалога.
        // Запрос такой страшный, потому что в MySQL ORDER BY работает только после
        // GROUP BY. Из-за этого в более простом запросе в каждой группе выбиралось
        // всегда первое сообщение, игнорируя сортировку.
        $this->result = database::get()->query(
            "SELECT * FROM messages INNER JOIN (
                SELECT MAX(id) AS max_id FROM messages
                WHERE from_id = {$me->id} OR to_id = {$me->id}
                GROUP BY from_id OR to_id) AS lasts ON id = lasts.max_id
            ORDER BY id DESC
            LIMIT $from, $pageLimit"
        );
    }

    public function countOnPage(): int
    {
        return $this->result->count();
    }

    public function getIterator(): \Iterator
    {
        while ($line = $this->result->readLine()) {
            yield new Dialog(new Message($line));
        }
    }
}