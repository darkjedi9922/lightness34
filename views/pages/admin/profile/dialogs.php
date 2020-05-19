<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use engine\messages\actions\DeleteDialog;
use engine\messages\DialogPagedList;
use engine\messages\Dialog;
use frame\lists\paged\PagerModel;
use function lightlib\shorten;
use engine\users\User;
use frame\actions\ViewAction;
use frame\lists\paged\PagerView;
use frame\tools\JsonEncoder;

InitAccess::accessRight('messages', 'use');
$me = User::getMe();
$page = PagerModel::getRoutePage();
$dialogs = new DialogPagedList($page);
$pageCount = $dialogs->getPager()->countPages();

$dialogListData = [
    'countAll' => $dialogs->countAll(),
    'list' => [],
    'pageCount' => $pageCount,
    'pagerHtml' => ($pageCount > 1 ? (new PagerView($dialogs->getPager(), 'admin'))->getHtml() : ''),
    'userMe' => [
        'id' => $me->id,
        'login' => $me->login
    ]
];

$delete = new ViewAction(DeleteDialog::class);

foreach ($dialogs as $dialog) {
    /** @var Dialog $dialog */
    $last = $dialog->getLastMessage();
    $newCount = 0;
    $sentCount = 0;
    $toId = (int)$last->to_id;
    if (!$last->readed) {
        if ($toId === $me->id) $newCount = $dialog->countNewMessages($toId);
        else $sentCount = $dialog->countNewMessages($toId);
    }
    $who = User::selectIdentity($toId !== $me->id ? $toId : (int)$last->from_id);
    $delete->setArg('uid', $who->id);

    $dialogListData['list'][] = [
        'newCount' => $newCount,
        'sentCount' => $sentCount,
        'whoId' => $who->id,
        'whoAvatar' => '/' . $who->getAvatarUrl(),
        'whoLogin' => $who->login,
        'lastMessage' => [
            'text' => shorten($last->loadText(), 50, '...'),
            'date' => date('d.m.Y H:i', $last->date)
        ],
        'deleteUrl' => $delete->getUrl()
    ];
}

$dialogListData = JsonEncoder::forHtmlAttribute($dialogListData);
?>

<div id="dialog-list" class="dialog-list" data-props="<?= $dialogListData ?>"></div>