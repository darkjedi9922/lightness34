<?php namespace engine\messages\actions;

use frame\actions\ActionBody;
use engine\users\User;
use frame\tools\Init;
use engine\users\cash\user_me;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;
use frame\database\Records;

class AddMessage extends ActionBody
{
    const E_TEXT_IS_EMPTY = 1;

    /** @var User */
    private $who;

    public function listGet(): array
    {
        return [
            'to_uid' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'text' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('messages', 'use');
        $this->who = User::selectIdentity($get['to_uid']->get());
        Init::require($this->who !== null);
    }

    public function validate(array $post, array $files)
    {
        $errors = [];
        if ($post['text']->isEmpty()) $errors[] = self::E_TEXT_IS_EMPTY;
        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $me = user_me::get();
        $date = time();
        $member1Id = null;
        $member2Id = null;
        if ($me->id < $this->who->id) {
            $member1Id = $me->id;
            $member2Id = $this->who->id;
        } else {
            $member1Id = $this->who->id;
            $member2Id = $me->id;
        }

        $id = Records::from('messages')->insert([
            'member1_sorted_id' => $member1Id,
            'member2_sorted_id' => $member2Id,
            'from_id' => $me->id,
            'to_id' => $this->who->id,
            'date' => $date,
            'readed' => $this->who->id === $me->id
        ]);

        Records::from('message_texts')->insert([
            'message_id' => $id,
            'text' => $post['text']->get()
        ]);

        return [
            'id' => $id,
            'date' => date('d.m.Y H:i', $date)
        ];
    }
}