<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\cash\user_me;
use engine\users\Group;

$me = user_me::get();

Init::access($me->group_id !== Group::GUEST_ID);

$self->setLayout('admin');
?>

<div class="box">
    <div class="dialogs">
        <span class="notice">Сообщений пока нет</span>
        <div class="dialogs__list">
            <div class="dialogs__item dialog">
                <div class="dialog__header">
                    <span class="dialog__date">06.12.2019 17:51</span>
                    <span class="dialog__text">Это текст какого-либо сообщения</span>
                </div>
                <div class="dialog__info">
                    <div class="dialog__status dialog__status--new">
                        <i class="dialog__status-icon fontello icon-ok"></i>
                        <span class="dialog__status-text">Новых: 2</span>
                    </div>
                    <span class="dialog__who">Some User</span>
                </div>
            </div>
            <div class="dialogs__item dialog">
                <div class="dialog__header">
                    <span class="dialog__date">06.12.2019 17:51</span>
                    <span class="dialog__text">Это текст какого-либо сообщения</span>
                </div>
                <div class="dialog__info">
                    <div class="dialog__status dialog__status--active">
                        <i class="dialog__status-icon fontello icon-ok"></i>
                        <span class="dialog__status-text">Непрочитаных: 1</span>
                    </div>
                    <span class="dialog__who">Some User</span>
                </div>
            </div>
            <div class="dialogs__item dialog">
                <div class="dialog__header">
                    <span class="dialog__date">06.12.2019 17:51</span>
                    <span class="dialog__text">Это текст какого-либо сообщения</span>
                </div>
                <div class="dialog__info">
                    <div class="dialog__status">
                        <i class="dialog__status-icon fontello icon-ok"></i>
                        <span class="dialog__status-text">Все сообщения прочитаны</span>
                    </div>
                    <span class="dialog__who">Some User</span>
                </div>
            </div>
        </div>
    </div>
</div>