<?php /** @var frame\views\Page $self */

use engine\statistics\actions\ClearAllStats;
use frame\auth\InitAccess;
use frame\stdlib\cash\config;
use frame\actions\ViewAction;
use engine\statistics\actions\EditConfig;
use frame\tools\JsonEncoder;

InitAccess::accessRight('stat', 'configure');

$config = config::get('statistics');
$edit = new ViewAction(EditConfig::class, ['name' => 'statistics']);
$clearAll = new ViewAction(ClearAllStats::class);

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'checkbox',
        'title' => 'Собирать статистику',
        'name' => 'enabled',
        'label' => 'Собирать',
        'defaultChecked' => $edit->getPost('enabled', $config->{'enabled'})
    ], [
        'type' => 'text',
        'title' => 'Лимит записей на странице истории',
        'name' => 'historyListLimit',
        'defaultValue' => (string)$edit->getPost(
            'historyListLimit',
            $config->{'historyListLimit'}
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
    <div class="actions">
        <a href="<?= $clearAll->getUrl() ?>" class="button button--red">
            <i class="icon-attention button__icon"></i>Очистить статистику
        </a>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>