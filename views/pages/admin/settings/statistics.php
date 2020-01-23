<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;
use frame\tools\JsonEncoder;

Init::accessGroup(Group::ROOT_ID);

$config = config::get('statistics');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'statistics']);

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'checkbox',
        'title' => 'Собирать статистику',
        'name' => 'enabled',
        'label' => 'Собирать',
        'defaultChecked' => $edit->getPost('enabled', (string)$config->{'enabled'})
    ], [
        'type' => 'text',
        'title' => 'Лимит истории маршрутов',
        'name' => 'routes->history->limit',
        'defaultValue' => $edit->getPost(
            'routes->history->limit',
            (string)$config->{'routes.history.limit'}
        )
    ], [
        'type' => 'text',
        'title' => 'Лимит истории событий',
        'name' => 'events->history->limit',
        'defaultValue' => $edit->getPost(
            'events->history->limit',
            (string)$config->{'events.history.limit'}
        )
    ], [
        'type' => 'text',
        'title' => 'Лимит истории видов',
        'name' => 'views->history->limit',
        'defaultValue' => $edit->getPost(
            'views->history->limit',
            (string)$config->{'views.history.limit'}
        )
    ], [
        'type' => 'text',
        'title' => 'Лимит истории действий',
        'name' => 'actions->history->limit',
        'defaultValue' => $edit->getPost(
            'actions->history->limit',
            (string)$config->{'actions.history.limit'}
        )
    ], [
        'type' => 'text',
        'title' => 'Лимит истории запросов БД',
        'name' => 'queries->history->limit',
        'defaultValue' => $edit->getPost(
            'queries->history->limit',
            (string)$config->{'queries.history.limit'}
        )
    ], [
        'type' => 'text',
        'title' => 'Лимит истории кеша',
        'name' => 'cash->history->limit',
        'defaultValue' => $edit->getPost(
            'cash->history->limit',
            (string)$config->{'cash.history.limit'}
        )
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
        <span class="breadcrumbs__item breadcrumbs__item--current">Мониторинг</span>
    </div>
</div>
<div class="box">
    <div id="stat-config-form" data-props="<?= $formProps ?>"></div>
</div>