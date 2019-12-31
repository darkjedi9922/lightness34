<?php /** @var frame\views\Page $self */

use frame\cash\pagenumber;
use engine\users\UserPagedList;
use engine\users\User;
use engine\users\Group;
use engine\users\Gender;

$pagenumber = pagenumber::get();
$users = new UserPagedList($pagenumber);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item breadcrumbs__item--current">Пользователи</span>
    </div>
</div>
<div class="box">
    <table width="100%">
        <tr>
            <td><b>ID</b></td>
            <td><b>Логин</b></td>
            <td><b>Имя</b></td>
            <td><b>Группа</b></td>
            <td><b>Пол</b></td>
            <td><b>E-mail</b></td>
        </tr>
        <?php foreach ($users as $user) : /** @var User $user */ ?>
        <?php $group = Group::selectIdentity($user->group_id) ?>
        <?php $gender = Gender::selectIdentity($user->gender_id) ?>
            <tr>
                <td><?= $user->id ?></td>
                <td><a class="link" href="/admin/users/profile/<?= $user->login ?>"><?= $user->login ?></a></td>
                <td><?= "{$user->name} {$user->surname}" ?></td>
                <td><?= $group->name ?></td>
                <td><?= $gender->name ?></td>
                <td><?= $user->email ?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php $users->getPager()->show('admin') ?>
</div>