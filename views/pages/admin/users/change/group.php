<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\route\InitRoute;
use frame\lists\base\IdentityList;
use engine\users\Group;
use engine\users\User;
use engine\users\actions\ChangeUserGroupAction;
use frame\actions\ViewAction;
use engine\users\cash\user_me;
use frame\tools\JsonEncoder;

$me = user_me::get();

InitAccess::access((int)$me->group_id === Group::ROOT_ID);

$id = (int)InitRoute::requireGet('id');
$user = User::selectIdentity($id);

InitRoute::require($user !== null);
InitRoute::require((int)$user->group_id !== Group::ROOT_ID);

$groups = new IdentityList(Group::class);
$action = new ViewAction(ChangeUserGroupAction::class, ['uid' => $id]);

$groupsProps = [];
foreach ($groups as $group) {
    if ($group->id === GROUP::GUEST_ID || $group->id === Group::ROOT_ID) continue;
    $groupsProps[] = [
        'label' => $group->name,
        'value' => (string)$group->id
    ];
}

$formProps = [
    'actionUrl' => $action->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'radio',
        'name' => 'group_id',
        'values' => $groupsProps,
        'currentValue' => (string)$user->group_id,
        'short' => true
    ]],
    'buttonText' => 'Сохранить'
];
$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/users" class="breadcrumbs__item breadcrumbs__item--link">Пользователи</a>
        <span class="breadcrumbs__divisor"></span>
        <a href="/admin/users/profile/<?= $user->login ?>" class="breadcrumbs__item breadcrumbs__item--link"><?= $user->login ?></a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Изменить группу</span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>