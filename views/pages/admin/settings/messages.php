<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;
use frame\tools\JsonEncoder;

Init::accessGroup(Group::ROOT_ID);

$config = config::get('messages');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'messages']);

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Диалогов на странице списка',
        'name' => 'dialogs->list->amount',
        'defaultValue' => $edit->getPost(
            'dialogs->list->amount',
            (string) $config->{'dialogs.list.amount'}
        )
    ], [
        'type' => 'text',
        'title' => 'Сообщений на странице диалога',
        'name' => 'messages->list->amount',
        'defaultValue' => $edit->getPost(
            'messages->list->amount',
            (string) $config->{'messages.list.amount'}
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
        <span class="breadcrumbs__item breadcrumbs__item--current">Сообщения</span>
    </div>
</div>
<div class="box">
    <div id="messages-config-form" data-props="<?= $formProps ?>"></div>
</div>