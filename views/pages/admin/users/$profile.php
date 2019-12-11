<?php /** @var frame\views\DynamicPage $self */

use engine\users\User;
use frame\tools\Init;
use engine\users\Gender;
use engine\users\Group;
use engine\users\cash\my_rights;
use frame\cash\pagenumber;
use engine\users\cash\user_me;
use engine\users\actions\DeleteAvatarAction;
use frame\actions\ViewAction;

$self->setLayout('admin');

Init::require(count($self->getArguments()) === 1);

$login = $self->getArgument(0);
$profile = User::select(['login' => $login]);

Init::require($profile !== null);

$gender = Gender::selectIdentity($profile->gender_id);
$group = Group::selectIdentity($profile->group_id);
$rights = my_rights::get('users');
$prevPagenumber = pagenumber::get(true);
$me = user_me::get();

$deleteAvatar = new ViewAction(DeleteAvatarAction::class, ['uid' => $profile->id]);
?>

<div class="box">
    <h3><a class="link" href="/admin/users?p=<?= $prevPagenumber ?>">Пользователи</a></h3><br>
    <div style="float:left;margin-right:1%;max-width:40%">
        <img src="/<?= $profile->getAvatarUrl() ?>" style="max-width:100%">
        <?php if ($rights->canOneOf(['edit-all' => $profile, 'edit-own' => $profile])) :
            ?>
            <?php if ($profile->avatar) : ?><br><a class="link" href="<?= $deleteAvatar->getUrl() ?>">Удалить аватар</a><?php endif ?>
            <br><a class="link" href="/admin/users/edit/profile?id=<?= $profile->id ?>">Редактировать профиль</a>
        <?php endif ?>
    </div>
    <div>
        Логин: <b><?= $profile->login ?></b>
        <?php if ($profile->name || $profile->surname): ?>
            <br />Имя: <?= $profile->name ?> <?= $profile->surname ?>
        <?php endif ?>
        <br />Пол: <?= $gender->name ?>
        <?php if ($profile->email) echo '<br/>E-mail: ', $profile->email ?>
        <br />Дата регистрации: <?= date('d.m.Y H:i', $profile->registration_date) ?>
        <br />Последний раз онлайн: <?php if ($profile->last_online_time): ?><?= date('d.m.Y H:i', $profile->last_online_time) ?>
        <?php else : ?>Никогда<?php endif ?>
        <br>Последнее устройство: <?= $profile->last_user_agent ?>
        <br />Группа: <?= $group->name ?>
        <?php if ((int)$me->group_id === Group::ROOT_ID && $group->id !== Group::ROOT_ID) : ?>
            <a class="link" href="/admin/users/change/group?id=<?= $profile->id ?>">[Изменить]</a>
        <?php endif ?>
        <br />Статус: <?php if ($profile->online === '1'): ?><span style="color:green">Онлайн</span>
        <?php else: ?><span style="color:red">Оффлайн</span><?php endif ?>
    </div>
</div>