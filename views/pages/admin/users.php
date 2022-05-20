<?php /** @var frame\views\Page $self */

use frame\lists\paged\PagerModel;
use engine\users\User;
use engine\users\Group;
use engine\users\Gender;
use frame\auth\InitAccess;
use frame\config\ConfigRouter;
use frame\database\Records;
use frame\lists\base\IdentityList;

InitAccess::accessRight('users', 'see-list');

$pageLimit = ConfigRouter::getDriver()->findConfig('users')->{'list.amount'};
$countUsers = Records::from(User::getTable())->count('id');
$pager = new PagerModel(PagerModel::getRoutePage(), $countUsers, $pageLimit);
$users = new IdentityList(User::class, ['id' => 'ASC'], $pager->getOffset(), $pager->getLimit());
$rights = User::getMyRights('users');

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
            Пользователи (<?= $users->count() ?>)
        </span>
    </div>
    <?php if ($pager->countPages() > 1): ?>
    <div class="content__pager">
        <?php $pager->show('admin') ?>
    </div>
    <?php endif ?>
    <?php if ($rights->can('add')): ?>
        <div class="actions">
            <div class="actions__item">
                <a href="/admin/users/add" class="button">Добавить пользователя</a>
            </div>
        </div>
    <?php endif ?>
</div>
<div id="users" data-props='<?= json_encode($tableProps, JSON_HEX_AMP) ?>'></div>