<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\statistics\actions\EditConfig;
use frame\tools\JsonEncoder;

Init::accessRight('stat', 'configure');

$config = config::get('statistics');
$edit = new ViewAction(EditConfig::class, ['name' => 'statistics']);

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
        'title' => 'Хранить данные за последние',
        'name' => 'storeTimeValue',
        'defaultValue' => (string)$edit->getPost(
            'storeTimeValue',
            EditConfig::calcStoreTimeFromSeconds(
                $config->storeTimeInSeconds,
                EditConfig::STORE_TIME_UNIT_HOURS
            )
        )
    ], [
        'type' => 'radio',
        'name' => 'storeTimeUnit',
        'title' => '',
        'values' => [[
            'label' => 'Часы',
            'value' => EditConfig::STORE_TIME_UNIT_HOURS
        ], [
            'label' => 'Дни',
            'value' => EditConfig::STORE_TIME_UNIT_DAYS
        ], [
            'label' => 'Месяцы',
            'value' => EditConfig::STORE_TIME_UNIT_MONTHS
        ]],
        'currentValue' => $edit->getPost(
            'storeTimeUnit',
            EditConfig::STORE_TIME_UNIT_HOURS
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
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>