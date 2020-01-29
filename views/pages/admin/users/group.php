<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use engine\users\cash\user_me;
use engine\admin\actions\EditUserGroupAction;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;

$me = user_me::get();

Init::access((int)$me->group_id === Group::ROOT_ID);

$id = (int)Init::requireGet('id');
$group = Group::selectIdentity($id);

Init::require($group !== null);

$action = new ViewAction(EditUserGroupAction::class, ['id' => $id]);

$formProps = [
    'actionUrl' => $action->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Название',
        'name' => 'name',
        'defaultValue' => $action->getPost('name', $group->name)
    ], [
        'type' => 'text',
        'title' => 'Иконка',
        'name' => 'icon',
        'defaultValue' => $action->getPost('icon', $group->icon)
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