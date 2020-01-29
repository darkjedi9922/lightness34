<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\articles\Article;
use frame\actions\ViewAction;
use engine\articles\actions\EditArticleAction;
use frame\tools\JsonEncoder;

$id = (int)Init::requireGet('id');
$article = Article::selectIdentity($id);

Init::require($article !== null);
Init::accessOneRight('articles', [
    'edit-own' => [$article],
    'edit-all' => null
]);

$edit = new ViewAction(EditArticleAction::class, ['id' => $id]);

$errorMessages = [];
if ($edit->hasError(EditArticleAction::E_NO_TITLE))
    $errorMessages[] = 'Название не указано';
if ($edit->hasError(EditArticleAction::E_NO_TEXT))
    $errorMessages[] = 'Текст не указан';
if ($edit->hasError(EditArticleAction::E_LONG_TITLE))
    $errorMessages[] = 'Название слишком длинное';

$formProps = JsonEncoder::forHtmlAttribute([
    'actionUrl' => $edit->getUrl(),
    'method' => 'post',
    'fields' => [[
        'title' => 'Название',
        'name' => 'title',
        'type' => 'text',
        'defaultValue' => $edit->getPost('title', $article->title)
    ], [
        'title' => 'Текст',
        'name' => 'text',
        'type' => 'textarea',
        'defaultValue' => $edit->getPost('text', $article->content)
    ]],
    'errors' => $errorMessages,
    'buttonText' => 'Сохранить'
]);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/articles" class="breadcrumbs__item breadcrumbs__item--link">
            Статьи
        </a>
        <span class="breadcrumbs__divisor"></span>
        <a href="/admin/article?id=<?= $id ?>" class="breadcrumbs__item breadcrumbs__item--link">
            ID <?= $id ?>
        </a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">
            Редактировать
        </span>
    </div>
</div>

<div class="box">
    <div class="react-form" data-props="<?= $formProps ?>"></div>
</div>