<?php namespace engine\messages;

use frame\database\Identity;
use frame\database\Records;
use frame\database\SqlDriver;

class Message extends Identity
{
    public static function getTable(): string
    {
        return 'messages';
    }

    public static function countUnreaded(int $userId): int
    {
        return (int) SqlDriver::getDriver()->query(
            "SELECT COUNT(id) FROM messages
            WHERE to_id = $userId AND readed = 0
                AND (removed_by_id IS NULL OR removed_by_id <> {$userId})"
        )->readScalar();
    }

    public function loadText(): string
    {
        return Records::from('message_texts', [
            'message_id' => $this->id
        ])->select(['text'])->readScalar();
    }
}