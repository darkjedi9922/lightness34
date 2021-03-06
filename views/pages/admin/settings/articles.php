<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\config\ConfigRouter;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;
use frame\tools\JsonEncoder;

InitAccess::accessRight('articles', 'configure');
$config = ConfigRouter::getDriver()->findConfig('articles');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'articles']);

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Максимальная длина названия',
        'name' => 'title->maxLength',
        'defaultValue' => $edit->getPost(
            'title->maxLength',
            (string) $config->{'title.maxLength'}
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
        <span class="breadcrumbs__item breadcrumbs__item--current">Статьи</span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>