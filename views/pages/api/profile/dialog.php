<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\cash\pagenumber;
use engine\messages\MessagePagedList;
use engine\messages\Message;
use engine\users\cash\user_me;
use engine\users\Group;
use engine\users\User;
use frame\actions\ViewAction;
use engine\messages\actions\AddMessage;

$me = user_me::get();

Init::access((int) $me->group_id !== Group::GUEST_ID);

$withWhoId = (int) Init::requireGet('withId');
$who = User::selectIdentity($withWhoId);

Init::require($who !== null);

$page = pagenumber::get();
$send = new ViewAction(AddMessage::class, ['to_uid' => $withWhoId]);

$result = [
    'users' => [
        $me->id => [
            'login' => $me->login,
            'avatarUrl' => '/' . $me->getAvatarUrl()
        ],
        $who->id => [
            'login' => $who->login,
            'avatarUrl' => '/' . $who->getAvatarUrl()
        ],
    ],
    'list' => [],
    'addMessageUrl' => $send->getUrl()
];

$list = new MessagePagedList($page, $me->id, $withWhoId);
foreach ($list as $message) {
    /** @var Message $message */
    $result['list'][] = [
        'id' => $message->id,
        'from_id' => (int) $message->from_id,
        'to_id' => (int) $message->to_id,
        'date' => date('d.m.Y H:i', $message->date),
        'readed' => (bool) $message->readed,
        'text' => $message->loadText()
    ];
}

$self->setLayout(null);
echo json_encode($result);