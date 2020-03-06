<?php /** @var frame\views\Widget $self */

use engine\users\cash\user_me;
use engine\users\cash\my_group;
use frame\tools\Client;
use engine\messages\Dialog;
use engine\users\actions\LogoutAction;
use frame\actions\ViewAction;
use engine\users\cash\my_rights;

$self->setMeta('title', 'Профиль');

$me = user_me::get();
$group = my_group::get();
$messageRights = my_rights::get('messages');
if ($messageRights->can('use')) $newMessagesCount = Dialog::countUnreaded($me->id);
$logout = new ViewAction(LogoutAction::class);
?>

<div class="mini-profile">
    <div class="mini-profile__title">
        <a class="link" href="/profile?login=<?= $me->login ?>"><?= $me->login ?></a>
    </div>
    <img class="mini-profile__avatar" src="/<?= $me->getAvatarUrl() ?>" width="90%">
    <hr>
    <p>Логин: <a class="link" href="/profile?login=<?= $me->login ?>"><?= $me->login ?></a></p>
    <?php if ($messageRights->can('use')): ?>
        <hr>
        <p>
            <a class="link" href="/profile/dialogs">Сообщения</a>
            <span <?php if ($newMessagesCount !== 0) : ?>style="color:red;font-weight:bold" <?php endif ?>>(<?= $newMessagesCount ?>)</span>
        </p>
    <?php endif ?>
    <hr>
    <p><?= $group->name ?></p>
    <hr>
    <p>Ваш IP: <?= Client::getIp() ?></p>
    <hr>
    <div class="mini-profile__exit">
        <a class="link" href="<?= $logout->getUrl() ?>">Выход</a>
    </div>
</div>