<?php /** @var frame\views\Page $self */

use cash\pagenumber;
use engine\users\UserPagedList;
use engine\users\User;
use engine\users\Group;
use engine\users\Gender;

$self->setLayout('admin');

$pagenumber = pagenumber::get();
$users = new UserPagedList($pagenumber);
?>

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
                <td><a href="/admin/users/profile/<?= $user->login ?>"><?= $user->login ?></a></td>
                <td><?= "{$user->name} {$user->surname}" ?></td>
                <td><?= $group->name ?></td>
                <td><?= $gender->name ?></td>
                <td><?= $user->email ?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php $users->getPager()->show('admin') ?>
</div>