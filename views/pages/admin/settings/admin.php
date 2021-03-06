<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use engine\users\Group;
use frame\config\ConfigRouter;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;
use engine\admin\actions\EditAdminConfig;

InitAccess::accessGroup(Group::ROOT_ID);

$config = ConfigRouter::getDriver()->findConfig('admin');
$edit = new ViewAction(EditAdminConfig::class);

$currentPasswordErrors = [];
if ($edit->hasError(EditAdminConfig::E_WRONG_CURRENT_PASSWORD))
    $currentPasswordErrors[] = 'Неверный пароль';

$newPasswordErrors = [];
if ($edit->hasError(EditAdminConfig::E_EMPTY_NEW_PASSWORD))
    $newPasswordErrors[] = 'Пароль не задан';

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'password',
        'title' => 'Текущий пароль',
        'name' => 'current-password',
        'errors' => $currentPasswordErrors
    ], [
        'type' => 'password',
        'title' => 'Новый пароль',
        'name' => 'new-password',
        'errors' => $newPasswordErrors
    ]],
    'buttonText' => 'Сохранить',
    'className' => 'form--short'
];

$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Настройки</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Админ-панель</span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>