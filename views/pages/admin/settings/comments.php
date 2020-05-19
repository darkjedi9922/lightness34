<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\config\ConfigRouter;
use frame\actions\ViewAction;
use engine\admin\actions\EditConfigAction;
use frame\tools\JsonEncoder;

InitAccess::accessRight('articles/comments', 'configure');
$config = ConfigRouter::getDriver()->findConfig('comments');
$edit = new ViewAction(EditConfigAction::class, ['name' => 'comments']);

$formProps = [
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'type' => 'text',
        'title' => 'Количество на странице списка',
        'name' => 'list->amount',
        'defaultValue' => $edit->getPost(
            'list->amount',
            (string) $config->{'list.amount'}
        )
    ], [
        'type' => 'radio',
        'title' => 'Порядок',
        'name' => 'list->order',
        'values' => [[
            'label' => 'Сначала старые',
            'value' => 'ASC'
        ], [
            'label' => 'Сначала новые',
            'value' => 'DESC'
        ]],
        'currentValue' => $edit->getPost(
            'list->order',
            $config->get('list.order')
        )
    ], [
        'type' => 'checkbox',
        'title' => 'Новые комментарии на странице новостей',
        'name' => 'new->setReadedOnNewsPage',
        'label' => 'Отмечать просмотренными',
        'defaultChecked' => $edit->getPost(
            'new->setReadedOnNewsPage',
            $config->get('new.setReadedOnNewsPage')
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
        <span class="breadcrumbs__item breadcrumbs__item--current">Комментарии</span>
    </div>
</div>
<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>