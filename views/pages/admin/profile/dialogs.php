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
$messages = new DialogPagedList($page);

$self->setLayout('admin');
?>

<div class="box">
    <div class="dialogs">
        <?php if ($messages->countAll() === 0) : ?>
            <span class="notice">Сообщений пока нет</span>
        <?php endif ?>
        <div class="dialogs__list">
            <?php foreach ($messages as $dialog) : /** @var Dialog $dialog */
                $last = $dialog->getLastMessage();
                $newCount = 0;
                $activeCount = 0;
                $toId = (int) $last->to_id;
                if ($last->readed === '0') {
                    if ($toId === $me->id) $newCount = $dialog->countNewMessages($toId);
                    else $activeCount = $dialog->countNewMessages($toId);
                }
                $who = User::selectIdentity($toId !== $me->id ? $toId : (int) $last->from_id);
            ?>
                <div class="dialogs__item dialog">
                    <div class="dialog__header">
                        <span class="dialog__date"><?= date('d.m.Y H:i', $last->date) ?></span>
                        <span class="dialog__text"><?= shorten($last->loadText(), 80, '...') ?></span>
                    </div>
                    <div class="dialog__info">
                        <div class="dialog__status
                            <?= $newCount != 0 ? 'dialog__status--new' :
                                ($activeCount != 0 ? 'dialog__status--active' : '') ?>">
                            <i class="dialog__status-icon fontello icon-ok"></i>
                            <span class="dialog__status-text"><?= 
                                $newCount != 0 ? "Новых: $newCount" :
                                ($activeCount != 0 ? "Непрочитанных: $activeCount" : 
                                'Все сообщения прочитаны')
                            ?></span>
                        </div>
                        <span class="dialog__who"><?= $who->login ?></span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>