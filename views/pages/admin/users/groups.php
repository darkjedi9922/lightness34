<?php /** @var \frame\views\Page $self */

use engine\users\cash\my_group;
use engine\users\Group;
use frame\lists\IdentityList;
use frame\tools\Init;

$self->setLayout('admin');

$myGroup = my_group::get();

Init::access($myGroup->id === $myGroup::ROOT_ID);

$groups = new IdentityList(Group::class);

// $NEW_GROUP  = new NewUserGroupAction;
// $DEL_GROUP  = new DeleteUserGroupAction;
?>

<div class="box">
    <table width="100%">
        <?php foreach($groups as $group): /** @var Group $group */ ?>
            <tr>
                <td>ID: <?= $group->id ?></td>
                <td><?= $group->name ?></td>
                <td><a href="/admin/users/group?id=<?= $group->id ?>" class="button">Редактировать</a></td>
                <td><a href="/admin/users/rights?id=<?= $group->id ?>" class="button">Права</a></td>
                <td><?php if (!$group->isSystem()): ?><a href="<?php //$DEL_GROUP->getUrl() ?>" class="button">Удалить</a><?php endif ?></td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
<div class="box">
    <h3>Добавить</h3><br>
    <?php //if ($NEW_GROUP->hasError($NEW_GROUP::E_NO_NAME)): ?><span class="error" style="margin-bottom:7px">Название не указано</span><?php //endif ?>
    <form action="<?php //$NEW_GROUP->getUrl() ?>" method="post">
        <table>
            <tr><td>Название:</td><td><input name="name" type="text"></td></tr>
        </table>
        <button>Добавить</button>
    </form>
</div>