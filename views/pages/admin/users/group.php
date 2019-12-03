<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use engine\users\cash\user_me;
use engine\admin\actions\EditUserGroupAction;
use frame\actions\ViewAction;

$self->setLayout('admin');

$me = user_me::get();

Init::access((int) $me->group_id === Group::ROOT_ID);

$id = (int) Init::requireGet('id');
$group = Group::selectIdentity($id);

Init::require($group !== null);

$action = new ViewAction(EditUserGroupAction::class, ['id' => $id]);
?>

<div class="box">
    <h3><a href="/admin/users/groups">Группы</a> - ID: <?= $group->id ?></h3><br>
    <form action="<?= $action->getUrl() ?>" method="post">
        <table>
            <tr>
                <td>Название:</td>
                <td><input name="name" type="text" value="<?= $action->getPost('name', $group->name) ?>"></td>
            </tr>
            <tr>
                <td>Иконка:</td>
                <td><input name="icon" type="text" value="<?= $action->getPost('icon', $group->icon) ?>"></td>
            </tr>
        </table>
        <button>Сохранить</button>
    </form>
</div>