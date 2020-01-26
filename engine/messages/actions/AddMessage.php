<?php namespace engine\messages\actions;

use frame\actions\ActionBody;
use engine\users\User;
use frame\tools\Init;
use engine\users\cash\user_me;
use engine\users\Group;
use frame\database\Records;

class AddMessage extends ActionBody
{
    const E_TEXT_IS_EMPTY = 1;

    /** @var User */
    private $me;
    /** @var User */
    private $who;

    public function listGet(): array
    {
        return [
            'to_uid' => [self::GET_INT, 'User id to whom send a message']
        ];
    }

    public function listPost(): array
    {
        return [
            'text' => [self::POST_TEXT, 'The text of the new message']
        ];
    }

    public function initialize(array $get)
    {
        $this->me = user_me::get();
        Init::access((int) $this->me->group_id !== Group::GUEST_ID);
        $this->who = User::selectIdentity($get['to_uid']);
        Init::require($this->who !== null);
    }

    public function validate(array $post, array $files)
    {
        $errors = [];
        if (empty($post['text'])) $errors[] = self::E_TEXT_IS_EMPTY;
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $date = time();
        $member1Id = null;
        $member2Id = null;
        if ($this->me->id < $this->who->id) {
            $member1Id = $this->me->id;
            $member2Id = $this->who->id;
        } else {
            $member1Id = $this->who->id;
            $member2Id = $this->me->id;
        }

        $id = Records::from('messages')->insert([
            'member1_sorted_id' => $member1Id,
            'member2_sorted_id' => $member2Id,
            'from_id' => $this->me->id,
            'to_id' => $this->who->id,
            'date' => $date
        ]);

        Records::from('message_texts')->insert([
            'message_id' => $id,
            'text' => $post['text']
        ]);

        return [
            'id' => $id,
            'date' => date('d.m.Y H:i', $date)
        ];
    }
}