<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\cash\user_me;
use engine\users\User;
use engine\users\Group;

$me = user_me::get();
Init::access($me->group_id !== Group::GUEST_ID);
$withId = (int)Init::requireGet('with');
$with = User::selectIdentity($withId);
Init::require($with !== null);

$self->setLayout('admin');
?>

<div class="box">
    <form action="" class="box-form">
        <span class="box-form__title">Новое сообщение</span>
        <textarea class="box-form__textarea" name="" rows="1" placeholder="Текст сообщения"></textarea>
        <button class="box-form__button">Отправить
            <i class="box-form__button-icon fontello icon-ok"></i></button>
    </form>
</div>
<div class="box">
    <span class="notice">Сообщений с пользователем
        <span class="notice__strong"><?= $with->login ?></span>
        пока нет
    </span>
    <div class="messages">
        <div class="messages__item message">
            <div class="message__header">
                <img src="/<?= $with->getAvatarUrl() ?>" class="message__from-avatar">
                <div class="message__info">
                    <span class="message__from-login">Some User</span>
                    <span class="message__date">07.12.2019 11:12</span>
                </div>
                <div class="message__status message__status--active">
                    <i class="message__status-icon fontello icon-ok"></i>
                    <span class="message__status-text">Отправлено</span>
                </div>
            </div>
            <span class="message__text">Some message text here</span>
        </div>
    </div>
</div>