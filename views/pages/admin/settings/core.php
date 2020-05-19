<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use engine\users\Group;
use engine\users\User;
use frame\config\ConfigRouter;
use engine\admin\actions\EditConfigAction;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;

$me = User::getMe();

InitAccess::access((int) $me->group_id === Group::ROOT_ID);

$config = ConfigRouter::getDriver()->findConfig('core');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'core']);

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Название сайта',
        'name' => 'site->name',
        'defaultValue' => $edit->getPost(
            'site->name',
            $config->{'site.name'}
        )
    ], [
        'type' => 'checkbox',
        'title' => 'Режим отладки',
        'name' => 'mode->debug',
        'label' => 'Включить',
        'defaultChecked' => $edit->getPost(
            'mode->debug',
            $config->{'mode.debug'}
        )
    ], [
        'type' => 'checkbox',
        'title' => 'Логирование',
        'name' => 'log->enabled',
        'label' => 'Включить',
        'defaultChecked' => $edit->getPost(
            'log->enabled',
            $config->{'log.enabled'}
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
        <span class="breadcrumbs__item breadcrumbs__item--current">Общие</span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>