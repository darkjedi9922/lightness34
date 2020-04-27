<?php namespace engine\messages;

use frame\lists\paged\PagedList;
use frame\database\SqlDriver;
use engine\users\cash\user_me;
use frame\stdlib\cash\config;

class DialogPagedList extends PagedList
{	
    private $result;

    public function __construct(int $page) 
    {
        $me = user_me::get();

        $countAll = (int) SqlDriver::getDriver()->query(
            "SELECT COUNT(`COUNT(id)`) FROM (
                SELECT COUNT(id) FROM `messages`
                WHERE (member1_sorted_id = {$me->id} 
                    OR member2_sorted_id = {$me->id}
                ) AND (removed_by_id IS NULL OR removed_by_id <> {$me->id})
                GROUP BY member1_sorted_id, member2_sorted_id
            ) AS dialogs"
        )->readScalar();

        $pageLimit = config::get('messages')->{'dialogs.list.amount'};

        parent::__construct($page, $countAll, $pageLimit);

        $from = $this->getPager()->getStartMaterialIndex();

        // Тут выбираются все последние сообщения каждого диалога.
        // Запрос такой страшный, потому что в MySQL ORDER BY работает только после
        // GROUP BY. Из-за этого в более простом запросе в каждой группе выбиралось
        // всегда первое сообщение, игнорируя сортировку.
        $this->result = SqlDriver::getDriver()->query(
            "SELECT * FROM messages INNER JOIN (
                SELECT MAX(id) AS max_id FROM messages
                WHERE (member1_sorted_id = {$me->id} 
                    OR member2_sorted_id = {$me->id}
                ) AND (removed_by_id IS NULL OR removed_by_id <> {$me->id})
                GROUP BY member1_sorted_id, member2_sorted_id
            ) AS lasts ON id = lasts.max_id
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