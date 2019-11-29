<?php /** @var frame\views\Block $self */

use frame\tools\FileReadingTracker;
use engine\articles\Article;
use engine\messages\Dialog;
use engine\users\cash\my_group;
use engine\users\cash\user_me;

$me = user_me::get();
$group = my_group::get();
$unreadedDialogs = Dialog::countUnreaded($me->id);
$unreadedArticles = Article::countUnreaded($me->id);
$logTracker = new FileReadingTracker('log.txt', $me->id);
$logUnreadedLines = $logTracker->countNewLines();
?>

<a href="/" class="site" target="_blank"><i class="fontello icon-television"></i></a>
<?php if ($group->id !== $group::GUEST_ID): ?>
	<div class="mini-profile">
		<?php if ($unreadedDialogs === 0): ?><a class="messages" href="/profile/dialogs" target="_blank"><i class="fontello icon-email"></i></a>
		<?php else: ?><a class="messages new" href="/profile/dialogs" target="_blank"><i class="fontello icon-email"></i><span class="amount"> <?= $unreadedDialogs ?></span></a>
		<?php endif ?>
		<img class="avatar" src='/<?=$me->getAvatarUrl()?>'>
		<a class="login" href='/profile?login=<?=$me->login?>' target="_blank"><?= $me->login ?></a>
	</div>
<?php endif ?>
<div class="notice-bar">
    <?php if($unreadedArticles !== 0):?><a href="/admin/new/articles"><i class="fontello icon-rss"></i> <?= $unreadedArticles ?></a><?php endif?>
    <?php if($logUnreadedLines !== 0):?><a href="/admin/log"><i class="fontello icon-attention"></i> <?= $logUnreadedLines ?></a><?php endif?>
</div>