<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use frame\tools\JsonEncoder;
use frame\actions\ViewAction;
use engine\articles\actions\NewArticleAction;

Init::accessRight('articles', 'add');

$add = new ViewAction(NewArticleAction::class);

$errorMessages = [];
if ($add->hasError(NewArticleAction::E_NO_TITLE))
    $errorMessages[] = 'Название не указано';
if ($add->hasError(NewArticleAction::E_NO_TEXT))
    $errorMessages[] = 'Текст не указан';
if ($add->hasError(NewArticleAction::E_LONG_TITLE))
    $errorMessages[] = 'Название слишком длинное';

$formProps = JsonEncoder::forHtmlAttribute([
    'actionUrl' => $add->getUrl(),
    'method' => 'post',
    'fields' => [[
        'title' => 'Название',
        'name' => 'title',
        'type' => 'text',
        'defaultValue' => $add->getPost('title')
    ], [
        'title' => 'Текст',
        'name' => 'text',
        'type' => 'textarea',
        'defaultValue' => $add->getPost('text')
    ]],
    'errors' => $errorMessages,
    'buttonText' => 'Добавить'
]);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/articles" class="breadcrumbs__item breadcrumbs__item--link">
            Статьи
        </a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">Добавить</span>
    </div>
</div>

<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>