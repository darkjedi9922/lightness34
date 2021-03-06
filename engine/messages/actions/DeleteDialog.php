<?php namespace engine\messages\actions;

use engine\messages\Message;
use engine\users\User;
use engine\users\Group;
use frame\actions\ActionBody;
use frame\actions\fields\IntegerField;
use frame\database\SqlDriver;
use frame\auth\InitAccess;

class DeleteDialog extends ActionBody
{
    /** @var int */
    private $uid;

    public function listGet(): array
    {
        return [
            'uid' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        InitAccess::accessRight('messages', 'use');
        $this->uid = $get['uid']->get();
    }

    public function succeed(array $post, array $files)
    {
        $table = Message::getTable();
        $me = User::getMe();
        $member1SortedId = $me->id;
        $member2SortedId = $this->uid;
        if ($member2SortedId < $member1SortedId) {
            $temp = $member1SortedId;
            $member1SortedId = $member2SortedId;
            $member2SortedId = $temp;
        }

        if ($member1SortedId === $member2SortedId) {
            // Это диалог сам с собой - удаляем все полностью.
            SqlDriver::getDriver()->query(
                "DELETE FROM $table
                WHERE member1_sorted_id = $member1SortedId 
                    AND member2_sorted_id = $member2SortedId"
            );
        }
        else {
            // Сначала удалим полностью все сообщения, уже удаленные собеседником.
            SqlDriver::getDriver()->query(
                "DELETE FROM $table
                WHERE member1_sorted_id = $member1SortedId 
                    AND member2_sorted_id = $member2SortedId
                    AND removed_by_id = {$this->uid}"
            );
            // Ставим флаг, что пользователь удалил сообщение, на остальных.
            SqlDriver::getDriver()->query(
                "UPDATE $table SET removed_by_id = {$me->id}
                WHERE member1_sorted_id = $member1SortedId 
                    AND member2_sorted_id = $member2SortedId
                    AND removed_by_id IS NULL"
            );
        }
    }
}