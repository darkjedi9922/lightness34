<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\route\InitRoute;
use frame\lists\paged\PagerModel;
use engine\messages\MessagePagedList;
use engine\messages\Message;
use engine\users\User;
use frame\actions\ViewAction;
use engine\messages\actions\AddMessage;
use engine\messages\Dialog;
use frame\route\Router;
use frame\tools\JsonEncoder;
use frame\lists\paged\PagerView;

InitAccess::accessRight('messages', 'use');

$me = User::getMe();
$withWhoId = (int) InitRoute::requireGet('withId');
$who = User::selectIdentity($withWhoId);

InitRoute::require($who !== null);

$page = PagerModel::getRoutePage();
$send = new ViewAction(AddMessage::class, ['to_uid' => $withWhoId]);

$list = new MessagePagedList($page, $withWhoId);
$listProps = [];
$anyMessage = null;
foreach ($list as $message) {
    /** @var Message $message */
    $listProps[] = [
        'id' => $message->id,
        'from_id' => (int) $message->from_id,
        'to_id' => (int) $message->to_id,
        'date' => date('d.m.Y H:i', $message->date),
        'readed' => (bool) $message->readed,
        'text' => $message->loadText()
    ];
    $anyMessage = $message;
}

$pagerHtml = null;
if ($list->getPager()->countPages() > 1) {
    $viewPager = new PagerView($list->getPager(), 'admin');
    $viewPager->setMeta('route', Router::getDriver()->getPreviousRoute()->toUrl());
    $pagerHtml = $viewPager->getHtml();
}

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
    'list' => $listProps,
    'pagerHtml' => $pagerHtml,
    'addMessageUrl' => $send->getUrl()
];

if ($anyMessage) {
    $dialog = new Dialog($anyMessage);
    $dialog->setReadedBy($me->id);
}

echo JsonEncoder::forViewText($result);