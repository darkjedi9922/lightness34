<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\config\ConfigRouter;
use engine\admin\actions\EditConfigAction;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;

InitAccess::accessRight('users', 'setup');
$config = ConfigRouter::getDriver()->findConfig('users');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'users']);

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Максимальная длина логина',
        'name' => 'login->max_length',
        'defaultValue' => $edit->getPost(
            'login->max_length',
            (string) $config->{'login.max_length'}
        )
    ], [
        'type' => 'text',
        'title' => 'Максимальная длина пароля',
        'name' => 'password->max_length',
        'defaultValue' => $edit->getPost(
            'password->max_length',
            (string) $config->{'password.max_length'}
        )
    ], [
        'type' => 'text',
        'title' => 'Максимальный размер аватара',
        'name' => 'avatar->max_size->value',
        'defaultValue' => $edit->getPost(
            'avatar->max_size->value',
            (string) $config->{'avatar.max_size.value'}
        )
    ], [
        'type' => 'radio',
        'title' => '',
        'name' => 'avatar->max_size->unit',
        'values' => [[
            'label' => 'KB',
            'value' => 'KB'
        ], [
            'label' => 'MB',
            'value' => 'MB'
        ]],
        'currentValue' => $edit->getPost(
            'avatar->max_size->unit',
            $config->{'avatar.max_size.unit'}
        )
    ], [
        'type' => 'text',
        'title' => 'Количество на странице списка',
        'name' => 'list->amount',
        'defaultValue' => $edit->getPost(
            'list->amount',
            (string) $config->{'list.amount'}
        )
    ]],
    'buttonText' => 'Сохранить',
    'short' => true
];
$formProps = JsonEncoder::forHtmlAttribute($formProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Настройки</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item">Пользователи</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Общие</span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>