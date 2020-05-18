<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\route\InitRoute;
use engine\users\Group;
use engine\admin\actions\EditUserGroupAction;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;

InitAccess::accessGroup(Group::ROOT_ID);
$id = (int)InitRoute::requireGet('id');
$group = Group::selectIdentity($id);

InitRoute::require($group !== null);

$action = new ViewAction(EditUserGroupAction::class, ['id' => $id]);

$formProps = [
    'actionUrl' => $action->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Название',
        'name' => 'name',
        'defaultValue' => $action->getPost('name', $group->name)
    ]],
    'buttonText' => 'Сохранить',
    'className' => 'form--short'
];

$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/users/groups" class="breadcrumbs__item breadcrumbs__item--link">Группы</a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">ID <?= $group->id ?></span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>