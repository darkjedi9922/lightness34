<?php /** @var frame\views\Page $self */

use frame\cash\pagenumber;
use engine\users\UserPagedList;
use engine\users\User;
use engine\users\Group;
use engine\users\Gender;

$pagenumber = pagenumber::get();
$users = new UserPagedList($pagenumber);

$tableProps = ['items' => []];
foreach ($users as $user) {
    /** @var User $user */
    $group = Group::selectIdentity($user->group_id);
    $gender = Gender::selectIdentity($user->gender_id);
    $tableProps['items'][] = [
        'id' => $user->id,
        'login' => $user->login,
        'name' => $user->name,
        'surname' => $user->surname,
        'group' => $group->name,
        'gender' => $gender->name,
        'email' => $user->email,
        'avatarUrl' => '/' . $user->getAvatarUrl()
    ];
}
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item breadcrumbs__item--current">
            Пользователи (<?= $users->countOnPage() ?>)
        </span>
    </div>
    <?php if ($users->getPager()->countPages() > 1): ?>
    <div class="content__pager">
        <?php $users->getPager()->show('admin') ?>
    </div>
    <?php endif ?>
    <div class="actions">
        <div class="actions__item">
            <a href="/admin/users/add" class="button">Добавить пользователя</a>
        </div>
    </div>
</div>
<div id="users" data-props='<?= json_encode($tableProps, JSON_HEX_AMP) ?>'></div>