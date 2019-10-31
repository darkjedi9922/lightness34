<?php namespace engine\messages;

use frame\database\Identity;
use cash\database;

class Dialog extends Identity
{
    public static function getTable(): string
    {
        return 'dialogs';
    }

    public static function countUnreaded(int $userId): int
    {
        return (int) database::get()->query(
            "SELECT COUNT(DISTINCT group_id) FROM messages
            WHERE id IN (SELECT message_id FROM unreaded_messages WHERE member_id = $userId)
            AND group_id NOT IN (SELECT dialog_id FROM deleted_dialogs WHERE deleted_user_id = $userId)"
        )->readScalar();
    }
}