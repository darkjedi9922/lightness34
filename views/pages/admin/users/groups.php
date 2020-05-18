<?php /** @var \frame\views\Page $self */

use engine\admin\actions\DeleteUserGroupAction;
use frame\actions\ViewAction;
use engine\users\Group;
use engine\admin\actions\NewUserGroupAction;
use frame\lists\base\IdentityList;
use frame\auth\InitAccess;
use frame\tools\JsonEncoder;

InitAccess::accessGroup(Group::ROOT_ID);
$groups = new IdentityList(Group::class);
$newGroup  = new ViewAction(NewUserGroupAction::class);
$delGroup  = new ViewAction(DeleteUserGroupAction::class);

$nameErrors = [];
if ($newGroup->hasError(NewUserGroupAction::E_NO_NAME))
    $nameErrors[] = 'Название не указано';

$formProps = [
    'actionUrl' => $newGroup->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Название',
        'name' => 'name',
        'defaultValue' => $newGroup->getPost('name', ''),
        'errors' => $nameErrors
    ]],
    'buttonText' => 'Добавить',
    'className' => 'form--short'
];

$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Пользователи</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Группы</span>
    </div>
</div>
<div class="box">
    <table width="100%">
        <?php foreach ($groups as $group) : /** @var Group $group */ ?>
            <?php $delGroup->setArg('id', $group->id) ?>
            <tr>
                <td>ID: <?= $group->id ?></td>
                <td><?= $group->name ?></td>
                <td><a href="/admin/users/group?id=<?= $group->id ?>" class="button">Редактировать</a></td>
                <td><a href="/admin/users/rights?id=<?= $group->id ?>" class="button">Права</a></td>
                <td><?php if (!$group->isSystem()) : ?><a href="<?= $delGroup->getUrl() ?>" class="button">Удалить</a><?php endif ?></td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
<span class="content__title">Добавить группу</span>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>