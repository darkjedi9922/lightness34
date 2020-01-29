<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;
use engine\admin\actions\EditAdminConfig;

Init::accessGroup(Group::ROOT_ID);

$config = config::get('admin');
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

<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>