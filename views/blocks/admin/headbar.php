<?php /** @var frame\views\Block $self */

use engine\articles\Article;
use engine\users\cash\user_me;
use engine\messages\Message;
use frame\tools\Logger;
use frame\tools\trackers\read\ReadLimitedProgressTracker as Tracker;
use engine\users\cash\my_rights;
use engine\comments\Comment;

$me = user_me::get();

$unreadedArticles = 0;
if (my_rights::get('articles')->can('see-new-list'))
    $unreadedArticles = Article::countUnreaded($me->id);

$unreadedComments = 0;
if (my_rights::get('articles/comments')->can('see-new-list'))
    $unreadedComments = Comment::countUnreaded($me->id);

$adminRights = my_rights::get('admin');
if ($adminRights->can('see-logs')) {
    $logger = new Logger(ROOT_DIR . '/log.txt');
    $logTracker = new Tracker('log', crc32('log.txt'), count($logger->read()), $me->id);
    $logNewRecords = $logTracker->loadUnreaded();
}
$usersRights = my_rights::get('users');
$messagesRights = my_rights::get('messages');
if ($messagesRights->can('use')) $unreadedMessages = Message::countUnreaded($me->id);
?>

<div class="head-bar__left">
    <a href="/" class="site" target="_blank"><i class="fontello icon-television"></i></a>
</div>
<div class="head-bar__right">
    <div class="notice-bar">
        <?php if ($unreadedArticles !== 0) : ?>
            <a href="/admin/new/articles"><i class="icon-doc-text-inv"></i> <?= $unreadedArticles ?></a>
        <?php endif ?>
        <?php if ($unreadedComments !== 0) : ?>
            <a href="/admin/new/comments"><i class="icon-commenting"></i> <?= $unreadedComments ?></a>
        <?php endif ?>
        <?php if ($adminRights->can('see-logs') && $logNewRecords !== 0) : ?>
            <a href="/admin/log"><i class="fontello icon-attention"></i> <?= $logNewRecords ?></a>
        <?php endif ?>
    </div>
    <div class="mini-profile">
        <?php if ($messagesRights->can('use')) : ?>
            <?php if ($unreadedMessages === 0) : ?>
                <a class="messages" href="/admin/profile/dialogs"><i class="fontello icon-email"></i></a>
            <?php else : ?>
                <a class="messages new" href="/admin/profile/dialogs"><i class="fontello icon-email"></i><span class="amount"> <?= $unreadedMessages ?></span></a>
            <?php endif ?>
        <?php endif ?>
        <img class="avatar" src='/<?= $me->getAvatarUrl() ?>'>
        <a class="login" href='/admin/users/profile/<?= $me->login ?>'><?= $me->login ?></a>
    </div>
</div>