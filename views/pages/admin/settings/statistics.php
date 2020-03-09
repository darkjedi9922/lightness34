<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\cash\config;
use frame\actions\ViewAction;
use engine\statistics\actions\EditConfig;
use frame\tools\JsonEncoder;
use frame\tools\units\TimeUnit;

Init::accessRight('stat', 'configure');

$config = config::get('statistics');
$edit = new ViewAction(EditConfig::class, ['name' => 'statistics']);
$storeTime = new TimeUnit($config->storeTimeInSeconds, TimeUnit::SECONDS);
list($maxStoreTimeIntValue, $maxStoreTimeIntUnit) = $storeTime->calcMaxInt([
    TimeUnit::HOURS, TimeUnit::DAYS, TimeUnit::MONTHS
]);

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
            $maxStoreTimeIntValue
        )
    ], [
        'type' => 'radio',
        'name' => 'storeTimeUnit',
        'title' => '',
        'values' => [[
            'label' => 'Часы',
            'value' => TimeUnit::HOURS
        ], [
            'label' => 'Дни',
            'value' => TimeUnit::DAYS
        ], [
            'label' => 'Месяцы',
            'value' => TimeUnit::MONTHS
        ]],
        'currentValue' => $edit->getPost(
            'storeTimeUnit',
            $maxStoreTimeIntUnit
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