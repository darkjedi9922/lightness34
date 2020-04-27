<?php namespace engine\messages;

use frame\stdlib\drivers\database\MySqlDriver;
use frame\database\Records;

/**
 * Диалог идентифицируется двумя id участников диалога.
 */
class Dialog
{
    private $last;

    public static function countUnreaded(int $userId): int
    {
        return (int)MySqlDriver::getDriver()->query(
            "SELECT COUNT(DISTINCT from_id) FROM messages
            WHERE to_id = $userId AND readed = 0
                AND (removed_by_id IS NULL OR removed_by_id <> {$userId})"
        )->readScalar();
    }

    public function __construct(Message $last)
    {
        $this->last = $last;
    }

    public function getLastMessage(): Message
    {
        return $this->last;
    }

    public function getWhoId(int $forUserId): int
    {
        if ((int) $this->last->to_id === $forUserId) return $this->last->from_id;
        else return $this->last->to_id;
    }

    public function countNewMessages(int $userId): int
    {
        return (int)MySqlDriver::getDriver()->query(
            "SELECT COUNT(id) FROM messages
            WHERE to_id = $userId 
                AND from_id = {$this->getWhoId($userId)} 
                AND readed = 0
                AND (removed_by_id IS NULL OR removed_by_id <> {$userId})"
        )->readScalar();
    }

    public function setReadedBy(int $userId)
    {
        Records::from('messages', [
            'from_id' => $this->getWhoId($userId),
            'to_id' => $userId
        ])->update(['readed' => true]);
    }
}