<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\cash\user_me;
use engine\users\Group;
use engine\messages\DialogPagedList;
use engine\messages\Dialog;
use frame\cash\pagenumber;
use function lightlib\shorten;
use engine\users\User;

$me = user_me::get();

Init::access($me->group_id !== Group::GUEST_ID);

$page = pagenumber::get();
$dialogs = new DialogPagedList($page);
$pageCount = $dialogs->getPager()->countPages();

$self->setLayout('admin');
?>

<?php if ($pageCount > 1): ?>
<div id="dialog-pager" style="display:none">
    <?php $dialogs->getPager()->show('admin') ?>
</div>
<?php endif ?>

<script>var _dialogListData = {
    countAll: <?= $dialogs->countAll() ?>,
    list: [<?php foreach ($dialogs as $dialog): /** @var Dialog $dialog */
        $last = $dialog->getLastMessage();
        $newCount = 0;
        $activeCount = 0;
        $toId = (int) $last->to_id;
        if ($last->readed === '0') 
            if ($toId === $me->id) $newCount = $dialog->countNewMessages($toId);
            else $activeCount = $dialog->countNewMessages($toId);
        
        $who = User::selectIdentity($toId !== $me->id ? $toId : (int) $last->from_id);
    ?>{
        newCount: <?= $newCount ?>,
        activeCount: <?= $activeCount ?>,
        whoId: <?= $who->id ?>,
        whoAvatar: '/<?= $who->getAvatarUrl() ?>',
        whoLogin: '<?= $who->login ?>',
        lastMessage: {
            text: '<?= str_replace("\n", '\n', shorten($last->loadText(), 80, '...')) ?>',
            date: '<?= date('d.m.Y H:i', $last->date) ?>'
        },
    }<?php endforeach ?>],
    pageCount: <?= $pageCount ?>,
    pagerHtml: 
        <?php if ($pageCount > 1): ?>document.getElementById('dialog-pager').innerHTML
        <?php else: ?>''<?php endif ?>
};</script>

<div id="dialog-list"></div>