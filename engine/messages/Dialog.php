<?php namespace engine\messages;

use frame\cash\database;

/**
 * Диалог идентифицируется двумя id участников диалога.
 */
class Dialog
{
    private $last;

    public static function countUnreaded(int $userId): int
    {
        return (int)database::get()->query(
            "SELECT COUNT(DISTINCT from_id) FROM messages
            WHERE to_id = $userId AND readed = 0"
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
        return (int)database::get()->query(
            "SELECT COUNT(id) FROM messages
            WHERE to_id = $userId 
                AND from_id = {$this->getWhoId($userId)} 
                AND readed = 0"
        )->readScalar();
    }
}