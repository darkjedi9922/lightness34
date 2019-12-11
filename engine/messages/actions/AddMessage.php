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
        $id = Records::select('messages')->insert([
            'from_id' => $this->me->id,
            'to_id' => $this->who->id,
            'date' => $date
        ]);

        Records::select('message_texts')->insert([
            'message_id' => $id,
            'text' => $post['text']
        ]);

        return [
            'id' => $id,
            'date' => date('d.m.Y H:i', $date)
        ];
    }
}