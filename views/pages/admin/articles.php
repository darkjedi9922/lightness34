<?php /** @var frame\views\Page $self */

use frame\lists\paged\PagerModel;
use engine\articles\Article;
use engine\articles\ArticlePagedList;
use engine\users\User;
use function lightlib\shorten;
use frame\tools\JsonEncoder;
use frame\auth\InitAccess;

InitAccess::accessRight('articles', 'see-list');
$pagenumber = PagerModel::getRoutePage();
$articles = new ArticlePagedList($pagenumber);
$rights = User::getMyRights('articles');

$tableProps = ['items' => []];
foreach ($articles as $article) {
    /** @var Article $article */
    $author = User::selectIdentity($article->author_id);
    $item = [
        'id' => $article->id,
        'title' => shorten($article->title, 80, '...'),
        'author' => $author->login,
        'date' => date('d.m.Y H:i', $article->date)
    ];
    $tableProps['items'][] = $item;
}
$tableProps = JsonEncoder::forHtmlAttribute($tableProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item breadcrumbs__item--current">
            Статьи (<?= $articles->countOnPage() ?>)
        </span>
    </div>
    <div class="actions">
        <?php if ($articles->getPager()->countPages() > 1) : ?>
            <div class="actions__item">
                <?php $articles->getPager()->show('admin') ?>
            </div>
        <?php endif ?>
        <?php if ($rights->can('add')): ?>
            <div class="actions__item">
                <a href="/admin/article/new" class="button">Добавить статью</a>
            </div>
        <?php endif ?>
    </div>
</div>
<div id="articles" data-props="<?= $tableProps ?>"></div>