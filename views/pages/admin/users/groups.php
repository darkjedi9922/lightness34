<?php /** @var \frame\views\Page $self */

use engine\admin\actions\DeleteUserGroupAction;
use frame\actions\ViewAction;
use engine\users\cash\my_group;
use engine\users\Group;
use engine\admin\actions\NewUserGroupAction;
use frame\lists\base\IdentityList;
use frame\tools\Init;

$myGroup = my_group::get();

Init::access($myGroup->id === $myGroup::ROOT_ID);

$groups = new IdentityList(Group::class);
$newGroup  = new ViewAction(NewUserGroupAction::class);
$delGroup  = new ViewAction(DeleteUserGroupAction::class);
?>

<div class="box">
    <table width="100%">
        <?php foreach($groups as $group): /** @var Group $group */ ?>
        <?php $delGroup->setArg('id', $group->id) ?>
            <tr>
                <td>ID: <?= $group->id ?></td>
                <td><?= $group->name ?></td>
                <td><a href="/admin/users/group?id=<?= $group->id ?>" class="button">Редактировать</a></td>
                <td><a href="/admin/users/rights?id=<?= $group->id ?>" class="button">Права</a></td>
                <td><?php if (!$group->isSystem()): ?><a href="<?= $delGroup->getUrl() ?>" class="button">Удалить</a><?php endif ?></td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
<div class="box">
    <h3>Добавить</h3><br>
    <?php if ($newGroup->hasError(NewUserGroupAction::E_NO_NAME)): ?>
        <span class="error" style="margin-bottom:7px">Название не указано</span>
    <?php endif ?>
    <form action="<?= $newGroup->getUrl() ?>" method="post">
        <table>
            <tr><td>Название:</td><td><input name="name" type="text"></td></tr>
        </table>
        <button>Добавить</button>
    </form>
</div>